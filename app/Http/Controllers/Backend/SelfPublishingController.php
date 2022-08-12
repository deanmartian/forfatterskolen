<?php

namespace App\Http\Controllers\Backend;

use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Http\FrontendHelpers;
use App\SelfPublishing;
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
        return view('backend.self-publishing.index', compact('publishingList'));
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
            'price'         => 'numeric',
            'editor_share'  => 'numeric'
        ]);


        $publishing = $id ? SelfPublishing::find($id) : new SelfPublishing();
        $publishing->title = $request->title;
        $publishing->description = $request->description;

        if ( $request->hasFile('manuscript') &&
            $request->file('manuscript')->isValid() ) :

            $destinationPath = 'storage/self-publishing-manuscript/'; // upload path
            $extension = pathinfo($_FILES['manuscript']['name'],PATHINFO_EXTENSION); // getting document extension
            $actual_name = pathinfo($request->manuscript->getClientOriginalName(),PATHINFO_FILENAME);
            $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension);// rename document

            $expFileName = explode('/', $fileName);
            $filePath = $destinationPath.end($expFileName);
            $request->manuscript->move($destinationPath, end($expFileName));

            // count words
            if($extension == "pdf") :
                $pdf  =  new \PdfToText( $destinationPath.end($expFileName) ) ;
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

            $publishing->manuscript = $filePath;
            $publishing->word_count = $word_count;
        endif;

        $publishing->price = $request->price;
        $publishing->editor_share = $request->editor_share;
        $publishing->save();
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