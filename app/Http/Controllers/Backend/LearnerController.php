<?php
namespace App\Http\Controllers\Backend;

use App\EmailTemplate;
use App\Invoice;
use App\LearnerLogin;
use App\Workshop;
use App\WorkshopMenu;
use App\WorkshopsTaken;
use App\WorkshopTakenCount;
use Carbon\Carbon;
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
    // DemoAS: fiken-demo-glede-og-bil-as2
    public $fikenInvoices = "https://fiken.no/api/v1/companies/forfatterskolen-as/invoices/";
    public $username = "cleidoscope@gmail.com";
    public $password = "moonfang";
    public $headers = [
        'Accept: application/hal+json, application/vnd.error+json',
        'Content-Type: application/hal+json'
   ];

    /**
     * CourseController constructor.
     */
    public function __construct()
    {
        // middleware to check if admin have access to this page
        $this->middleware('checkPageAccess:4');
    }


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
            // Check if course has year extension
            if( $courseTaken->package->course->extend_courses > 0 ) :
                foreach( $learner->coursesTaken->where('id', '<>', $courseTaken->id) as $learnerCourseTaken ) :
                    $learnerCourseTaken->years = 1;
                    $learnerCourseTaken->save();
                endforeach;
            endif;
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


        // Check if course has year extension
        if( $courseTaken->package->course->extend_courses > 0 ) :
            foreach( $learner->coursesTaken->where('id', '<>', $courseTaken->id) as $learnerCourseTaken ) :
                $learnerCourseTaken->years = $courseTaken->package->course->extend_courses;
                $learnerCourseTaken->save();
            endforeach;
        endif;

        // Check for included courses
        if( $courseTaken->package->included_courses->count() > 0 ) :
            foreach( $package->included_courses as $included_course ) :
                $includedCourse = CoursesTaken::firstOrNew(['user_id' => $courseTaken->user->id, 'package_id' => $included_course->included_package_id]);
                $includedCourse->is_active = true;
                $includedCourse->save();
            endforeach;
        endif;

    	return redirect()->back();
    }




    public function activate_course_taken(Request $request)
    {
        $courseTaken = CoursesTaken::findOrFail($request->coursetaken_id);
        $isGroupCourse = 0;

        //added this line
        if ($courseTaken->package->course->type == 'Group') {
            $courseTaken->started_at = Carbon::now();
            $courseTaken->end_date = Carbon::today()->addYear(1);
            $isGroupCourse++;
        }

        $courseTaken->is_active = 1;
        $courseTaken->save();

        // Check if course has year extension
        if( $courseTaken->package->course->extend_courses > 0 ) :
            foreach( $courseTaken->user->coursesTaken->where('id', '<>', $courseTaken->id) as $learnerCourseTaken ) :
                $learnerCourseTaken->years = $courseTaken->package->course->extend_courses;
                $learnerCourseTaken->save();
            endforeach;
        endif;

        // Check for included courses
        if( $courseTaken->package->included_courses->count() > 0 ) :
            foreach( $courseTaken->package->included_courses as $included_course ) :
                $hasIncludedCourseAlready = CoursesTaken::where(['user_id' => $courseTaken->user->id, 'package_id' => $included_course->included_package_id])->first();
                $includedCourse = CoursesTaken::firstOrNew(['user_id' => $courseTaken->user->id, 'package_id' => $included_course->included_package_id]);

                //check if not started yet
                if ($hasIncludedCourseAlready && !$includedCourse->started_at) {
                    $includedCourse->started_at = Carbon::now(); // added this one
                }

                $includedCourse->end_date = Carbon::today()->addYear(1); // added this one
                $includedCourse->is_active = true;
                $includedCourse->save();
            endforeach;
        endif;

        // check if the course to activate is group course
        // then update all of the end date to the same date
        if ($isGroupCourse > 0) {
            $user_id = $courseTaken->user_id;
            CoursesTaken::where('user_id', $user_id)
                ->update(['end_date' => Carbon::today()->addYear(1)]);
        }

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
        $emailTemplate = EmailTemplate::where('page_name', '=', 'Manuscript')->first();
        return view('backend.learner.shopManuscriptTaken', compact('shopManuscriptTaken', 'learner', 'emailTemplate'));
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

    /**
     *  Get the statistics
     * @param $goal_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function goalStatistic($goal_id)
    {
        $statistics = [];
        $totalStatistic = 0;
        $goal = \App\WordWrittenGoal::find($goal_id);
        $from_ymd = date('Y-m-d', strtotime($goal->from_date));
        $to_ymd = date('Y-m-d', strtotime($goal->to_date));

        $statisticsData = \App\WordWritten::where('user_id',$goal->user_id)
            ->whereBetween('date',[$from_ymd, $to_ymd])
            ->select(\DB::raw('sum(words) as `words`'),  \DB::raw('YEAR(date) year, MONTH(date) month'))
            ->groupby('year', 'month')
            ->get();


        foreach ($statisticsData as $statistic) {
            $statistics[] = [
                'words' => (int) $statistic['words'],
                'year' => $statistic['year'],
                'month' => FrontendHelpers::convertMonthLanguage($statistic['month']),
            ];
            $totalStatistic += $statistic['words'];
        }
        $statistics[] = [
            'words' => $totalStatistic,
            'month' => 'Total Words'
        ];

        return response()->json($statistics);
    }

    /**
     * Delete learner invoice
     * @param $invoice_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteInvoice($invoice_id)
    {
        $invoice = Invoice::find($invoice_id);
        if ($invoice) {
            $invoice->forceDelete();
            return redirect()->back()->with([
                'alert_type' => 'success',
                'errors' => AdminHelpers::createMessageBag('Invoice deleted successfully.'),
                'not-former-courses' => true
            ]);
        }

        return redirect()->back();
    }

    /**
     * Remove learner from webinar-pakke
     * @param $course_taken_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteFromCourse($course_taken_id)
    {
        $courseTaken = CoursesTaken::find($course_taken_id);
        if ($courseTaken) {

            // remove from mailing list
            $user_email     = $courseTaken->user->email;
            $automation_id  = 82;
            $user_name      = $courseTaken->user->first_name;

            AdminHelpers::addToAutomation($user_email,$automation_id,$user_name);
            $courseTaken->forceDelete();
            return redirect()->back()->with([
                'alert_type' => 'success',
                'errors' => AdminHelpers::createMessageBag('Learner removed from Webinar-pakke successfully.'),
                'not-former-courses' => true
            ]);
        }
        return redirect()->back();
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
            $shopManuscriptTaken->manuscript_uploaded_date = Carbon::now()->toDateTimeString();
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

        // check if the course to update is Webinar pakke then update all the courses end date
        if ($courseTaken->package->course_id == 17) {
            $userCourses = CoursesTaken::where('user_id', $courseTaken->user_id);
            $userCourses->update(['end_date' => $request->end_date]);
            return redirect()->back();
        }

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

    public function addToWorkshop(Request $request)
    {
        $workshop = Workshop::find($request->workshop_id);
        $menu = WorkshopMenu::where('workshop_id', $request->workshop_id)->first();

        $workshopTaken = new WorkshopsTaken();
        $workshopTaken->user_id = $request->user_id;
        $workshopTaken->workshop_id = $workshop->id;
        $workshopTaken->menu_id = $menu->id;
        $workshopTaken->notes = NULL;
        $workshopTaken->is_active = FALSE;
        $workshopTaken->save();

        return redirect()->back();
    }

    /**
     * Update the workshop count of the leaner
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateWorkshopCount($id, Request $request)
    {
        $workshopTakenCount = WorkshopTakenCount::firstOrNew([
            'user_id' => $id
        ]);
        $workshopTakenCount->workshop_count = $request->workshop_count;
        $workshopTakenCount->save();
        return redirect()->route('admin.learner.show', $id);
    }

    /**
     * Download the synopsis attached to the manuscript
     * @param $id ShopManuscriptsTaken id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadManuscriptSynopsis($id)
    {
        $shopManuscriptTaken = ShopManuscriptsTaken::find($id);
        if ($shopManuscriptTaken) {
            $filename = $shopManuscriptTaken->synopsis;
            return response()->download(public_path($filename));
        }

        return redirect('shop-manuscript');
    }

    /**
     * Update the synopsis field
     * @param $id ShopManuscriptsTaken id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function saveSynopsis($id, Request $request)
    {
        $shopManuscriptTaken = ShopManuscriptsTaken::find($id);
        if ($shopManuscriptTaken) {
            if ($request->hasFile('synopsis') && $request->file('synopsis')->isValid()) :
                $extension = pathinfo($_FILES['synopsis']['name'],PATHINFO_EXTENSION);
                $extensions = ['pdf', 'docx', 'odt'];

                if( !in_array($extension, $extensions) ) :
                    return redirect()->back();
                endif;

                $time = time();
                $destinationPath = 'storage/shop-manuscripts-synopsis/';
                $fileName = $time.'.'.$extension; // rename document
                $request->synopsis->move($destinationPath, $fileName);
                $shopManuscriptTaken->synopsis = '/'.$destinationPath.$fileName;
                $shopManuscriptTaken->save();
            endif;
            return redirect()->back();
        }
        return redirect('shop-manuscript');
    }

    public function sendEmail($id, Request $request)
    {
        $learner    = User::findOrFail($id);
        $to         = $learner->email;
        $from       = 'elin@forfatterskolen.no';//$request->from_email;
        $message    = nl2br($request->message);
        $subject    = $request->subject;
        //AdminHelpers::send_mail( $to, $subject, $message, $from);
        AdminHelpers::send_email($subject,
            $from, $to, $message);

        return redirect()->back();
    }

    public function addNotes($id, Request $request)
    {
        $user = User::find($id);
        if ($user) {
            $user->notes = $request->notes;
            $user->save();
        }

        return redirect()->back();
    }

    /**
     * Update manuscript locked status
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateManuscriptLockedStatus(Request $request)
    {
        $shopManuscriptsTaken = ShopManuscriptsTaken::find($request->shop_manuscript_taken_id);
        $success = false;

        if ($shopManuscriptsTaken) {
            $shopManuscriptsTaken->is_manuscript_locked = $request->is_manuscript_locked;
            $shopManuscriptsTaken->save();
            $success = TRUE;
        }

        return response()->json([
            'data' => [
                'success' => $success,
            ]
        ]);
    }

    public function loginActivity($login_id)
    {
        $login = LearnerLogin::find($login_id);
        if ($login) {
            return view('backend.learner.login_activity', compact('login'));
        }

        return redirect()->back();
    }
    
}
