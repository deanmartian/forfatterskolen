<?php

namespace App\Http\Controllers\Backend;

use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Http\FrontendHelpers;
use App\SelfPublishing;
use App\SelfPublishingFeedback;
use App\SelfPublishingLearner;
use App\User;
use Illuminate\Http\Request;

include_once($_SERVER['DOCUMENT_ROOT'].'/Docx2Text.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/Pdf2Text.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/Odt2Text.php');

class SelfPublishingController extends Controller
{

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $publishingList = SelfPublishing::all();
        $editors = AdminHelpers::editorList();
        $learners = User::where('role', 2)->get();
        return view('backend.self-publishing.index', compact('publishingList', 'editors', 'learners'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store( Request $request )
    {
        $this->saveData($request);

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Self publishing created successfully.'),
            'alert_type' => 'success'
        ]);
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update( $id, Request $request )
    {
        $this->saveData($request, $id);

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Self publishing updated successfully.'),
            'alert_type' => 'success'
        ]);
    }

    /**
     * @param Request $request
     * @param null $id
     * @throws \Illuminate\Validation\ValidationException
     */
    public function saveData( Request $request, $id = null )
    {
        $this->validate($request,[
            'title'         => 'required',
            'description'   => 'required',
            'file_path'     => 'mimes:pdf,doc,docx',
        ]);

        $publishing = $id ? SelfPublishing::find($id) : new SelfPublishing();
        $publishing->title = $request->title;
        $publishing->description = $request->description;

        if ( $request->hasFile('manuscript') ) :

            $filesWithPath = '';
            $word_count = 0;
            $destinationPath = '/storage/self-publishing-manuscript/'; // upload path

            foreach ($request->file('manuscript') as $k => $file) {
                $extension = pathinfo($_FILES['manuscript']['name'][$k],PATHINFO_EXTENSION); // getting document extension
                $actual_name = pathinfo($_FILES['manuscript']['name'][$k],PATHINFO_FILENAME);
                $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension);// rename document

                $expFileName = explode('/', $fileName);
                $filePath = $destinationPath.end($expFileName);
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

            $publishing->manuscript = $filesWithPath = trim($filesWithPath,", ");
            $publishing->word_count = $word_count;
        endif;

        $publishing->editor_id = $request->editor_id;
        $publishing->price = $request->price;
        $publishing->editor_share = $request->editor_share;
        $publishing->expected_finish = $request->expected_finish;
        $publishing->save();

        if ($request->learners) {
            foreach($request->learners as $learner ) {
                $publishing->learners()->create([
                    'user_id' => $learner
                ]);
            }
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy( $id )
    {
        $publishing = SelfPublishing::find($id);
        $publishing->delete();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Record deleted successfully.'),
            'alert_type' => 'success'
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function removeLearnerFromPublishing( $id )
    {
        $publishingLearner = SelfPublishingLearner::find($id);
        $publishingLearner->delete();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Learner removed from self-publishing successfully.'),
            'alert_type' => 'success',
            'not-former-courses' => true
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function learners( $id )
    {
        $selfPublishing = SelfPublishing::find($id);
        $learners = $selfPublishing->learners;
        $availableLearners = User::where('role', 2)->whereNotIn('id', $learners->pluck('user_id')->toArray())
            ->get();
        return view('backend.self-publishing.learners', compact('selfPublishing','learners', 'availableLearners'));
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addLearners( $id, Request $request )
    {
        foreach ($request->learners as $learner_id) {
            SelfPublishingLearner::create([
                'user_id' => $learner_id,
                'self_publishing_id' => $id
            ]);
        }

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Learners added from self-publishing successfully.'),
            'alert_type' => 'success',
            'not-former-courses' => true
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

    public function addFeedback( $id, Request $request )
    {
        $this->validate($request, [
            'manuscript' => 'required'
        ]);

        $filesWithPath = '';
        $word_count = 0;
        $destinationPath = '/storage/self-publishing-feedback/'; // upload path

        foreach ($request->file('manuscript') as $k => $file) {
            $extension = pathinfo($_FILES['manuscript']['name'][$k],PATHINFO_EXTENSION); // getting document extension
            $actual_name = pathinfo($_FILES['manuscript']['name'][$k],PATHINFO_FILENAME);
            $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension);// rename document

            $expFileName = explode('/', $fileName);
            $filePath = $destinationPath.end($expFileName);
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
        $feedback->self_publishing_id = $id;
        $feedback->feedback_user_id = \Auth::user()->id;
        $feedback->manuscript = $filesWithPath = trim($filesWithPath,", ");
        $feedback->notes = $request->notes;
        $feedback->save();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Self publishing feedback saved successfully.'),
            'alert_type' => 'success'
        ]);
    }

    /**
     * @param $learner_id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function deleteLearner( $learner_id )
    {
        $learner = SelfPublishingLearner::find($learner_id);
        $learner->delete();
        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Learner deleted from self-publishing successfully.'),
            'alert_type' => 'success',
            'not-former-courses' => true
        ]);
    }
}