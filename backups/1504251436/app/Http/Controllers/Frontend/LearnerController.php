<?php
namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Course;
use App\Manuscript;
use App\CoursesTaken;
use App\WorkshopsTaken;
use App\ShopManuscriptsTaken;
use App\ShopManuscriptComment;
use App\Lesson;
use App\Invoice;
use App\Address;
use App\Assignment;
use Hash;
use File;
use App\Http\FrontendHelpers;


include_once($_SERVER['DOCUMENT_ROOT'].'/Docx2Text.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/Pdf2Text.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/Odt2Text.php');

class LearnerController extends Controller
{
    // Demo: fiken-demo-nordisk-og-tidlig-rytme-enk 
    // Forfatterskolen: forfatterskolen-as
    public $fikenInvoices = "https://fiken.no/api/v1/companies/forfatterskolen-as/invoices";
    public $username = "cleidoscope@gmail.com";
    public $password = "moonfang";
    public $headers = [
        'Accept: application/hal+json, application/vnd.error+json',
        'Content-Type: application/hal+json'
   ];

   
    public function dashboard()
    {
        return view('frontend.learner.dashboard');
    }




    public function course()
    {
        return view('frontend.learner.course');
    }




    public function shopManuscript()
    {
        return view('frontend.learner.shop-manuscript');
    }



    public function shopManuscriptShow($id)
    {
        $shopManuscriptTaken = ShopManuscriptsTaken::where('user_id', Auth::user()->id)->where('id', $id)->where('is_active', true)->first();
        if( $shopManuscriptTaken ) :
            return view('frontend.learner.shopManuscriptShow', compact('shopManuscriptTaken'));
        endif;
        return abort('503');
    }



    public function workshop()
    {
        return view('frontend.learner.workshop');
    }



    public function webinar()
    {
        return view('frontend.learner.webinar');
    }




    public function courseShow($id)
    {
        $courseTaken = CoursesTaken::findOrFail($id);

        if( Auth::user()->can('participateCourse', $courseTaken) &&
            FrontendHelpers::isCourseTakenAvailable($courseTaken) &&
            FrontendHelpers::isCourseAvailable($courseTaken->package->course) &&
            !$courseTaken->hasEnded
            ) :
            return view('frontend.learner.course_show', compact('courseTaken'));
        endif;
        return abort('503');
    }



    public function calendar()
    {
        $events = [];

        foreach( Auth::user()->coursesTaken as $courseTaken ) :
            // Course lessons
            $token = str_random(10);
            foreach( $courseTaken->package->course->lessons as $lesson ) :
                $availability = strtotime(FrontendHelpers::lessonAvailability($courseTaken->started_at, $lesson->delay, $lesson->period)) * 1000;
                $events[] = [
                    'id' => $lesson->course->id,
                    'title' => 'Lesson: ' . $lesson->title . ' from ' . $lesson->course->title,
                    'class' => 'event-important',
                    'start' => $availability,
                    'end' => $availability,
                ];
            endforeach;

            // Course webinars
            $token = str_random(10);
            foreach( $courseTaken->package->course->webinars as $webinar ) :
                $events[] = [
                    'id' => $webinar->course->id,
                    'title' => 'Webinar: ' . $webinar->title . ' from ' . $webinar->course->title,
                    'class' => 'event-warning',
                    'start' => strtotime($webinar->start_date) * 1000,
                    'end' => strtotime($webinar->start_date) * 1000,
                ];
            endforeach;
        endforeach;

    	$event_1 = [
    		'title' => 'Event 1',
    		'class' => 'event-important',
    		'start' => '1494259200000',
    		'end' => '1494259300000',1503292298
    	];

    	return view('frontend.learner.calendar', compact('events'));
    }



    public function assignment()
    {
        return view('frontend.learner.assignment');
    }


    public function assignmentShow($id)
    {
        $assignment = Assignment::findOrFail($id);
        $coursesTaken = Auth::user()->coursesTaken;

        $courseIDs = [];
        foreach( $coursesTaken as $taken ) :
            $courseIDs[] = $taken->package->course->id;
        endforeach;

        if( in_array($assignment->course_id, $courseIDs) ) :
            return view('frontend.learner.assignmentShow', compact('assignment'));
        endif;
        return abort('503');
    }


    public function manuscript()
    {
        return view('frontend.learner.manuscript');
    }


