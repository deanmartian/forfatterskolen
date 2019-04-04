<?php
namespace App\Http\Controllers\Backend;

use App\AssignmentFeedbackNoGroup;
use App\Helpers\DocumentParser;
use App\Helpers\Html2Text;
use App\Helpers\PdfParser;
use App\Http\FrontendHelpers;
use App\Mail\AssignmentManuscriptEmailToList;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Course;
use App\Assignment;
use App\AssignmentManuscript;
use App\AssignmentLearner;
use App\User;
use App\Http\AdminHelpers;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Reader\HTML;


include_once($_SERVER['DOCUMENT_ROOT'].'/Docx2Text.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/Pdf2Text.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/Odt2Text.php');

class AssignmentController extends Controller
{

    /**
     * AssignmentController constructor.
     */
    public function __construct()
    {
        // middleware to check if admin have access to this page
        $this->middleware('checkPageAccess:5');
    }

    public function index()
    {
        $assignments = Assignment::orderBy('created_at', 'desc')->paginate(15);
    	return view('backend.assignment.index', compact('assignments'));
    }





    public function show($course_id, $id)
    {
    	$course = Course::findOrFail($course_id);
    	$assignment = Assignment::findOrFail($id);
    	$assignments = Assignment::where('id', '!=', $id)->get();
        $editors = \App\User::where('role', 1)->get();

    	$section = 'assignments';
    	if( $assignment->course->id == $course->id ) :
    		return view('backend.assignment.show', compact('course', 'assignment', 'editors', 'section',
                'assignments'));
    	endif;
    	return abort('404');
    }


    public function setGrade($id, Request $request)
    {
        $assignmentManuscript = AssignmentManuscript::findOrFail($id);
        $assignmentManuscript->grade = $request->grade;
        $assignmentManuscript->save();
        return redirect()->back();
    }



    public function store($course_id, Request $request)
    {
    	$course = Course::findOrFail($course_id);
    	if( $request->title ) :
    		Assignment::create([
    			'title' => $request->title,
    			'description' => $request->description,
    			'course_id' => $course->id,
                'submission_date' => $request->submission_date,
                'available_date' => $request->available_date,
                'allowed_package' => isset($request->allowed_package) ? json_encode($request->allowed_package) : NULL,
                'add_on_price' => $request->add_on_price,
                'max_words' => (int) $request->max_words,
                'for_editor' => isset($request->for_editor) ? 1 : 0,
                'show_join_group_question' => isset($request->show_join_group_question) ? 1 : 0
    		]);

    	endif;
    	return redirect()->back();
    }



    public function update($course_id, $id, Request $request)
    {
    	$course = Course::findOrFail($course_id);
    	$assignment = Assignment::findOrFail($id);
    	
    	if( $assignment->course->id == $course->id && $request->title ) :
    		$assignment->title = $request->title;
    		$assignment->description = $request->description;
    		$assignment->submission_date = $request->submission_date;
            $assignment->available_date = $request->available_date;
    		$assignment->allowed_package = isset($request->allowed_package) ? json_encode($request->allowed_package) : NULL;
            $assignment->add_on_price = $request->add_on_price;
            $assignment->max_words = (int) $request->max_words;
            $assignment->for_editor = isset($request->for_editor) ? 1 : 0;
            $assignment->show_join_group_question = isset($request->show_join_group_question) ? 1 : 0;
    		$assignment->save();
    	endif;
    	return redirect()->back();
    }



    public function destroy($course_id, $id, Request $request)
    {
    	$course = Course::findOrFail($course_id);
    	$assignment = Assignment::findOrFail($id);
    	
    	if( $assignment->course->id == $course->id ) :
    		$assignment->forceDelete();
    	endif;
    	return redirect(route('admin.course.show', $course->id).'?section=assignments');
    }



    public function deleteManuscript($id)
    {
        //this code will delete the manuscript and not just empty the manuscript
        $manuscript = AssignmentManuscript::findOrFail($id);
        $manuscript->forceDelete();
        return redirect()->back();
    }


