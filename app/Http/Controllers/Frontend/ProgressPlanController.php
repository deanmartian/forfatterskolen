<?php

namespace App\Http\Controllers\Frontend;

use AdminHelpers;
use App\CopyEditingManuscript;
use App\CorrectionManuscript;
use App\Http\Controllers\Controller;
use App\ProjectAudio;
use App\ProjectEbook;
use App\ProjectGraphicWork;
use App\ProjectManuscript;
use App\ProjectRegistration;
use App\ProjectRoadmapStep;
use App\ProjectTypeSetting;
use App\Services\ProjectService;
use Auth;
use FrontendHelpers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProgressPlanController extends Controller
{
    public function index()
    {
        $standardProject = FrontendHelpers::getLearnerStandardProject(Auth::id());

        $steps = [];

        if ($standardProject) {
            // Get saved steps from DB, keyed by step number
            $saved = ProjectRoadmapStep::where('project_id', $standardProject->id)
                ->get()
                ->keyBy('step_number');

            // Build full step list from constants
            $steps = collect(ProjectRoadmapStep::STEPS)->map(function ($title, $number) use ($saved) {
                $step = $saved->get($number);

                return [
                    'step_number' => $number,
                    'title' => $title,
                    'status_text' => $step->status_text ?? 'Not Planned',
                    'expected_date' => $step->expected_date ?? null,
                ];
            });
        }

        return view('frontend.learner.self-publishing.progress-plan', compact('steps'));
    }

    public function planStep($stepNumber): View
    {
        $standardProject = FrontendHelpers::getLearnerStandardProject(Auth::id());
        if (! $standardProject) {
            abort(404);
        }

        $projectId = $standardProject->id;
        $stepTitle = ProjectRoadmapStep::STEPS[$stepNumber] ?? 'Ukjent steg'; // Default if step doesn't exist

        switch ($stepNumber) {
            case 1:
                $manuscripts = ProjectManuscript::where('project_id', $projectId)->get();

                return view('frontend.learner.self-publishing.progress-plan-steps.manuscripts',
                    compact('stepNumber', 'stepTitle', 'manuscripts'));
                break;
            case 2:
                $copyEditings = $standardProject ? CopyEditingManuscript::leftJoin('projects',
                    'copy_editing_manuscripts.project_id', '=', 'projects.id')
                    ->select('copy_editing_manuscripts.*')
                    ->where('copy_editing_manuscripts.user_id', Auth::id())
                    ->where('projects.id', $projectId)->latest('copy_editing_manuscripts.created_at')->get() : [];

                return view('frontend.learner.self-publishing.progress-plan-steps.copy-editing', compact('copyEditings'));
            case 3:
                $corrections = CorrectionManuscript::leftJoin('projects', 'correction_manuscripts.project_id', '=', 'projects.id')
                    ->select('correction_manuscripts.*')
                    ->where('correction_manuscripts.user_id', Auth::id())
                    ->where('projects.id', $projectId)
                    ->latest('correction_manuscripts.created_at')->get();

                return view('frontend.learner.self-publishing.progress-plan-steps.correction', compact('corrections'));
            case 4:
                $covers = ProjectGraphicWork::cover()->where('project_id', $projectId)->get();
                $isbns = ProjectRegistration::isbns()->where('project_id', $projectId)->get();

                return view('frontend.learner.self-publishing.progress-plan-steps.cover', compact('covers', 'isbns'));
            case 5:
                $settings = ProjectTypeSetting::where('project_id', $projectId)->get();

                return view('frontend.learner.self-publishing.progress-plan-steps.type_setting', compact('stepTitle', 'settings'));
            case 6:
                $epubs = ProjectEbook::epub()->where('project_id', $projectId)->get();
                $mobis = ProjectEbook::mobi()->where('project_id', $projectId)->get();
                $covers = ProjectEbook::cover()->where('project_id', $projectId)->get();
                $saveEbookRoute = 'learner.progress-plan.save-ebook';

                return view('frontend.learner.self-publishing.progress-plan-steps.e-book', compact('epubs', 'saveEbookRoute',
                    'mobis', 'covers'));
            case 7:
                $files = ProjectAudio::files()->where('project_id', $projectId)->get();
                $covers = ProjectAudio::cover()->where('project_id', $projectId)->get();
                $saveAudioRoute = 'learner.progress-plan.save-audio';

                return view('frontend.learner.self-publishing.progress-plan-steps.audio', compact('stepTitle', 'files', 'covers',
                    'saveAudioRoute'));
            case 8:
                $print = $standardProject->print;
                $savePrintRoute = 'learner.progress-plan.save-print';

                return view('frontend.learner.self-publishing.progress-plan-steps.print', compact('stepTitle', 'print',
                    'savePrintRoute'));
            default:
                $view = 'frontend.learner.self-publishing.progress-plan-step';
                break;
        }

        return view('frontend.learner.self-publishing.progress-plan-step', compact('stepNumber', 'stepTitle'));

    }

    public function uploadManuscript(Request $request): RedirectResponse
    {
        $extensions = ['pdf', 'doc', 'docx', 'odt'];

        if ($request->hasFile('manuscript') && $request->file('manuscript')->isValid()) {
            $extension = pathinfo($_FILES['manuscript']['name'], PATHINFO_EXTENSION);
            $actual_name = pathinfo($_FILES['manuscript']['name'], PATHINFO_FILENAME);

            if (! in_array($extension, $extensions)) {
                return redirect()->back()->with(
                    'manuscript_test_error', 'Invalid file format. Allowed formats are PDF, DOC, DOCX, ODT'
                );
            }

            $standardProject = FrontendHelpers::getLearnerStandardProject(Auth::id());
            $destinationPath = 'Forfatterskolen_app/project/project-'.$standardProject->id.'/project-manuscripts/';
            $fileName = AdminHelpers::getUniqueFilename('dropbox', $destinationPath, $actual_name.'.'.$extension);
            $expFileName = explode('/', $fileName);
            $dropboxFileName = end($expFileName);

            $request->file('manuscript')->storeAs($destinationPath, $dropboxFileName, 'dropbox');
            $wholeFilePath = $destinationPath.$dropboxFileName;
            $filePath = '/'.$wholeFilePath;

            ProjectManuscript::create([
                'project_id' => $standardProject->id,
                'file' => $filePath,
            ]);

            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag(trans('site.learner.upload-manuscript-success')),
                'alert_type' => 'success',
            ]);
        }

    }

    public function uploadOtherServiceManuscript($type, Request $request, ProjectService $projectService): RedirectResponse
    {
        $request->validate(['manuscript' => 'required']);

        if (in_array($type, [1, 2])) {
            $model = $type == 1 ? CopyEditingManuscript::class : CorrectionManuscript::class;
            $data = $request->id ? $model::find($request->id) : new $model;
            $request->merge(['type' => $type]);

            $folderName = $type == 1 ? 'copy-editing-manuscripts' : 'correction-manuscripts';
            $projectId = $data->project_id ?? $request->project_id;
            $destinationPath = "Forfatterskolen_app/project/project-{$projectId}/{$folderName}/";

            $requestFilename = 'manuscript';
            $file = \request()->file($requestFilename);

            $extension = pathinfo($_FILES[$requestFilename]['name'], PATHINFO_EXTENSION);
            $original_filename = $file->getClientOriginalName();
            $actual_name = pathinfo($original_filename, PATHINFO_FILENAME);

            $fileName = AdminHelpers::getUniqueFilename('dropbox', $destinationPath, $actual_name.'.'.$extension);
            $expFileName = explode('/', $fileName);
            $dropboxFileName = end($expFileName);

            // Store the file in Dropbox
            $file->storeAs($destinationPath, $dropboxFileName, 'dropbox');

            // File path in Dropbox
            $filePath = $destinationPath.$dropboxFileName;
            $calculatedPrice = $projectService->calculateFileTextPrice($filePath, $type);

            $data->file = '/'.$filePath;
            $data->payment_price = $calculatedPrice;
            $data->user_id = $request->user_id;
            $data->project_id = $request->project_id;
            $data->save();
        }

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag(trans('site.learner.upload-manuscript-success')),
            'alert_type' => 'success',
        ]);
    }

    public function saveEbook($projectId, Request $request, ProjectService $projectService): RedirectResponse
    {
        $request->merge(['project_id' => $projectId]);

        $projectService->saveEbook($request);

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag(ucfirst(str_replace('-', ' ', $request->type)).' saved successfully.'),
                'alert_type' => 'success']);
    }

    public function saveAudio($project_id, Request $request, ProjectService $projectService): RedirectResponse
    {
        $request->merge(['project_id' => $project_id]);

        $projectService->saveAudio($request);

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag(ucfirst(str_replace('-', ' ', $request->type)).' saved successfully.'),
                'alert_type' => 'success']);
    }

    public function savePrint($project_id, Request $request, ProjectService $projectService)
    {
        try {
            $request->validate([
                'isbn' => 'required',
                'number' => 'required',
                'pages' => 'required',
                'width' => 'required',
                'height' => 'required',
                'number_of_color_pages' => 'required',
            ]);

            $format = $request->input('format');
            $customFormat = $request->width.'x'.$request->height;

            // If "Other" is selected, use the custom format
            if ((empty($format) || is_null($format)) && ! empty($request->width) && ! empty($request->height)) {
                $finalFormat = $customFormat;
            } else {
                // Use the selected predefined format
                $finalFormat = $format;
            }

            // Merge project_id and final format into the request
            $request->merge([
                'project_id' => $project_id,
                'format' => $finalFormat,
            ]);

            // Save the print details via the service
            $projectService->savePrint($request);

            // Return a JSON success response for AJAX
            return response()->json(['success' => true, 'message' => 'Print details saved successfully.']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation errors as a JSON response
            return response()->json([
                'success' => false,
                'errors' => $e->validator->errors(),
            ], 422); // Use 422 Unprocessable Entity status code for validation errors
        } catch (\Exception $e) {
            // Handle any other error and return a JSON error response
            return response()->json([
                'success' => false,
                'message' => 'Failed to save print details. Error: '.$e->getMessage(),
            ], 500);
        }
    }

    public function uploadTypeSetting(Request $request): RedirectResponse
    {
        $extensions = ['pdf', 'doc', 'docx', 'odt'];

        if ($request->hasFile('manuscript') && $request->file('manuscript')->isValid()) {
            $extension = pathinfo($_FILES['manuscript']['name'], PATHINFO_EXTENSION);
            $actual_name = pathinfo($_FILES['manuscript']['name'], PATHINFO_FILENAME);

            if (! in_array($extension, $extensions)) {
                return redirect()->back()->with(
                    'manuscript_test_error', 'Invalid file format. Allowed formats are PDF, DOC, DOCX, ODT'
                );
            }

            $standardProject = FrontendHelpers::getLearnerStandardProject(Auth::id());
            $destinationPath = 'Forfatterskolen_app/project/project-'.$standardProject->id.'/type-setting/';
            $fileName = AdminHelpers::getUniqueFilename('dropbox', $destinationPath, $actual_name.'.'.$extension);
            $expFileName = explode('/', $fileName);
            $dropboxFileName = end($expFileName);

            $request->file('manuscript')->storeAs($destinationPath, $dropboxFileName, 'dropbox');
            $wholeFilePath = $destinationPath.$dropboxFileName;
            $filePath = '/'.$wholeFilePath;

            ProjectTypeSetting::create([
                'project_id' => $standardProject->id,
                'file' => $filePath,
            ]);

            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag(trans('site.learner.upload-manuscript-success')),
                'alert_type' => 'success',
            ]);
        }

    }
}