    public function invoice()
    {   
        $invoices = Invoice::orderBy('created_at', 'desc')->paginate(15);
        $ch = curl_init($this->fikenInvoices); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, "$this->username:$this->password");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        $data = curl_exec($ch);
        $data = json_decode($data);
        $fikenInvoices = $data->_embedded->{'https://fiken.no/api/v1/rel/invoices'};
        return view('frontend.learner.invoice', compact('fikenInvoices'));
    }




    public function invoiceShow($id)
    {
        $invoice = Invoice::findOrFail($id);
        if(Auth::user()->can('viewInvoice', $invoice)) :
            $ch = curl_init($this->fikenInvoices); 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_USERPWD, "$this->username:$this->password");
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);;
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
            $data = curl_exec($ch);
            $data = json_decode($data);
            $fikenInvoices = $data->_embedded->{'https://fiken.no/api/v1/rel/invoices'};
            return view('frontend.learner.invoiceShow', compact('invoice', 'fikenInvoices'));
        endif;
            return abort('503');
    }
    





    public function takeCourse(Request $request)
    {
        $courseTaken = CoursesTaken::findOrFail($request->courseTakenId);
        if( Auth::user()->can('participateCourse', $courseTaken) && 
            FrontendHelpers::isCourseTakenAvailable($courseTaken) &&
            FrontendHelpers::isCourseAvailable($courseTaken->package->course) ) :
            $courseTaken->started_at = date('Y-m-d h:i:s');
            $courseTaken->save();
            return redirect(route('learner.course.show', ['id' => $courseTaken->id]));
        endif;
        return redirect()->back();
    }





    public function profile()
    {
        return view('frontend.learner.profile');
    }





    public function profileUpdate(ProfileUpdateRequest $request)
    {
        if(! empty($request->new_password) ) :
            if (Hash::check($request->old_password, Auth::user()->password)) :
                Auth::user()->password = bcrypt($request->new_password);
            else :
                return redirect()->back()->withErrors('Invalid old password.');
            endif;
        endif;

        if ($request->hasFile('image') && $request->file('image')->isValid()) :
            $image = substr(Auth::user()->profile_image, 1);
            if( Auth::user()->hasProfileImage && File::exists($image) ) :
                File::delete($image);
            endif;
            $destinationPath = 'storage/profile-images/'; // upload path
            $extension = $request->image->extension(); // getting image extension
            $fileName = time().'.'.$extension; // renameing image
            $request->image->move($destinationPath, $fileName);
            // optimize image
            if ( strtolower( $extension ) == "png" ) : 
                $image = imagecreatefrompng($destinationPath.$fileName);
                imagepng($image, $destinationPath.$fileName, 9);
            else :
                $image = imagecreatefromjpeg($destinationPath.$fileName);
                imagejpeg($image, $destinationPath.$fileName, 70);
            endif;
            Auth::user()->profile_image = '/'.$destinationPath.$fileName;
        endif;

        Auth::user()->first_name = $request->first_name;
        Auth::user()->last_name = $request->last_name;
        Auth::user()->save();

        // User Address
        $address = Address::firstOrNew(['user_id' => Auth::user()->id]);
        $address->street = $request->street;
        $address->city = $request->city;
        $address->zip = $request->zip;
        $address->phone = $request->phone;
        $address->save();

        return redirect()->back()->with('profile_success', 'Profile successfully updated.');
    }


    public function lesson($course_id, $id)
    {
        $course = Course::findOrFail($course_id);
        $lesson = Lesson::findOrFail($id);
        $courseTaken = CoursesTaken::where('user_id', Auth::user()->id)->whereIn('package_id', $course->packages->pluck('id')->toArray())->first();
        if( ($courseTaken &&
            \FrontendHelpers::isLessonAvailable($courseTaken->started_at, $lesson->delay, $lesson->period) &&
            FrontendHelpers::isCourseTakenAvailable($courseTaken) &&
            FrontendHelpers::isCourseAvailable($courseTaken->package->course) &&
            !$courseTaken->hasEnded )
            || ( $courseTaken && FrontendHelpers::hasLessonAccess($courseTaken, $lesson) )
            ) :
            return view('frontend.learner.lesson_show', compact('lesson', 'course', 'courseTaken'));
        endif;
        return abort('503');
    }


    public function uploadManuscript($id, Request $request)
    {
        $courseTaken = CoursesTaken::findOrFail($id);
        $coursesTaken_ids = Auth::user()->coursesTaken->pluck('id')->toArray();
        $extensions = ['pdf', 'docx', 'odt'];
        
        if( $courseTaken->manuscripts->count() < $courseTaken->package->manuscripts_count && in_array($courseTaken->id, $coursesTaken_ids) ) :
            if( $request->hasFile('file') &&  $request->file('file')->isValid() ) :
                if( Auth::user()->can('participateCourse', $courseTaken) &&
                    !$courseTaken->hasEnded
                ) :
                    $time = time();
                    $destinationPath = 'storage/manuscripts/'; // upload path
                    $extension = pathinfo($_FILES['file']['name'],PATHINFO_EXTENSION); // getting document extension
                    $fileName = $time.'.'.$extension; // rename document
                    $request->file->move($destinationPath, $fileName);

                    if( !in_array($extension, $extensions) ) :
                        return redirect()->back();
                    endif;

                    // count words
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


                    Manuscript::create([
                        'coursetaken_id' => $courseTaken->id,
                        'filename' => '/'.$destinationPath.$fileName,
                        'word_count' => $word_count
                    ]);
                else :
                    return abort('503');
                endif;
            endif;
        endif;

        return redirect()->back();
    }


    public function manuscriptShow($id)
    {
        $manuscript = Manuscript::findOrFail($id);
        if( Auth::user()->id == $manuscript->courseTaken->user_id ) :
            return view('frontend.learner.manuscriptShow', compact('manuscript'));
        else :
            return abort('503');
        endif;
    }



    public function shopManuscriptPostComment( $id, Request $request )
    {
        $shopManuscriptsTaken = ShopManuscriptsTaken::where('id', $id)->where('user_id', Auth::user()->id)->firstOrFail();
        if( !empty($request->comment) && $shopManuscriptsTaken->is_active ) :
            $ShopManuscriptComment = new ShopManuscriptComment();
            $ShopManuscriptComment->shop_manuscript_taken_id = $shopManuscriptsTaken->id;
            $ShopManuscriptComment->user_id = Auth::user()->id;
            $ShopManuscriptComment->comment = $request->comment;
            $ShopManuscriptComment->save();
            return redirect()->back();
        else :
            return abort('503');
        endif;
    }



}
