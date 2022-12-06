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
use App\Http\Requests\ProjectActivityRequest;
use App\Http\Requests\ProjectBookRequest;
use App\Http\Requests\ProjectCopyEditingRequest;
use App\Http\Requests\ProjectRequest;
use App\MarketingPlan;
use App\Project;
use App\ProjectActivity;
use App\ProjectBook;
use App\ProjectBookFormatting;
use App\ProjectBookPicture;
use App\ProjectGraphicWork;
use App\ProjectMarketing;
use App\ProjectRegistration;
use App\ProjectWholeBook;
use App\Services\LearnerService;
use App\Services\ProjectService;
use App\Settings;
use App\TimeRegister;
use App\User;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\PhpWord;

class ProjectController extends Controller
{

    public function index()
    {
        $learners =  User::where('role', 2)->where('is_self_publishing_learner', 1)->get();
        $activities = ProjectActivity::all();
        $projects = Project::all();
        $projectNotes = Settings::getByName('project-notes');
        $layout = str_contains(request()->getHttpHost(), 'giutbok') ? 'giutbok.layout' : 'backend.layout';
        return view('backend.project.index', compact('learners', 'activities', 'projects', 'layout',
            'projectNotes'));
    }

    public function show($id)
    {
        $project = Project::find($id)->load(['books', 'user', 'selfPublishingList']);
        $editors = AdminHelpers::editorList();
        $learners = User::where('role', 2)->where('is_self_publishing_learner', 1)->get();
        $activities = ProjectActivity::all();
        $timeRegisters = TimeRegister::where('user_id', $project->user_id)->whereNull('project_id')->with('project')->get();
        $projectTimeRegisters = TimeRegister::where('project_id', $project->id)->with('project')->get();
        $projects = Project::all();
        $correctionFeedbackTemplate = AdminHelpers::emailTemplate('Correction Feedback');
        $copyEditingFeedbackTemplate = AdminHelpers::emailTemplate('Copy Editing Feedback');
        $bookPictures = ProjectBookPicture::where('project_id', $id)->get();
        $wholeBooks = ProjectWholeBook::where('project_id', $id)->get();
        $bookFormattingList = ProjectBookFormatting::where('project_id', $id)->get();

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
        $saveBookPicturesRoute = 'admin.project.save-picture';
        $deleteBookPicturesRoute = 'admin.project.delete-picture';
        $downloadOtherService = 'editor.other-service.download-doc';
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

        return view('backend.project.show', compact('project', 'editors', 'learners', 'activities',
            'timeRegisters', 'projectTimeRegisters', 'projects', 'layout', 'addOtherServiceRoute', 'selfPublishingStoreRoute',
            'selfPublishingUpdateRoute', 'selfPublishingDeleteRoute', 'selfPublishingAddFeedbackRoute',
            'selfPublishingDownloadFeedbackRoute', 'selfPublishingLearnersRoute', 'assignEditorRoute',
            'updateExpectedFinishRoute', 'updateStatusRoute', 'otherServiceDeleteRoute', 'correctionFeedbackTemplate',
            'copyEditingFeedbackTemplate', 'otherServiceFeedbackRoute', 'saveBookPicturesRoute', 'bookPictures',
            'deleteBookPicturesRoute', 'wholeBooks', 'downloadOtherService', 'saveBookFormattingRoute', 'bookFormattingList',
            'deleteBookFormattingRoute'));
    }

    public function saveProject( ProjectRequest $request, ProjectService $projectService )
    {
        return $projectService->saveProject($request);
    }

    public function deleteProject( $project_id )
    {
        $project = Project::find($project_id);

        $activity = ProjectActivity::where('project_id', $project_id)->update([
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
            $request->book_content = $projectService->uploadWholeBook( $request );
        } else {
            $this->validate($request, ['book_content' => 'required']);
        }

        $wholeBook = $request->id ? ProjectWholeBook::find($request->id) : new ProjectWholeBook();
        $wholeBook->project_id = $project_id;
        $wholeBook->book_content = $request->book_content;
        $wholeBook->description = $request->description;
        $wholeBook->is_file = filter_var($request->is_file, FILTER_VALIDATE_BOOLEAN);
        $wholeBook->save();

        return $wholeBook;

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

    public function downloadWholeBook( $project_id, $whole_book_id )
    {
        $wholeBook = ProjectWholeBook::find($whole_book_id);
        $project = Project::find($project_id);

        if ($wholeBook->is_file) {
            $pathinfo = pathinfo($wholeBook->book_content);
            $extension = $pathinfo['extension'];
            $filename = $pathinfo['filename'];
            return response()->download(public_path($wholeBook->book_content),$filename.'.'.$extension);
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
        $this->validate($request, ['file' => 'required|mimes:pdf']);
        $request->merge(['project_id' => $project_id]);
        $projectService->saveBookFormatting($request);

        return redirect()->back()->with([
            'errors'                => AdminHelpers::createMessageBag('Book formatting saved successfully.'),
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

            $manuType =  $projectService->saveOtherService($request->merge([
                'user_id' => $project->user_id,
                'project_id' => $project_id
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

        return view('backend.project.graphic-work', compact('project', 'layout', 'backRoute', 'saveGraphicRoute',
            'deleteGraphicRoute', 'covers', 'barCodes', 'rewriteScripts', 'trialPages', 'sampleBookPDFs'));
    }

    public function saveGraphicWork( $project_id, Request $request, ProjectService $projectService )
    {
        $request->merge(['project_id' => $project_id]);

        // create graphic work folder first
        AdminHelpers::createDirectory('storage/project-graphic-work');

        if (!$request->id){
            switch ($request->type) {
                case 'cover':
                    $this->validate($request, ['cover' => 'required|mimes:jpeg,jpg,png,gif']);
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

                case 'sample-book-pdf':
                    $this->validate($request, ['sample_book_pdf' => 'required|mimes:pdf']);
                    break;
            }
        }

        $projectService->saveGraphicWorks($request);

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag(ucfirst(str_replace('-',' ', $request->type)) . ' saved successfully.'),
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
        $centralDistributions = ProjectRegistration::centralDistributions()->where('project_id', $project_id)->get();
        $mentorBookBases = ProjectRegistration::mentorBookBase()->where('project_id', $project_id)->get();
        $uploadFilesToMentorBookBases = ProjectRegistration::uploadFilesToMentorBookBase()
            ->where('project_id', $project_id)->get();

        return view('backend.project.registration', compact('project', 'layout', 'saveRegistrationRoute',
            'deleteRegistrationRoute', 'isbns', 'centralDistributions', 'mentorBookBases', 'uploadFilesToMentorBookBases',
            'backRoute'));
    }

    public function saveRegistration( $project_id, Request $request )
    {
        $data = $request->merge(['project_id' => $project_id])->except('_token');
        switch ($request->type) {
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
        if (AdminHelpers::isGiutbokPage()) {
            $layout = 'giutbok.layout';
            $uploadContractRoute = 'g-admin.project.contract-upload';
            $createContractRoute = 'g-admin.project.contract-create';
            $signedUploadRoute = 'g-admin.project.contract-signed-upload';
            $contractShowRoute = 'g-admin.project.contract-show';
            $contractEditRoute = 'g-admin.project.contract-edit';
        }

        $project = Project::find($project_id);
        $contracts = Contract::whereNotNull('project_id')->paginate(10);

        return view('backend.project.contract.index', compact('project', 'layout', 'contracts',
            'uploadContractRoute', 'createContractRoute', 'signedUploadRoute', 'contractShowRoute', 'contractEditRoute'));
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
}