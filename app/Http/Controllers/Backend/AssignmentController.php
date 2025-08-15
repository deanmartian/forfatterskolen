<?php

namespace App\Http\Controllers\Backend;

use App\Assignment;
use App\AssignmentAddon;
use App\AssignmentDisabledLearner;
use App\AssignmentFeedbackNoGroup;
use App\AssignmentGroup;
use App\AssignmentGroupLearner;
use App\AssignmentManuscript;
use App\AssignmentTemplate;
use App\Course;
use App\DelayedEmail;
use App\Exports\AssignmentEmailListExport;
use App\Exports\AssignmentLearnersExport;
use App\Exports\GenericExport;
use App\Helpers\DocumentParser;
use App\Helpers\Html2Text;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Http\FrontendHelpers;
use App\Jobs\AddMailToQueueJob;
use App\Mail\AssignmentManuscriptEmailToList;
use App\Mail\SubjectBodyEmail;
use App\Package;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use PhpOffice\PhpWord\PhpWord;
use PhpParser\Node\Expr\Assign;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

include_once $_SERVER['DOCUMENT_ROOT'].'/Docx2Text.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/Pdf2Text.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/Odt2Text.php';

class AssignmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkPageAccess:5');
    }

    public function index(): View
    {
        $assignments = Assignment::forCourseOnly()->orderBy('created_at', 'desc')->paginate(15);
        $assignmentTemplate = new AssignmentTemplate;
        $templatePaginated = $assignmentTemplate->paginate(15);
        $assignmentTemplates = $assignmentTemplate->get();
        $learnerAssignments = Assignment::forLearnerOnly()->orderBy('created_at', 'desc')->paginate(15);

        return view('backend.assignment.index', compact('assignments', 'templatePaginated',
            'assignmentTemplates', 'learnerAssignments'));
    }

    public function show($course_id, $id)
    {
        $course = Course::findOrFail($course_id);
        $assignment = Assignment::findOrFail($id);
        $assignments = Assignment::where('id', '!=', $id)->get();
        $editors = \App\User::whereIn('role', [1, 3])->where('is_active', 1)->get();

        $section = 'assignments';
        if ($assignment->course->id == $course->id) {
            $assignmentManuscripts = $assignment->manuscripts()->whereHas('user')
                ->orderByRaw('editor_id = 0 DESC')
                ->orderByRaw('editor_expected_finish IS NULL, editor_expected_finish ASC')
                ->get();

            return view('backend.assignment.show', compact('course', 'editors', 'assignment', 'section',
                'assignments', 'assignmentManuscripts'));
        }

        return abort('404');
    }

    public function listManuscriptsWithoutEditor($course_id, $assignment_id)
    {
        $course = Course::findOrFail($course_id);
        $assignment = Assignment::findOrFail($assignment_id);

        if ($assignment->course->id == $course->id) {
            $manuscripts = AssignmentManuscript::with('user')->where('assignment_id', $assignment_id)
                ->where('editor_id', 0)->get();

            return response()->json($manuscripts);
        }

        return abort('404');
    }

    public function assignEditorToManuscripts($course_id, $assignment_id, Request $request): RedirectResponse
    {
        foreach ($request->learner_id as $learner_id) {
            $manuscript = AssignmentManuscript::where('user_id', $learner_id)
                ->where('assignment_id', $assignment_id)->first();

            if ($manuscript) {
                $manuscript->editor_id = $request->editor_id;
                $manuscript->editor_expected_finish = $request->editor_expected_finish;
                $manuscript->save();
            }
        }

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Editor assigned to manuscripts successfully.'),
            'alert_type' => 'success',
        ]);
    }

    public function setGrade($id, Request $request): RedirectResponse
    {
        $assignmentManuscript = AssignmentManuscript::findOrFail($id);
        $assignmentManuscript->grade = $request->grade;
        $assignmentManuscript->save();

        return redirect()->back();
    }

    public function generateGroup($assignmentID, Request $request)
    {
        $request->validate([
            'submission_date' => 'required',
        ]);

        // get all users where not in assignment group learners
        $existingGroups = Assignment::find($assignmentID)->groups->pluck('id');
        $learnersInGroup = AssignmentGroupLearner::whereIn('assignment_group_id', $existingGroups)->pluck('user_id');
        $learnersToGroup = AssignmentManuscript::where('assignment_id', $assignmentID)
            ->whereNotIn('user_id', $learnersInGroup)
            ->where('join_group', 1)->orderBy('type')->get();

        // group by 3 according to genre (prioritize grouping by genre)
        $assignmentType = FrontendHelpers::assignmentType();
        $assignmentType[] = ['id' => '', 'name' => 'none'];

        foreach ($assignmentType as $genre) {

            $saved = [];
            $min = 1;

            $learnedToGroupFiltered = $learnersToGroup->filter(function ($value, $key) use ($saved, $genre) {
                return ! (in_array($value->user_id, $saved)) && ($value->type == $genre['id']);
            });

            $groupCount = AssignmentGroup::where('assignment_id', $assignmentID)->count() + 1;

            if ($learnedToGroupFiltered->count() >= $min) {

                $count = 0;
                $max = 3;
                $assignmentGroup = null;

                // echo 'genre: '.$genre['id'].'</br></br>';
                // print_r($learnedToGroupFiltered);
                // echo '</br></br></br>';

                foreach ($learnedToGroupFiltered as $key) {

                    if ($count == 0) {
                        // create assignment group
                        $assignmentGroup = new AssignmentGroup;
                        $assignmentGroup->assignment_id = $assignmentID;
                        $assignmentGroup->title = trans('site.group-number').' '.$groupCount;
                        $assignmentGroup->submission_date = $request->submission_date;
                        $assignmentGroup->allow_feedback_download = isset($request->allow_feedback_download) ? 1 : 0;
                        $assignmentGroup->availability = null;
                        $assignmentGroup->save();
                        $groupCount++;
                    }

                    // create assignment group learners
                    $assignment_group_learners = new AssignmentGroupLearner;
                    $assignment_group_learners->assignment_group_id = $assignmentGroup->id;
                    $assignment_group_learners->user_id = $key->user_id;
                    $assignment_group_learners->save();

                    $count++;
                    if ($count == $max) {
                        $count = 0;
                    }

                    array_push($saved, $key->user_id);
                }
            }
        }

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Groups generated successfully.'),
            'alert_type' => 'success']);
    }

    public function store($course_id, Request $request): RedirectResponse
    {
        $course = Course::findOrFail($course_id);
        if ($request->title) {
            $assignment = Assignment::create([
                'title' => $request->title,
                'description' => $request->description,
                'course_id' => $course->id,
                'submission_date' => $request->submission_date,
                'available_date' => $request->available_date,
                'allowed_package' => isset($request->allowed_package) ? json_encode($request->allowed_package) : null,
                'add_on_price' => $request->add_on_price,
                'max_words' => (int) $request->max_words,
                'allow_up_to' => (int) $request->allow_up_to,
                'for_editor' => isset($request->for_editor) ? 1 : 0,
                'editor_manu_generate_count' => $request->editor_manu_generate_count,
                'show_join_group_question' => isset($request->show_join_group_question) ? 1 : 0,
                'send_letter_to_editor' => isset($request->send_letter_to_editor) ? 1 : 0,
                'check_max_words' => isset($request->check_max_words) ? 1 : 0,
                'assigned_editor' => ! isset($request->check_max_words) ? $request->assigned_editor : null,
                'editor_expected_finish' => $request->editor_expected_finish,
                'parent' => $request->linked_assignment ? 'assignment' : null,
                'parent_id' => $request->linked_assignment,
            ]);

            if ($request->linked_assignment) {
                $linkedAssignment = Assignment::find($request->linked_assignment);

                if ($linkedAssignment->parent === 'assignment') {
                    $previouslyLinkedAssignment = Assignment::find($linkedAssignment->parent_id);
                    $previouslyLinkedAssignment->parent = null;
                    $previouslyLinkedAssignment->parent_id = null;
                    $previouslyLinkedAssignment->save();
                }
                $linkedAssignment->parent = 'assignment';
                $linkedAssignment->parent_id = $assignment->id;
                $linkedAssignment->save();
            }

        }

        return redirect()->back();
    }

    public function update($course_id, $id, Request $request): RedirectResponse
    {
        $course = Course::findOrFail($course_id);
        $assignment = Assignment::findOrFail($id);
        $previouslyLinkedAssignmentID = $assignment->parent_id;

        if ($assignment->course->id == $course->id && $request->title) {
            $assignment->title = $request->title;
            $assignment->description = $request->description;
            $assignment->submission_date = $request->submission_date;
            $assignment->available_date = $request->available_date;
            $assignment->allowed_package = isset($request->allowed_package) ? json_encode($request->allowed_package) : null;
            $assignment->add_on_price = $request->add_on_price;
            $assignment->max_words = (int) $request->max_words;
            $assignment->allow_up_to = (int) $request->allow_up_to;
            $assignment->for_editor = isset($request->for_editor) ? 1 : 0;
            $assignment->editor_manu_generate_count = isset($request->for_editor) ? $request->editor_manu_generate_count : null;
            $assignment->show_join_group_question = isset($request->show_join_group_question) ? 1 : 0;
            $assignment->send_letter_to_editor = isset($request->send_letter_to_editor) ? 1 : 0;
            $assignment->check_max_words = isset($request->check_max_words) ? 1 : 0;
            $assignment->assigned_editor = ! isset($request->check_max_words) ? $request->assigned_editor : null;
            $assignment->editor_expected_finish = $request->editor_expected_finish;
            $assignment->expected_finish = $request->expected_finish;

            if ($request->linked_assignment) {
                $linkedAssignment = Assignment::find($request->linked_assignment);

                if (($linkedAssignment->parent === 'assignment' && ! is_null($linkedAssignment->parent_id) && $linkedAssignment->parent_id != $assignment->id)
                    || $previouslyLinkedAssignmentID && $previouslyLinkedAssignmentID != $request->linked_assignment) {

                    $previouslyLinkedAssignment = Assignment::find($linkedAssignment->parent_id ? $linkedAssignment->parent_id : $previouslyLinkedAssignmentID);
                    $previouslyLinkedAssignment->parent = null;
                    $previouslyLinkedAssignment->parent_id = null;
                    $previouslyLinkedAssignment->save();
                }

                $linkedAssignment->parent = 'assignment';
                $linkedAssignment->parent_id = $assignment->id;
                $linkedAssignment->save();
            }

            $assignment->parent = $request->linked_assignment ? 'assignment' : null;
            $assignment->parent_id = $request->linked_assignment;
            $assignment->save();

            if (! isset($request->check_max_words) && $request->assigned_editor) {
                $assignment->manuscripts()->update(
                    ['editor_id' => $request->assigned_editor]
                );
            }

        }

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Assignment updatd successfully.'),
            'alert_type' => 'success']);
    }

    public function destroy($course_id, $id, Request $request): RedirectResponse
    {
        $course = Course::findOrFail($course_id);
        $assignment = Assignment::findOrFail($id);

        if ($assignment->course->id == $course->id) {
            $assignment->forceDelete();
        }

        return redirect(route('admin.course.show', $course->id).'?section=assignments');
    }

    public function deleteManuscript($id): RedirectResponse
    {
        // this code will delete the manuscript and not just empty the manuscript
        $manuscript = AssignmentManuscript::findOrFail($id);
        $manuscript->forceDelete();

        return redirect()->back();
    }

    /**
     * Move an assignment to another assignment
     */
    public function moveManuscript($manuscript_id, Request $request): RedirectResponse
    {
        $manuscript = AssignmentManuscript::findOrFail($manuscript_id);
        if ($manuscript) {
            $manuscript->assignment_id = $request->assignment_id;
            $manuscript->save();

            return redirect()->back();
        }

        return redirect()->route('admin.assignment.index');
    }

    public function uploadManuscript($id, Request $request)
    {
        $assignment = Assignment::findOrFail($id);
        $learner = User::findOrFail($request->learner_id);

        if ($request->hasFile('filename') &&
            $request->file('filename')->isValid()) {
            $time = time();
            $destinationPath = 'storage/assignment-manuscripts/'; // upload path
            $extensions = ['pdf', 'docx', 'odt', 'doc'];
            $extension = pathinfo($_FILES['filename']['name'], PATHINFO_EXTENSION); // getting document extension
            $actual_name = $learner->id;
            $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension); // rename document

            $expFileName = explode('/', $fileName);
            $request->filename->move($destinationPath, end($expFileName));

            if (! in_array($extension, $extensions)) {
                return redirect()->back();
            }

            // count words
            $word_count = 0;
            if ($extension == 'pdf') {
                $pdf = new \PdfToText($destinationPath.end($expFileName));
                $pdf_content = $pdf->Text;
                $word_count = FrontendHelpers::get_num_of_words($pdf_content);
            } elseif ($extension == 'docx') {
                $docObj = new \Docx2Text($destinationPath.end($expFileName));
                $docText = $docObj->convertToText();
                $word_count = FrontendHelpers::get_num_of_words($docText);
            } elseif ($extension == 'doc') {
                $docText = FrontendHelpers::readWord($destinationPath.end($expFileName));
                $word_count = FrontendHelpers::get_num_of_words($docText);
            } elseif ($extension == 'odt') {
                $doc = odt2text($destinationPath.end($expFileName));
                $word_count = FrontendHelpers::get_num_of_words($doc);
            }

            AssignmentManuscript::create([
                'assignment_id' => $assignment->id,
                'user_id' => $learner->id,
                'words' => $word_count,
                'filename' => '/'.$fileName,
                'join_group' => $request->join_group,
                'editor_expected_finish' => $assignment->editor_expected_finish
                    ? strftime('%Y-%m-%d', strtotime($assignment->editor_expected_finish))
                    : null,
                'uploaded_at' => now(),
            ]);

            return redirect()->back();
        }
    }

    /**
     * Add assignment add-on for learner
     */
    public function addOnForLearner($assignment_id, Request $request): RedirectResponse
    {
        AssignmentAddon::firstOrCreate([
            'assignment_id' => $assignment_id,
            'user_id' => $request->learner_id,
        ]);

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Assignment added to learner successfully.'),
            'alert_type' => 'success']);
    }

    public function updateSubmissionDate($assignment_id, Request $request): RedirectResponse
    {
        $assignment = Assignment::find($assignment_id);
        $assignment->submission_date = $request->submission_date;
        $assignment->save();

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Submission date updated.'),
            'alert_type' => 'success', 'not-former-courses' => true]);
    }

    public function updateAvailableDate($assignment_id, Request $request): RedirectResponse
    {
        $assignment = Assignment::find($assignment_id);
        $assignment->available_date = $request->available_date;
        $assignment->save();

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Available date updated.'),
            'alert_type' => 'success', 'not-former-courses' => true]);
    }

    public function updateMaxWords($assignment_id, Request $request): RedirectResponse
    {
        $assignment = Assignment::find($assignment_id);
        $assignment->max_words = $request->max_words;

        if ($request->has('allow_up_to')){
            $assignment->allow_up_to = $request->allow_up_to;
        }

        $assignment->save();

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Max words updated.'),
            'alert_type' => 'success', 'not-former-courses' => true]);
    }

    public function replaceManuscript($id, Request $request): RedirectResponse
    {
        $assignmentManuscript = AssignmentManuscript::find($id);

        if ($assignmentManuscript) {
            if ($request->hasFile('filename') && $request->file('filename')->isValid()) {
                $time = time();
                $destinationPath = 'storage/assignment-manuscripts/'; // upload path
                $extensions = ['pdf', 'docx', 'odt'];
                $extension = pathinfo($_FILES['filename']['name'], PATHINFO_EXTENSION); // getting document extension
                $actual_name = $assignmentManuscript->user_id;
                $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension); // rename document

                $expFileName = explode('/', $fileName);
                $request->filename->move($destinationPath, end($expFileName));

                if (! in_array($extension, $extensions)) {
                    return redirect()->back();
                }

                // count words
                $word_count = 0;
                if ($extension == 'pdf') {
                    $pdf = new \PdfToText($destinationPath.end($expFileName));
                    $pdf_content = $pdf->Text;
                    $word_count = FrontendHelpers::get_num_of_words($pdf_content);
                } elseif ($extension == 'docx') {
                    $docObj = new \Docx2Text($destinationPath.end($expFileName));
                    $docText = $docObj->convertToText();
                    $word_count = FrontendHelpers::get_num_of_words($docText);
                } elseif ($extension == 'odt') {
                    $doc = odt2text($destinationPath.end($expFileName));
                    $word_count = FrontendHelpers::get_num_of_words($doc);
                }

                $assignmentManuscript->words = $word_count;
                $assignmentManuscript->filename = '/'.$destinationPath.end($expFileName);
                $assignmentManuscript->type = $request->type;
                $assignmentManuscript->manu_type = $request->manu_type;
                $assignmentManuscript->save();
            }
        }

        return redirect()->back();
    }

    /**
     * Update the lock status of assignment manuscript
     */
    public function updateLockStatus(Request $request): JsonResponse
    {
        $assignmentManuscript = AssignmentManuscript::find($request->manuscript_id);
        $success = false;

        if ($assignmentManuscript) {
            $assignmentManuscript->locked = $request->locked;
            $assignmentManuscript->save();
            $success = true;
        }

        return response()->json([
            'data' => [
                'success' => $success,
            ],
        ]);
    }

    public function updateDashboardStatus(Request $request): JsonResponse
    {
        $assignmentManuscript = AssignmentManuscript::find($request->manuscript_id);
        $success = false;

        if ($assignmentManuscript) {
            $assignmentManuscript->show_in_dashboard = $request->locked;
            $assignmentManuscript->save();
            $success = true;
        }

        return response()->json([
            'data' => [
                'success' => $success,
            ],
        ]);
    }

    /**
     * Download the assignment manuscript
     *
     * @param  $id  int assignment id
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadManuscript($id)
    {
        $assignmentManuscript = AssignmentManuscript::find($id);

        if ($assignmentManuscript) {
            $filename = $assignmentManuscript->filename;

            return response()->download(public_path($filename));
        }

        return redirect()->back();
    }

    public function downloadManuscriptLetter($id)
    {
        $assignmentManuscript = AssignmentManuscript::find($id);

        if ($assignmentManuscript) {
            $filename = $assignmentManuscript->letter_to_editor;

            return response()->download(public_path($filename));
        }

        return redirect()->back();
    }

    public function downloadAllManuscript($id)
    {
        $assignment = Assignment::find($id);
        $assignmentManuscripts = AssignmentManuscript::where('assignment_id', $id)->get();

        $zipFileName = str_replace('/', '-', $assignment->title).' Manuscripts.zip';
        $public_dir = public_path('storage');
        $zip = new \ZipArchive;

        if ($assignmentManuscripts) {

            if ($zip->open($public_dir.'/'.$zipFileName, \ZIPARCHIVE::CREATE | \ZIPARCHIVE::OVERWRITE) !== true) {
                exit('An error occurred creating your ZIP file.');
            }

            foreach ($assignmentManuscripts as $manuscript) {
                if (file_exists(public_path().'/'.$manuscript->filename)) {

                    // get the correct filename
                    $expFileName = explode('/', $manuscript->filename);
                    $file = str_replace('\\', '/', public_path());

                    // physical file location and name of the file
                    $zip->addFile($file.$manuscript->filename, end($expFileName));
                }
            }

            $zip->close();

            $headers = [
                'Content-Type' => 'application/octet-stream',
            ];

            $fileToPath = $public_dir.'/'.$zipFileName;

            if (file_exists($fileToPath)) {
                return response()->download($fileToPath, $zipFileName, $headers)->deleteFileAfterSend(true);
            }
        }

        return redirect()->back();

    }

    /**
     * Export list of emails
     */
    public function exportEmailList($id): RedirectResponse
    {
        $assignment = Assignment::find($id);
        $assignmentManuscripts = AssignmentManuscript::where('assignment_id', $id)->get();

        if ($assignmentManuscripts) {

            $excel = \App::make('excel');
            $manuscripts = $assignment->manuscripts;
            $emailList = [];

            // loop all the learners
            foreach ($manuscripts as $manuscript) {
                $emailList[] = [$manuscript->user->email];
            }

            return $excel->download(new AssignmentEmailListExport($emailList), $assignment->title.' Emails.xlsx');
            /*$excel->create($assignment->title.' Emails', function($excel) use ($emailList) {

                // Build the spreadsheet, passing in the payments array
                $excel->sheet('sheet1', function($sheet) use ($emailList) {
                    // prevent inserting an empty first row
                    $sheet->fromArray($emailList, null, 'A1', false, false);
                });
            })->download('xlsx');*/
        }

        return redirect()->back();
    }

    /**
     * Download learners with the assignment even if they don't submit assignment manuscript yet
     * include the users that have the assignment as add-on
     */
    public function exportLearnersIncludeAddOnLearners($assignment_id)
    {
        $assignment = Assignment::find($assignment_id);

        $packages = Package::where('course_id', $assignment->course->id)->get()->pluck('id');
        $learners = \DB::table('users')
            ->join('courses_taken', 'courses_taken.user_id', '=', 'users.id')
            ->whereIn('package_id', $packages)
            ->get();

        if ($packages = $assignment->allowed_packages) {
            $learners = \DB::table('users')
                ->join('courses_taken', 'courses_taken.user_id', '=', 'users.id')
                ->whereIn('package_id', $packages)
                ->get();
        }

        $addOnLearners = \DB::table('users')
            ->join('assignment_addons', 'assignment_addons.user_id', '=', 'users.id')
            ->where('assignment_id', $assignment_id)
            ->get();

        $allLearners = collect($learners)->merge($addOnLearners);

        if ($allLearners->count()) {
            $excel = \App::make('excel');
            $learnerList = [];
            // $learnerList[]  = ['Name', 'Email'];

            // loop all the learners
            foreach ($allLearners as $learner) {
                $learnerList[] = [$learner->first_name.' '.$learner->last_name, $learner->email];
            }

            return $excel->download(new AssignmentLearnersExport($learnerList), $assignment->title.' Learners.xlsx');
            /*$excel->create($assignment->title.' Learners', function($excel) use ($learnerList) {

                // Build the spreadsheet, passing in the payments array
                $excel->sheet('sheet1', function($sheet) use ($learnerList) {
                    // prevent inserting an empty first row
                    $sheet->fromArray($learnerList, null, 'A1', false, false);
                });
            })->download('xlsx');*/
        }

        return redirect()->back();

    }

    /**
     * Send email to the learners that sent assignment
     */
    public function sendEmailToList($id, Request $request): RedirectResponse
    {
        $assignment = Assignment::find($id);
        $manuscripts = $assignment->manuscripts;

        if ($manuscripts) {
            foreach ($manuscripts as $manuscript) {
                $userEmail = $manuscript->user->email;
                $emailData['data'] = $request->except('_token');
                // queue sending of email for fast loading
                // \Mail::to($userEmail)->queue(new AssignmentManuscriptEmailToList($emailData));
                dispatch(new AddMailToQueueJob($userEmail, $request->subject, $request->message,
                    'post@forfatterskolen.no', null, null,
                    'assignment-manuscripts', $manuscript->id));
            }

            return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Email sent successfully.'),
                'alert_type' => 'success']);
        }

        return redirect()->back();
    }

    public function emailManuscriptUser($id, Request $request): RedirectResponse
    {
        $manuscript = AssignmentManuscript::find($id);
        dispatch(new AddMailToQueueJob($manuscript->user->email, $request->subject, $request->message,
            $request->from_email, null, null,
            'assignment-manuscripts', $manuscript->id));

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Email sent successfully.'),
            'alert_type' => 'success', 'not-former-courses' => true]);
    }

    /**
     * Auto-generate a document from 10 student and put it to one file before downloading
     */
    public function generateDoc($assignmentId)
    {
        $assignment = Assignment::find($assignmentId);

        if ($assignment) {
            // take manuscript based on assignment assigned value if not the default is 10
            $takeCount = $assignment->editor_manu_generate_count ?: 10;
            $assignmentManuscripts = AssignmentManuscript::where('assignment_id', $assignmentId)
                // ->where('filename', 'like', '%docx%')
                ->orderByRaw('RAND()')->take($takeCount)->get();

            $newDoc = new PhpWord;
            $newDoc->setDefaultFontSize(11); // set default size
            $newDoc->setDefaultFontName('Calibri (Body)'); // set default font
            $count = 1;
            $destinationPath = 'storage/generated-manuscripts'; // upload path
            $actual_name = $assignment->title;
            $extension = 'docx';
            $generatedDocFileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension); // rename document

            foreach ($assignmentManuscripts as $manuscript) {

                $getExtension = explode('.', $manuscript->filename);
                $fileExtension = end($getExtension);
                // $readDoc = $this->read_docx(public_path($manuscript->filename));/*$manuscript->filename*/
                if ($fileExtension == 'pdf') {
                    $pdf = new \PdfToText(public_path($manuscript->filename));
                    $readDoc = $pdf->Text;

                } elseif ($fileExtension == 'docx') {
                    $readDoc = $this->read_docx(public_path($manuscript->filename)); /* $manuscript->filename */

                } elseif ($fileExtension == 'doc') {
                    $readDoc = FrontendHelpers::getContentFromDocFile(public_path($manuscript->filename));
                } else {
                    // $readDoc = odt2text(public_path($manuscript->filename));
                    $odtToHtml = DocumentParser::parseFromFile(public_path($manuscript->filename));
                    $text = new Html2Text($odtToHtml);
                    $readDoc = $text->getText();
                }

                // Adding an empty Section to the document...
                $section = $newDoc->addSection();
                // Adding Text element to the Section having font styled by default...
                $section->addText(
                    'Tekst '.$count,
                    ['name' => 'Calibri Light (Heading)', 'size' => 16, 'color' => '4472C4']
                );

                // this is for adding spacing in the document
                $textlines = explode("\n", $readDoc);
                $textrun = $section->addTextRun();
                $textrun->addText(array_shift($textlines));
                foreach ($textlines as $line) {
                    $textrun->addTextBreak();
                    $textrun->addText($line); // , array('name' => 'Calibri (Body)', 'size' => 11)
                }

                /*$section->addText(
                    $readDoc,
                    array('name' => 'Calibri (Body)', 'size' => 11)
                );*/

                $userEmail = $manuscript->user->email;
                $subject = 'Din tekst på dagens redigeringswebinar';
                $message = 'Du har fått tekst nr. "'.$count.'"';
                $from = 'postmail@forfatterskolen.no';

                $updateAssignment = AssignmentManuscript::find($manuscript->id);
                $updateAssignment->text_number = $count;
                $updateAssignment->save();

                // AdminHelpers::send_mail( $userEmail, $subject, $message, $from);
                /*AdminHelpers::send_email($subject,
                    'postmail@forfatterskolen.no', $userEmail, $message);*/
                $emailData['email_subject'] = $subject;
                $emailData['email_message'] = $message;
                $emailData['from_name'] = null;
                $emailData['from_email'] = $from;
                $emailData['attach_file'] = null;

                $emailTemplate = AdminHelpers::emailTemplate('Text Number');
                $message = AdminHelpers::formatEmailContent($emailTemplate->email_content, $userEmail,
                    $manuscript->user->first_name, '');
                $message = str_replace(':text_number', $count, $message);

                // \Mail::to($userEmail)->queue(new SubjectBodyEmail($emailData));
                dispatch(new AddMailToQueueJob($userEmail, $emailTemplate->subject, $message, $from, null, null,
                    'assignment-manuscripts', $manuscript->id));
                $count++;
            }

            // check if directory does not exists
            if (! \File::exists($destinationPath)) {
                \File::makeDirectory($destinationPath);
            }

            // generate the document file
            $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($newDoc, 'Word2007');
            $objWriter->save($generatedDocFileName);

            $assignment->generated_filepath = $generatedDocFileName;
            $assignment->save();

            return response()->download(public_path($generatedDocFileName));
        }

        return redirect()->back();
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadGenerateDoc($assignmentId)
    {
        $assignment = Assignment::find($assignmentId);
        if ($assignment) {
            return response()->download(public_path($assignment->generated_filepath));
        }

        return redirect()->back();
    }

    /**
     * Update assignment type
     */
    public function updateTypes($id, Request $request): RedirectResponse
    {
        $assignmentManuscript = AssignmentManuscript::find($id);

        if ($assignmentManuscript) {
            if (isset($request->type)) {
                $assignmentManuscript->type = $request->type;
            }

            if (isset($request->manu_type)) {
                $assignmentManuscript->manu_type = $request->manu_type;
            }

            $assignmentManuscript->save();
        }

        return redirect()->back();
    }

    /**
     * Assign Editor for the manuscript
     */
    public function assignManuscriptEditor($id, Request $request): RedirectResponse
    {
        $assignmentManuscript = AssignmentManuscript::find($id);

        if ($assignmentManuscript) {
            $assignmentManuscript->editor_id = $request->editor_id;

            if ($request->has('expected_finish')) {
                $assignmentManuscript->expected_finish = $request->expected_finish;

                $emailTemplate = AdminHelpers::emailTemplate('Assignment Manuscript Expected Finish');
                $replace_string = \Carbon\Carbon::parse($assignmentManuscript->expected_finish)->format('d.m.Y');
                $subject = $emailTemplate->subject;
                $from_email = $emailTemplate->from_email;
                $to = $assignmentManuscript->user->email;
                $email_content = AdminHelpers::formatEmailContent($emailTemplate->email_content, $to, $assignmentManuscript->user->first_name, '');
                $email_content = str_replace('_date_', $replace_string, $email_content);

                dispatch(new AddMailToQueueJob($to, $subject, $email_content, $from_email, null, null,
                    'assignment-manuscripts', $assignmentManuscript->id));
            }

            if ($request->has('editor_expected_finish')) {
                $assignmentManuscript->editor_expected_finish = $request->editor_expected_finish;
            }

            $assignment = $assignmentManuscript->assignment;
            if ($assignment->parent === 'users') {
                $assignment->editor_id = $request->editor_id;
                $assignment->save();
            }

            $assignmentManuscript->save();
        }

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Editor assigned successfully.'),
            'alert_type' => 'success',
        ]);
    }

    public function assignEditor($assignment_id, Request $request): RedirectResponse
    {
        $assignment = Assignment::find($assignment_id);

        if ($assignment) {
            $assignment->editor_id = $request->editor_id;
            $assignment->save();
        }

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Editor assigned successfully.'),
            'alert_type' => 'success',
        ]);
    }

    public function removeManuscriptEditor($id): RedirectResponse
    {
        $assignmentManuscript = AssignmentManuscript::find($id);

        if ($assignmentManuscript) {
            $assignmentManuscript->editor_id = 0;
            $assignmentManuscript->save();
        }

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Editor removed successfully.'),
            'alert_type' => 'success',
        ]);
    }

    public function assignManuscriptEditDates($assignment_manuscript_id, Request $request): RedirectResponse
    {
        $assignmentManuscript = AssignmentManuscript::findOrFail($assignment_manuscript_id);

        if ($request->has('expected_finish')) {
            $assignmentManuscript->expected_finish = $request->expected_finish;
        }

        if ($request->has('editor_expected_finish')) {
            $assignmentManuscript->editor_expected_finish = $request->editor_expected_finish;
        }

        $assignmentManuscript->save();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Expected finish date updated successfully.'),
            'alert_type' => 'success',
        ]);
    }

    public function removeEditor($id): RedirectResponse
    {
        $assignment = Assignment::find($id);

        if ($assignment) {
            $assignment->editor_id = 0;
            $assignment->save();
        }

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Editor removed successfully.'),
            'alert_type' => 'success',
        ]);
    }

    /**
     * Download manuscript based on the assigned editor
     *
     * @return $this|\Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadEditorManuscript($id, Request $request)
    {
        $assignment = Assignment::find($id);
        $assignmentManuscripts = AssignmentManuscript::where('assignment_id', $id)
            ->where('editor_id', $request->editor_id)
            ->get();
        $assignmentManuscriptsCount = $assignmentManuscripts->count();
        if ($assignmentManuscriptsCount) {
            if ($assignmentManuscriptsCount > 1) {
                $zipFileName = str_replace('/', '-', $assignment->title).' Manuscripts.zip';
                $public_dir = public_path('storage');

                $zip = new \ZipArchive;
                if ($zip->open($public_dir.'/'.$zipFileName, \ZIPARCHIVE::CREATE | \ZIPARCHIVE::OVERWRITE) !== true) {
                    exit('An error occurred creating your ZIP file.');
                }

                foreach ($assignmentManuscripts as $manuscript) {
                    if (file_exists(public_path().'/'.$manuscript->filename)) {
                        // get the correct filename
                        $expFileName = explode('/', $manuscript->filename);
                        $file = str_replace('\\', '/', public_path());

                        // physical file location and name of the file
                        $zip->addFile($file.$manuscript->filename, end($expFileName));
                    }
                }

                $zip->close();

                $headers = [
                    'Content-Type' => 'application/octet-stream',
                ];

                $fileToPath = $public_dir.'/'.$zipFileName;

                if (file_exists($fileToPath)) {
                    return response()->download($fileToPath, $zipFileName, $headers)->deleteFileAfterSend(true);
                }

            } else {
                return response()->download(public_path($assignmentManuscripts[0]->filename));
            }
        }

        return redirect()->back();

    }

    /**
     * Download assignment manuscript details
     */
    public function downloadExcelSheet($assignmentId): RedirectResponse
    {
        $assignment = Assignment::find($assignmentId);
        if ($assignment) {
            $excel = \App::make('excel');
            $manuscripts = $assignment->manuscripts;
            $manuscriptList = [];
            // $manuscriptList[]  = ['learner id', 'genre', 'where in manu']; // first row in excel
            $headers = ['learner id', 'genre', 'where in manu'];

            // loop all the learners
            foreach ($manuscripts as $manuscript) {
                $manuscriptList[] = [$manuscript->user->id, AdminHelpers::assignmentType($manuscript->type),
                    AdminHelpers::manuscriptType($manuscript->manu_type)];
            }

            return $excel->download(new GenericExport($manuscriptList, $headers), $assignment->title.' Learners.xlsx');
            /*$excel->create($assignment->title.' Learners', function($excel) use ($manuscriptList) {

                // Build the spreadsheet, passing in the payments array
                $excel->sheet('sheet1', function($sheet) use ($manuscriptList) {
                    // prevent inserting an empty first row
                    $sheet->fromArray($manuscriptList, null, 'A1', false, false);
                });
            })->download('xlsx');*/
        }

        return redirect()->back();
    }

    /**
     * Add feedback to assignments that don't have a group
     */
    public function manuscriptFeedbackNoGroup($manuscript_id, $learner_id, Request $request): RedirectResponse
    {

        $filesWithPath = $this->getFiles($request, $learner_id);
        $assignmentManuscript = AssignmentManuscript::find($manuscript_id);
        $manuscriptFeedback = $assignmentManuscript->noGroupFeedbacks()->first();
        if ($manuscriptFeedback) {
            $request->merge(['feedback_id' => $manuscriptFeedback->id]);
        }

        if ($request->feedback_id) { // update

            $assignmentFeedbackNoGroup = AssignmentFeedbackNoGroup::find($request->feedback_id);
            if ($filesWithPath) {
                // check if replace or add manuscript
                if ($request->replaceFiles || $manuscriptFeedback) {
                    $assignmentFeedbackNoGroup->filename = $filesWithPath;
                } else {
                    $assignmentFeedbackNoGroup->filename = $assignmentFeedbackNoGroup->filename.', '.$filesWithPath;
                }
            }
            $assignmentFeedbackNoGroup->hours_worked = $request->hours;
            $assignmentFeedbackNoGroup->notes_to_head_editor = $request->notes_to_head_editor;
            $assignmentFeedbackNoGroup->save();

            if (is_numeric($request->grade)) {
                $assignmentManuscript->grade = $request->grade;
            }
            $assignmentManuscript->save();

            return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Feedback updated successfully.'),
                'alert_type' => 'success']);

        } else { // new

            if ($request->hasFile('filename')) {

                $assignmentManuscript->has_feedback = 1;
                $assignmentManuscript->status = 0;
                // set grade
                if (is_numeric($request->grade)) {
                    $assignmentManuscript->grade = $request->grade;
                }
                $assignmentManuscript->save();

                $assignmentFeedbackNoGroup = AssignmentFeedbackNoGroup::create([
                    'assignment_manuscript_id' => $manuscript_id,
                    'learner_id' => $learner_id,
                    'feedback_user_id' => Auth::user()->id,
                    'filename' => $filesWithPath,
                    'is_admin' => true,
                    'is_active' => false,
                    'hours_worked' => $request->hours,
                    'notes_to_head_editor' => $request->notes_to_head_editor,
                    'availability' => $request->has('availability') ? $request->availability : null,
                ]);

                // send email to head editor
                $emailTemplate = AdminHelpers::emailTemplate('New Pending Feedback');
                $to = User::where('role', 1)->where('head_editor', 1)->first();

                dispatch(new AddMailToQueueJob($to->email, $emailTemplate->subject, $emailTemplate->email_content, $emailTemplate->from_email,
                    null, null, 'new-pending-assignment-feedback-no-group', $assignmentFeedbackNoGroup->id));

                return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Feedback saved successfully.'),
                    'alert_type' => 'success']);

            } else {

                return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Please provide a file.'),
                    'alert_type' => 'warning']);

            }

        }

    }

    public function getFiles($request, $learner_id)
    {
        if ($request->hasFile('filename')) {
            $filesWithPath = '';
            $time = time();
            $destinationPath = 'storage/assignment-feedbacks'; // upload path
            $extensions = ['pdf', 'docx', 'odt', 'doc'];
            // loop through all the uploaded files
            foreach ($request->file('filename') as $k => $file) {
                $extension = pathinfo($_FILES['filename']['name'][$k], PATHINFO_EXTENSION);
                $actual_name = $learner_id;
                $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name.'f', $extension);
                $filesWithPath .= '/'.AdminHelpers::checkFileName($destinationPath, $actual_name.'f', $extension).', ';
                if (! in_array($extension, $extensions)) {
                    return redirect()->back();
                }
                $file->move($destinationPath, $fileName);
            }

            return $filesWithPath = trim($filesWithPath, ', ');
        }
    }

    public function approveFeedbackNoGroup($manuscript_id, $learner_id, Request $request): RedirectResponse
    {
        // update feedback
        $assignmentFeedbackNoGroup = AssignmentFeedbackNoGroup::find($request->feedback_id);

        $filesWithPath = $this->getFiles($request, $learner_id);
        if ($filesWithPath) {
            $assignmentFeedbackNoGroup->filename = $filesWithPath;
        }
        $assignmentFeedbackNoGroup->availability = $request->availability;
        $assignmentFeedbackNoGroup->is_active = 1;
        $assignmentFeedbackNoGroup->save();

        // set status = 1 in assignmentManuscript
        $assignmentManuscript = AssignmentManuscript::find($manuscript_id);
        $assignmentManuscript->has_feedback = 1;
        $assignmentManuscript->status = 1;
        $assignmentManuscript->grade = $request->grade;
        $assignmentManuscript->save();

        // send an email
        $email_content = $request->message;
        $to = $assignmentManuscript->user->email;
        $first_name = $assignmentManuscript->user->first_name;

        if ($request->has('send_email')) {
            if ($request->availability && Carbon::parse($request->availability)->gt(Carbon::today())) {
                $redirect_link = route('learner.assignment', 'tab=feedback-from-editor');
                $formattedMailContent = AdminHelpers::formatEmailContent($email_content, $to, $first_name, $redirect_link);

                DelayedEmail::create([
                    'subject' => $request->subject,
                    'message' => $formattedMailContent,
                    'from_email' => $request->from_email,
                    'recipient' => $to,
                    'send_date' => $request->availability,
                    'parent' => 'assignment-manuscripts',
                    'parent_id' => $assignmentManuscript->id,
                ]);
            } else {
                $this->sendAssignmentFeedbackMail($email_content, $to, $first_name, $request->subject,
                    $request->from_email, $assignmentManuscript->id);
            }
        }

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Feedback successfully sent'),
            'alert_type' => 'success']);
    }

    public function manuscriptFeedbackNoGroupUpdate($feedback_id, Request $request): RedirectResponse
    {
        $feedback = AssignmentFeedbackNoGroup::find($feedback_id);

        if ($feedback) {
            $manuscript_id = $feedback->assignment_manuscript_id;
            $learner_id = $feedback->learner_id;

            $assignmentManuscript = AssignmentManuscript::find($manuscript_id);
            $assignmentManuscript->has_feedback = 1;
            // set grade
            if (is_numeric($request->grade)) {
                $assignmentManuscript->grade = $request->grade;
            }
            $assignmentManuscript->save();

            if ($request->hasFile('filename')) {
                $time = time();
                $destinationPath = 'storage/assignment-feedbacks'; // upload path
                $extensions = ['pdf', 'docx', 'odt', 'doc'];
                $filesWithPath = '';
                // loop through all the uploaded files
                foreach ($request->file('filename') as $k => $file) {
                    $extension = pathinfo($_FILES['filename']['name'][$k], PATHINFO_EXTENSION);
                    $actual_name = $learner_id;
                    $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name.'f', $extension);
                    $filesWithPath .= '/'.AdminHelpers::checkFileName($destinationPath, $actual_name.'f', $extension).', ';

                    if (! in_array($extension, $extensions)) {
                        return redirect()->back();
                    }

                    $file->move($destinationPath, $fileName);

                }

                $filesWithPath = trim($filesWithPath, ', ');

                $feedback->filename = $filesWithPath;
            }
            $feedback->assignment_manuscript_id = $manuscript_id;
            $feedback->learner_id = $learner_id;
            $feedback->feedback_user_id = Auth::user()->id;
            $feedback->availability = $request->availability;
            $feedback->save();

            // send email
            $email_content = $request->message;
            $to = $assignmentManuscript->user->email;
            $first_name = $assignmentManuscript->user->first_name;

            if ($request->availability && Carbon::parse($request->availability)->gt(Carbon::today())) {
                $redirect_link = route('learner.assignment', 'tab=feedback-from-editor');
                $formattedMailContent = AdminHelpers::formatEmailContent($email_content, $to, $first_name, $redirect_link);

                DelayedEmail::create([
                    'subject' => $request->subject,
                    'message' => $formattedMailContent,
                    'from_email' => $request->from_email,
                    'recipient' => $to,
                    'send_date' => $request->availability,
                    'parent' => 'assignment-manuscripts',
                    'parent_id' => $assignmentManuscript->id,
                ]);
            } else {
                $this->sendAssignmentFeedbackMail($email_content, $to, $first_name, $request->subject,
                    $request->from_email, $assignmentManuscript->id);
            }

            return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Feedback sent successfully.'),
                'alert_type' => 'success']);
        }

        return redirect()->route('admin.course.index');
    }

    /**
     * Update availability of feedback with no group
     */
    public function manuscriptFeedbackNoGroupUpdateAvailability($feedback_id, Request $request): RedirectResponse
    {
        $feedback = AssignmentFeedbackNoGroup::find($feedback_id);

        if ($feedback) {
            $feedback->availability = $request->availability;
            $feedback->save();

            return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Feedback sent successfully.'),
                'alert_type' => 'success']);
        }

        return redirect()->route('admin.course.index');
    }

    /**
     * Update the join group field
     */
    public function updateJoinGroup($manuscript_id, Request $request): RedirectResponse
    {
        $assignment = AssignmentManuscript::find($manuscript_id);
        if ($assignment) {

            $assignment->update($request->except('_token'));

            return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Join Group updated successfully.'),
                'alert_type' => 'success']);
        }

        return redirect()->back();
    }

    /**
     * @param  null  $id
     */
    public function saveAssignmentTemplate(Request $request, $id = null): RedirectResponse
    {
        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'submission_date' => $request->submission_date,
            'available_date' => $request->available_date,
            'max_words' => (int) $request->max_words,
        ];

        if ($id) {
            AssignmentTemplate::find($id)->update($data);
        } else {
            AssignmentTemplate::create($data);
        }

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Record saved successfully.'),
            'alert_type' => 'success']);
    }

    public function deleteAssignmentTemplate($id): RedirectResponse
    {
        AssignmentTemplate::find($id)->delete();

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Record deleted successfully.'),
            'alert_type' => 'success']);
    }

    /**
     * @param  null  $id
     */
    public function learnerAssignment(Request $request, $id = null)
    {
        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'submission_date' => $request->submission_date,
            'available_date' => $request->available_date,
            'max_words' => (int) $request->max_words,
            'allow_up_to' => (int) $request->allow_up_to,
            'show_join_group_question' => 0,
            'course_id' => $request->course_id,
            'parent_id' => $request->learner_id,
            'parent' => 'users',
            'editor_id' => $request->editor_id,
            'editor_expected_finish' => $request->editor_expected_finish,
            'send_letter_to_editor' => isset($request->send_letter_to_editor) ? 1 : 0,
        ];

        if ($id) {
            Assignment::find($id)->update($data);

            $assignmentManuscript = AssignmentManuscript::where('assignment_id', $id)->first();
            if ($assignmentManuscript) {
                $assignmentManuscript->editor_id = $request->editor_id;
                $assignmentManuscript->save();
            }
        } else {
            Assignment::create($data);
        }

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Record saved successfully.'),
            'alert_type' => 'success']);
    }

    public function disabledLearnerAssignment($assignment_id, Request $request): RedirectResponse
    {
        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'submission_date' => $request->submission_date,
            'available_date' => $request->available_date,
            'max_words' => (int) $request->max_words,
            'show_join_group_question' => 0,
            'course_id' => $request->course_id,
            'parent_id' => $request->learner_id,
            'parent' => 'users',
            'editor_id' => $request->editor_id,
            'editor_expected_finish' => $request->editor_expected_finish,
            'send_letter_to_editor' => isset($request->send_letter_to_editor) ? 1 : 0,
        ];

        $assignment = Assignment::create($data);

        AssignmentDisabledLearner::updateOrCreate([
            'assignment_id' => $assignment_id,
            'user_id' => $request->learner_id,
        ], [
            'personal_assignment_id' => $assignment->id,
        ]);

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Record saved successfully.'),
            'alert_type' => 'success']);
    }

    public function multipleLearnerAssignment(Request $request): RedirectResponse
    {

        foreach ($request->templates as $t) {
            $template = AssignmentTemplate::find($t);

            Assignment::create([
                'title' => $template->title,
                'description' => $template->description,
                'submission_date' => $template->submission_date,
                'available_date' => $template->available_date,
                'max_words' => (int) $template->max_words,
                'show_join_group_question' => 0,
                'course_id' => $request->course_id,
                'parent_id' => $request->learner_id,
                'parent' => 'users',
                'editor_id' => $request->editor_id,
                'editor_expected_finish' => $request->editor_expected_finish,
                'send_letter_to_editor' => isset($request->send_letter_to_editor) ? 1 : 0,
            ]);
        }

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Record saved successfully.'),
            'alert_type' => 'success']);
    }

    public function deleteLearnerAssignment($assignment_id): RedirectResponse
    {
        Assignment::find($assignment_id)->delete();

        return redirect()->to('/assignment?tab=learner')
            ->with(['errors' => AdminHelpers::createMessageBag('Record deleted successfully.'),
                'alert_type' => 'success']);
    }

    public function assignmentWithCourseLearner($assignmentId, $courseId): View
    {
        $assignment = Assignment::findOrFail($assignmentId);
        $disabledLearners = $assignment->disabledLearners()->pluck('user_id')->toArray();

        $course = Course::findOrFail($courseId);
        $courseLearners = $course->learners->get();

        return view('backend.assignment._disable_learners', compact('assignment', 'disabledLearners', 'courseLearners'));
    }

    public function disableLearner($assignmentId, Request $request)
    {
        $disabledLearner = AssignmentDisabledLearner::where([
            'assignment_id' => $assignmentId,
            'user_id' => $request->user_id,
        ])->first();

        filter_var($request->isChecked, FILTER_VALIDATE_BOOLEAN)
            ? AssignmentDisabledLearner::create(['assignment_id' => $assignmentId, 'user_id' => $request->user_id])
            : $disabledLearner?->delete();

        return $request->all();
    }

    public function sendAssignmentFeedbackMail($email_content, $to, $first_name, $subject, $from_email, $manuscript_id)
    {
        $redirect_link = route('learner.assignment', 'tab=feedback-from-editor');
        $formattedMailContent = AdminHelpers::formatEmailContent($email_content, $to, $first_name, $redirect_link);
        dispatch(new AddMailToQueueJob($to, $subject, $formattedMailContent, $from_email, null, null,
            'assignment-manuscripts', $manuscript_id));
        // AdminHelpers::queue_mail($to, $subject, $formattedMailContent, $from_email);
    }

    /**
     * Read document file and return the content
     *
     * @return bool|string
     */
    private function read_docx($filename)
    {

        $striped_content = '';
        $content = '';

        $zip = zip_open($filename);

        if (! $zip || is_numeric($zip)) {
            return false;
        }

        while ($zip_entry = zip_read($zip)) {

            if (zip_entry_open($zip, $zip_entry) == false) {
                continue;
            }

            if (zip_entry_name($zip_entry) != 'word/document.xml') {
                continue;
            }

            $content .= zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));

            zip_entry_close($zip_entry);
        }// end while

        zip_close($zip);

        $content = str_replace('</w:r></w:p></w:tc><w:tc>', ' ', $content);
        $content = str_replace('</w:r></w:p>', "\r\n", $content);
        $striped_content = strip_tags($content);

        return $striped_content;
    }

    public function updateAssignmentManuscriptStatus($manu_id, Request $request): RedirectResponse
    {
        $assignmentManu = AssignmentManuscript::find($manu_id);
        $assignmentManu->manuscript_status = $request->manuscript_status;
        $assignmentManu->save();

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Record saved successfully.'),
            'alert_type' => 'success']);
    }

    public function assignmentManuscriptFinished($assignment_manuscript_id): RedirectResponse
    {
        if ($assignment = AssignmentManuscript::find($assignment_manuscript_id)) {
            $assignment->status = AssignmentManuscript::FINISHED_STATUS;
            $assignment->save();

            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Assignment manuscript saved successfully.'),
                'alert_type' => 'success',
            ]);
        }

        return redirect()->back();
    }

    public function specialCharacters($string)
    {
        $characters = [
            '&lt;' => '', '&gt;' => '', '&#039;' => '', '&amp;' => '',
            '&quot;' => '', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'Ae',
            '&Auml;' => 'A', 'Å' => 'A', 'Ā' => 'A', 'Ą' => 'A', 'Ă' => 'A', 'Æ' => 'Ae',
            'Ç' => 'C', 'Ć' => 'C', 'Č' => 'C', 'Ĉ' => 'C', 'Ċ' => 'C', 'Ď' => 'D', 'Đ' => 'D',
            'Ð' => 'D', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ē' => 'E',
            'Ę' => 'E', 'Ě' => 'E', 'Ĕ' => 'E', 'Ė' => 'E', 'Ĝ' => 'G', 'Ğ' => 'G',
            'Ġ' => 'G', 'Ģ' => 'G', 'Ĥ' => 'H', 'Ħ' => 'H', 'Ì' => 'I', 'Í' => 'I',
            'Î' => 'I', 'Ï' => 'I', 'Ī' => 'I', 'Ĩ' => 'I', 'Ĭ' => 'I', 'Į' => 'I',
            'İ' => 'I', 'Ĳ' => 'IJ', 'Ĵ' => 'J', 'Ķ' => 'K', 'Ł' => 'K', 'Ľ' => 'K',
            'Ĺ' => 'K', 'Ļ' => 'K', 'Ŀ' => 'K', 'Ñ' => 'N', 'Ń' => 'N', 'Ň' => 'N',
            'Ņ' => 'N', 'Ŋ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O',
            'Ö' => 'Oe', '&Ouml;' => 'Oe', 'Ø' => 'O', 'Ō' => 'O', 'Ő' => 'O', 'Ŏ' => 'O',
            'Œ' => 'OE', 'Ŕ' => 'R', 'Ř' => 'R', 'Ŗ' => 'R', 'Ś' => 'S', 'Š' => 'S',
            'Ş' => 'S', 'Ŝ' => 'S', 'Ș' => 'S', 'Ť' => 'T', 'Ţ' => 'T', 'Ŧ' => 'T',
            'Ț' => 'T', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'Ue', 'Ū' => 'U',
            '&Uuml;' => 'Ue', 'Ů' => 'U', 'Ű' => 'U', 'Ŭ' => 'U', 'Ũ' => 'U', 'Ų' => 'U',
            'Ŵ' => 'W', 'Ý' => 'Y', 'Ŷ' => 'Y', 'Ÿ' => 'Y', 'Ź' => 'Z', 'Ž' => 'Z',
            'Ż' => 'Z', 'Þ' => 'T', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a',
            'ä' => 'ae', '&auml;' => 'ae', 'å' => 'a', 'ā' => 'a', 'ą' => 'a', 'ă' => 'a',
            'æ' => 'ae', 'ç' => 'c', 'ć' => 'c', 'č' => 'c', 'ĉ' => 'c', 'ċ' => 'c',
            'ď' => 'd', 'đ' => 'd', 'ð' => 'd', 'è' => 'e', 'é' => 'e', 'ê' => 'e',
            'ë' => 'e', 'ē' => 'e', 'ę' => 'e', 'ě' => 'e', 'ĕ' => 'e', 'ė' => 'e',
            'ƒ' => 'f', 'ĝ' => 'g', 'ğ' => 'g', 'ġ' => 'g', 'ģ' => 'g', 'ĥ' => 'h',
            'ħ' => 'h', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ī' => 'i',
            'ĩ' => 'i', 'ĭ' => 'i', 'į' => 'i', 'ı' => 'i', 'ĳ' => 'ij', 'ĵ' => 'j',
            'ķ' => 'k', 'ĸ' => 'k', 'ł' => 'l', 'ľ' => 'l', 'ĺ' => 'l', 'ļ' => 'l',
            'ŀ' => 'l', 'ñ' => 'n', 'ń' => 'n', 'ň' => 'n', 'ņ' => 'n', 'ŉ' => 'n',
            'ŋ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'oe',
            '&ouml;' => 'oe', 'ø' => 'o', 'ō' => 'o', 'ő' => 'o', 'ŏ' => 'o', 'œ' => 'oe',
            'ŕ' => 'r', 'ř' => 'r', 'ŗ' => 'r', 'š' => 's', 'ù' => 'u', 'ú' => 'u',
            'û' => 'u', 'ü' => 'ue', 'ū' => 'u', '&uuml;' => 'ue', 'ů' => 'u', 'ű' => 'u',
            'ŭ' => 'u', 'ũ' => 'u', 'ų' => 'u', 'ŵ' => 'w', 'ý' => 'y', 'ÿ' => 'y',
            'ŷ' => 'y', 'ž' => 'z', 'ż' => 'z', 'ź' => 'z', 'þ' => 't', 'ß' => 'ss',
            'ſ' => 'ss', 'ый' => 'iy', 'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G',
            'Д' => 'D', 'Е' => 'E', 'Ё' => 'YO', 'Ж' => 'ZH', 'З' => 'Z', 'И' => 'I',
            'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
            'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F',
            'Х' => 'H', 'Ц' => 'C', 'Ч' => 'CH', 'Ш' => 'SH', 'Щ' => 'SCH', 'Ъ' => '',
            'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'YU', 'Я' => 'YA', 'а' => 'a',
            'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo',
            'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l',
            'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's',
            'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch',
            'ш' => 'sh', 'щ' => 'sch', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e',
            'ю' => 'yu', 'я' => 'ya',
        ];

        return $string;

        return str_replace(array_keys($characters), $characters, $string);
    }
}
