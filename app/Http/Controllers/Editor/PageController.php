<?php

namespace App\Http\Controllers\Editor;

use App\Assignment;
use App\EmailTemplate;
use App\FreeManuscript;
use App\Http\AdminHelpers;
use App\Http\FrontendHelpers;
use App\SelfPublishing;
use App\SelfPublishingFeedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\ShopManuscriptsTaken;
use App\AssignmentManuscript;
use App\Course;
use App\CorrectionManuscript;
use App\CopyEditingManuscript;
use App\Settings;
use Carbon\Carbon;

include_once($_SERVER['DOCUMENT_ROOT'].'/Docx2Text.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/Pdf2Text.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/Odt2Text.php');

class PageController extends Controller
{
    public function dashboard()
    {
        $assigned_shop_manuscripts = ShopManuscriptsTaken::where('feedback_user_id', Auth::user()->id)->get();
        $assignedAssignments = AssignmentManuscript::where('editor_id', Auth::user()->id) // assigned manuscript group / course
        ->where('status', 0)
        ->whereHas('assignment', function($query){
            $query->whereNull('parent');
            $query->orWhere('parent', 'assignment');
        })
        ->get();
        $coachingTimers = Auth::user()->assignedCoachingTimers()->where('status',0)->get();
        $corrections = Auth::user()->assignedCorrections;
        $copyEditings = Auth::user()->assignedCopyEditing;
        $singleCourses = Course::where('type', 'Single')
            ->where('id', '!=', 17)
            ->where('is_free', 0)
            ->get()->pluck('id');
        $assignedAssignmentManuscripts = AssignmentManuscript::where('editor_id', Auth::user()->id) //assigned manuscript no group
            ->where('status', 0)
            ->whereHas('assignment', function($query) {
                $query->where('parent', 'users');
            })
            ->get();
        $shopManuscriptRequests = Auth::user()->shopManuscriptRequests->where('answer', '')->where('answer_until', '>=', strftime('%Y-%m-%d', strtotime(Carbon::now())));
        $freeManuscripts = FreeManuscript::where('is_feedback_sent', '=',0)
            ->where('editor_id', Auth::user()->id)
            ->orderBy('created_at', 'desc')->get();
        $freeManuscriptEmailTemplate = EmailTemplate::where('page_name', 'Free Manuscript')->first();
        $freeManuscriptEmailTemplate2 = EmailTemplate::where('page_name', 'Free Manuscript 2')->first();
        $selfPublishingList = SelfPublishing::where('editor_id', Auth::user()->id)
            ->whereDoesntHave('feedback')
            ->get();

        return view('editor.dashboard', compact('assigned_shop_manuscripts', 'assignedAssignments', 'coachingTimers',
        'corrections', 'copyEditings', 'assignedAssignmentManuscripts', 'shopManuscriptRequests', 'freeManuscripts', 'freeManuscriptEmailTemplate',
            'freeManuscriptEmailTemplate2', 'selfPublishingList'));

    }

    public function upcomingAssignments()
    {
        $upcomingAssignments = Assignment::where('editor_id', '=', Auth::user()->id)
            ->whereDoesntHave('manuscripts') // check if there's no submitted manuscript yet
            ->oldest('submission_date')
            ->get();

        return view('editor.upcoming-assignment', compact('upcomingAssignments'));
    }

