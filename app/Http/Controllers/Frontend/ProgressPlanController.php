<?php
namespace App\Http\Controllers\Frontend;

use AdminHelpers;
use App\Http\Controllers\Controller;
use App\ProjectManuscript;
use App\ProjectRoadmapStep;
use Auth;
use FrontendHelpers;
use Illuminate\Http\Request;

class ProgressPlanController extends Controller {

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
                    'status' => $step->status ?? 'Ikke påbegynt',
                    'expected_date' => $step->expected_date ?? null,
                ];
            });
        }
        
        return view('frontend.learner.self-publishing.progress-plan', compact('steps'));
    }

    public function planStep($stepNumber)
    {
        $standardProject = FrontendHelpers::getLearnerStandardProject(Auth::id());
        if (!$standardProject) {
            abort(404);
        }

        $stepTitle = ProjectRoadmapStep::STEPS[$stepNumber] ?? 'Ukjent steg'; // Default if step doesn't exist

        switch($stepNumber) {
            case 1:
                $manuscripts = ProjectManuscript::where('project_id', $standardProject->id)->get();
                $view = 'frontend.learner.self-publishing.progress-plan-steps.manuscripts';
                return view($view, compact('stepNumber', 'stepTitle', 'manuscripts'));
                break;
            default:
                $view = 'frontend.learner.self-publishing.progress-plan-step';
                break;
        }
        
        return view('frontend.learner.self-publishing.progress-plan-step', compact('stepNumber', 'stepTitle'));
        
    }

    public function uploadManuscript(Request $request)
    {
        $extensions = ['pdf', 'doc', 'docx', 'odt'];
        
        if ($request->hasFile('manuscript') && $request->file('manuscript')->isValid()) {
            $extension = pathinfo($_FILES['manuscript']['name'],PATHINFO_EXTENSION);
            $actual_name = pathinfo($_FILES['manuscript']['name'],PATHINFO_FILENAME);

            if( !in_array($extension, $extensions) ) :
                return redirect()->back()->with(
                    'manuscript_test_error', 'Invalid file format. Allowed formats are PDF, DOC, DOCX, ODT'
                );
            endif;

            $standardProject = FrontendHelpers::getLearnerStandardProject(Auth::id());
            $destinationPath = 'Forfatterskolen_app/project/project-'. $standardProject->id . '/project-manuscripts/';
            $fileName = AdminHelpers::getUniqueFilename('dropbox', $destinationPath, $actual_name . "." . $extension);
            $expFileName = explode('/', $fileName);
            $dropboxFileName = end($expFileName);

            $request->file('manuscript')->storeAs($destinationPath, $dropboxFileName, 'dropbox');
            $wholeFilePath = $destinationPath . $dropboxFileName;
            $filePath = "/".$wholeFilePath;

            ProjectManuscript::create([
                'project_id' => $standardProject->id,
                'file' => $filePath
            ]);

            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag(trans('site.learner.upload-manuscript-success')),
                'alert_type' => 'success'
            ]);
        }
        
    }

}