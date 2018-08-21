<?php
namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Manuscript;
use App\Feedback;
use App\User;
use App\CoursesTaken;
use App\Http\AdminHelpers;
use App\Http\Requests\FeedbackCreateRequest;
use File;
use Illuminate\Support\Str;

include_once($_SERVER['DOCUMENT_ROOT'].'/Docx2Text.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/Pdf2Text.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/Odt2Text.php');

class ManuscriptController extends Controller
{
   
    public function index()
    {
        $manuscripts = Manuscript::orderBy('created_at', 'desc')->paginate(15);
    	return view('backend.manuscript.index', compact('manuscripts'));
    }


    public function show($id)
    {
        $manuscript = Manuscript::findOrFail($id);
    	return view('backend.manuscript.show', compact('manuscript'));
    }



    public function store(Request $request)
    {   
        $courseTaken = CoursesTaken::findOrFail($request->coursetaken_id);
        if( $request->hasFile('file') &&  $request->file('file')->isValid() ) :
            $time = time();
            $destinationPath = 'storage/manuscripts/'; // upload path
            $extension = pathinfo($_FILES['file']['name'],PATHINFO_EXTENSION); // getting document extension
            $fileName = $time.'.'.$extension; // rename document
            $request->file->move($destinationPath, $fileName);
            // count words
            if($extension == "pdf") :
              $pdf  =  new \PdfToText ( $destinationPath.$fileName ) ;
              $pdf_content = $pdf->Text; 
              $word_count = AdminHelpers::get_num_of_words($pdf_content);
            elseif($extension == "docx") :
              $docObj = new \Docx2Text($destinationPath.$fileName);
              $docText= $docObj->convertToText();
              $word_count = AdminHelpers::get_num_of_words($docText);
            elseif($extension == "odt") :
              $doc = odt2text($destinationPath.$fileName);
              $word_count = AdminHelpers::get_num_of_words($doc);
            endif;

            Manuscript::create([
                'coursetaken_id' => $courseTaken->id,
                'filename' => '/'.$destinationPath.$fileName,
                'word_count' => $word_count
            ]);
        endif;
        return redirect()->back();
    }

    public function update($id, Request $request)
    {
        $manuscript = Manuscript::findOrFail($id);
        $manuscript->grade = $request->grade;
        $manuscript->save();
        return redirect()->back();
    }

    public function destroy($id, Request $request)
    {
        $manuscript = Manuscript::findOrFail($id);
        $file = substr($manuscript->filename, 1);
        if( File::exists( $file ) ) :
            File::delete( $file );
        endif;
        $manuscript->forceDelete();

        return redirect(route('admin.manuscript.index'));
    }


    public function addFeedback($manuscript_id, FeedbackCreateRequest $request)
    {
        $manuscript = Manuscript::findOrFail($manuscript_id);
        if( $request->hasFile('files') && $manuscript->feedbacks->count() == 0 ) :
            $files = [];
            foreach( $request->file('files') as $file ) :
                $time = Str::random(10).'-'.time();
                $destinationPath = 'storage/feedbacks/'; // upload path
                $extension = $file->getClientOriginalExtension(); // getting document extension
                $fileName = $time.'.'.$extension; // rename document
                $file->move($destinationPath, $fileName);
                $files[] = '/'.$destinationPath.$fileName;
            endforeach;

            Feedback::create([
                'manuscript_id' => $manuscript->id,
                'filename' => json_encode($files),
                'notes' => $request->notes
            ]);
        endif;

        return redirect()->back();
    }
    
    public function destroyFeedback($id)
    {
        $feedback = Feedback::findOrFail($id);
        $feedback->forceDelete();
        return redirect()->back();
    }


    public function assignEditor($manuscript_id, Request $request)
    {
        $manuscript = Manuscript::findOrFail($manuscript_id);
        $manuscript->feedback_user_id = $request->feedback_user_id;
        $manuscript->save();
        return redirect()->back();
    }
}
