<?php
namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\User;
use App\Address;
use App\Package;
use App\Course;
use App\CoursesTaken;
use App\ShopManuscriptsTaken;
use App\ShopManuscriptComment;
use App\Http\Controllers\Controller;
use Validator;
use App\ShopManuscript;
use App\Lesson;
use App\Http\AdminHelpers;
use File;
use App\Http\FrontendHelpers;

include_once($_SERVER['DOCUMENT_ROOT'].'/Docx2Text.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/Pdf2Text.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/Odt2Text.php');

class LearnerController extends Controller
{
    // Demo: fiken-demo-nordisk-og-tidlig-rytme-enk 
    // Forfatterskolen: forfatterskolen-as
    public $fikenInvoices = "https://fiken.no/api/v1/companies/forfatterskolen-as/invoices/";
    public $username = "cleidoscope@gmail.com";
    public $password = "moonfang";
    public $headers = [
        'Accept: application/hal+json, application/vnd.error+json',
        'Content-Type: application/hal+json'
   ];


    public function index(Request $request)
    {
        if( $request->search && !empty($request->search) ) :
            $learners = User::where('first_name', 'LIKE', '%' . $request->search  . '%')->orWhere('email', 'LIKE', '%' . $request->search  . '%')->orderBy('created_at', 'desc')->paginate(25);
        else :
            $learners = User::orderBy('created_at', 'desc')->paginate(25);
        endif;
    	return view('backend.learner.index', compact('learners'));
    }