    public function assignmentArchive(Request $request)
    {
        if( $request->search_shop_manuscript && !empty($request->search_shop_manuscript) ) :
            $assigned_shop_manuscripts = ShopManuscriptsTaken::where('feedback_user_id', Auth::user()->id)
            ->whereHas('feedbacks', function($query){
                $query->where('approved', 1);
            }) //only the finished
            ->whereHas('user', function($query) use ($request){
                $query->where('id',$request->search_shop_manuscript);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10, ["*"], "assigned_shop_manuscripts");
        else :
            $assigned_shop_manuscripts = ShopManuscriptsTaken::where('feedback_user_id', Auth::user()->id)
            ->whereHas('feedbacks', function($query){
                $query->where('approved', 1);
            }) //only the finished
            ->orderBy('created_at', 'desc')
            ->paginate(10, ["*"], "assigned_shop_manuscripts");
        endif;
       
        if( $request->search_my_assignments && !empty($request->search_my_assignments) ) :
            $assignedAssignments = AssignmentManuscript::where('editor_id', Auth::user()->id) // assigned masunscript group / course
            ->where('status', 1)
            ->whereHas('assignment', function($query){
                $query->whereNull('parent');
                $query->orWhere('parent', 'assignment');
            })
            ->where('user_id', $request->search_my_assignments)
            ->orderBy('created_at', 'desc')
            ->paginate(10, ["*"], "assignedAssignments");
        else :
            $assignedAssignments = AssignmentManuscript::where('editor_id', Auth::user()->id) // assigned masunscript group / course
            ->where('status', 1)
            ->whereHas('assignment', function($query){
                $query->whereNull('parent');
                $query->orWhere('parent', 'assignment');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10, ["*"], "assignedAssignments");
        endif;

        if( $request->search_coaching_timer && !empty($request->search_coaching_timer) ) :
            $coachingTimers = Auth::user()->assignedCoachingTimers()->where('status',1) 
            ->whereHas('user', function($query) use ($request){
                $query->where('id',$request->search_coaching_timer);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10, ["*"], "coachingTimers");
        else :
            $coachingTimers = Auth::user()->assignedCoachingTimers()->where('status',1) 
            ->orderBy('created_at', 'desc')
            ->paginate(10, ["*"], "coachingTimers");
        endif;

        if( $request->search_correction && !empty($request->search_correction) ) : 
            $corrections = CorrectionManuscript::where('editor_id', Auth::user()->id)
            ->where('status', 2)
            ->whereHas('user', function($query) use ($request){
                $query->where('id',$request->search_correction);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10, ["*"], "corrections");
        else :
            $corrections = CorrectionManuscript::where('editor_id', Auth::user()->id)
            ->where('status', 2)
            ->orderBy('created_at', 'desc')
            ->paginate(10, ["*"], "corrections");
        endif;
        if( $request->search_copy_editing && !empty($request->search_copy_editing) ) :
            $copyEditings = CopyEditingManuscript::where('editor_id', Auth::user()->id)
            ->where('status', 2)
            ->whereHas('user', function($query) use ($request){
                $query->where('id',$request->search_copy_editing);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10, ["*"], "copyEditings");
        else :
            $copyEditings = CopyEditingManuscript::where('editor_id', Auth::user()->id)
            ->where('status', 2)
            ->orderBy('created_at', 'desc')
            ->paginate(10, ["*"], "copyEditings");
        endif;

        if( $request->search_personal_assignment && !empty($request->search_personal_assignment) ) :
            $assignedAssignmentManuscripts = AssignmentManuscript::where('editor_id', Auth::user()->id) //assigned manuscript no group
            ->where('status', 1)
            ->whereHas('assignment', function($query){
                $query->where('parent','users');
            })
            ->whereHas('user', function($query) use ($request){
                $query->where('id',$request->search_personal_assignment);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10, ["*"], "assignedAssignmentManuscripts");
            // $courses = Course::where('title', 'LIKE', '%' . $request->search  . '%')->orderBy('created_at', 'desc')->paginate(25);
        else :
            $assignedAssignmentManuscripts = AssignmentManuscript::where('editor_id', Auth::user()->id) //assigned manuscript no group
            ->where('status', 1)
            ->whereHas('assignment', function($query){
                $query->where('parent','users');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10, ["*"], "assignedAssignmentManuscripts");
        endif;
       

        return view('editor.assignment-archive', compact('assigned_shop_manuscripts', 'assignedAssignments', 'coachingTimers',
        'corrections', 'copyEditings', 'assignedAssignmentManuscripts'));
    }

    public function yearlyCalendar()
    {
        return view('editor.yearly-calendar');
    }

    public function editorsNote()
    {
        $note = Settings::editorsNote();
        return view('editor.editors-note', compact('note'));
    }

    public function selfPublishingFeedback( $publishing_id, Request $request )
    {
        $this->validate($request, [
            'manuscript' => 'required'
        ]);

        $filesWithPath = '';
        $word_count = 0;
        $destinationPath = 'storage/self-publishing-feedback/'; // upload path

        foreach ($request->file('manuscript') as $k => $file) {
            $extension = pathinfo($_FILES['manuscript']['name'][$k],PATHINFO_EXTENSION); // getting document extension
            $actual_name = pathinfo($_FILES['manuscript']['name'][$k],PATHINFO_FILENAME);
            $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension);// rename document

            $expFileName = explode('/', $fileName);
            $filePath = "/".$destinationPath.end($expFileName);
            $file->move($destinationPath, end($expFileName));

            $filesWithPath .= $filePath.", ";

            // count words
            if($extension == "pdf") :
                $pdf  =  new \PdfToText( $destinationPath.end($expFileName) ) ;
                $pdf_content = $pdf->Text;
                $word_count += FrontendHelpers::get_num_of_words($pdf_content);
            elseif($extension == "docx") :
                $docObj = new \Docx2Text($destinationPath.end($expFileName));
                $docText= $docObj->convertToText();
                $word_count += FrontendHelpers::get_num_of_words($docText);
            elseif($extension == "doc") :
                $docText = FrontendHelpers::readWord($destinationPath.end($expFileName));
                $word_count += FrontendHelpers::get_num_of_words($docText);
            elseif($extension == "odt") :
                $doc = odt2text($destinationPath.end($expFileName));
                $word_count += FrontendHelpers::get_num_of_words($doc);
            endif;
        }

        $feedback = new SelfPublishingFeedback();
        $feedback->self_publishing_id = $publishing_id;
        $feedback->feedback_user_id = \Auth::user()->id;
        $feedback->manuscript = $filesWithPath = trim($filesWithPath,", ");
        $feedback->notes = $request->notes;
        $feedback->save();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Self publishing feedback saved successfully.'),
            'alert_type' => 'success'
        ]);

    }

    public function selfPublishingDownloadManuscript( $publishing_id )
    {
        $publishing = SelfPublishing::find($publishing_id);
        $manuscripts = explode(', ', $publishing->manuscript);
        if (count($manuscripts) > 1) {
            $zipFileName    = $publishing->title.'.zip';
            $public_dir     = public_path('storage');
            $zip            = new \ZipArchive();

            // open zip file connection and create the zip
            if ($zip->open($public_dir . '/' . $zipFileName, \ZIPARCHIVE::CREATE | \ZIPARCHIVE::OVERWRITE) !== TRUE) {
                die ("An error occurred creating your ZIP file.");
            }

            foreach($manuscripts as $feedFile) {
                if (file_exists(public_path().'/'.trim($feedFile))) {

                    //get the correct filename
                    $expFileName = explode('/', $feedFile);
                    $file = str_replace('\\', '/', public_path());

                    // physical file location and name of the file
                    $zip->addFile(trim($file.trim($feedFile)), end($expFileName));
                }
            }

            $zip->close(); // close zip connection

            $headers = array(
                'Content-Type' => 'application/octet-stream',
            );

            $fileToPath = $public_dir.'/'.$zipFileName;

            if(file_exists($fileToPath)){
                return response()->download($fileToPath, $zipFileName, $headers)->deleteFileAfterSend(true);
            }

            return redirect()->back();
        }
        return response()->download(public_path($manuscripts[0]));
    }

}
