<?php

namespace App\Http\Controllers\Backend;


use App\Contract;
use App\ContractTemplate;
use App\CopyEditingManuscript;
use App\CorrectionManuscript;
use App\Helpers\FileToText;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Http\FrontendHelpers;
use App\Http\PowerOffice;
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
use App\ProjectEbook;
use App\ProjectGraphicWork;
use App\ProjectInvoice;
use App\ProjectManualInvoice;
use App\ProjectMarketing;
use App\ProjectRegistration;
use App\ProjectTask;
use App\ProjectWholeBook;
use App\SelfPublishing;
use App\Services\LearnerService;
use App\Services\ProjectService;
use App\Settings;
use App\StorageBook;
use App\StorageDetail;
use App\StorageSale;
use App\StorageVarious;
use App\TimeRegister;
use App\User;
use App\UserBookForSale;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Time;
use PhpOffice\PhpWord\PhpWord;
use Spatie\Dropbox\Client;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProjectController extends Controller
{

    public function index()
    {
        $learners =  User::where('role', 2)->get(); //->where('is_self_publishing_learner', 1)
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
        $layout = str_contains(request()->getHttpHost(), 'giutbok') ? 'giutbok.layout' : 'backend.layout';
        return view('backend.project.index', compact('learners', 'activities', 'projects', 'layout',
            'projectNotes', 'nextProjectNumber'));
    }

    public function show($id)
    {
        $project = Project::find($id)->load(['books', 'user', 'selfPublishingList']);
        $editors = AdminHelpers::editorList();
        $copyEditingEditors = AdminHelpers::copyEditingEditors();
        $correctionEditors = AdminHelpers::correctionEditors();
        $editorAndAdminList = AdminHelpers::editorAndAdminList();
        $learners = [];//User::where('role', 2)->get(); //->where('is_self_publishing_learner', 1)
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
        $model = $request->id ? ProjectTask::find($request->id) : new ProjectTask();
        $model->fill($request->all());
        $model->save();

        return $model->load('editor');
    }

    public function updateTask($task_id, Request $request)
    {
        $task = ProjectTask::find($task_id);
        
        if (!$task) {
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

    public function saveProject( ProjectRequest $request, ProjectService $projectService )
    {
        $project = $projectService->saveProject($request);
        $nextProjectNumber = DB::table('projects')
            ->select(DB::raw('CAST(identifier AS UNSIGNED) as identifier_numeric'))
            ->orderByRaw('identifier_numeric DESC')
            ->value('identifier') + 1;

        return response()->json([
            'nextProjectNumber' => $nextProjectNumber,
            'project' => $project
        ]);
    }

    public function deleteProject( $project_id )
    {
        $project = Project::find($project_id);

        $activity = ProjectActivity::where('project_id', $project_id)->update([
            'project_id' => NULL
        ]);

        Contract::where('project_id', $project_id)->update([
            'project_id' => NULL
        ]);

        TimeRegister::where('project_id', $project_id)->update([
            'project_id' => NULL
        ]);

        $project->delete();
        return response()->json();
    }

    public function saveActivity( ProjectActivityRequest $request, ProjectService $projectService )
    {
        return $projectService->saveActivity($request);
    }

    public function deleteActivity( $id )
    {
        ProjectActivity::find($id)->delete();
        return response()->json();
    }

    public function saveNote( $project_id, Request $request )
    {
        $project = Project::find($project_id);
        $project->notes = $request->notes;
        $project->save();

        return response()->json($project);
    }

    public function addLearner( $project_id, Request $request, LearnerService $learnerService )
    {
        $this->validate($request, [
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
            'project' => $project
        ]);
    }

    /**
     * @param $project_id
     * @param Request $request
     * @param ProjectService $projectService
     * @return ProjectWholeBook|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
    public function saveWholeBook( $project_id, Request $request, ProjectService $projectService )
    {

        $request->merge(['project_id' => $project_id]);
        if (filter_var($request->is_file, FILTER_VALIDATE_BOOLEAN)) {
            if (!$request->id) {
                $this->validate($request, ['book_file' => 'required']);
            }
            $request->book_content = $projectService->uploadWholeBook($project_id, $request );
        } else {
            $this->validate($request, ['book_content' => 'required']);
        }

        if (filter_var($request->send_to_designer, FILTER_VALIDATE_BOOLEAN)) {
            $this->validate($request, [
                'designer_id' => 'required',
                'width' => 'required',
                'height' => 'required'
            ]);
        }

        $wholeBook = $request->id ? ProjectWholeBook::find($request->id) : new ProjectWholeBook();
        if ($request->has('is_book_critique')) {
            $wholeBook = $request->id ? ProjectBookCritique::find($request->id) : new ProjectBookCritique();
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
                "<a href='$loginLink'>Klikk her for å logge inn</a>"
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

    /**
     * @param $whole_book_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteWholeBook( $whole_book_id )
    {
        ProjectWholeBook::find($whole_book_id)->delete();
        return response()->json();
    }

    public function deleteBookCritique( $whole_book_id )
    {
        ProjectBookCritique::find($whole_book_id)->delete();
        return response()->json();
    }

    public function saveBookCritiqueFeedback($id, Request $request, ProjectService $projectService)
    {
        $request->merge(['project_id' => $id]);
        $this->validate($request, ['feedback' => 'required']);
        $record = ProjectBookCritique::find($id);
        $record->feedback = $projectService->uploadFeedback( $request );
        $record->save();

        return $record;

    }

    public function downloadWholeBook( $project_id, $whole_book_id )
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
                    'Content-Disposition' => 'attachment; filename="' . basename($wholeBook->book_content) . '"',
                ]);
            } catch (\Exception $e) {
                return redirect()->back()->with([
                    'errors' => AdminHelpers::createMessageBag('Failed to download the file from Dropbox: ' . $e->getMessage()),
                    'alert_type' => 'danger'
                ]);
            }
        } else {
            $phpWord = new PhpWord();

            $section = $phpWord->addSection();
            $content = view('docx.generic', compact('wholeBook'));
            \PhpOffice\PhpWord\Shared\Html::addHtml($section,$content,true);
            header('Content-Type: application/.docx');
            header('Content-Disposition: attachment;filename="'.$wholeBook->id.'.docx"');
            $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
            $objWriter->save('php://output');
            exit(); // added to prevent corrupt file
        }
    }

    public function saveBook( $project_id, ProjectBookRequest $request, ProjectService $projectService )
    {
        $request->merge(['project_id' => $project_id]);
        $response = $projectService->saveBook($request);

        return response()->json($response);
    }

    public function deleteBook( $id )
    {
        ProjectBook::find($id)->delete();
        return response()->json();
    }

    /**
     * @param $project_id
     * @param Request $request
     * @param ProjectService $projectService
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function saveBookPicture( $project_id, Request $request, ProjectService $projectService )
    {
        $this->validate($request, ['images' => 'required']);

        if ($request->id && count($request->file('images')) > 1) {
            return redirect()->back()->with([
                'errors'                => AdminHelpers::createMessageBag('only one image is allowed in update'),
                'alert_type'            => 'danger',
                'not-former-courses'    => true
            ]);
        }

        $request->merge(['project_id' => $project_id]);
        $projectService->saveBookPicture($request);

        return redirect()->back()->with([
            'errors'                => AdminHelpers::createMessageBag('Book picture saved successfully.'),
            'alert_type'            => 'success',
            'not-former-courses'    => true
        ]);

    }

    /**
     * @param $id
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function deleteBookPicture( $id )
    {
        $bookPicture = ProjectBookPicture::find($id);
        $bookPicture->delete();

        return redirect()->back()->with([
            'errors'                => AdminHelpers::createMessageBag('Book picture deleted successfully.'),
            'alert_type'            => 'success',
            'not-former-courses'    => true
        ]);
    }

    /**
     * @param $project_id
     * @param Request $request
     * @param ProjectService $projectService
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function saveBookFormatting( $project_id, Request $request, ProjectService $projectService )
    {
        if (!$request->id) {
            $this->validate($request, ['file' => 'required|mimes:doc,docx']);
        } 
        
        $request->merge(['project_id' => $project_id]);
        $projectService->saveBookFormatting($request);

        return redirect()->back()->with([
            'errors'                => AdminHelpers::createMessageBag('Book formatting saved successfully.'),
            'alert_type'            => 'success',
            'not-former-courses'    => true
        ]);
    }

    public function approveBookFormattingFeedback($id)
    {
        $bookFormatting = ProjectBookFormatting::find($id);
        $bookFormatting->feedback_status = 'completed';
        $bookFormatting->save();

        return redirect()->back()->with([
            'errors'                => AdminHelpers::createMessageBag('Book formatting feedback completed successfully.'),
            'alert_type'            => 'success',
            'not-former-courses'    => true
        ]);
    }

    /**
     * @param $id
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function deleteBookFormatting( $id )
    {
        $bookFormatting = ProjectBookFormatting::find($id);
        $bookFormatting->delete();

        return redirect()->back()->with([
            'errors'                => AdminHelpers::createMessageBag('Book formatting deleted successfully.'),
            'alert_type'            => 'success',
            'not-former-courses'    => true
        ]);
    }

    /**
     * Add to correction or copy editing
     * @param $project_id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addOtherService($project_id, ProjectCopyEditingRequest $request, ProjectService $projectService)
    {
        if ($project = Project::find($project_id)) {

            $manuType =  $projectService->saveOtherService($project_id, $request->merge([
                'user_id' => $project->user_id,
                'project_id' => $project_id,
                'type' => $request->is_copy_editing
            ]));

            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag($manuType.' Manuscript added successfully.'),
                'alert_type' => 'success',
                'not-former-courses' => true
            ]);

        }

        return redirect()->back();
    }

    public function graphicWork( $project_id )
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

        if (AdminHelpers::isGiutbokPage()) {
            $layout = 'giutbok.layout';
            $backRoute = 'g-admin.project.show';
            $saveGraphicRoute = 'g-admin.project.save-graphic-work';
            $deleteGraphicRoute = 'g-admin.project.delete-graphic-work';
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
             'saveBookFormattingRoute', 'bookFormattingList', 'deleteBookFormattingRoute', 'indesigns', 'designers', 'isbns'));
    }

    public function saveGraphicWork( $project_id, Request $request, ProjectService $projectService )
    {
        $request->merge(['project_id' => $project_id]);

        // create graphic work folder first
        AdminHelpers::createDirectory('storage/project-graphic-work');

        if (!$request->id){
            switch ($request->type) {
                case 'cover':
                    $this->validate($request, [
                        'cover.*' => 'required|mimes:jpeg,jpg,png,gif',
                        'description' => 'required',
                        'isbn_id' => 'required',
                        'cover_format' => 'required'
                    ]);
                    break;

                /*case 'barcode':
                    $this->validate($request, ['barcode' => 'required|mimes:jpeg,jpg,png,gif']);
                    break;*/

                case 'rewrite-script':
                    $this->validate($request, ['rewrite_script' => 'required|mimes:pdf']);
                    break;

                case 'trial-page':
                    $this->validate($request, ['trial_page' => 'required|mimes:jpeg,jpg,png,gif']);
                    break;

                case 'print-ready':
                        $this->validate($request, [
                            'print_ready' => 'required|mimes:pdf',
                            'format' => 'required'
                        ]);
                        break;

                case 'indesign':
                    /* if (!$request->id) {
                        $this->validate($request, ['cover' => 'required']);
                    } */
                    break;

                case 'sample-book-pdf':
                    $this->validate($request, ['sample_book_pdf' => 'required|mimes:pdf']);
                    break;
            }
        }

        $projectService->saveGraphicWorks($request);

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag(ucfirst(str_replace(['-', '_'],' ', $request->type)) . ' saved successfully.'),
                'alert_type' => 'success']);
    }

    public function deleteGraphicWork( $project_id, $graphic_work_id )
    {
        $graphicWork = ProjectGraphicWork::find($graphic_work_id);
        $type = $graphicWork->type;
        $graphicWork->delete();

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag(ucfirst(str_replace('-',' ', $type)) . ' delete successfully.'),
                'alert_type' => 'success']);
    }

    public function registration( $project_id )
    {
        $project = Project::find($project_id);
        $layout = 'backend.layout';
        $backRoute = 'admin.project.show';
        $saveRegistrationRoute = 'admin.project.save-registration';
        $deleteRegistrationRoute = 'admin.project.delete-registration';
        if (AdminHelpers::isGiutbokPage()) {
            $layout = 'giutbok.layout';
            $backRoute = 'g-admin.project.show';
            $saveRegistrationRoute = 'g-admin.project.save-registration';
            $deleteRegistrationRoute = 'g-admin.project.delete-registration';
        }

        $isbns = ProjectRegistration::isbns()->where('project_id', $project_id)->get();
        $isbnTypes = (new ProjectRegistration())->isbnTypes();
        
        $centralDistributions = ProjectRegistration::centralDistributions()->where('project_id', $project_id)->get();
        $mentorBookBases = ProjectRegistration::mentorBookBase()->where('project_id', $project_id)->get();
        $uploadFilesToMentorBookBases = ProjectRegistration::uploadFilesToMentorBookBase()
            ->where('project_id', $project_id)->get();

        return view('backend.project.registration', compact('project', 'layout', 'saveRegistrationRoute',
            'deleteRegistrationRoute', 'isbns', 'isbnTypes', 'centralDistributions', 'mentorBookBases', 
            'uploadFilesToMentorBookBases', 'backRoute'));
    }

    public function saveRegistration( $project_id, Request $request )
    {
        $data = $request->merge(['project_id' => $project_id])->except('_token');
        switch ($request->field) {
            case 'isbn':
                $this->validate($request, ['isbn' => 'required']);
                $data['value'] = $request->isbn;
                break;

            case 'central-distribution':
                $this->validate($request, ['central_distribution' => 'required|numeric']);
                $data['value'] = $request->central_distribution;
                break;

            case 'mentor-book-base':
                $this->validate($request, ['mentor_book_base' => 'required']);
                $data['value'] = $request->mentor_book_base;
                break;

            case 'upload-files-to-mentor-book-base':
                $this->validate($request, ['upload_files_to_mentor_book_base' => 'required|date']);
                $data['value'] = $request->upload_files_to_mentor_book_base;
                break;
        }

        if ($request->id) {
            $registration = ProjectRegistration::find($request->id);
            $registration->update($data);
        } else {
            $registration = ProjectRegistration::create($data);
        }

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag(ucfirst(str_replace('-',' ', $request->type)) . ' saved successfully.'),
                'alert_type' => 'success']);
    }

    public function deleteRegistration($project_id, $registration_id )
    {
        $registration = ProjectRegistration::find($registration_id);
        $type = $registration->type;
        $registration->delete();

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag(ucfirst(str_replace('-',' ', $type)) . ' delete successfully.'),
                'alert_type' => 'success']);
    }

    public function marketing( $project_id )
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
        $culturalCouncils = ProjectMarketing::culturalCouncils()->where('project_id', $project_id)->get();
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
            'setupOnlineStore', 'setupFacebook', 'advertisementFacebook', 'manuscriptSentToPrint', 'culturalCouncils',
            'freeWords', 'printEBooks', 'sampleBookApproved', 'pdfPrintIsApproved', 'numberOfAuthorBooks',
            'updateTheBookBase', 'ebookOrdered', 'ebookReceived', 'agreementOnTimeRegistration'));
    }

    public function saveMarketing( $project_id, Request $request, ProjectService $projectService )
    {
        $data = $request->merge(['project_id' => $project_id])->except('_token');

        // create graphic work folder first
        AdminHelpers::createDirectory('storage/project-marketing');
        $is_finished_field = 'is_finished';

        switch ($request->type) {
            case 'email-bookstore':
                if (!$request->id) {
                    $this->validate($request, ['email_bookstore' => 'required']);
                }
                $data['value'] = $projectService->saveMarketingFileOrImage($request, 'email_bookstore');
                break;

            case 'email-library':
                if (!$request->id) {
                    $this->validate($request, ['email_library' => 'required']);
                }
                $data['value'] = $projectService->saveMarketingFileOrImage($request, 'email_library');
                break;

            case 'email-press':
                if (!$request->id) {
                    $this->validate($request, ['email_press' => 'required']);
                }
                $data['value'] = $projectService->saveMarketingFileOrImage($request, 'email_press');
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
                if (!$request->id) {
                    $this->validate($request, ['cultural_council' => 'required']);
                }
                $data['value'] = $projectService->saveMarketingFileOrImage($request, 'cultural_council');
                $is_finished_field = 'is_finished_cultural_council';
                break;

            case 'application-free-word':
                if (!$request->id) {
                    $this->validate($request, ['free_word' => 'required']);
                }
                $data['value'] = $projectService->saveMarketingFileOrImage($request, 'free_word');
                $is_finished_field = 'is_finished_free_word';
                break;

            case 'agreement-on-time-registration':
                $is_finished_field = 'is_finished_agreement_on_time_registration';
                break;

            case 'print-ebook':
                if (!$request->id) {
                    $this->validate($request, ['print_ebook' => 'required']);
                }
                $data['value'] = $projectService->saveMarketingFileOrImage($request, 'print_ebook');
                $is_finished_field = 'is_finished_print_ebook';
                break;

            case 'sample-book-approved':
                if (!$request->id) {
                    $this->validate($request, ['sample_book_approved' => 'required']);
                }
                $data['value'] = $projectService->saveMarketingFileOrImage($request, 'sample_book_approved');
                $is_finished_field = 'is_finished_sample_book_approved';
                break;

            case 'pdf-print-is-approved':
                if (!$request->id) {
                    $this->validate($request, ['pdf_print_is_approved' => 'required|mimes:pdf']);
                }
                $data['value'] = $projectService->saveMarketingFileOrImage($request, 'pdf_print_is_approved');
                $is_finished_field = 'is_finished_pdf_print_is_approved';
                break;

            case 'number-of-author-books':
                if (!$request->id) {
                    $this->validate($request, ['number_of_author_books' => 'required|numeric']);
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
            ->with(['errors' => AdminHelpers::createMessageBag(ucfirst(str_replace('-',' ', $request->type)) . ' saved successfully.'),
                'alert_type' => 'success']);
    }

    public function deleteMarketing($project_id, $marketing_id )
    {
        $marketing = ProjectMarketing::find($marketing_id);
        $type = $marketing->type;
        $marketing->delete();

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag(ucfirst(str_replace('-',' ', $type)) . ' delete successfully.'),
                'alert_type' => 'success']);
    }

    public function marketingPlan( $project_id )
    {
        $project = Project::find($project_id);
        $marketingPlans = MarketingPlan::with(['questions.answers' => function($query) use ($project_id) {
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

    /**
     * @param $project_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function contract( $project_id )
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
     * @param $project_id
     * @param Request $request
     * @param ProjectService $projectService
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function uploadContract( $project_id, Request $request, ProjectService $projectService )
    {
        $request->merge([
            'project_id' => $project_id,
            'title' => 'Contract'
        ]);
        $projectService->uploadContract($request);

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag('Contract uploaded successfully.'),
                'alert_type' => 'success']);

    }

    public function uploadSignedContract( $project_id, $contract_id, Request $request, ProjectService $projectService )
    {
        $request->merge([
            'id' => $contract_id
        ]);
        $projectService->uploadContract($request);

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag('Signed contract uploaded successfully.'),
                'alert_type' => 'success']);
    }

    public function createContract( $project_id )
    {
        $route = route('admin.project.contract-store', $project_id);
        $action = 'create';
        $contract = [
            'title' => '',
            'details' => '',
            'signature' => '',
            'signature_label' => 'Signature',
            'end_date' => null,
            'is_file' => ''
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

    public function storeContract( $project_id, Request $request, ProjectService $projectService )
    {
        $contract = $projectService->saveContract( $request->merge(['project_id' => $project_id]) );

        $route = 'admin.project.contract-edit';
        if (AdminHelpers::isGiutbokPage()) {
            $route = 'g-admin.project.contract-edit';
        }

        return redirect(route($route, [$project_id, $contract->id]))
            ->with(['errors' => AdminHelpers::createMessageBag('Contract saved successfully.'),
                'alert_type' => 'success']);
    }

    /**
     * @param $project_id
     * @param $contract_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function editContract( $project_id, $contract_id )
    {
        $contract = Contract::findOrFail($contract_id)->toArray();

        if ($contract['signature']) {
            return redirect()->route('admin.project.contract-show', $contract['id']);
        }

        $action = 'edit';
        $title = 'Edit ' . $contract['title'];
        $backRoute = route('admin.project.contract', $project_id);
        $route = route('admin.project.contract-update', [$project_id, $contract['id']]);
        $layout = 'backend.layout';
        if (AdminHelpers::isGiutbokPage()) {
            $backRoute = route('g-admin.project.contract', $project_id);
            $layout = 'giutbok.layout';
            $route = route('g-admin.project.contract-update', [$project_id, $contract['id']]);
        }
        return view('backend.contract.form', compact('route', 'action', 'contract', 'title', 'backRoute',
            'layout'));
    }

    /**
     * @param $project_id
     * @param $contract_id
     * @param Request $request
     * @param ProjectService $projectService
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function updateContract( $project_id, $contract_id, Request $request, ProjectService $projectService )
    {
        $projectService->saveContract( $request, $contract_id);
        $route = 'admin.project.contract-edit';
        if (AdminHelpers::isGiutbokPage()) {
            $route = 'g-admin.project.contract-edit';
        }

        return redirect(route($route, [$project_id, $contract_id]))
            ->with(['errors' => AdminHelpers::createMessageBag('Contract saved successfully.'),
                'alert_type' => 'success']);
    }

    /**
     * @param $project_id
     * @param $contract_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showContract( $project_id, $contract_id )
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
     * @param $project_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function invoice( $project_id )
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
     * @param $project_id
     * @param Request $request
     * @param ProjectService $projectService
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function saveInvoice( $project_id, Request $request, ProjectService $projectService )
    {

        // create graphic work folder first
        AdminHelpers::createDirectory('storage/project-invoice');
        $invoice = $request->id ? ProjectInvoice::find($request->id) : new ProjectInvoice();

        if (!$request->id) {
            $this->validate($request, [
                'invoice' => 'required|mimes:pdf'
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
     * @param $project_id
     * @param $invoice_id
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function deleteInvoice( $project_id, $invoice_id )
    {
        $invoice = ProjectInvoice::find($invoice_id);
        $invoice->delete();

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag('Invoice deleted successfully.'),
                'alert_type' => 'success']);
    }

    public function saveManualInvoice( $project_id, Request $request )
    {
        $this->validate($request, [
            'invoice' => 'required'
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

    public function deleteManualInvoice( $project_id, $invoice_id )
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
        $project = Project::find($projectId);
        $projectUserBook = $project->userBookForSale;
        $projectUserBookId = $project->userBookForSale ? $project->userBookForSale->id : '';
        $userBooksForSale = UserBookForSale::where('user_id', $project->user_id)
        ->where(function($query) use ($projectUserBookId){
            $query->whereNull('project_id')
            ->orWhere('id', $projectUserBookId);
        })
        ->get();

        $totalBookSold = 0;
        $totalBookSale = 0;
        $currentYear = Carbon::now()->format('Y');
        $years = [];
        $quantitySoldList = [];
        $turnedOverList = [];

        if ($projectUserBook) {
            $totalBookSold = $projectUserBook->sales()->sum('quantity');
            $totalBookSale = $projectUserBook->sales()->sum('amount');

            $years = range($currentYear, $currentYear - 1);
        }

        $yearlyData = [
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
        ];

        return view('backend.project.storage', compact('backRoute', 'layout', 'projectId', 'project', 
        'projectUserBook', 'userBooksForSale', 'totalBookSold', 'totalBookSale', 'years', 'yearlyData'));
    }

    public function saveStorageBook($projectId, Request $request)
    {
        $currentProjectBookForSale = UserBookForSale::where('project_id', $projectId)->update([
            'project_id' => NULL
        ]);

        $userBookForSale = UserBookForSale::find($request->user_book_for_sale_id);
        $userBookForSale->project_id = $projectId;
        $userBookForSale->save();

        return back()
            ->with(['errors' => AdminHelpers::createMessageBag('Storage Book saved successfully.'),
                'alert_type' => 'success']); 
    }

    public function deleteStorageBook($projectId)
    {
        $userBookForSale = UserBookForSale::where('project_id', $projectId)->first();
        $userBookForSale->project_id = NULL;
        $userBookForSale->save();

        if ($userBookForSale->detail) {
            $userBookForSale->detail->delete();
        }

        if ($userBookForSale->various) {
            $userBookForSale->various->delete();
        }

        return back()
            ->with(['errors' => AdminHelpers::createMessageBag('Book removed from project successfully.'),
                'alert_type' => 'success']);
    }

    public function saveStorageBookDetails($book_id, Request $request)
    {
        StorageDetail::updateOrCreate([
                'user_book_for_sale_id' => $book_id
            ], [
                'subtitle'                  => $request->subtitle,
                'author'                    => $request->author,
                'editor'                    => $request->editor,
                'publisher'                 => $request->publisher,
                'book_group'                => $request->book_group,
                'item_number'               => $request->item_number,
                'isbn'                      => $request->isbn,
                'isbn_ebook'                => $request->isbn_ebook,
                'edition_on_sale'           => $request->edition_on_sale,
                'edition_total'             => $request->edition_total,
                'release_date'              => $request->release_date,
                'release_date_for_media'    => $request->release_date_for_media,
                'price_vat'                 => $request->price_vat,
                'registered_with_council'   => $request->registered_with_council,
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

    public function saveStorageVarious($book_id, Request $request)
    {

        StorageVarious::updateOrCreate([
            'user_book_for_sale_id' => $book_id
        ], [
            'publisher' => $request->publisher,
            'minimum_stock' => $request->minimum_stock,
            'weight' => $request->weight,
            'height' => $request->height,
            'width' => $request->width,
            'thickness' => $request->thickness,
            'cost' => $request->cost,
            'material_cost' => $request->material_cost
        ]);

        return back()
            ->with(['errors' => AdminHelpers::createMessageBag('Storage various saved successfully.'),
                'alert_type' => 'success']);
    }

    public function ebook($project_id)
    {
        $project = Project::find($project_id);

        $layout = 'backend.layout';
        $backRoute = route('admin.project.show', $project_id);
        $saveEbookRoute = 'admin.project.save-ebook';
        $deleteEbookRoute = 'admin.project.delete-ebook';

        $epubs = ProjectEbook::epub()->where('project_id', $project_id)->get();
        $mobis = ProjectEbook::mobi()->where('project_id', $project_id)->get();
        $covers = ProjectEbook::cover()->where('project_id', $project_id)->get();

        return view('backend.project.e-book', compact('layout', 'project', 'saveEbookRoute', 'epubs', 
            'deleteEbookRoute' ,'mobis', 'covers', 'backRoute'));
    }

    public function saveEbook( $project_id, Request $request, ProjectService $projectService )
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
            ->with(['errors' => AdminHelpers::createMessageBag(ucfirst(str_replace('-',' ', $request->type)) . ' saved successfully.'),
                'alert_type' => 'success']);
    }

    public function deleteEbook( $project_id, $ebook_id )
    {
        $ebook = ProjectEbook::find($ebook_id);
        $type = $ebook->type;
        $ebook->delete();

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag(ucfirst(str_replace('-',' ', $type)) . ' delete successfully.'),
                'alert_type' => 'success']);
    }

    public function audio($project_id)
    {
        $project = Project::find($project_id);

        $layout = 'backend.layout';
        $backRoute = route('admin.project.show', $project_id);
        $saveAudioRoute = 'admin.project.save-audio';
        $deleteAudioRoute = 'admin.project.delete-audio';

        $files = ProjectAudio::files()->where('project_id', $project_id)->get();
        $covers = ProjectAudio::cover()->where('project_id', $project_id)->get();
        
        return view('backend.project.audio', compact('layout', 'project', 'saveAudioRoute', 'files', 'deleteAudioRoute', 
            'covers', 'backRoute'));
    }

    public function saveAudio( $project_id, Request $request, ProjectService $projectService )
    {
        $request->merge(['project_id' => $project_id]);
        

        $projectService->saveAudio($request);

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag(ucfirst(str_replace('-',' ', $request->type)) . ' saved successfully.'),
                'alert_type' => 'success']);
    }

    public function deleteAudio( $project_id, $audio_id )
    {
        $audio = ProjectAudio::find($audio_id);
        $type = $audio->type;
        $audio->delete();

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag(ucfirst(str_replace('-',' ', $type)) . ' delete successfully.'),
                'alert_type' => 'success']);
    }

    public function print($project_id)
    {
        $project = Project::find($project_id);
        $print = $project->print;

        $layout = 'backend.layout';
        $backRoute = route('admin.project.show', $project_id);
        
        return view('backend.project.print', compact('layout', 'project', 'backRoute', 'print'));
    }

    public function savePrint($project_id, Request $request, ProjectService $projectService)
    {
        $request->merge(['project_id' => $project_id]);

        $projectService->savePrint($request);

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag('Print details saved successfully.'),
                'alert_type' => 'success']);
    }

    public function showNotes( $project_id )
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

    private function storageSalesByType($user_book_for_sale_id, $type) {
        return StorageSale::where('user_book_for_sale_id', $user_book_for_sale_id)
        ->where('type', $type)
        ->when(request()->filled('year') && request('year') != 'all', function ($query) {
            $query->whereYear('date', request('year'));
        })
        ->when(request()->filled('month') && request('month') != 'all', function ($query) {
            $query->whereMonth('date', request('month'));
        })->sum('value');
    }

    private function storageYearSalesByType($user_book_for_sale_id, $type) {
        $yearsData = DB::table('storage_sales')
        ->select(DB::raw('YEAR(date) AS year'), DB::raw('SUM(value) AS sum_value'))
        ->where('date', '>=', Carbon::now()->subYears(4))
        ->where('user_book_for_sale_id', $user_book_for_sale_id)
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
}