    /**
     * Move an assignment to another assignment
     * @param $manuscript_id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function moveManuscript($manuscript_id, Request $request)
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

        if ( $request->hasFile('filename') && 
            $request->file('filename')->isValid() ) :
            $time = time();
            $destinationPath = 'storage/assignment-manuscripts/'; // upload path
            $extensions = ['pdf', 'docx', 'odt', 'doc'];
            $extension = pathinfo($_FILES['filename']['name'],PATHINFO_EXTENSION); // getting document extension
            $actual_name = $learner->id;
            $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension);// rename document

            $expFileName = explode('/', $fileName);
            $request->filename->move($destinationPath, end($expFileName));

            if( !in_array($extension, $extensions) ) :
                return redirect()->back();
            endif;

            // count words
            $word_count = 0;
            if($extension == "pdf") :
                $pdf  =  new \PdfToText ( $destinationPath.end($expFileName) ) ;
                $pdf_content = $pdf->Text;
                $word_count = FrontendHelpers::get_num_of_words($pdf_content);
            elseif($extension == "docx") :
                $docObj = new \Docx2Text($destinationPath.end($expFileName));
                $docText= $docObj->convertToText();
                $word_count = FrontendHelpers::get_num_of_words($docText);
            elseif($extension == "doc") :
                $docText = FrontendHelpers::readWord($destinationPath.end($expFileName));
                $word_count = FrontendHelpers::get_num_of_words($docText);
            elseif($extension == "odt") :
                $doc = odt2text($destinationPath.end($expFileName));
                $word_count = FrontendHelpers::get_num_of_words($doc);
            endif;

            AssignmentManuscript::create([
                'assignment_id' => $assignment->id,
                'user_id' => $learner->id,
                'words' => $word_count,
                'filename' => '/'.$fileName,
                'join_group' => $request->join_group
            ]);
            return redirect()->back();
        endif;
    }

    public function replaceManuscript($id, Request $request)
    {
        $assignmentManuscript = AssignmentManuscript::find($id);

        if ($assignmentManuscript) {
            if ( $request->hasFile('filename') && $request->file('filename')->isValid() ) {
                $time = time();
                $destinationPath = 'storage/assignment-manuscripts/'; // upload path
                $extensions = ['pdf', 'docx', 'odt'];
                $extension = pathinfo($_FILES['filename']['name'],PATHINFO_EXTENSION); // getting document extension
                $actual_name = $assignmentManuscript->user_id;
                $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension);// rename document

                $expFileName = explode('/', $fileName);
                $request->filename->move($destinationPath, end($expFileName));

                if( !in_array($extension, $extensions) ) :
                    return redirect()->back();
                endif;

                // count words
                $word_count = 0;
                if($extension == "pdf") :
                    $pdf  =  new \PdfToText ( $destinationPath.end($expFileName) ) ;
                    $pdf_content = $pdf->Text;
                    $word_count = FrontendHelpers::get_num_of_words($pdf_content);
                elseif($extension == "docx") :
                    $docObj = new \Docx2Text($destinationPath.end($expFileName));
                    $docText= $docObj->convertToText();
                    $word_count = FrontendHelpers::get_num_of_words($docText);
                elseif($extension == "odt") :
                    $doc = odt2text($destinationPath.end($expFileName));
                    $word_count = FrontendHelpers::get_num_of_words($doc);
                endif;

                $assignmentManuscript->words        = $word_count;
                $assignmentManuscript->filename     = '/'.$destinationPath.end($expFileName);
                $assignmentManuscript->type         = $request->type;
                $assignmentManuscript->manu_type    = $request->manu_type;
                $assignmentManuscript->save();
            }
        }

        return redirect()->back();
    }

    /**
     * Update the lock status of assignment manuscript
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateLockStatus(Request $request)
    {
        $assignmentManuscript = AssignmentManuscript::find($request->manuscript_id);
        $success = false;

        if ($assignmentManuscript) {
            $assignmentManuscript->locked = $request->locked;
            $assignmentManuscript->save();
            $success = TRUE;
        }

        return response()->json([
            'data' => [
                'success' => $success,
            ]
        ]);
    }

    /**
     * Download the assignment manuscript
     * @param $id int assignment id
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

    public function downloadAllManuscript($id)
    {
        $assignment             = Assignment::find($id);
        $assignmentManuscripts  = AssignmentManuscript::where('assignment_id', $id)->get();

        $zipFileName    = $assignment->title.' Manuscripts.zip';
        $public_dir     = public_path('storage');
        $zip            = new \ZipArchive();

        if ($assignmentManuscripts) {

            if ($zip->open($public_dir . '/' . $zipFileName, \ZIPARCHIVE::CREATE | \ZIPARCHIVE::OVERWRITE) !== TRUE) {
                die ("An error occurred creating your ZIP file.");
            }

            foreach($assignmentManuscripts as $manuscript) {
                if (file_exists(public_path().'/'.$manuscript->filename)) {

                    //get the correct filename
                    $expFileName = explode('/', $manuscript->filename);
                    $file = str_replace('\\', '/', public_path());

                    // physical file location and name of the file
                    $zip->addFile($file.$manuscript->filename, end($expFileName));
                }
            }

            $zip->close();

            $headers = array(
                'Content-Type' => 'application/octet-stream',
            );

            $fileToPath = $public_dir.'/'.$zipFileName;

            if(file_exists($fileToPath)){
                return response()->download($fileToPath, $zipFileName, $headers)->deleteFileAfterSend(true);
            }
        }

        return redirect()->back();

    }

    /**
     * Export list of emails
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function exportEmailList($id)
    {
        $assignment             = Assignment::find($id);
        $assignmentManuscripts  = AssignmentManuscript::where('assignment_id', $id)->get();

        if ($assignmentManuscripts) {

            $excel              = \App::make('excel');
            $manuscripts        = $assignment->manuscripts;
            $emailList          = [];

            // loop all the learners
            foreach ($manuscripts as $manuscript) {
                $emailList[] = [$manuscript->user->email];
            }

            $excel->create($assignment->title.' Emails', function($excel) use ($emailList) {

                // Build the spreadsheet, passing in the payments array
                $excel->sheet('sheet1', function($sheet) use ($emailList) {
                    // prevent inserting an empty first row
                    $sheet->fromArray($emailList, null, 'A1', false, false);
                });
            })->download('xlsx');
        }

        return redirect()->back();
    }

    /**
     * Send email to the learners that sent assignment
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendEmailToList($id, Request $request)
    {
        $assignment     = Assignment::find($id);
        $manuscripts    = $assignment->manuscripts;

        if ($manuscripts) {
            foreach($manuscripts as $manuscript) {
                $userEmail = $manuscript->user->email;
                $emailData['data'] = $request->except('_token');
                // queue sending of email for fast loading
                \Mail::to($userEmail)->queue(new AssignmentManuscriptEmailToList($emailData));
            }

            return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Email sent successfully.'),
                'alert_type' => 'success']);
        }

        return redirect()->back();
    }

    /**
     * Auto-generate a document from 10 student and put it to one file before downloading
     * @param $assignmentId
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function generateDoc($assignmentId)
    {
        $assignment = Assignment::find($assignmentId);

        if ($assignment) {
            $assignmentManuscripts  = AssignmentManuscript::where('assignment_id', $assignmentId)
                //->where('filename', 'like', '%docx%')
                ->orderByRaw('RAND()')->take(10)->get();

            $newDoc                 = new PhpWord();
            $count                  = 1;
            $destinationPath        = 'storage/generated-manuscripts'; // upload path
            $actual_name            = $assignment->title;
            $extension              = 'docx';
            $generatedDocFileName   = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension);// rename document

            foreach ($assignmentManuscripts as $manuscript) {


                $getExtension = explode('.',$manuscript->filename);
                $fileExtension = end($getExtension);
                $readDoc = $this->read_docx(public_path($manuscript->filename));/*$manuscript->filename*/
                if ($fileExtension == 'pdf') {
                    $pdf  =  new \PdfToText ( public_path($manuscript->filename) ) ;
                    $readDoc = $pdf->Text;

                } elseif ($fileExtension == 'docx') {
                    $readDoc = $this->read_docx(public_path($manuscript->filename));/*$manuscript->filename*/

                } elseif ($fileExtension == 'doc') {
                    $readDoc = FrontendHelpers::readWord(public_path($manuscript->filename));
                } else {
                    //$readDoc = odt2text(public_path($manuscript->filename));
                    $odtToHtml = DocumentParser::parseFromFile(public_path($manuscript->filename));
                    $text = new Html2Text($odtToHtml);
                    $readDoc = $text->getText();
                }

