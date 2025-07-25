<?php

namespace App\Http\Controllers\Backend;

use App\Contract;
use App\ContractTemplate;
use App\Exports\GenericExport;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Http\FrontendHelpers;
use App\Http\Requests\ProjectActivityRequest;
use App\Http\Requests\ProjectBookRequest;
use App\Http\Requests\ProjectCopyEditingRequest;
use App\Http\Requests\ProjectRequest;
use App\Jobs\AddMailToQueueJob;
use App\Jobs\UpdateDropboxLink;
use App\MarketingPlan;
use App\PowerOfficeInvoice;
use App\Project;
use App\ProjectActivity;
use App\ProjectAudio;
use App\ProjectBook;
use App\ProjectBookCritique;
use App\ProjectBookFormatting;
use App\ProjectBookPicture;
use App\ProjectBookSale;
use App\ProjectEbook;
use App\ProjectGraphicWork;
use App\ProjectInvoice;
use App\ProjectManualInvoice;
use App\ProjectManuscript;
use App\ProjectMarketing;
use App\ProjectRegistration;
use App\ProjectRegistrationDistribution;
use App\ProjectRoadmapStep;
use App\ProjectTask;
use App\ProjectTypeSetting;
use App\ProjectWholeBook;
use App\SelfPublishing;
use App\Services\LearnerService;
use App\Services\ProjectService;
use App\Settings;
use App\StorageDetail;
use App\StorageDistributionCost;
use App\StoragePayout;
use App\StoragePayoutLog;
use App\StorageSale;
use App\StorageVarious;
use App\TimeRegister;
use App\User;
use App\UserBookForSale;
use Carbon\Carbon;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use PhpOffice\PhpWord\PhpWord;
use Spatie\Dropbox\Client;
use Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProjectController extends Controller
{
    public function index(): View
    {
        $learners = []; // User::where('role', 2)->get(); //->where('is_self_publishing_learner', 1)
        $activities = ProjectActivity::all();
        $projects = Project::with('user')
            ->orderByRaw("CASE WHEN status='active' 
            THEN 1 WHEN status='lead' 
            THEN 2 WHEN status='finish' 
            THEN 3 ELSE 4 END, 
            status IS NULL ,status")->get();
        $nextProjectNumber = DB::table('projects')
            ->select(DB::raw('CAST(identifier AS UNSIGNED) as identifier_numeric'))
            ->orderByRaw('identifier_numeric DESC')
            ->value('identifier') + 1;

        $projectNotes = Settings::getByName('project-notes');
        $editors = AdminHelpers::editorList();

        $layout = str_contains(request()->getHttpHost(), 'giutbok') ? 'giutbok.layout' : 'backend.layout';

        return view('backend.project.index', compact('learners', 'activities', 'projects', 'layout',
            'projectNotes', 'nextProjectNumber', 'editors'));
    }

    public function show($id): View
    {
        $project = Project::find($id)->load(['books', 'user', 'selfPublishingList']);
        $editors = AdminHelpers::editorList();
        $copyEditingEditors = AdminHelpers::copyEditingEditors();
        $correctionEditors = AdminHelpers::correctionEditors();
        $editorAndAdminList = AdminHelpers::editorAndAdminList();
        $learners = []; // User::where('role', 2)->get(); //->where('is_self_publishing_learner', 1)
        $activities = ProjectActivity::all();
        $timeRegisters = TimeRegister::where('user_id', $project->user_id)->whereNull('project_id')->with('project')->get();
        $projectTimeRegisters = TimeRegister::where('project_id', $project->id)->with('project')->get();
        $projects = Project::all();
        $correctionFeedbackTemplate = AdminHelpers::emailTemplate('Correction Feedback');
        $copyEditingFeedbackTemplate = AdminHelpers::emailTemplate('Copy Editing Feedback');
        $bookPictures = ProjectBookPicture::where('project_id', $id)->get();
        $wholeBooks = ProjectWholeBook::with('designer')->where('project_id', $id)->get();
        $bookFormattingList = ProjectBookFormatting::where('project_id', $id)->get();
        $tasks = ProjectTask::with('editor')->where('project_id', $id)->where('status', 0)->get();
        $bookCritiques = ProjectBookCritique::where('project_id', $id)->get();

        $layout = 'backend.layout';
        $addOtherServiceRoute = 'admin.project.add-other-service';
        $selfPublishingStoreRoute = 'admin.self-publishing.store';
        $selfPublishingUpdateRoute = 'admin.self-publishing.update';
        $selfPublishingDeleteRoute = 'admin.self-publishing.destroy';
        $selfPublishingAddFeedbackRoute = 'admin.self-publishing.add-feedback';
        $selfPublishingDownloadFeedbackRoute = 'admin.self-publishing.download-feedback';
        $selfPublishingLearnersRoute = 'admin.self-publishing.learners';
        $assignEditorRoute = 'admin.other-service.assign-editor';
        $updateExpectedFinishRoute = 'admin.other-service.update-expected-finish';
        $updateStatusRoute = 'admin.other-service.update-status';
        $otherServiceDeleteRoute = 'admin.other-service.delete';
        $otherServiceFeedbackRoute = 'admin.other-service.add-feedback';
        $otherServiceDownloadFeedbackRoute = 'admin.other-service.download-feedback';
        $saveBookPicturesRoute = 'admin.project.save-picture';
        $deleteBookPicturesRoute = 'admin.project.delete-picture';
        $downloadOtherService = 'admin.other-service.download-doc';
        $saveBookFormattingRoute = 'admin.project.save-book-formatting';
        $deleteBookFormattingRoute = 'admin.project.delete-book-formatting';

        if (str_contains(request()->getHttpHost(), 'giutbok')) {
            $layout = 'giutbok.layout';
            $addOtherServiceRoute = 'g-admin.project.add-other-service';
            $selfPublishingStoreRoute = 'g-admin.self-publishing.store';
            $selfPublishingUpdateRoute = 'g-admin.self-publishing.update';
            $selfPublishingDeleteRoute = 'g-admin.self-publishing.destroy';
            $selfPublishingAddFeedbackRoute = 'g-admin.self-publishing.add-feedback';
            $selfPublishingDownloadFeedbackRoute = 'g-admin.self-publishing.download-feedback';
            $selfPublishingLearnersRoute = 'g-admin.self-publishing.learners';
            $assignEditorRoute = 'g-admin.other-service.assign-editor';
            $updateExpectedFinishRoute = 'g-admin.other-service.update-expected-finish';
            $updateStatusRoute = 'g-admin.other-service.update-status';
            $otherServiceDeleteRoute = 'g-admin.other-service.delete';
            $otherServiceFeedbackRoute = 'g-admin.other-service.add-feedback';
            $saveBookPicturesRoute = 'g-admin.project.save-picture';
            $deleteBookPicturesRoute = 'g-admin.project.delete-picture';
            $downloadOtherService = 'g-admin.other-service.download-doc';
            $saveBookFormattingRoute = 'g-admin.project.save-book-formatting';
            $deleteBookFormattingRoute = 'g-admin.project.delete-book-formatting';
        }

        return view('backend.project.show', compact('project', 'editors', 'copyEditingEditors', 'correctionEditors',
            'learners', 'activities', 'timeRegisters', 'projectTimeRegisters', 'projects', 'layout',
            'addOtherServiceRoute', 'selfPublishingStoreRoute', 'selfPublishingUpdateRoute',
            'selfPublishingDeleteRoute', 'selfPublishingAddFeedbackRoute',
            'selfPublishingDownloadFeedbackRoute', 'selfPublishingLearnersRoute', 'assignEditorRoute',
            'updateExpectedFinishRoute', 'updateStatusRoute', 'otherServiceDeleteRoute', 'correctionFeedbackTemplate',
            'copyEditingFeedbackTemplate', 'otherServiceFeedbackRoute', 'saveBookPicturesRoute', 'bookPictures',
            'deleteBookPicturesRoute', 'wholeBooks', 'downloadOtherService', 'saveBookFormattingRoute', 'bookFormattingList',
            'deleteBookFormattingRoute', 'editorAndAdminList', 'tasks', 'bookCritiques', 'otherServiceDownloadFeedbackRoute'));
    }

    public function saveTask(Request $request)
    {
        $model = $request->id ? ProjectTask::find($request->id) : new ProjectTask;
        $model->fill($request->all());
        $model->save();

        return $model->load('editor');
    }

    public function updateTask($task_id, Request $request): RedirectResponse
    {
        $task = ProjectTask::find($task_id);

        if (! $task) {
            return redirect()->back();
        }

        $task->update($request->except('_token'));

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag('Task updated successfully.'),
                'alert_type' => 'success',
                'not-former-courses' => true]);
    }

    public function finishTask($id)
    {
        $task = ProjectTask::find($id);
        $task->status = 1;
        $task->save();

        if (request()->ajax()) {
            return response()->json();
        }

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag('Task finished successfully.'),
                'alert_type' => 'success',
                'not-former-courses' => true]);

    }

    public function deleteTask($id)
    {
        ProjectTask::find($id)->delete();

        if (request()->ajax()) {
            return response()->json();
        }

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag('Task finished successfully.'),
                'alert_type' => 'success',
                'not-former-courses' => true]);
    }

    public function saveProject(ProjectRequest $request, ProjectService $projectService): JsonResponse
    {
        $project = $projectService->saveProject($request);
        $nextProjectNumber = DB::table('projects')
            ->select(DB::raw('CAST(identifier AS UNSIGNED) as identifier_numeric'))
            ->orderByRaw('identifier_numeric DESC')
            ->value('identifier') + 1;

        $book = ProjectBook::updateOrCreate(
            ['project_id' => $project->id],
            [
                'user_id' => $project->user_id,
                'book_name' => $project->name,
            ]
        );

        return response()->json([
            'nextProjectNumber' => $nextProjectNumber,
            'project' => $project,
            'book' => $book,
        ]);
    }

    public function deleteProject($project_id): JsonResponse
    {
        $project = Project::find($project_id);

        $activity = ProjectActivity::where('project_id', $project_id)->update([
            'project_id' => null,
        ]);

        Contract::where('project_id', $project_id)->update([
            'project_id' => null,
        ]);

        TimeRegister::where('project_id', $project_id)->update([
            'project_id' => null,
        ]);

        $project->delete();

        return response()->json();
    }

    public function generateProjectBook()
    {
        $projects = Project::whereDoesntHave('book')->get();

        $counter = 0;
        foreach ($projects as $project) {
            ProjectBook::updateOrCreate(
                ['project_id' => $project->id],
                [
                    'user_id' => $project->user_id,
                    'book_name' => $project->name,
                ]
            );
            $counter++;
        }

        return "$counter total books created";
    }

    public function saveActivity(ProjectActivityRequest $request, ProjectService $projectService)
    {
        return $projectService->saveActivity($request);
    }

    public function deleteActivity($id): JsonResponse
    {
        ProjectActivity::find($id)->delete();

        return response()->json();
    }

    public function saveNote($project_id, Request $request): JsonResponse
    {
        $project = Project::find($project_id);
        $project->notes = $request->notes;
        $project->save();

        return response()->json($project);
    }

    public function addLearner($project_id, Request $request, LearnerService $learnerService): JsonResponse
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string',
        ]);

        $user = $learnerService->registerLearner($request, true);

        $project = Project::find($project_id);
        $project->user_id = $user->id;
        $project->save();

        return response()->json([
            'user' => $user,
            'project' => $project,
        ]);
    }

    /**
     * @return ProjectWholeBook|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
    public function saveWholeBook($project_id, Request $request, ProjectService $projectService)
    {

        $request->merge(['project_id' => $project_id]);
        if (filter_var($request->is_file, FILTER_VALIDATE_BOOLEAN)) {
            if (! $request->id) {
                $request->validate(['book_file' => 'required']);
            }
            $request->book_content = $projectService->uploadWholeBook($project_id, $request);
        } else {
            $request->validate(['book_content' => 'required']);
        }

        if (filter_var($request->send_to_designer, FILTER_VALIDATE_BOOLEAN)) {
            $request->validate([
                'designer_id' => 'required',
                'width' => 'required',
                'height' => 'required',
            ]);
        }

        $wholeBook = $request->id ? ProjectWholeBook::find($request->id) : new ProjectWholeBook;
        if ($request->has('is_book_critique')) {
            $wholeBook = $request->id ? ProjectBookCritique::find($request->id) : new ProjectBookCritique;
        }

        $wholeBook->project_id = $project_id;
        $wholeBook->book_content = $request->book_content;
        $wholeBook->description = $request->description;
        $wholeBook->is_file = filter_var($request->is_file, FILTER_VALIDATE_BOOLEAN);

        if (filter_var($request->send_to_designer, FILTER_VALIDATE_BOOLEAN)) {
            $wholeBook->designer_id = $request->designer_id;
            $wholeBook->width = $request->width;
            $wholeBook->height = $request->height;

            $emailTemplate = AdminHelpers::emailTemplate('Graphic Designer Notification');
            $user = User::find($request->designer_id);
            $to = $user->email;

            $loginLink = route('giutbok.login.emailRedirect', [encrypt($user->email), encrypt(route('g-admin.dashboard'))]);
            $searchString = [
                ':login_link',
            ];

            $replaceString = [
                "<a href='$loginLink'>Klikk her for å logge inn</a>",
            ];

            $emailContent = str_replace($searchString, $replaceString, $emailTemplate->email_content);

            dispatch(new AddMailToQueueJob($to, $emailTemplate->subject, $emailContent,
                $emailTemplate->from_email, null, null,
                'admin', $user->id));
        }

        $wholeBook->save();

        if ($wholeBook->is_file) {
            dispatch(new UpdateDropboxLink($wholeBook));
        }

        return $wholeBook;

    }

    public function saveWholeBookStatus($id, Request $request)
    {
        $book = ProjectWholeBook::find($id);
        $book->status = $request->status;
        $book->save();
    }

    public function deleteWholeBook($whole_book_id): JsonResponse
    {
        ProjectWholeBook::find($whole_book_id)->delete();

        return response()->json();
    }

    public function deleteBookCritique($whole_book_id): JsonResponse
    {
        ProjectBookCritique::find($whole_book_id)->delete();

        return response()->json();
    }

    public function saveBookCritiqueFeedback($id, Request $request, ProjectService $projectService)
    {
        $request->merge(['project_id' => $id]);
        $request->validate(['feedback' => 'required']);
        $record = ProjectBookCritique::find($id);
        $record->feedback = $projectService->uploadFeedback($request);
        $record->save();

        return $record;

    }

    public function downloadWholeBook($project_id, $whole_book_id)
    {
        $wholeBook = ProjectWholeBook::find($whole_book_id);
        $project = Project::find($project_id);

        if ($wholeBook->is_file) {
            /* $pathinfo = pathinfo($wholeBook->book_content);
            $extension = $pathinfo['extension'];
            $fileName = $pathinfo['filename'];
            return response()->download(public_path($wholeBook->book_content),$filename.'.'.$extension); */

            try {
                // Create Dropbox client
                $dropbox = new Client(config('filesystems.disks.dropbox.authorization_token'));
                $dropboxFilePath = $wholeBook->book_content;
                // Download the file from Dropbox
                $response = $dropbox->download($dropboxFilePath);

                return new StreamedResponse(function () use ($response) {
                    echo stream_get_contents($response);
                }, 200, [
                    'Content-Type' => 'application/octet-stream',
                    'Content-Disposition' => 'attachment; filename="'.basename($wholeBook->book_content).'"',
                ]);
            } catch (\Exception $e) {
                return redirect()->back()->with([
                    'errors' => AdminHelpers::createMessageBag('Failed to download the file from Dropbox: '.$e->getMessage()),
                    'alert_type' => 'danger',
                ]);
            }
        } else {
            $phpWord = new PhpWord;

            $section = $phpWord->addSection();
            $content = view('docx.generic', compact('wholeBook'));
            \PhpOffice\PhpWord\Shared\Html::addHtml($section, $content, true);
            header('Content-Type: application/.docx');
            header('Content-Disposition: attachment;filename="'.$wholeBook->id.'.docx"');
            $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
            $objWriter->save('php://output');
            exit(); // added to prevent corrupt file
        }
    }

    public function saveBook($project_id, ProjectBookRequest $request, ProjectService $projectService): JsonResponse
    {
        $request->merge(['project_id' => $project_id]);
        $response = $projectService->saveBook($request);

        return response()->json($response);
    }

    public function deleteBook($id): JsonResponse
    {
        ProjectBook::find($id)->delete();

        return response()->json();
    }

    /**
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function saveBookPicture($project_id, Request $request, ProjectService $projectService): RedirectResponse
    {
        $request->validate(['images' => 'required']);

        if ($request->id && count($request->file('images')) > 1) {
            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('only one image is allowed in update'),
                'alert_type' => 'danger',
                'not-former-courses' => true,
            ]);
        }

        $request->merge(['project_id' => $project_id]);
        $projectService->saveBookPicture($request);

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Book picture saved successfully.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);

    }

    /**
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function deleteBookPicture($id): RedirectResponse
    {
        $bookPicture = ProjectBookPicture::find($id);
        $bookPicture->delete();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Book picture deleted successfully.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }

    /**
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function saveBookFormatting($project_id, Request $request, ProjectService $projectService): RedirectResponse
    {
        if (! $request->id) {
            $request->validate(['file.*' => 'required|mimes:doc,docx']);
        }

        $request->merge(['project_id' => $project_id]);
        $projectService->saveBookFormatting($request);

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Book formatting saved successfully.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }

    public function approveBookFormattingFeedback($id): RedirectResponse
    {
        $bookFormatting = ProjectBookFormatting::find($id);
        $bookFormatting->feedback_status = 'completed';
        $bookFormatting->save();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Book formatting feedback completed successfully.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }

    /**
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function deleteBookFormatting($id): RedirectResponse
    {
        $bookFormatting = ProjectBookFormatting::find($id);
        $bookFormatting->delete();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Book formatting deleted successfully.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }

    /**
     * Add to correction or copy editing
     */
    public function addOtherService($project_id, ProjectCopyEditingRequest $request, ProjectService $projectService): RedirectResponse
    {
        if ($project = Project::find($project_id)) {

            $manuType = $projectService->saveOtherService($project_id, $request->merge([
                'user_id' => $project->user_id,
                'project_id' => $project_id,
                'type' => $request->is_copy_editing,
            ]));

            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag($manuType.' Manuscript added successfully.'),
                'alert_type' => 'success',
                'not-former-courses' => true,
            ]);

        }

        return redirect()->back();
    }

    public function graphicWork($project_id): View
    {
        $project = Project::find($project_id);
        $layout = 'backend.layout';
        $backRoute = 'admin.project.show';
        $saveGraphicRoute = 'admin.project.save-graphic-work';
        $deleteGraphicRoute = 'admin.project.delete-graphic-work';
        $saveBookPicturesRoute = 'admin.project.save-picture';
        $saveBookFormattingRoute = 'admin.project.save-book-formatting';
        $deleteBookPicturesRoute = 'admin.project.delete-picture';
        $deleteBookFormattingRoute = 'admin.project.delete-book-formatting';
        $showGraphicWorkRoute = 'admin.project.cover.show';

        if (AdminHelpers::isGiutbokPage()) {
            $layout = 'giutbok.layout';
            $backRoute = 'g-admin.project.show';
            $saveGraphicRoute = 'g-admin.project.save-graphic-work';
            $deleteGraphicRoute = 'g-admin.project.delete-graphic-work';
            $showGraphicWorkRoute = 'g-admin.project.cover.show';
        }
        $covers = ProjectGraphicWork::cover()->where('project_id', $project_id)->get();
        $barCodes = ProjectGraphicWork::barcode()->where('project_id', $project_id)->get();
        $rewriteScripts = ProjectGraphicWork::rewriteScripts()->where('project_id', $project_id)->get();
        $trialPages = ProjectGraphicWork::trialPage()->where('project_id', $project_id)->get();
        $sampleBookPDFs = ProjectGraphicWork::sampleBookPdf()->where('project_id', $project_id)->get();
        $printReadyList = ProjectGraphicWork::printReady()->where('project_id', $project_id)->get();
        $bookPictures = ProjectBookPicture::where('project_id', $project_id)->get();
        $bookFormattingList = ProjectBookFormatting::where('project_id', $project_id)->get();
        $indesigns = ProjectGraphicWork::indesigns()->where('project_id', $project_id)->get();
        $designers = AdminHelpers::giutbokUsers();
        $isbns = ProjectRegistration::isbns()->where('project_id', $project_id)->get();

        return view('backend.project.graphic-work', compact('project', 'layout', 'backRoute', 'saveGraphicRoute',
            'deleteGraphicRoute', 'covers', 'barCodes', 'rewriteScripts', 'trialPages', 'sampleBookPDFs',
            'saveBookPicturesRoute', 'bookPictures', 'deleteBookPicturesRoute', 'printReadyList',
            'saveBookFormattingRoute', 'bookFormattingList', 'deleteBookFormattingRoute', 'indesigns', 'designers', 'isbns',
            'showGraphicWorkRoute'));
    }

    public function saveGraphicWork($project_id, Request $request, ProjectService $projectService): RedirectResponse
    {
        $request->merge(['project_id' => $project_id]);

        // create graphic work folder first
        AdminHelpers::createDirectory('storage/project-graphic-work');

        if (! $request->id) {
            switch ($request->type) {
                case 'cover':
                    $request->validate([
                        'cover.*' => 'required|mimes:jpeg,jpg,png,gif',
                        'description' => 'required',
                        'isbn_id' => 'required',
                    ]);
                    break;

                    /*case 'barcode':
                        $this->validate($request, ['barcode' => 'required|mimes:jpeg,jpg,png,gif']);
                        break;*/

                case 'rewrite-script':
                    $request->validate(['rewrite_script' => 'required|mimes:pdf']);
                    break;

                case 'trial-page':
                    $request->validate(['trial_page' => 'required|mimes:jpeg,jpg,png,gif']);
                    break;

                case 'print-ready':
                    $request->validate([
                        'print_ready' => 'required|mimes:pdf',
                        'width' => 'required',
                        'height' => 'required',
                    ]);
                    break;

                case 'indesign':
                    /* if (!$request->id) {
                        $this->validate($request, ['cover' => 'required']);
                    } */
                    break;

                case 'sample-book-pdf':
                    $request->validate(['sample_book_pdf' => 'required|mimes:pdf']);
                    break;
            }
        }

        $projectService->saveGraphicWorks($request);

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag(ucfirst(str_replace(['-', '_'], ' ', $request->type)).' saved successfully.'),
                'alert_type' => 'success']);
    }

    public function deleteGraphicWork($project_id, $graphic_work_id): RedirectResponse
    {
        $graphicWork = ProjectGraphicWork::find($graphic_work_id);
        $type = $graphicWork->type;
        $graphicWork->delete();

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag(ucfirst(str_replace('-', ' ', $type)).' delete successfully.'),
                'alert_type' => 'success']);
    }

    public function cover($project_id, $cover_id): View
    {
        $project = Project::find($project_id);
        $cover = ProjectGraphicWork::find($cover_id);
        $isbns = ProjectRegistration::isbns()->where('project_id', $project_id)->get();

        $saveGraphicRoute = 'admin.project.save-graphic-work';

        if (AdminHelpers::isGiutbokPage()) {
            $saveGraphicRoute = 'g-admin.project.save-graphic-work';
        }

        return view('backend.project.cover', compact('project', 'cover', 'isbns', 'saveGraphicRoute'));
    }

    public function bookFormat($project_id, $format_id): View
    {
        $project = Project::find($project_id);
        $bookFormatting = ProjectBookFormatting::find($format_id);
        $designers = AdminHelpers::giutbokUsers();

        $saveBookFormattingRoute = 'admin.project.save-book-formatting';

        return view('backend.project.book-format', compact('project', 'bookFormatting', 'designers', 'saveBookFormattingRoute'));
    }

    public function registration($project_id): View
    {
        $project = Project::find($project_id);
        $layout = 'backend.layout';
        $backRoute = 'admin.project.show';
        $saveRegistrationRoute = 'admin.project.save-registration';
        $deleteRegistrationRoute = 'admin.project.delete-registration';
        $saveMarketingRoute = 'admin.project.save-marketing';
        $deleteMarketingRoute = 'admin.project.delete-marketing';
        if (AdminHelpers::isGiutbokPage()) {
            $layout = 'giutbok.layout';
            $backRoute = 'g-admin.project.show';
            $saveRegistrationRoute = 'g-admin.project.save-registration';
            $deleteRegistrationRoute = 'g-admin.project.delete-registration';
            $saveMarketingRoute = 'g-admin.project.save-marketing';
            $deleteMarketingRoute = 'g-admin.project.delete-marketing';
        }

        $isbns = ProjectRegistration::isbns()->with(['childMentorBookBase', 'childUploadMentorBookBase'])
            ->where('project_id', $project_id)->get();
       
        foreach ($isbns as $isbn) {
            if (!$isbn->childMentorBookBase) {
                ProjectRegistration::create([
                    'project_id' => $project_id,
                    'field'      => 'mentor-book-base',
                    'value'      => 0,
                    'type'       => 0,
                    'parent_id'  => $isbn->id,
                ]);
            }

            if (!$isbn->childUploadMentorBookBase) {
                ProjectRegistration::create([
                    'project_id' => $project_id,
                    'field'      => 'upload-files-to-mentor-book-base',
                    'value'      => 0,
                    'type'       => 0,
                    'parent_id'  => $isbn->id,
                ]);
            }
        }
        $isbns->load(['childMentorBookBase', 'childUploadMentorBookBase']); //reload relationship

        $isbnTypes = (new ProjectRegistration)->isbnTypes();
        $culturalCouncils = ProjectMarketing::culturalCouncils()->where('project_id', $project_id)->get();

        $centralDistributions = ProjectRegistration::centralDistributions()->where('project_id', $project_id)->get();
        /* $mentorBookBases = ProjectRegistration::mentorBookBase()->where('project_id', $project_id)->get();
        if ($mentorBookBases->isEmpty()) {
            // Create a new record if no records are found
            $newMentorBookBase = ProjectRegistration::create([
                'project_id' => $project_id,
                'field' => 'mentor-book-base',
                'value' => 0,
                'type' => 0,
            ]);

            $mentorBookBases = collect([$newMentorBookBase]);
        } */

        /* $uploadFilesToMentorBookBases = ProjectRegistration::uploadFilesToMentorBookBase()
            ->where('project_id', $project_id)->get();
        if ($uploadFilesToMentorBookBases->isEmpty()) {
            // Create a new record if no records are found
            $newUploadFilesToMentorBookBases = ProjectRegistration::create([
                'project_id' => $project_id,
                'field' => 'upload-files-to-mentor-book-base',
                'value' => 0,
                'type' => 0,
            ]);

            $uploadFilesToMentorBookBases = collect([$newUploadFilesToMentorBookBases]);
        } */

        return view('backend.project.registration', compact('project', 'layout', 'saveRegistrationRoute',
            'deleteRegistrationRoute', 'isbns', 'isbnTypes', 'centralDistributions', 
            'backRoute', 'culturalCouncils', 'saveMarketingRoute', 'deleteMarketingRoute'));
    }

    public function saveRegistration($project_id, Request $request): RedirectResponse
    {
        $data = $request->merge(['project_id' => $project_id])->except('_token');
        switch ($request->field) {
            case 'isbn':
                $request->validate(['isbn' => 'required']);
                $data['value'] = $request->isbn;
                break;

            case 'central-distribution':
                $request->validate(['central_distribution' => 'required|exists:project_registrations,value']);
                $data['value'] = $request->central_distribution;
                $data['type'] = 0;
                break;

            case 'mentor-book-base':
                // $this->validate($request, ['mentor_book_base' => 'required']);
                // $data['value'] = $request->has('mentor_book_base') ? 1 : 0;
                $data['value'] = $request->mentor_book_base;
                break;

            case 'upload-files-to-mentor-book-base':
                /* $this->validate($request, ['upload_files_to_mentor_book_base' => 'required|date']); */
                $data['value'] = $request->upload_files_to_mentor_book_base;
                // $data['value'] = $request->has('upload_files_to_mentor_book_base') ? 1 : 0;
                break;
        }

        if ($request->id) {
            $registration = ProjectRegistration::find($request->id);
            $registration->update($data);
        } else {
            $registration = ProjectRegistration::create($data);
        }

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag(ucfirst(str_replace('-', ' ', $request->field)).' saved successfully.'),
                'alert_type' => 'success']);
    }

    public function deleteRegistration($project_id, $registration_id): RedirectResponse
    {
        $registration = ProjectRegistration::find($registration_id);
        $type = $registration->type;
        $registration->delete();

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag(ucfirst(str_replace('-', ' ', $type)).' delete successfully.'),
                'alert_type' => 'success']);
    }

    public function marketing($project_id): View
    {
        $layout = 'backend.layout';
        $backRoute = 'admin.project.show';
        $saveMarketingRoute = 'admin.project.save-marketing';
        $deleteMarketingRoute = 'admin.project.delete-marketing';
        if (AdminHelpers::isGiutbokPage()) {
            $layout = 'giutbok.layout';
            $backRoute = 'g-admin.project.show';
            $saveMarketingRoute = 'g-admin.project.save-marketing';
            $deleteMarketingRoute = 'g-admin.project.delete-marketing';
        }
        $project = Project::find($project_id);
        $emailBookstores = ProjectMarketing::emailBookstores()->where('project_id', $project_id)->get();
        $emailLibraries = ProjectMarketing::emailLibraries()->where('project_id', $project_id)->get();
        $emailPresses = ProjectMarketing::emailPress()->where('project_id', $project_id)->get();
        $reviewCopiesSent = ProjectMarketing::reviewCopiesSent()->where('project_id', $project_id)->get();
        $setupOnlineStore = ProjectMarketing::setupOnlineStore()->where('project_id', $project_id)->get();
        $setupFacebook = ProjectMarketing::setupFacebook()->where('project_id', $project_id)->get();
        $advertisementFacebook = ProjectMarketing::advertisementFacebook()->where('project_id', $project_id)->get();
        $manuscriptSentToPrint = ProjectMarketing::manuscriptSentToPrint()->where('project_id', $project_id)->get();
        $freeWords = ProjectMarketing::freeWords()->where('project_id', $project_id)->get();
        $agreementOnTimeRegistration = ProjectMarketing::agreementOnTimeRegistration()->where('project_id', $project_id)->get();
        $printEBooks = ProjectMarketing::printEbooks()->where('project_id', $project_id)->get();
        $sampleBookApproved = ProjectMarketing::sampleBookApproved()->where('project_id', $project_id)->get();
        $pdfPrintIsApproved = ProjectMarketing::pdfPrintIsApproved()->where('project_id', $project_id)->get();
        $numberOfAuthorBooks = ProjectMarketing::numberOfAuthorBooks()->where('project_id', $project_id)->get();
        $updateTheBookBase = ProjectMarketing::updateTheBookBase()->where('project_id', $project_id)->get();
        $ebookOrdered = ProjectMarketing::ebookOrdered()->where('project_id', $project_id)->get();
        $ebookReceived = ProjectMarketing::ebookReceived()->where('project_id', $project_id)->get();

        return view('backend.project.marketing', compact('project', 'layout', 'backRoute', 'saveMarketingRoute',
            'deleteMarketingRoute', 'emailBookstores', 'emailLibraries', 'emailPresses', 'reviewCopiesSent',
            'setupOnlineStore', 'setupFacebook', 'advertisementFacebook', 'manuscriptSentToPrint',
            'freeWords', 'printEBooks', 'sampleBookApproved', 'pdfPrintIsApproved', 'numberOfAuthorBooks',
            'updateTheBookBase', 'ebookOrdered', 'ebookReceived', 'agreementOnTimeRegistration'));
    }

    public function saveMarketing($project_id, Request $request, ProjectService $projectService): RedirectResponse
    {
        $data = $request->merge(['project_id' => $project_id])->except('_token');

        // create graphic work folder first
        AdminHelpers::createDirectory('storage/project-marketing');
        $is_finished_field = 'is_finished';

        switch ($request->type) {
            case 'email-bookstore':
                if (! $request->id) {
                    $request->validate(['email_bookstore' => 'required']);
                }
                $data['value'] = $projectService->saveMarketingFileOrImage($request, 'email_bookstore');
                $data['date'] = $request->email_bookstore_date;
                break;

            case 'email-library':
                if (! $request->id) {
                    $request->validate(['email_library' => 'required']);
                }
                $data['value'] = $projectService->saveMarketingFileOrImage($request, 'email_library');
                $data['date'] = $request->email_library_date;
                break;

            case 'email-press':
                if (! $request->id) {
                    $request->validate(['email_press' => 'required']);
                }
                $data['value'] = $projectService->saveMarketingFileOrImage($request, 'email_press');
                 $data['date'] = $request->email_press_date;
                break;

            case 'review-copies-sent':
                $is_finished_field = 'is_finished_review_copies_sent';
                break;

            case 'setup-online-store':
                $data['value'] = $request->link_address;
                $is_finished_field = 'is_finished_setup_online_store';
                break;

            case 'setup-facebook':
                $data['value'] = $request->link_address;
                $is_finished_field = 'is_finished_setup_facebook';
                break;

            case 'advertisement-facebook':
                if ($request->has('advertisement_facebook')) {
                    $data['value'] = $projectService->saveMarketingFileOrImage($request, 'advertisement_facebook');
                }
                $is_finished_field = 'is_finished_advertisement_facebook';
                break;

            case 'manuscripts-sent-to-print':
                $is_finished_field = 'is_finished_manuscripts_sent_to_print';
                break;

            case 'cultural-council':
                if (! $request->id) {
                    $request->validate(['cultural_council' => 'required']);
                }
                $data['value'] = $projectService->saveMarketingFileOrImage($request, 'cultural_council');
                $is_finished_field = 'is_finished_cultural_council';
                break;

            case 'application-free-word':
                if (! $request->id) {
                    $request->validate(['free_word' => 'required']);
                }
                $data['value'] = $projectService->saveMarketingFileOrImage($request, 'free_word');
                $is_finished_field = 'is_finished_free_word';
                break;

            case 'agreement-on-time-registration':
                $is_finished_field = 'is_finished_agreement_on_time_registration';
                break;

            case 'print-ebook':
                if (! $request->id) {
                    $request->validate(['print_ebook' => 'required']);
                }
                $data['value'] = $projectService->saveMarketingFileOrImage($request, 'print_ebook');
                $is_finished_field = 'is_finished_print_ebook';
                break;

            case 'sample-book-approved':
                if (! $request->id) {
                    $request->validate(['sample_book_approved' => 'required']);
                }
                $data['value'] = $projectService->saveMarketingFileOrImage($request, 'sample_book_approved');
                $is_finished_field = 'is_finished_sample_book_approved';
                break;

            case 'pdf-print-is-approved':
                if (! $request->id) {
                    $request->validate(['pdf_print_is_approved' => 'required|mimes:pdf']);
                }
                $data['value'] = $projectService->saveMarketingFileOrImage($request, 'pdf_print_is_approved');
                $is_finished_field = 'is_finished_pdf_print_is_approved';
                break;

            case 'number-of-author-books':
                if (! $request->id) {
                    $request->validate(['number_of_author_books' => 'required|numeric']);
                }
                $data['value'] = $request->number_of_author_books;
                $is_finished_field = 'is_finished_number_of_author_books';
                break;

            case 'update-the-book-base':
                $is_finished_field = 'is_finished_update_the_book_base';
                break;

            case 'ebook-ordered':
                $is_finished_field = 'is_finished_ebook_ordered';
                break;

            case 'ebook-received':
                $is_finished_field = 'is_finished_ebook_received';
                break;
        }

        $data['is_finished'] = $request->has($is_finished_field) && $request[$is_finished_field] ? 1 : 0;

        if ($request->id) {
            $marketing = ProjectMarketing::find($request->id);
            $marketing->update($data);
        } else {
            $marketing = ProjectMarketing::create($data);
        }

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag(ucfirst(str_replace('-', ' ', $request->type)).' saved successfully.'),
                'alert_type' => 'success']);
    }

    public function deleteMarketing($project_id, $marketing_id): RedirectResponse
    {
        $marketing = ProjectMarketing::find($marketing_id);
        $type = $marketing->type;
        $marketing->delete();

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag(ucfirst(str_replace('-', ' ', $type)).' delete successfully.'),
                'alert_type' => 'success']);
    }

    public function marketingPlan($project_id): View
    {
        $project = Project::find($project_id);
        $marketingPlans = MarketingPlan::with(['questions.answers' => function ($query) use ($project_id) {
            $query->where('marketing_plan_question_answers.project_id', $project_id);
        }])->get();
        $layout = 'backend.layout';
        $backRoute = 'admin.project.show';

        if (AdminHelpers::isGiutbokPage()) {
            $layout = 'giutbok.layout';
            $backRoute = 'g-admin.project.show';
        }

        return view('backend.project.marketing-plan', compact('layout', 'backRoute', 'project', 'marketingPlans'));
    }

    public function progressPlan($project_id)
    {
        $project = Project::find($project_id);
        $layout = 'backend.layout';
        $backRoute = 'admin.project.show';

        $saved = ProjectRoadmapStep::where('project_id', $project->id)
            ->get()
            ->keyBy('step_number');

        $steps = collect(ProjectRoadmapStep::STEPS)->map(function ($title, $number) use ($saved) {
            $step = $saved->get($number);

            return [
                'step_number' => $number,
                'title' => $title,
                'status' => $step->status ?? 'not_started',
                'status_text' => $step->status_text ?? 'Not Planned',
                'expected_date' => $step->expected_date ?? null,
            ];
        });

        return view('backend.project.progress-plan.steps', compact('layout', 'backRoute', 'project', 'steps'));
    }

    public function progressPlanSave(Request $request): RedirectResponse
    {
        $step = ProjectRoadmapStep::updateOrCreate([
            'project_id' => $request->project_id,
            'step_number' => $request->step_number,
        ], [
            'status' => $request->status,
            'expected_date' => $request->expected_date,
        ]);

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag('Record updated successfully.'),
                'alert_type' => 'success']);
    }

    public function progressPlanStep($project_id, $stepNumber)
    {
        $project = Project::find($project_id);
        $layout = 'backend.layout';
        $backRoute = 'admin.project.progress-plan';
        $stepTitle = ProjectRoadmapStep::STEPS[$stepNumber] ?? 'Ukjent steg'; // Default if step doesn't exist

        $copyEditingFeedbackTemplate = AdminHelpers::emailTemplate('Copy Editing Feedback');
        $correctionFeedbackTemplate = AdminHelpers::emailTemplate('Correction Feedback');

        $assignEditorRoute = 'admin.other-service.assign-editor';
        $updateExpectedFinishRoute = 'admin.other-service.update-expected-finish';
        $otherServiceFeedbackRoute = 'admin.other-service.add-feedback';
        $otherServiceDownloadFeedbackRoute = 'admin.other-service.download-feedback';
        $updateStatusRoute = 'admin.other-service.update-status';
        $otherServiceDeleteRoute = 'admin.other-service.delete';
        $downloadOtherService = 'admin.other-service.download-doc';
        $showGraphicWorkRoute = 'admin.project.cover.show';
        $saveGraphicRoute = 'admin.project.save-graphic-work';
        $deleteGraphicRoute = 'admin.project.delete-graphic-work';
        $saveEbookRoute = 'admin.project.save-ebook';
        $deleteEbookRoute = 'admin.project.delete-ebook';
        $saveAudioRoute = 'admin.project.save-audio';
        $deleteAudioRoute = 'admin.project.delete-audio';

        switch ($stepNumber) {
            case 1:
                $manuscripts = ProjectManuscript::where('project_id', $project_id)->get();
                $view = 'backend.project.progress-plan.manuscripts';

                return view($view, compact('project', 'layout', 'backRoute', 'stepNumber', 'stepTitle', 'manuscripts'));
                break;
            case 2:
                return view('backend.project.progress-plan.copy-editing',
                    compact('project', 'layout', 'backRoute', 'stepNumber', 'stepTitle', 'assignEditorRoute',
                        'updateExpectedFinishRoute', 'otherServiceFeedbackRoute', 'copyEditingFeedbackTemplate', 'updateStatusRoute',
                        'otherServiceDeleteRoute', 'downloadOtherService', 'otherServiceDownloadFeedbackRoute'));
                break;
            case 3:
                return view('backend.project.progress-plan.correction',
                    compact('project', 'layout', 'backRoute', 'stepNumber', 'stepTitle', 'assignEditorRoute',
                        'updateExpectedFinishRoute', 'otherServiceFeedbackRoute', 'copyEditingFeedbackTemplate', 'updateStatusRoute',
                        'otherServiceDeleteRoute', 'downloadOtherService', 'otherServiceDownloadFeedbackRoute',
                        'correctionFeedbackTemplate'));
            case 4:
                $covers = ProjectGraphicWork::cover()->where('project_id', $project_id)->get();
                $isbns = ProjectRegistration::isbns()->where('project_id', $project_id)->get();

                return view('backend.project.progress-plan.cover',
                    compact('project', 'layout', 'backRoute', 'stepNumber', 'stepTitle', 'covers', 'showGraphicWorkRoute',
                        'deleteGraphicRoute', 'saveGraphicRoute', 'isbns'));
            case 5:
                $settings = ProjectTypeSetting::where('project_id', $project_id)->get();

                return view('backend.project.progress-plan.type_setting',
                    compact('project', 'layout', 'backRoute', 'stepNumber', 'stepTitle', 'settings'));
            case 6:
                $epubs = ProjectEbook::epub()->where('project_id', $project_id)->get();
                $mobis = ProjectEbook::mobi()->where('project_id', $project_id)->get();
                $covers = ProjectEbook::cover()->where('project_id', $project_id)->get();

                return view('backend.project.progress-plan.e-book',
                    compact('project', 'layout', 'backRoute', 'stepNumber', 'stepTitle', 'epubs', 'mobis', 'covers',
                        'saveEbookRoute', 'deleteEbookRoute'));

            case 7:
                $files = ProjectAudio::files()->where('project_id', $project_id)->get();
                $covers = ProjectAudio::cover()->where('project_id', $project_id)->get();

                return view('backend.project.progress-plan.audio',
                    compact('project', 'layout', 'backRoute', 'stepNumber', 'stepTitle', 'files', 'covers', 'saveAudioRoute',
                        'deleteAudioRoute'));

            case 8:
                $print = $project->print;
                $savePrintRoute = 'admin.project.save-print';

                return view('backend.project.progress-plan.print',
                    compact('project', 'layout', 'backRoute', 'stepNumber', 'stepTitle', 'print', 'savePrintRoute'));
            default:
                $view = 'frontend.learner.self-publishing.progress-plan-step';
                break;
        }
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function contract($project_id): View
    {
        $layout = 'backend.layout';
        $uploadContractRoute = 'admin.project.contract-upload';
        $createContractRoute = 'admin.project.contract-create';
        $signedUploadRoute = 'admin.project.contract-signed-upload';
        $contractShowRoute = 'admin.project.contract-show';
        $contractEditRoute = 'admin.project.contract-edit';
        $backRoute = route('admin.project.show', $project_id);
        if (AdminHelpers::isGiutbokPage()) {
            $layout = 'giutbok.layout';
            $uploadContractRoute = 'g-admin.project.contract-upload';
            $createContractRoute = 'g-admin.project.contract-create';
            $signedUploadRoute = 'g-admin.project.contract-signed-upload';
            $contractShowRoute = 'g-admin.project.contract-show';
            $contractEditRoute = 'g-admin.project.contract-edit';
            $backRoute = route('g-admin.project.show', $project_id);
        }

        $project = Project::find($project_id);
        $contracts = Contract::where('project_id', $project_id)->paginate(10);

        return view('backend.project.contract.index', compact('project', 'layout', 'contracts',
            'uploadContractRoute', 'createContractRoute', 'signedUploadRoute', 'contractShowRoute', 'contractEditRoute',
            'backRoute'));
    }

    /**
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function uploadContract($project_id, Request $request, ProjectService $projectService): RedirectResponse
    {
        $request->merge([
            'project_id' => $project_id,
            'title' => 'Contract',
        ]);
        $projectService->uploadContract($request);

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag('Contract uploaded successfully.'),
                'alert_type' => 'success']);

    }

    public function uploadSignedContract($project_id, $contract_id, Request $request, ProjectService $projectService): RedirectResponse
    {
        $request->merge([
            'id' => $contract_id,
        ]);
        $projectService->uploadContract($request);

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag('Signed contract uploaded successfully.'),
                'alert_type' => 'success']);
    }

    public function createContract($project_id): View
    {
        $route = route('admin.project.contract-store', $project_id);
        $action = 'create';
        $contract = [
            'title' => '',
            'details' => '',
            'signature' => '',
            'signature_label' => 'Signature',
            'end_date' => null,
            'is_file' => '',
        ];
        $title = 'Create Contract';
        $templates = ContractTemplate::where('show_in_project', 1)->get();
        $backRoute = route('admin.project.contract', $project_id);
        $layout = 'backend.layout';
        if (AdminHelpers::isGiutbokPage()) {
            $backRoute = route('g-admin.project.contract', $project_id);
            $layout = 'giutbok.layout';
            $route = route('g-admin.project.contract-store', $project_id);
        }

        return view('backend.contract.form', compact('route', 'action', 'contract', 'title', 'templates', 'backRoute', 'layout'));
    }

    public function storeContract($project_id, Request $request, ProjectService $projectService): RedirectResponse
    {
        $data = $request->merge([
            'project_id' => $project_id,
            'admin_name' => 'Sven Inge Henningsen',
            'admin_signature' => 'storage/contract-signatures/sign.jpg',
        ]);
        $contract = $projectService->saveContract($data);

        $route = 'admin.project.contract-edit';
        if (AdminHelpers::isGiutbokPage()) {
            $route = 'g-admin.project.contract-edit';
        }

        return redirect(route($route, [$project_id, $contract->id]))
            ->with(['errors' => AdminHelpers::createMessageBag('Contract saved successfully.'),
                'alert_type' => 'success']);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function editContract($project_id, $contract_id)
    {
        $contract = Contract::findOrFail($contract_id)->toArray();

        if ($contract['signature']) {
            return redirect()->route('admin.project.contract-show', $contract['id']);
        }

        $action = 'edit';
        $title = 'Edit '.$contract['title'];
        $backRoute = route('admin.project.contract', $project_id);
        $route = route('admin.project.contract-update', [$project_id, $contract['id']]);
        $layout = 'backend.layout';
        $project = Project::find($project_id);
        if (AdminHelpers::isGiutbokPage()) {
            $backRoute = route('g-admin.project.contract', $project_id);
            $layout = 'giutbok.layout';
            $route = route('g-admin.project.contract-update', [$project_id, $contract['id']]);
        }

        return view('backend.contract.form', compact('route', 'action', 'contract', 'title', 'backRoute',
            'layout', 'project'));
    }

    /**
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function updateContract($project_id, $contract_id, Request $request, ProjectService $projectService): RedirectResponse
    {
        $projectService->saveContract($request, $contract_id);
        $route = 'admin.project.contract-edit';
        if (AdminHelpers::isGiutbokPage()) {
            $route = 'g-admin.project.contract-edit';
        }

        return redirect(route($route, [$project_id, $contract_id]))
            ->with(['errors' => AdminHelpers::createMessageBag('Contract saved successfully.'),
                'alert_type' => 'success']);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showContract($project_id, $contract_id): View
    {
        $contract = Contract::findOrFail($contract_id);
        $backRoute = route('admin.project.contract', $project_id);

        $layout = 'backend.layout';
        if (AdminHelpers::isGiutbokPage()) {
            $backRoute = route('g-admin.project.contract', $project_id);
            $layout = 'giutbok.layout';
        }

        return view('backend.contract.show', compact('contract', 'backRoute', 'layout'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function invoice($project_id): View
    {
        $layout = 'backend.layout';
        $backRoute = route('admin.project.show', $project_id);
        $saveInvoiceRoute = 'admin.project.invoice.save';
        $deleteInvoiceRoute = 'admin.project.invoice.delete';
        $saveManualInvoiceRoute = 'admin.project.manual-invoice.save';
        $deleteManualInvoiceRoute = 'admin.project.manual-invoice.delete';
        if (AdminHelpers::isGiutbokPage()) {
            $layout = 'giutbok.layout';
            $backRoute = route('g-admin.project.show', $project_id);
            $saveInvoiceRoute = 'g-admin.project.invoice.save';
            $deleteInvoiceRoute = 'g-admin.project.invoice.delete';
            $saveManualInvoiceRoute = 'g-admin.project.manual-invoice.save';
            $deleteManualInvoiceRoute = 'g-admin.project.manual-invoice.delete';
        }

        $project = Project::find($project_id);
        $invoices = ProjectInvoice::where('project_id', $project_id)->get();
        $manualInvoices = ProjectManualInvoice::where('project_id', $project_id)->get();

        $poInvoices = PowerOfficeInvoice::with('selfPublishing')
            ->where('parent', 'self-publishing')
            ->where('user_id', $project->user_id)
            ->get();
        $selfPublishingList = SelfPublishing::where('project_id', $project_id)
            ->whereNotIn('id', $poInvoices->pluck('parent_id'))->get();

        return view('backend.project.invoice', compact('project', 'backRoute', 'layout', 'saveInvoiceRoute',
            'invoices', 'deleteInvoiceRoute', 'saveManualInvoiceRoute', 'manualInvoices', 'deleteManualInvoiceRoute',
            'poInvoices', 'selfPublishingList'));
    }

    /**
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function saveInvoice($project_id, Request $request, ProjectService $projectService): RedirectResponse
    {

        // create graphic work folder first
        AdminHelpers::createDirectory('storage/project-invoice');
        $invoice = $request->id ? ProjectInvoice::find($request->id) : new ProjectInvoice;

        if (! $request->id) {
            $request->validate([
                'invoice' => 'required|mimes:pdf',
            ]);
        }

        if ($request->hasFile('invoice')) {
            $invoice->invoice_file = $projectService->saveFileOrImage('storage/project-invoice', 'invoice');
        }

        $invoice->project_id = $project_id;
        $invoice->notes = $request->notes;
        $invoice->save();

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag('Invoice saved successfully.'),
                'alert_type' => 'success']);
    }

    /**
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function deleteInvoice($project_id, $invoice_id): RedirectResponse
    {
        $invoice = ProjectInvoice::find($invoice_id);
        $invoice->delete();

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag('Invoice deleted successfully.'),
                'alert_type' => 'success']);
    }

    public function saveManualInvoice($project_id, Request $request): RedirectResponse
    {
        $request->validate([
            'invoice' => 'required',
        ]);

        $invoice = ProjectManualInvoice::firstOrNew(['id' => $request->id]);
        $invoice->project_id = $project_id;
        $invoice->invoice = $request->invoice;
        $invoice->amount = $request->amount;
        $invoice->assigned_to = $request->assigned_to;
        $invoice->date = $request->date;
        $invoice->note = $request->note;
        $invoice->save();

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag('Invoice saved successfully.'),
                'alert_type' => 'success']);
    }

    public function deleteManualInvoice($project_id, $invoice_id): RedirectResponse
    {
        $invoice = ProjectManualInvoice::find($invoice_id);
        $invoice->delete();

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag('Invoice deleted successfully.'),
                'alert_type' => 'success']);
    }

    public function storage($projectId)
    {
        $layout = 'backend.layout';
        $backRoute = route('admin.project.show', $projectId);
        $saveBookRoute = 'admin.project.storage.save-book';
        $deleteBookRoute = 'admin.project.storage.delete-book';

        $project = Project::find($projectId);
        $projectBook = $project->book;
        $centralISBNs = ProjectRegistration::centralDistributions()->where('project_id', $projectId)
            ->where('in_storage', 0)->get()->map(function ($isbn) {
                $record = ProjectRegistration::where('field', 'isbn')->where('value', $isbn['value'])->first();
                $isbn['custom_type'] = $record?->isbn_type;

                return $isbn;
            });
        /* $projectCentralDistributions = $project->registrations()
            ->where([
                'field' => 'central-distribution',
                'in_storage' => 1
            ])
            ->get(); */
        $projectCentralDistributions = ProjectRegistration::from('project_registrations as cd')
            ->join(DB::raw("
                (
                    SELECT MIN(id) as id, value, type, project_id
                    FROM project_registrations
                    WHERE field = 'ISBN'
                    GROUP BY value, project_id
                ) as isbn
            "), function ($join) {
                $join->on('cd.value', '=', 'isbn.value')
                    ->on('cd.project_id', '=', 'isbn.project_id');
            })
            ->join('project_books', 'cd.project_id', '=', 'project_books.project_id')
            ->where('cd.field', 'central-distribution')
            ->where('cd.in_storage', 1)
            ->where('cd.project_id', $projectId)
            ->select('cd.*', 'project_books.book_name', 'isbn.type as type_of_isbn')
            ->get();
        $isbnTypes = (new ProjectRegistration)->isbnTypes();

        if (AdminHelpers::isGiutbokPage()) {
            $layout = 'giutbok.layout';
            $backRoute = route('g-admin.project.show', $projectId);
            $saveBookRoute = 'g-admin.project.storage.save-book';
            $deleteBookRoute = 'g-admin.project.storage.delete-book';
        }

        return view('backend.project.storage', compact('layout', 'backRoute', 'centralISBNs', 'saveBookRoute', 'projectId',
            'projectCentralDistributions', 'projectBook', 'deleteBookRoute', 'isbnTypes'));
    }

    public function storageDetails($projectId, $registration_id)
    {
        $layout = 'backend.layout';
        $backRoute = route('admin.project.storage', $projectId);
        $saveBookRoute = 'admin.project.storage.save-book';
        $deleteBookRoute = 'admin.project.storage.delete-book';
        $saveDetailsRoute = 'admin.project.storage.save-details';
        $saveVariousRoute = 'admin.project.storage.save-various';
        $saveDistributionRoute = 'admin.project.storage.save-distribution-cost';
        $deleteDistributionRoute = 'admin.project.storage.delete-distribution-cost';
        $saveBookSaleRoute = 'admin.project.storage.save-book-sales';
        $importBookSaleRoute = 'admin.project.storage.import-book-sales';
        $deleteBookSaleRoute = 'admin.project.storage.delete-book-sales';
        $saveStorageSaleRoute = 'admin.project.storage.save-sales';
        $deleteStorageSaleRoute = 'admin.project.storage.delete-sales';

        $project = Project::find($projectId);
        $projectBook = $project->book;
        // $projectUserBook = $project->userBookForSale;
        $projectUserBookId = $project->userBookForSale ? $project->userBookForSale->id : '';
        $userBooksForSale = UserBookForSale::where('user_id', $project->user_id)
            ->where(function ($query) use ($projectUserBookId) {
                $query->whereNull('project_id')
                    ->orWhere('id', $projectUserBookId);
            })
            ->get();
        $bookSale = new ProjectBookSale;
        $bookSaleTypes = $bookSale->saleTypes();

        $centralISBNs = ProjectRegistration::centralDistributions()->where('project_id', $projectId)->get()->map(function ($isbn) {
            $record = ProjectRegistration::where('field', 'isbn')->where('value', $isbn['value'])->first();
            $isbn['custom_type'] = $record?->isbn_type;

            return $isbn;
        });
        $projectUserBook = ProjectRegistration::find($registration_id);

        $totalBookSold = 0;
        $totalBookSale = 0;
        $currentYear = Carbon::now()->format('Y');
        $years = [];
        $quantitySoldList = [];
        $turnedOverList = [];

        if ($projectBook && $projectBook->sales) {
            $totalBookSold = $projectBook->sales()->where('project_registration_id', $registration_id)->sum('quantity');
            $totalBookSale = $projectBook->sales()->where('project_registration_id', $registration_id)->sum('amount');

            $years = range($currentYear, $currentYear - 1);
        }

        $project_book_id = $projectUserBook->id;

        $inventorySales = StorageSale::where('project_book_id', $projectUserBook->id)
            ->where('type', 'like', 'inventory_%')->get();

        $inventorySalesGroup = StorageSale::where('project_book_id', $projectUserBook->id)
            ->where('type', 'like', 'inventory_%')
            ->select('type', DB::raw('SUM(value) as total_sales'))
            ->groupBy('type')
            ->get();

        $inventoryPhysicalItems = 0;
        $inventoryDelivered = 0;
        $inventoryReturns = 0;

        foreach ($inventorySalesGroup as $sale) {
            switch ($sale->type) {
                case 'inventory_physical_items':
                    $inventoryPhysicalItems = $sale->total_sales;
                    break;
                case 'inventory_delivered':
                    $inventoryDelivered = $sale->total_sales;
                    break;
                case 'inventory_returns':
                    $inventoryReturns = $sale->total_sales;
                    break;
                    // Add more cases as needed for other types
            }
        }
        $inventoryTotal = $inventoryPhysicalItems + $inventoryDelivered + $inventoryReturns;

        // $categories = ['quantity-sold', 'turned-over', 'free', 'commission', 'shredded'];

        $categories = ['quantitySoldCount' => 'quantity-sold', 'turnedOverCount' => 'turned-over',
            'freeCount' => 'free', 'commissionCount' => 'commission', 'shreddedCount' => 'shredded',
            'defectiveCount' => 'defective', 'correctionsCount' => 'corrections', 'countsCount' => 'counts',
            'returnsCount' => 'returns'];

        $counts = array_map(function ($label) use ($project_book_id) {
            return $this->salesReportCounter($project_book_id, $label);
        }, $categories);

        extract($counts);

        $types = [
            'quantity-sold' => 'Quantity Sold',
            'turned-over' => 'Turned Over',
            'free' => 'Free',
            'commission' => 'Commission',
            'shredded' => 'Shredded',
            'defective' => 'Defective',
            'corrections' => 'Corrections',
            'counts' => 'Counts',
            // 'returns' => 'Returns'
        ];

        $baseQuery = ProjectBookSale::leftJoin('project_books', 'project_book_sales.project_book_id', '=', 'project_books.id')
            ->where('project_registration_id', $registration_id)
            ->where('project_id', $projectId);

        $quantitySold = (clone $baseQuery)
            ->when(request()->filled('year') && request('year') != 'all', function ($query) {
                $query->whereYear('date', request('year'));
            })
            ->when(request()->filled('month') && request('month') != 'all', function ($query) {
                $query->whereMonth('date', request('month'));
            })
            ->sum('quantity');

        $totalQuantitySold = (clone $baseQuery)->sum('quantity');

        $dataMapper = function ($typeKey, $typeName, $field) use ($projectUserBook, $quantitySold) {
            return [
                'name' => $typeName,
                'value' => $typeKey == 'quantity-sold'
                    ? $quantitySold
                    : ($projectUserBook ? $this->storageSalesByType($projectUserBook->id, $typeKey)[$field] : 0),
            ];
        };

        $yearlyData = array_map(function ($key, $name) use ($dataMapper) {
            return $dataMapper($key, $name, 'yearly');
        }, array_keys($types), $types);

        $overallData = array_map(function ($key, $name) use ($dataMapper) {
            return $dataMapper($key, $name, 'overall');
        }, array_keys($types), $types);

        $calculatedBalance = array_reduce($overallData, function ($sum, $data) {
            return ! in_array($data['name'], ['Quantity Sold']) ? $sum + $data['value'] : $sum;
        }, 0);

        // Find the value for "Free"
        /* $freeValue = array_reduce($yearlyData, function($free, $data) {
            return $data['name'] === 'Free' ? $data['value'] : $free;
        }, 0);

        // Deduct the "Free" value from the total balance
        $calculatedBalance -= $freeValue; */
        $balanceCount = $this->salesReportCounter($project_book_id, 'balance');

        $totalBalance = $balanceCount; /* $balanceCount ? $balanceCount
            : $inventoryTotal - ($calculatedBalance + $totalQuantitySold); */

        /* $yearlyData = [
            [
                'name' => 'Quantity Sold',
                'value' => $projectUserBook ? $this->storageSalesByType($projectUserBook->id, 'quantity-sold') : 0
            ],
            [
                'name' => 'Turned Over',
                'value' => $projectUserBook ? $this->storageSalesByType($projectUserBook->id, 'turned-over') : 0
            ],
            [
                'name' => 'Free',
                'value' => $projectUserBook ? $this->storageSalesByType($projectUserBook->id, 'free') : 0
            ],
            [
                'name' => 'Commission',
                'value' => $projectUserBook ? $this->storageSalesByType($projectUserBook->id, 'commission') : 0
            ],
            [
                'name' => 'Shredded',
                'value' => $projectUserBook ? $this->storageSalesByType($projectUserBook->id, 'shredded') : 0
            ],
            [
                'name' => 'Defective',
                'value' => $projectUserBook ? $this->storageSalesByType($projectUserBook->id, 'defective') : 0
            ],
            [
                'name' => 'Corrections',
                'value' => $projectUserBook ? $this->storageSalesByType($projectUserBook->id, 'corrections') : 0
            ],
            [
                'name' => 'Counts',
                'value' => $projectUserBook ? $this->storageSalesByType($projectUserBook->id, 'counts') : 0
            ],
            [
                'name' => 'Returns',
                'value' => $projectUserBook ? $this->storageSalesByType($projectUserBook->id, 'returns') : 0
            ]
        ]; */

        $startYear = 2024; // Change if needed
        $currentYear = Carbon::now()->year;
        $years = range($startYear, $currentYear);
        $quarters = [1, 2, 3, 4]; // Define quarters

        // Get Total Sales by Year and Quarter
        $salesData = DB::table('project_books as books')
            ->select(
                DB::raw('YEAR(sales.date) as year'),
                DB::raw('QUARTER(sales.date) as quarter'),
                DB::raw('SUM(amount) as total_sales')
            )
            ->leftJoin('project_book_sales as sales', 'sales.project_book_id', '=', 'books.id')
            ->whereBetween(DB::raw('YEAR(sales.date)'), [$startYear, $currentYear])
            ->where('books.project_id', $project->id)
            ->groupBy('year', 'quarter')
            ->orderBy('year', 'ASC')
            ->orderBy('quarter', 'ASC')
            ->get()
            ->groupBy('year'); // keyBy('year') // Store results by year for easy lookup

        // Get Total Distributions by Year and Quarter
        $distributionsData = DB::table('project_registrations as distribution')
            ->select(
                DB::raw('YEAR(distribution_costs.date) as year'),
                DB::raw('QUARTER(distribution_costs.date) as quarter'),
                DB::raw('SUM(amount) as total_distributions')
            )
            ->leftJoin('storage_distribution_costs as distribution_costs',
                'distribution_costs.project_book_id', '=', 'distribution.id')
            ->where('distribution.id', $registration_id)
            ->groupBy('year', 'quarter')
            ->orderBy('year', 'ASC')
            ->orderBy('quarter', 'ASC')
            ->get()
            ->groupBy('year'); // Store results by year for easy lookup

        // Merge Data for Year and Quarter
        $storageCosts = collect($years)->map(function ($year) use ($salesData, $distributionsData, $quarters) {
            // $sales = isset($salesData[$year]) ? $salesData[$year]->total_sales : 0;
            $distributions = [];
            $quarterSales = [];

            // Initialize distribution values for all quarters
            foreach ($quarters as $quarter) {
                $distributions[$quarter] = isset($distributionsData[$year])
                    ? ($distributionsData[$year]->firstWhere('quarter', $quarter)->total_distributions ?? 0) * 1.2
                    : 0;
                $quarterSales[$quarter] = isset($salesData[$year])
                    ? (collect($salesData[$year])->firstWhere('quarter', $quarter)->total_sales ?? 0)
                    : 0;
            }

            // Calculate totals
            $totalDistributions = array_sum($distributions);
            $totalSales = array_sum($quarterSales);
            $payout = $totalSales - $totalDistributions;

            return [
                'year' => $year,
                'q1_distributions' => $distributions[1],
                'q1_sales' => $quarterSales[1],
                'q2_distributions' => $distributions[2],
                'q2_sales' => $quarterSales[2],
                'q3_distributions' => $distributions[3],
                'q3_sales' => $quarterSales[3],
                'q4_distributions' => $distributions[4],
                'q4_sales' => $quarterSales[4],
                'total_sales' => $totalSales,
                'total_distributions' => $totalDistributions,
                'payout' => $payout,
            ];
        })->sortByDesc('year');

        if (AdminHelpers::isGiutbokPage()) {
            $layout = 'giutbok.layout';
            $backRoute = route('g-admin.project.show', $projectId);
            $saveBookRoute = 'g-admin.project.storage.save-book';
            $deleteBookRoute = 'g-admin.project.storage.delete-book';
            $saveDetailsRoute = 'g-admin.project.storage.save-details';
            $saveVariousRoute = 'g-admin.project.storage.save-various';
        }

        $projectBookSales = $projectBook
            ? $projectBook->sales()->where('project_registration_id', $registration_id)->get()
            : [];

        $registrationDistributionCosts = ProjectRegistrationDistribution::where('project_registration_id', $registration_id)->first();
        $paidDistributionYears = $registrationDistributionCosts->years ?? [];

        $payouts = StoragePayout::where('project_registration_id', $registration_id)->get()->groupBy(['year', 'quarter']);

        return view('backend.project.storage-details', compact('backRoute', 'layout', 'projectId', 'project',
            'projectUserBook', 'userBooksForSale', 'totalBookSold', 'totalBookSale', 'years', 'yearlyData', 'saveBookRoute',
            'deleteBookRoute', 'saveDetailsRoute', 'saveVariousRoute', 'projectBook', 'saveDistributionRoute',
            'deleteDistributionRoute', 'bookSaleTypes', 'saveBookSaleRoute', 'importBookSaleRoute', 'deleteBookSaleRoute',
            'centralISBNs', 'saveStorageSaleRoute', 'inventorySales', 'deleteStorageSaleRoute', array_keys($categories),
            'inventoryPhysicalItems', 'inventoryDelivered', 'inventoryReturns', 'totalBalance', 'inventoryTotal', 'quantitySold',
            'totalQuantitySold', 'storageCosts', 'registration_id', 'projectBookSales', 'paidDistributionYears', 'balanceCount',
            'payouts'));
    }

    public function saveStorageBook($projectId, Request $request)
    {
        /* $currentProjectBookForSale = UserBookForSale::where('project_id', $projectId)->update([
            'project_id' => NULL
        ]);

        $userBookForSale = UserBookForSale::find($request->user_book_for_sale_id);
        $userBookForSale->project_id = $projectId;
        $userBookForSale->save(); */
        $registration = ProjectRegistration::where('project_id', $projectId)
            ->centralDistributions()
            ->where('value', $request->user_book_for_sale_id)->first();
        $registration->in_storage = 1;
        $registration->save();

        return back()
            ->with(['errors' => AdminHelpers::createMessageBag('Storage Book saved successfully.'),
                'alert_type' => 'success']);
    }

    public function deleteStorageBook($registration_id): RedirectResponse
    {
        /* $userBookForSale = UserBookForSale::where('project_id', $projectId)->first();
        $userBookForSale->project_id = NULL;
        $userBookForSale->save(); */

        $userBookForSale = ProjectRegistration::find($registration_id);
        $userBookForSale->in_storage = 0;
        $userBookForSale->save();

        if ($userBookForSale->detail) {
            $userBookForSale->detail->delete();
        }

        if ($userBookForSale->various) {
            $userBookForSale->various->delete();
        }

        return redirect()->route('admin.project.storage', $userBookForSale->project_id)
            ->with(['errors' => AdminHelpers::createMessageBag('Book removed from project successfully.'),
                'alert_type' => 'success']);
    }

    public function saveBookSales($project_id, Request $request): RedirectResponse
    {
        $request->merge(['project_id' => $project_id]);

        ProjectBookSale::updateOrCreate([
            'id' => $request->id,
        ], $request->except('id'));

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Book sale saved successfully.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }

    public function importBookSales($project_book_id, Request $request)
    {
        $file = $request->file('book_sale');
        $data = array_map('trim', explode(PHP_EOL, file_get_contents($file->getRealPath())));
        $headers = explode("\t", strtolower($data[1]));

        $formattedData = array_filter(array_map(function ($row) use ($headers) {
            $rowData = explode("\t", $row);
            if (count($rowData) === count($headers)) {
                $rowAssoc = array_combine($headers, array_pad($rowData, count($headers), null));

                return $this->hasValues($rowAssoc) ? $rowAssoc : null;
            }
        }, array_slice($data, 2)));

        foreach ($formattedData as $importData) {
            ProjectBookSale::updateOrCreate([
                'project_book_id' => $project_book_id,
                'project_registration_id' => $request->project_registration_id,
                'invoice_number' => $importData['faktnr'],
            ],
                [
                    'customer_name' => $importData['kundenavn'],
                    'quantity' => $importData['ant'],
                    'full_price' => $importData['lpris'],
                    'discount' => $importData['rab'],
                    'amount' => AdminHelpers::formatPrice($importData['belop']),
                    'date' => $importData['dato'],
                ]);
        }

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Book sales imported successfully.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }

    /* public function importBookSalesOrig($project_book_id, Request $request)
    {

        $file = $request->file('book_sale');

         // Read the entire file content as plain text
        $content = file_get_contents($file->getRealPath());

         // Split the content into rows based on newlines
        $data = explode(PHP_EOL, $content);

        $headers = explode("\t", trim(strtolower($data[1])));

        $formattedData = [];

        for ($i = 2; $i < count($data); $i++) {
            //Split each row by tab
            $rowData = explode("\t", trim($data[$i]));

            //Check if row has fewer columns than headers
            if (count($rowData) < count($headers)) {
                // Fill missing columns with empty values
                $rowData = array_pad($rowData, count($headers), null);
            }

            // Combine headers with row data to form an associative array
            if (count($headers) === count($rowData)) {
                $rowAssoc = array_combine($headers, $rowData);
                //$formattedData[] = array_combine($headers, $rowData);
                if ($this->hasValues($rowAssoc)) {
                    $formattedData[] = $rowAssoc; // Add the formatted row to the array
                }
            } else {
                // Handle the mismatch (optional logging or error handling)
                echo "Row $i has a mismatch: expected " . count($headers) . " columns but got " . count($rowData) . "\n";
            }
        }

        foreach($formattedData as $importData) {
            ProjectBookSale::create([
                'project_book_id' => $project_book_id,
                'customer_name' => $importData['kundenavn'],
                'quantity' => $importData['ant'],
                'full_price' => $importData['lpris'],
                'discount' => $importData['rab'],
                'amount' => AdminHelpers::formatPrice($importData['belop']),
                'date' => $importData['dato'],
            ]);
        }

        return redirect()->back()->with([
            'errors'                => AdminHelpers::createMessageBag(count($formattedData) . 'sale imported successfully.'),
            'alert_type'            => 'success',
            'not-former-courses'    => true
        ]);
    } */

    public function deleteBookSales($sale_id): RedirectResponse
    {
        ProjectBookSale::find($sale_id)->delete();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Book sale deleted successfully.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }

    public function saveStorageBookDetails($book_id, Request $request)
    {
        StorageDetail::updateOrCreate([
            'project_book_id' => $book_id,
        ], [
            'subtitle' => $request->subtitle,
            'original_title' => $request->original_title,
            'author' => $request->author,
            'editor' => $request->editor,
            'publisher' => $request->publisher,
            'book_group' => $request->book_group,
            'item_number' => $request->item_number,
            'isbn' => $request->isbn,
            'isbn_ebook' => $request->isbn_ebook,
            'edition_on_sale' => $request->edition_on_sale,
            'edition_total' => $request->edition_total,
            'release_date' => $request->release_date,
            'release_date_for_media' => $request->release_date_for_media,
            'price_vat' => $request->price_vat,
            'registered_with_council' => $request->registered_with_council,
        ]);

        if ($request->isbn) {
            $bookForSale = UserBookForSale::find($book_id);
            $bookForSale->isbn = $request->isbn;
            $bookForSale->save();
        }

        return back()
            ->with(['errors' => AdminHelpers::createMessageBag('Storage details saved successfully.'),
                'alert_type' => 'success']);
    }

    public function saveRegistrationPaidDistribution($registration_id, Request $request)
    {
        $model = ProjectRegistrationDistribution::where('project_registration_id', $registration_id)->first();

        if (! $model) {
            $model = new ProjectRegistrationDistribution;
            $model->project_registration_id = $registration_id;
            $model->years = [];
        }

        // Ensure $model->years is an array
        $years = is_array($model->years) ? $model->years : [];

        $year = $request->input('year');
        $isChecked = $request->input('is_checked');

        if ($isChecked == '1') {
            // Add the year if not already in the array
            if (! in_array($year, $years)) {
                $years[] = $year;
            }
        } else {
            // Remove the year if it exists
            $years = array_filter($years, function ($y) use ($year) {
                return $y != $year;
            });
        }

        // Update and save
        $model->years = array_values($years); // Reindex array
        $model->save();

        return response()->json(['message' => 'Updated successfully', 'years' => $model->years]);
    }

    public function storePayout(Request $request)
    {
        $request->validate([
            'project_registration_id' => 'required|exists:project_registrations,id',
            'year' => 'required|integer|min:2000|max:'.(date('Y') + 1),
            'quarter' => 'required|integer|min:1|max:4',
        ]);

        $data = $request->except('_token');
        $data['is_paid'] = $request->has('is_paid');
        $data['paid_at'] = $request->has('is_paid') ? now() : null;

        if ($request->filled('id')) {
            // Update existing record
            $payout = StoragePayout::findOrFail($request->input('id'));
            $payout->update($data);

            return back()
                ->with(['errors' => AdminHelpers::createMessageBag('Quarterly payout updated successfully.'),
                    'alert_type' => 'success']);
        } else {
            // Create new record
            StoragePayout::create($data);

            return back()
                ->with(['errors' => AdminHelpers::createMessageBag('Quarterly payout created successfully.'),
                    'alert_type' => 'success']);
        }
    }

    public function exportStorageCost($project_id, $registration_id, $selectedYear)
    {
        $quarters = [1, 2, 3, 4];

        $projectBook = ProjectBook::where('project_id', $project_id)->first();
        $bookName = $projectBook?->book_name;

        // Process data
        $data = $this->exportStorageCostWithSales($project_id, $registration_id, $selectedYear);

        $pdf = \App::make('dompdf.wrapper');
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
        $pdf->setPaper('letter', 'landscape');
        $pdf->loadHTML(view('frontend.pdf.distribution-cost', compact('data')));

        return $pdf->download('Royalty_'.$selectedYear.'_'.$bookName.'.pdf');
        // return $pdf->stream('distribution-cost.pdf');
    }

    public function excelExportStorageCost($project_id, $registration_id, $selectedYear)
    {
        $data = $this->exportStorageCostData($project_id, $registration_id, $selectedYear);
        $excel = \App::make('excel');
        $storageCosts = [];
        $headers = [
            trans('site.year'), trans('site.q1-cost'), trans('site.q2-cost'), trans('site.q3-cost'), trans('site.q4-cost'),
            trans('site.author-portal-menu.sales'), trans('site.total-storage-cost'), trans('site.payout'),
        ];

        foreach ($data as $storageCost) {
            $storageCosts[] = [
                $storageCost['year'],
                FrontendHelpers::currencyFormat($storageCost['q1_distributions']),
                FrontendHelpers::currencyFormat($storageCost['q2_distributions']),
                FrontendHelpers::currencyFormat($storageCost['q3_distributions']),
                FrontendHelpers::currencyFormat($storageCost['q4_distributions']),
                FrontendHelpers::currencyFormat($storageCost['total_sales']),
                FrontendHelpers::currencyFormat($storageCost['total_distributions']),
                FrontendHelpers::currencyFormat($storageCost['payout']),
            ];
        }

        return $excel->download(new GenericExport($storageCosts, $headers), 'Distribution Cost Report.xlsx');
    }

    public function storageCostSendEmail($project_id, $registration_id, $selectedYear, Request $request): RedirectResponse
    {
        if (! $request->has('quarters')) {
            return redirect()->back()
                ->with(['errors' => AdminHelpers::createMessageBag('Please select a quarter.'),
                    'alert_type' => 'danger']);
        }

        $selectedQuarters = array_map('intval', array_keys($request->quarters));

        $project = Project::find($project_id);
        $user = $project->user;

        if (! $user) {
            return redirect()->back()
                ->with(['errors' => AdminHelpers::createMessageBag('Error on sending email. No user on the project'),
                    'alert_type' => 'danger']);
        }

        $projectBook = ProjectBook::where('project_id', $project_id)->first();
        $bookName = $projectBook?->book_name;

        $data = $this->exportStorageCostWithSales($project_id, $registration_id, $selectedYear, $selectedQuarters);

        // Generate the PDF
        $pdf = \App::make('dompdf.wrapper');
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
        $pdf->setPaper('letter', 'landscape');
        $pdf->loadHTML(view('frontend.pdf.distribution-cost', compact('data', 'selectedQuarters'))->render()); // ->render());

        // Save PDF to Storage
        $destinationPath = '/exports';
        $actual_name = "Royalty_{$selectedYear}_".str_slug($bookName);
        $fileName = AdminHelpers::getUniqueFilename('public', $destinationPath, $actual_name.'.'.'pdf');
        $filePath = "exports/{$fileName}";
        Storage::put("public/{$filePath}", $pdf->output());

        $user_email = $user->email;
        $subject = $request->subject;
        $from = $request->from_email;
        $message = str_replace([
            '[name]',
            '[year]',
            '[total_payout]',
            '[book_name]',
        ], [
            $user->full_name,
            $selectedYear,
            FrontendHelpers::currencyFormat($data[0]['payout_by_quarter']),
            $bookName,
        ], $request->message);

        dispatch(new AddMailToQueueJob($user_email, $subject, $message, $from, null, storage_path("app/public/{$filePath}"),
            'project-registration', $registration_id));

        // create log for payout
        $record = $data[0];
        foreach ($selectedQuarters as $q) {
            StoragePayoutLog::create([
                'project_registration_id' => $registration_id,
                'year' => $selectedYear,
                'quarter' => $q,
                'amount' => $record["q{$q}_sales"] - $record["q{$q}_distributions"],
            ]);
        }

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag('Email sent successfully.'),
                'alert_type' => 'success']);
    }

    private function exportStorageCostData($project_id, $registration_id, $selectedYear)
    {
        $quarters = [1, 2, 3, 4];

        // Fetch sales data
        $salesData = DB::table('project_books as books')
            ->select(DB::raw('YEAR(sales.date) as year'), DB::raw('SUM(amount) as total_sales'))
            ->leftJoin('project_book_sales as sales', 'sales.project_book_id', '=', 'books.id')
            ->whereRaw('YEAR(sales.date) = ?', [$selectedYear])
            ->where('books.project_id', $project_id)
            ->groupBy('year')
            ->get()
            ->keyBy('year');

        // Fetch distributions data
        $distributionsData = DB::table('project_registrations as distribution')
            ->select(DB::raw('YEAR(distribution_costs.date) as year'), DB::raw('QUARTER(distribution_costs.date) as quarter'),
                DB::raw('SUM(amount) as total_distributions'))
            ->leftJoin('storage_distribution_costs as distribution_costs',
                'distribution_costs.project_book_id', '=', 'distribution.id')
            ->where('distribution.id', $registration_id)
            ->whereRaw('YEAR(distribution_costs.date) = ?', [$selectedYear])
            ->groupBy('year', 'quarter')
            ->orderBy('year')
            ->orderBy('quarter')
            ->get()
            ->groupBy('year');

        // Process data
        return collect([$selectedYear])->map(function ($year) use ($salesData, $distributionsData, $quarters) {
            $sales = isset($salesData[$year]) ? $salesData[$year]->total_sales : 0;
            $distributions = [];

            foreach ($quarters as $quarter) {
                $distributions[$quarter] = isset($distributionsData[$year])
                    ? ($distributionsData[$year]->firstWhere('quarter', $quarter)->total_distributions ?? 0) * 1.2
                    : 0;
            }

            return [
                'year' => $year,
                'q1_distributions' => $distributions[1],
                'q2_distributions' => $distributions[2],
                'q3_distributions' => $distributions[3],
                'q4_distributions' => $distributions[4],
                'total_sales' => $sales,
                'total_distributions' => array_sum($distributions),
                'payout' => $sales - array_sum($distributions),
            ];
        });
    }

    private function exportStorageCostWithSales($project_id, $registration_id, $selectedYear, $selectedQuarters = [1, 2, 3, 4])
    {
        $quarters = [1, 2, 3, 4];

        // Fetch sales data
        $salesData = DB::table('project_books as books')
            ->select(DB::raw('YEAR(sales.date) as year'), DB::raw('QUARTER(sales.date) as quarter'),
                DB::raw('SUM(amount) as total_sales'))
            ->leftJoin('project_book_sales as sales', 'sales.project_book_id', '=', 'books.id')
            ->whereRaw('YEAR(sales.date) = ?', [$selectedYear])
            ->where('books.project_id', $project_id)
            ->groupBy('year', 'quarter')// ->groupBy('year')
            ->orderBy('year')
            ->orderBy('quarter')
            ->get()
            ->groupBy('year'); // ->keyBy('year');

        // Fetch distributions data
        $distributionsData = DB::table('project_registrations as distribution')
            ->select(DB::raw('YEAR(distribution_costs.date) as year'), DB::raw('QUARTER(distribution_costs.date) as quarter'),
                DB::raw('SUM(amount) as total_distributions'))
            ->leftJoin('storage_distribution_costs as distribution_costs',
                'distribution_costs.project_book_id', '=', 'distribution.id')
            ->where('distribution.id', $registration_id)
            ->whereRaw('YEAR(distribution_costs.date) = ?', [$selectedYear])
            ->groupBy('year', 'quarter')
            ->orderBy('year')
            ->orderBy('quarter')
            ->get()
            ->groupBy('year');

        // Process data
        return collect([$selectedYear])->map(function ($year) use ($salesData, $distributionsData, $quarters, $selectedQuarters) {
            // $sales = isset($salesData[$year]) ? $salesData[$year]->total_sales : 0;
            $allSales = [];
            $allDistributions = [];

            foreach ($quarters as $quarter) {
                $sales = isset($salesData[$year])
                    ? ($salesData[$year]->firstWhere('quarter', $quarter)->total_sales ?? 0)
                    : 0;

                $distribution = isset($distributionsData[$year])
                    ? ($distributionsData[$year]->firstWhere('quarter', $quarter)->total_distributions ?? 0) * 1.2
                    : 0;

                $allSales[$quarter] = $sales;
                $allDistributions[$quarter] = $distribution;
            }

            // Only include selected quarters in aggregated totals
            $filteredSales = collect($allSales)->only($selectedQuarters);
            $filteredDistributions = collect($allDistributions)->only($selectedQuarters);

            $salesByQuarter = $filteredSales->sum();
            $distributionsByQuarter = $filteredDistributions->sum();
            $payoutByQuarter = $salesByQuarter - $distributionsByQuarter;

            return [
                'year' => $year,
                'q1_distributions' => $allDistributions[1],
                'q1_sales' => $allSales[1],
                'q2_distributions' => $allDistributions[2],
                'q2_sales' => $allSales[2],
                'q3_distributions' => $allDistributions[3],
                'q3_sales' => $allSales[3],
                'q4_distributions' => $allDistributions[4],
                'q4_sales' => $allSales[4],
                'total_sales' => $salesByQuarter,
                'total_distributions' => $distributionsByQuarter,
                'payout' => $payoutByQuarter,
                'sales_by_quarter' => $salesByQuarter,
                'distributions_by_quarter' => $distributionsByQuarter,
                'payout_by_quarter' => $payoutByQuarter,
            ];
        });
    }

    public function saveStorageVarious($book_id, Request $request)
    {

        StorageVarious::updateOrCreate([
            'project_book_id' => $book_id,
        ], [
            'publisher' => $request->publisher,
            'minimum_stock' => $request->minimum_stock,
            'weight' => $request->weight,
            'height' => $request->height,
            'width' => $request->width,
            'thickness' => $request->thickness,
            'cost' => $request->cost,
            'material_cost' => $request->material_cost,
        ]);

        return back()
            ->with(['errors' => AdminHelpers::createMessageBag('Storage various saved successfully.'),
                'alert_type' => 'success']);
    }

    public function saveDistributionCost($book_id, Request $request)
    {
        StorageDistributionCost::updateOrCreate([
            'id' => $request->id,
            'project_book_id' => $book_id,
        ], $request->except('id'));

        return back()->with([
            'errors' => AdminHelpers::createMessageBag('Distribution Cost saved successfully.'),
            'alert_type' => 'success',
        ]);
    }

    public function deleteDistributionCost($distribution_cost_id)
    {
        StorageDistributionCost::find($distribution_cost_id)->delete();

        return back()->with([
            'errors' => AdminHelpers::createMessageBag('Distribution cost deleted successfully.'),
            'alert_type' => 'success',
        ]);
    }

    public function saveStorageSales($project_book_id, Request $request)
    {
        StorageSale::updateOrCreate([
            'id' => $request->id,
            'project_book_id' => $project_book_id,
        ], $request->except('id'));

        return back()->with([
            'errors' => AdminHelpers::createMessageBag('Sales updated successfully.'),
            'alert_type' => 'success',
        ]);
    }

    public function deleteStorageSales($id)
    {
        StorageSale::find($id)->delete();

        return back()->with([
            'errors' => AdminHelpers::createMessageBag('Sales delete successfully.'),
            'alert_type' => 'success',
        ]);
    }

    public function storageSalesDetails($project_book_id, Request $request): JsonResponse
    {
        $details = StorageSale::where('project_book_id', $project_book_id)->where('type', $request->type)
            ->get();

        return response()->json([
            'details' => $details,
        ]);
    }

    public function ebook($project_id): View
    {
        $project = Project::find($project_id);

        $layout = 'backend.layout';
        $backRoute = route('admin.project.show', $project_id);
        $saveEbookRoute = 'admin.project.save-ebook';
        $deleteEbookRoute = 'admin.project.delete-ebook';

        $epubs = ProjectEbook::epub()->where('project_id', $project_id)->get();
        $mobis = ProjectEbook::mobi()->where('project_id', $project_id)->get();
        $covers = ProjectEbook::cover()->where('project_id', $project_id)->get();

        if (AdminHelpers::isGiutbokPage()) {
            $layout = 'giutbok.layout';
            $backRoute = route('g-admin.project.show', $project_id);
            $saveEbookRoute = 'g-admin.project.save-ebook';
            $deleteEbookRoute = 'g-admin.project.delete-ebook';
        }

        return view('backend.project.e-book', compact('layout', 'project', 'saveEbookRoute', 'epubs',
            'deleteEbookRoute', 'mobis', 'covers', 'backRoute'));
    }

    public function saveEbook($project_id, Request $request, ProjectService $projectService): RedirectResponse
    {
        $request->merge(['project_id' => $project_id]);

        /* if (!$request->id){
            switch ($request->type) {
                case 'epub':
                    $this->validate($request, ['cover' => 'required|mimes:jpeg,jpg,png,gif']);
                    break;
            }
        } */

        $projectService->saveEbook($request);

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag(ucfirst(str_replace('-', ' ', $request->type)).' saved successfully.'),
                'alert_type' => 'success']);
    }

    public function deleteEbook($project_id, $ebook_id): RedirectResponse
    {
        $ebook = ProjectEbook::find($ebook_id);
        $type = $ebook->type;
        $ebook->delete();

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag(ucfirst(str_replace('-', ' ', $type)).' delete successfully.'),
                'alert_type' => 'success']);
    }

    public function audio($project_id): View
    {
        $project = Project::find($project_id);

        $layout = 'backend.layout';
        $backRoute = route('admin.project.show', $project_id);
        $saveAudioRoute = 'admin.project.save-audio';
        $deleteAudioRoute = 'admin.project.delete-audio';

        $files = ProjectAudio::files()->where('project_id', $project_id)->get();
        $covers = ProjectAudio::cover()->where('project_id', $project_id)->get();

        if (AdminHelpers::isGiutbokPage()) {
            $layout = 'giutbok.layout';
            $backRoute = route('g-admin.project.show', $project_id);
            $saveAudioRoute = 'g-admin.project.save-audio';
            $deleteAudioRoute = 'g-admin.project.delete-audio';
        }

        return view('backend.project.audio', compact('layout', 'project', 'saveAudioRoute', 'files', 'deleteAudioRoute',
            'covers', 'backRoute'));
    }

    public function saveAudio($project_id, Request $request, ProjectService $projectService)/* : RedirectResponse */
    {
        if ($request->type == 'files') {
            $request->validate([
                'files' => 'required'
            ]);
        }

        if ($request->type == 'cover') {
            $request->validate([
                'cover' => 'required'
            ]);
        }

        $request->merge(['project_id' => $project_id]);

        $projectService->saveAudio($request);

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag(ucfirst(str_replace('-', ' ', $request->type)).' saved successfully.'),
                'alert_type' => 'success']);
    }

    public function deleteAudio($project_id, $audio_id): RedirectResponse
    {
        $audio = ProjectAudio::find($audio_id);
        $type = $audio->type;
        $audio->delete();

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag(ucfirst(str_replace('-', ' ', $type)).' delete successfully.'),
                'alert_type' => 'success']);
    }

    public function print($project_id): View
    {
        $project = Project::find($project_id);
        $print = $project->print;

        $layout = 'backend.layout';
        $backRoute = route('admin.project.show', $project_id);
        $savePrintRoute = 'admin.project.save-print';

        if (AdminHelpers::isGiutbokPage()) {
            $layout = 'giutbok.layout';
            $backRoute = route('g-admin.project.show', $project_id);
            $savePrintRoute = 'g-admin.project.save-print';
        }

        return view('backend.project.print', compact('layout', 'project', 'backRoute', 'print', 'savePrintRoute'));
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

    public function showNotes($project_id): View
    {
        $project = Project::find($project_id);
        $backRoute = route('admin.project.show', $project_id);

        $layout = 'backend.layout';
        if (AdminHelpers::isGiutbokPage()) {
            $backRoute = route('g-admin.project.show', $project_id);
            $layout = 'giutbok.layout';
        }

        return view('backend.project.notes', compact('project', 'backRoute', 'layout'));
    }

    private function storageSalesByType($user_book_for_sale_id, $type)
    {
        $baseQuery = StorageSale::where('project_book_id', $user_book_for_sale_id)
            ->where('type', $type);

        $sales = (clone $baseQuery)
            ->when(request()->filled('year') && request('year') != 'all', function ($query) {
                $query->whereYear('date', request('year'));
            })
            ->when(request()->filled('month') && request('month') != 'all', function ($query) {
                $query->whereMonth('date', request('month'));
            })
            ->sum('value');

        $overallSales = (clone $baseQuery)->sum('value');

        return [
            'yearly' => $sales,
            'overall' => $overallSales,
        ];
    }

    private function storageYearSalesByType($user_book_for_sale_id, $type)
    {
        $yearsData = DB::table('storage_sales')
            ->select(DB::raw('YEAR(date) AS year'), DB::raw('SUM(value) AS sum_value'))
            ->where('date', '>=', Carbon::now()->subYears(4))
            ->where('project_book_id', $user_book_for_sale_id)
            ->where('type', $type)
            ->groupBy('year')
            ->pluck('sum_value', 'year')
            ->toArray();

        $years = range(Carbon::now()->subYears(4)->format('Y'), Carbon::now()->format('Y'));

        // Assign a sum of 0 to years with no records
        $yearsData = array_replace(array_fill_keys($years, 0), $yearsData);

        krsort($yearsData);

        return $yearsData;
    }

    private function salesReportCounter($project_book_id, $type)
    {
        return StorageSale::where('project_book_id', $project_book_id)
            ->where('type', $type)
            ->sum('value');
    }

    private function hasValues($row)
    {
        // Filter the row to keep only non-empty values
        $filtered = array_filter($row, function ($value) {
            return ! empty($value); // Keep non-empty values
        });

        // If the filtered array is not empty, return true, meaning it has values
        return ! empty($filtered);
    }
}