    public function show($id)
    {
        $learner = User::findOrFail($id);
        $fikenInvoices = [];
        if( count($learner->invoices) > 0 ) :
            $ch = curl_init($this->fikenInvoices); 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_USERPWD, "$this->username:$this->password");
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);;
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
            $data = curl_exec($ch);
            $data = json_decode($data);
            $fikenInvoices = $data->_embedded->{'https://fiken.no/api/v1/rel/invoices'};
        endif;
        return view('backend.learner.show', compact('learner', 'fikenInvoices'));
    }





    public function update($id, Request $request)
    {
        $learner = User::findOrFail($id);


        switch( $request->field ) :
            case 'password' :
                $validator = Validator::make($request->all(), [
                    'password' => 'required|confirmed'
                ]);
                if($validator->fails()) :
                    return redirect()->back()->withErrors($validator);
                endif;

                $learner->password = bcrypt($request->password);
                $learner->save();
                return redirect()->back()->with(['profile_success' => 'Password updated successfully.']);
                break;

            case 'contact' :
                $learner->first_name = $request->first_name;
                $learner->last_name = $request->last_name;
                $learner->save();
                
                $address = Address::firstOrNew([
                    'user_id' => $learner->id
                ]);
                $address->phone = $request->phone;
                $address->street = $request->street;
                $address->zip = $request->zip;
                $address->city = $request->city;
                $address->save();
                return redirect()->back()->with(['profile_success' => 'Contact Info updated successfully.']);
                break;
        endswitch;


        
    }


    public function removeLearner(Request $request)
    {
    	$learner = User::findOrFail($request->learner_id);
    	$package = Package::findOrFail($request->package_id);
    	$course = Course::findOrFail($package->course_id);

    	$packageIds = $course->packages->pluck('id')->toArray();
    	$courseTaken = CoursesTaken::where('user_id', $learner->id)->whereIn('package_id', $packageIds)->first();
    	
    	if( $courseTaken ) :
    		$courseTaken->forceDelete();
    	endif;
    	return redirect()->back();
    }




    public function addLearner(Request $request)
    {
    	$learner = User::findOrFail($request->learner_id);
    	$package = Package::findOrFail($request->package_id);
    	$course = Course::findOrFail($package->course_id);

    	$packageIds = $course->packages->pluck('id')->toArray();
    	$courseTaken = CoursesTaken::where('user_id', $learner->id)->whereIn('package_id', $packageIds)->first();

    	if( !$courseTaken ) :
    		$courseTaken = new CoursesTaken;
    		$courseTaken->user_id = $learner->id;
    		$courseTaken->package_id = $package->id;
    	endif;

        $courseTaken->started_at = NULL;
		$courseTaken->is_active = 1;
		$courseTaken->save();

    	return redirect()->back();
    }




    public function activate_course_taken(Request $request)
    {
        $courseTaken = CoursesTaken::findOrFail($request->coursetaken_id);
        $courseTaken->is_active = 1;
        $courseTaken->save();
        return redirect()->back();
    }



    public function delete_course_taken(Request $request)
    {
        $courseTaken = CoursesTaken::findOrFail($request->coursetaken_id);
        $courseTaken->forceDelete();
        return redirect()->back();
    }


    public function activate_shop_manuscript_taken(Request $request)
    {
        $courseTaken = ShopManuscriptsTaken::findOrFail($request->shop_manuscript_id);
        $courseTaken->is_active = 1;
        $courseTaken->save();
        return redirect()->back();
    }



    public function delete_shop_manuscript_taken(Request $request)
    {
        $courseTaken = ShopManuscriptsTaken::findOrFail($request->shop_manuscript_id);
        $courseTaken->forceDelete();
        return redirect()->back();
    }



    public function shopManuscriptTakenShow($id, $shopManuscriptTakenID)
    {
        $learner = User::findOrFail($id);
        $shopManuscriptTaken = ShopManuscriptsTaken::where('id', $shopManuscriptTakenID)->where('user_id', $learner->id)->firstOrFail();
        return view('backend.learner.shopManuscriptTaken', compact('shopManuscriptTaken', 'learner'));
    }


    public function shopManuscriptTakenShowComment($id, $shopManuscriptTakenID, Request $request)
    {
        $learner = User::findOrFail($id);
        $shopManuscriptTaken = ShopManuscriptsTaken::where('id', $shopManuscriptTakenID)->where('user_id', $learner->id)->firstOrFail();
        if( !empty($request->comment) && $shopManuscriptTaken->is_active ) :
            $ShopManuscriptComment = new ShopManuscriptComment();
            $ShopManuscriptComment->shop_manuscript_taken_id = $shopManuscriptTaken->id;
            $ShopManuscriptComment->user_id = Auth::user()->id;
            $ShopManuscriptComment->comment = $request->comment;
            $ShopManuscriptComment->save();
            return redirect()->back();
        else :
            return abort('503');
        endif;
    }



    public function updateDocumentShopManuscriptTaken($id, Request $request)
    {
        $shopManuscriptTaken = ShopManuscriptsTaken::findOrFail($id);

        if ($request->hasFile('manuscript') && $request->file('manuscript')->isValid()) :

            $extensions = ['pdf', 'docx', 'odt'];
            $extension = pathinfo($_FILES['manuscript']['name'],PATHINFO_EXTENSION);
            $original_filename = $request->manuscript->getClientOriginalName();

            if( !in_array($extension, $extensions) ) :
                return redirect()->back();
            endif;

            $time = time();
            $destinationPath = 'storage/shop-manuscripts/';
            $fileName = $time.'.'.$extension; // rename document
            $request->manuscript->move($destinationPath, $fileName);
            if($extension == "pdf") :
              $pdf  =  new \PdfToText ( $destinationPath.$fileName ) ;
              $pdf_content = $pdf->Text; 
              $word_count = FrontendHelpers::get_num_of_words($pdf_content);
            elseif($extension == "docx") :
              $docObj = new \Docx2Text($destinationPath.$fileName);
              $docText= $docObj->convertToText();
              $word_count = FrontendHelpers::get_num_of_words($docText);
            elseif($extension == "odt") :
              $doc = odt2text($destinationPath.$fileName);
              $word_count = FrontendHelpers::get_num_of_words($doc);
            endif;
            $word_count = (int) $word_count;
            $shopManuscriptTaken->file = '/'.$destinationPath.$fileName;
            $shopManuscriptTaken->words = $word_count;
        endif;

        $shopManuscriptTaken->save();
        return redirect()->back();
    }




    public function addShopManuscript($id, Request $request)
    {
        $learner = User::findOrFail($id);
        $shopManuscript = ShopManuscript::findOrFail($request->shop_manuscript_id);
        
        $shopManuscriptTaken = new ShopManuscriptsTaken();
        if( $request->hasFile('manuscript') &&  $request->file('manuscript')->isValid() ) :
            $time = time();
            $destinationPath = 'storage/shop-manuscripts/'; // upload path
            $extension = pathinfo($_FILES['manuscript']['name'],PATHINFO_EXTENSION); // getting document extension
            $fileName = $time.'.'.$extension; // rename document
            $request->manuscript->move($destinationPath, $fileName);
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
            $shopManuscriptTaken->file = '/'.$destinationPath.$fileName;
            $shopManuscriptTaken->words = $word_count;
        endif;
        $shopManuscriptTaken->user_id = $learner->id;
        $shopManuscriptTaken->shop_manuscript_id = $shopManuscript->id;
        $shopManuscriptTaken->is_active = TRUE;
        $shopManuscriptTaken->save();
            
        return redirect()->back();
    }

    

    public function destroy($id, Request $request)
    {
        $learner = User::findOrFail($id);
        if( $request->moveStatus && count($request->moveItems) > 0 && $request->move_learner_id ) :
            $moveLearner = User::findOrFail($request->move_learner_id);

            if( in_array('courses_taken', $request->moveItems) ) :
                $learner->coursesTaken()->update([
                    'user_id' => $moveLearner->id
                ]);
            endif;
        
            if( in_array('shop_manuscripts', $request->moveItems) ) :
                $learner->shopManuscriptsTaken()->update([
                    'user_id' => $moveLearner->id
                ]);
            endif;


            if( in_array('invoices', $request->moveItems) ) :
                $learner->invoices()->update([
                    'user_id' => $moveLearner->id
                ]);
            endif;
        endif;
        $learner->forceDelete();
        return redirect(route('admin.learner.index'));
    }



    public function setCourseTakenAvailability($id, Request $request)
    {
        $courseTaken = CoursesTaken::findOrFail($id);
        $courseTaken->start_date = $request->start_date;
        $courseTaken->end_date = $request->end_date;
        $courseTaken->save();
        return redirect()->back();
    }


    public function allow_lesson_access( $course_taken_id, $lesson_id )
    {
        $courseTaken = CoursesTaken::findOrFail($course_taken_id);
        $lesson = Lesson::findOrFail($lesson_id);
        if( $courseTaken->package->course->id ==  $lesson->course->id ) :
            $lesson_access = $courseTaken->access_lessons;
            if( !in_array($lesson->id, $lesson_access) ) :
                $lesson_access[] = $lesson->id;
            endif;
            $courseTaken->access_lessons = json_encode($lesson_access);
            $courseTaken->save();
        endif;
        return redirect()->back();
    }



    public function default_lesson_access( $course_taken_id, $lesson_id )
    {
        $courseTaken = CoursesTaken::findOrFail($course_taken_id);
        $lesson = Lesson::findOrFail($lesson_id);
        if( $courseTaken->package->course->id ==  $lesson->course->id ) :
            $lesson_access = $courseTaken->access_lessons;
            $new_lesson_access = array_diff($lesson_access, [$lesson->id]);
            $courseTaken->access_lessons = json_encode($new_lesson_access);
            $courseTaken->save();
        endif;
        return redirect()->back();
    }
    
}