                // Adding an empty Section to the document...
                $section = $newDoc->addSection();
                // Adding Text element to the Section having font styled by default...
                $section->addText(
                    'Tekst '.$count,
                    array('name' => 'Calibri Light (Heading)', 'size' => 16, 'color' => '4472C4')
                );

                //this is for adding spacing in the document
                $textlines = explode("\n", $readDoc);
                $textrun = $section->addTextRun();
                $textrun->addText(array_shift($textlines));
                foreach($textlines as $line) {
                    $textrun->addTextBreak();
                    $textrun->addText($line, array('name' => 'Calibri (Body)', 'size' => 11));
                }

                /*$section->addText(
                    $readDoc,
                    array('name' => 'Calibri (Body)', 'size' => 11)
                );*/

                $userEmail  = $manuscript->user->email;
                $subject    = 'Din tekst på dagens redigeringswebinar';
                $message    = 'Du har fått tekst nr. "'.$count.'"';
                $from       = 'post@forfatterskolen.no';

                $updateAssignment = AssignmentManuscript::find($manuscript->id);
                $updateAssignment->text_number = $count;
                $updateAssignment->save();

                //AdminHelpers::send_mail( $userEmail, $subject, $message, $from);
                AdminHelpers::send_email($subject,
                    'post@forfatterskolen.no', $userEmail, $message);
                $count++;
            }

            // check if directory does not exists
            if (!\File::exists($destinationPath)) {
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
     * @param $assignmentId
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
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateTypes($id, Request $request)
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
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function assignManuscriptEditor($id, Request $request)
    {
        $assignmentManuscript = AssignmentManuscript::find($id);

        if ($assignmentManuscript) {
            $assignmentManuscript->editor_id = $request->editor_id;
            $assignmentManuscript->save();
        }

        return redirect()->back();
    }

    /**
     * Download manuscript based on the assigned editor
     * @param $id
     * @param Request $request
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

                $zipFileName    = $assignment->title.' Manuscripts.zip';
                $public_dir     = public_path('storage');

                $zip            = new \ZipArchive();
                if ($zip->open($public_dir . '/' . $zipFileName, \ZIPARCHIVE::CREATE | \ZIPARCHIVE::OVERWRITE) !== TRUE) {
                    die ("An error occurred creating your ZIP file.");
                }

                foreach ($assignmentManuscripts as $manuscript) {
                    if (file_exists(public_path().'/'.$manuscript->filename)) {
                        //get the correct filename
                        $expFileName = explode('/', $manuscript->filename);
                        $file = str_replace('\\', '/', public_path());

                        // physical file location and name of the file
                        $zip->addFile($file.$manuscript->filename, end($expFileName));
                    }
                }

                $zip->close();

                $headers = array(
                    'Content-Type' => 'application/octet-stream',
                );

                $fileToPath = $public_dir.'/'.$zipFileName;

                if(file_exists($fileToPath)){
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
     * @param $assignmentId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function downloadExcelSheet($assignmentId)
    {
        $assignment = Assignment::find($assignmentId);
        if ($assignment) {
            $excel              = \App::make('excel');
            $manuscripts        = $assignment->manuscripts;
            $manuscriptList    = [];
            $manuscriptList[]  = ['learner id', 'genre', 'where in manu']; // first row in excel

            // loop all the learners
            foreach ($manuscripts as $manuscript) {
                $manuscriptList[] = [$manuscript->user->id,AdminHelpers::assignmentType($manuscript->type),
                    AdminHelpers::manuscriptType($manuscript->manu_type)];
            }

            $excel->create($assignment->title.' Learners', function($excel) use ($manuscriptList) {

                // Build the spreadsheet, passing in the payments array
                $excel->sheet('sheet1', function($sheet) use ($manuscriptList) {
                    // prevent inserting an empty first row
                    $sheet->fromArray($manuscriptList, null, 'A1', false, false);
                });
            })->download('xlsx');
        }

        return redirect()->back();
    }

    /**
     * Add feedback to assignments that don't have a group
     * @param $manuscript_id
     * @param $learner_id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function manuscriptFeedbackNoGroup($manuscript_id, $learner_id, Request $request)
    {
        $assignmentManuscript = AssignmentManuscript::find($manuscript_id);
        $assignmentManuscript->has_feedback = 1;
        // set grade
        if (is_numeric($request->grade)) {
            $assignmentManuscript->grade = $request->grade;
        }
        $assignmentManuscript->save();

        if ( $request->hasFile('filename')) :
            $time = time();
            $destinationPath = 'storage/assignment-feedbacks'; // upload path
            $extensions = ['pdf', 'docx', 'odt'];
            $filesWithPath = '';

            // loop through all the uploaded files
            foreach ($request->file('filename') as $k => $file) {
                $extension = pathinfo($_FILES['filename']['name'][$k],PATHINFO_EXTENSION);
                $actual_name = $learner_id;
                $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name."f", $extension);
                $filesWithPath .= "/".AdminHelpers::checkFileName($destinationPath, $actual_name."f", $extension).", ";

                if( !in_array($extension, $extensions) ) :
                    return redirect()->back();
                endif;

                $file->move($destinationPath, $fileName);

            }

            $filesWithPath = trim($filesWithPath,", ");

            AssignmentFeedbackNoGroup::create([
                'assignment_manuscript_id' => $manuscript_id,
                'learner_id' => $learner_id,
                'feedback_user_id' => Auth::user()->id,
                'filename' => $filesWithPath,
                'is_admin' => true,
                'is_active' => true,
                'availability' => $request->availability,
            ]);
            return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Feedback sent successfully.'),
                'alert_type' => 'success']);
        endif;
    }

    public function manuscriptFeedbackNoGroupUpdate($feedback_id, Request $request)
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

            if ( $request->hasFile('filename')) :
                $time = time();
                $destinationPath = 'storage/assignment-feedbacks'; // upload path
                $extensions = ['pdf', 'docx', 'odt'];
                $filesWithPath = '';

                // loop through all the uploaded files
                foreach ($request->file('filename') as $k => $file) {
                    $extension = pathinfo($_FILES['filename']['name'][$k],PATHINFO_EXTENSION);
                    $actual_name = $learner_id;
                    $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name."f", $extension);
                    $filesWithPath .= "/".AdminHelpers::checkFileName($destinationPath, $actual_name."f", $extension).", ";

                    if( !in_array($extension, $extensions) ) :
                        return redirect()->back();
                    endif;

                    $file->move($destinationPath, $fileName);

                }

                $filesWithPath = trim($filesWithPath,", ");

                $feedback->filename = $filesWithPath;
            endif;
            $feedback->assignment_manuscript_id = $manuscript_id;
            $feedback->learner_id = $learner_id;
            $feedback->feedback_user_id = Auth::user()->id;
            $feedback->availability = $request->availability;
            $feedback->save();
            return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Feedback sent successfully.'),
                'alert_type' => 'success']);
        }

        return redirect()->route('admin.course.index');
    }

    /**
     * Update availability of feedback with no group
     * @param $feedback_id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function manuscriptFeedbackNoGroupUpdateAvailability($feedback_id, Request $request)
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
     * @param $manuscript_id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateJoinGroup($manuscript_id, Request $request)
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
     * Read document file and return the content
     * @param $filename
     * @return bool|string
     */
    private function read_docx($filename){

        $striped_content = '';
        $content = '';

        $zip = zip_open($filename);

        if (!$zip || is_numeric($zip)) return false;

        while ($zip_entry = zip_read($zip)) {

            if (zip_entry_open($zip, $zip_entry) == FALSE) continue;

            if (zip_entry_name($zip_entry) != "word/document.xml") continue;

            $content .= zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));

            zip_entry_close($zip_entry);
        }// end while

        zip_close($zip);

        $content = str_replace('</w:r></w:p></w:tc><w:tc>', " ", $content);
        $content = str_replace('</w:r></w:p>', "\r\n", $content);
        $striped_content = strip_tags($content);

        return $striped_content;
    }
    
}
