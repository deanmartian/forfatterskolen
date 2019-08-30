<?php
namespace App\Http\Controllers\Backend;

use App\AssignmentGroupLearner;
use App\CoachingTimerManuscript;
use App\CopyEditingManuscript;
use App\CorrectionManuscript;
use App\Diploma;
use App\EmailTemplate;
use App\Helpers\FileToText;
use App\Http\FikenInvoice;
use App\Invoice;
use App\LearnerLogin;
use App\Mail\SubjectBodyEmail;
use App\PaymentMode;
use App\PaymentPlan;
use App\UserEmail;
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


    public function index(Request $request, User $user)
    {
        $learners = $user->newQuery();
        if( $request->sfname || $request->slname || $request->semail) :
            if ($request->sfname) {
                $learners->where('first_name', 'LIKE', '%' . $request->sfname  . '%');
            }

            if ($request->slname) {
                $learners->where('last_name', 'LIKE', '%' . $request->slname  . '%');
            }

            if ($request->semail) {
                $learners->where('email', 'LIKE', '%' . $request->semail  . '%');
            }

            $learners->orderBy('first_name', 'asc')
                ->orderBy('email', 'asc');
            /*$learners->where(function($query) use ($request) {
                $query->where('first_name', 'LIKE', '%' . $request->search  . '%')
                    ->orWhere('email', 'LIKE', '%' . $request->search  . '%');
            })
            ->orderBy('first_name', 'asc')
            ->orderBy('email', 'asc');*/
        endif;

        if ($request->has('free-course')) {
            $learners->has('freeCourses');
        }

        if ($request->has('workshop')) {
            $learners->has('workshopsTaken');
        }

        if ($request->has('shop-manuscript')) {
            $learners->has('shopManuscriptsTaken');
        }

        if ($request->has('course')) {
            if ($request->has('free-course')) {
                $learners->has('coursesTaken');
            } else {
                $learners->has('coursesTakenNoFree');
            }
        }

        $learners->orderBy('created_at', 'desc');
        $learners = $learners->paginate(25);

    	return view('backend.learner.index', compact('learners'));
    }




    public function show($id)
    {
        $learner = User::findOrFail($id);
        return view('backend.learner.show', compact('learner'));
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

		if ($course->is_free) {
            $courseTaken->is_free = 1;
        }

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
            $courseTaken->end_date = $courseTaken->package->validity_period > 0
                ? Carbon::today()->addMonth($courseTaken->package->validity_period) : Carbon::today()->addYear(1);
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

                $includedCourse->end_date = $courseTaken->package->validity_period > 0
                    ? Carbon::today()->addMonth($courseTaken->package->validity_period) : Carbon::today()->addYear(1);
                $includedCourse->is_active = true;
                $includedCourse->save();
            endforeach;
        endif;

        // check if the course to activate is group course
        // then update all of the end date to the same date
        if ($isGroupCourse > 0) {
            $user_id = $courseTaken->user_id;
            $end_date = $courseTaken->package->validity_period > 0
                ? Carbon::today()->addMonth($courseTaken->package->validity_period) : Carbon::today()->addYear(1);
            CoursesTaken::where('user_id', $user_id)
                ->update(['end_date' => $end_date]);
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

            // check if webinar-pakke and add to automation
            if ($courseTaken->package->course->id == 17) {
                AdminHelpers::addToAutomation($user_email,$automation_id,$user_name);
            }
            $courseTaken->forceDelete();
            return redirect()->back()->with([
                'alert_type' => 'success',
                'errors' => AdminHelpers::createMessageBag('Learner removed from '
            .$courseTaken->package->course->title.' successfully.'),
                'not-former-courses' => true
            ]);
        }
        return redirect()->back();
    }

    /**
     * Renew a learners course
     * @param $learner_id
     * @param $course_taken_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function renewCourse($learner_id, $course_taken_id)
    {
        $courseTaken = CoursesTaken::where(['user_id' => $learner_id, 'id' => $course_taken_id])->first();
        if ($courseTaken) {
            $user           = User::find($learner_id);
            $package        = Package::findOrFail($courseTaken->package_id);
            $payment_mode   = 'Bankoverføring';
            $price          = (int)1490*100;
            $product_ID     = 280763803;//$package->full_price_product;
            $send_to        = $user->email;
            $dueDate        = Carbon::today()->addDay(14)->format('Y-m-d');

            $comment = '(Kurs: ' . $package->course->title . ' ['.$package->variation.'], ';
            $comment .= 'Betalingsmodus: ' . $payment_mode . ')';

            $invoice_fields = [
                'user_id'       => $user->id,
                'first_name'    => $user->first_name,
                'last_name'     => $user->last_name,
                'netAmount'     => $price,
                'dueDate'       => $dueDate,
                'description'   => 'Kursordrefaktura',
                'productID'     => $product_ID,
                'email'         => $send_to,
                'telephone'     => $user->address->phone,
                'address'       => $user->address->street,
                'postalPlace'   => $user->address->city,
                'postalCode'    => $user->address->zip,
                'comment'       => $comment,
                'payment_mode'  => "Faktura",
            ];
            $invoice = new FikenInvoice();
            $invoice->create_invoice($invoice_fields);

            // check if course taken have set end date and add one year to it
            if ($courseTaken->end_date) {
                $addYear = date("Y-m-d", strtotime(date("Y-m-d", strtotime($courseTaken->end_date)) . " + 1 year"));
                $courseTaken->end_date = $addYear;
            }

            $courseTaken->started_at = Carbon::now();
            $courseTaken->save();

            // add to automation
            $user_email     = $user->email;
            $automation_id  = 73;
            $user_name      = $user->first_name;

            AdminHelpers::addToAutomation($user_email,$automation_id,$user_name);
            return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Webinar-pakke renewed'),
            'alert_type' => 'success']);

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

            if( in_array('assignments', $request->moveItems) ) :
                AssignmentGroupLearner::where('user_id', $id)->update([
                    'user_id' => $moveLearner->id
                ]);
                $learner->assignmentManuscripts()->update([
                    'user_id' => $moveLearner->id
                ]);
            endif;

            if( in_array('diplomas', $request->moveItems) ) :
                $learner->diplomas()->update([
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

        if (!$menu) {
            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Please add a menu on the workshop before assigning it to learner.'),
                'alert_type' => 'danger',
                'not-former-courses' => true
            ]);
        }

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

    /**
     * Send Email to learner
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendLearnerEmail($id, Request $request)
    {
        $learner = User::find($id);
        if (!$learner) {
            return redirect()->back();
        }

        $this->validate($request,
            [
                'subject' => 'required',
                'message' => 'required'
            ]
        );

        $data = $request->except('_token');
        $data['email'] = $data['message'];
        $learner->emails()->create($data);

        $from_email = $request->from_email ?: 'post@forfatterskolen.no';
        $from_name  = $request->from_name ?: 'Forfatterskolen';

        $email = $learner->email;
        $encode_email = encrypt($email);
        $loginLink = "<a href='".route('auth.login.email', $encode_email)."'>Klikk her for å logge inn</a>";
        $password = $learner->need_pass_update ? 'Z5C5E5M2jv' : 'Skjult (kan endres inne i portalen eller via glemt passord)';

        if (strpos($request->message, "[redirect]")) {
            $extractLink        = FrontendHelpers::getTextBetween($request->message, "[redirect]", "[/redirect]");
            $formatRedirectLink = route('auth.login.emailRedirect',[$encode_email, encrypt($extractLink)]);
            $redirectLabel      =  FrontendHelpers::getTextBetween($request->message, "[redirect_label]", "[/redirect_label]");
            $redirectLink       = "<a href='".$formatRedirectLink."'>".$redirectLabel."</a>";
            $search_string = [
                '[redirect]'.$extractLink.'[/redirect]', '[redirect_label]'.$redirectLabel.'[/redirect_label]'
            ];
            $replace_string = [
                $redirectLink, ''
            ];
            $message = str_replace($search_string, $replace_string, $request->message);
        } else {
            $search_string = [
                '[login_link]', '[username]', '[password]'
            ];
            $replace_string = [
                $loginLink, $email, $password
            ];
            $message = str_replace($search_string, $replace_string, $request->message);
        }

        $emailData['email_subject'] = $request->subject;
        $emailData['email_message'] = $message;
        $emailData['from_name'] = $from_name;
        $emailData['from_email'] = $from_email;
        $emailData['attach_file'] = NULL;

        \Mail::to($email)->queue(new SubjectBodyEmail($emailData));

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Email sent.'),
            'alert_type' => 'success', 'not-former-courses' => true]);
    }

    public function sendEmail($id, Request $request)
    {
        $learner    = User::findOrFail($id);
        $to         = $learner->email;
        $from       = 'elin@forfatterskolen.no';//$request->from_email;
        $message    = $request->message;
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
     * List learners that have notes
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function listNotes()
    {
        $userNotes = User::whereNotNull('notes')->where('notes', '<>', '')
            ->orderBy('id', 'DESC')
            ->paginate(25);

        return view('backend.learner.list_notes', compact('userNotes'));
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

    /**
     * Add to correction or copy editing
     * @param $user_id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addOtherService($user_id, Request $request)
    {
        if ($user = User::find($user_id)) {
            $data = $request->except('_token');

            $extensions = ['docx'];
            if ($request->hasFile('manuscript') && $request->file('manuscript')->isValid()) :
                $extension = pathinfo($_FILES['manuscript']['name'], PATHINFO_EXTENSION);
                $original_filename = $request->manuscript->getClientOriginalName();

                if( !in_array($extension, $extensions) ) :
                    return redirect()->back()->with([
                        'alert_type' => 'danger',
                        'errors' => AdminHelpers::createMessageBag('File type not allowed.'),
                    ]);
                endif;

                $destinationPath = 'storage/correction-manuscripts/'; // upload path

                if ($data['is_copy_editing'] == 1) {
                    $destinationPath = 'storage/copy-editing-manuscripts/'; // upload path
                }

                $time = time();
                $fileName = $time.'.'.$extension;//$original_filename; // rename document
                $request->manuscript->move($destinationPath, $fileName);

                $file = $destinationPath.$fileName;

                $docObj = new FileToText($file);
                // count characters with space
                $word_count = strlen($docObj->convertToText()) - 2;

                $word_per_price = 1000;
                $price_per_word = 25;
                $title = 'Korrektur';

                if ($data['is_copy_editing'] == 1) {
                    $word_per_price = 1000;
                    $price_per_word = 30;
                    $title = 'Språkvask';
                }

                $rounded_word       = FrontendHelpers::roundUpToNearestMultiple($word_count);
                $calculated_price   = ($rounded_word/$word_per_price) * $price_per_word;
                $productID         = $data['is_copy_editing'] == 1 ? 599886093 : 599110997;
                $data['price']      = $calculated_price;

                // check if the admin wants to send out invoice
                if (isset($data['send_invoice'])) {
                    $paymentMode = PaymentMode::findOrFail(3); // hardcoded faktura payment
                    $paymentPlan = PaymentPlan::findOrFail(6);
                    $payment_plan = ( $paymentMode->mode == "Paypal" ) ?  "Hele beløpet" : $paymentPlan->plan;

                    $comment = '(Manuskript: ' . $title . ', ';
                    $comment .= 'Betalingsmodus: ' . $paymentMode->mode . ', ';
                    $comment .= 'Betalingsplan: 14 dager)';

                    $dueDate = date("Y-m-d");
                    $dueDate = Carbon::parse($dueDate);

                    $dueDate->addDays(14);

                    $dueDate = date_format(date_create($dueDate), 'Y-m-d');
                    $price = $data['price'] * 100;

                    $invoice_fields = [
                        'user_id'       => $user->id,
                        'first_name'    => $user->first_name,
                        'last_name'     => $user->last_name,
                        'netAmount'     => $price,
                        'dueDate'       => $dueDate,
                        'description'   => 'Kursordrefaktura',
                        'productID'     => $productID,
                        'email'         => $user->email,
                        'telephone'     => $user->telephone,
                        'address'       => $user->street,
                        'postalPlace'   => $user->city,
                        'postalCode'    => $user->zip,
                        'comment'       => $comment,
                        'payment_mode'  => $paymentMode->mode,
                    ];

                    $invoice = new FikenInvoice();
                    $invoice->create_invoice($invoice_fields);
                }

                $manuType = 'Correction';
                if ($data['is_copy_editing'] == 1) {
                    $manuType = 'Copy Editing';
                    CopyEditingManuscript::create([
                        'user_id'       => $user_id,
                        'file'          => $file,
                        'payment_price' => $data['price'],
                        'editor_id'     => $request->exists('editor_id') ? $data['editor_id'] : NULL
                    ]);
                } else {
                    CorrectionManuscript::create([
                        'user_id'       => $user_id,
                        'file'          => $file,
                        'payment_price' => $data['price'],
                        'editor_id'     => $request->exists('editor_id') ? $data['editor_id'] : NULL
                    ]);
                }


                return redirect()->back()->with([
                    'errors' => AdminHelpers::createMessageBag($manuType.' Manuscript added successfully.'),
                    'alert_type' => 'success',
                    'not-former-courses' => true
                ]);
            endif;

        }

        return redirect()->route('admin.learner.index');
    }

    /**
     * Assign editor to other service manuscript
     * @param $service_id
     * @param $service_type
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function otherServiceAssignEditor($service_id, $service_type, Request $request)
    {
        if ($service_type == 1 || $service_type == 2 || $service_type == 3) {
            if ($service_type == 1) {
                $copyEditing = CopyEditingManuscript::find($service_id);
                $copyEditing->editor_id = $request->editor_id;
                $copyEditing->save();
            }

            if ($service_type == 2){
                $correction = CorrectionManuscript::find($service_id);
                $correction->editor_id = $request->editor_id;
                $correction->save();
            }

            if ($service_type == 3){
                $correction = CoachingTimerManuscript::find($service_id);
                $correction->editor_id = $request->editor_id;
                $correction->save();
            }

            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Editor assigned successfully.'),
                'alert_type' => 'success',
                'not-former-courses' => true
            ]);
        }

        return redirect()->back();
    }

    /**
     * Add coaching session for a user
     * @param $user_id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addCoachingTimer($user_id, Request $request)
    {
        if ($user = User::find($user_id)) {
            $data = $request->except('_token');
            $data['price'] = 1690;
            /*$suggested_dates = $data['suggested_date'];
            // format the sent suggested dates
            foreach ($suggested_dates as $k => $suggested_date) {
                $suggested_dates[$k] = Carbon::parse($suggested_date)->format('Y-m-d H:i:s');
            }*/

            $extensions = ['docx'];
            $file   = NULL;

            if ($request->hasFile('manuscript') && $request->file('manuscript')->isValid()) :
                $extension = pathinfo($_FILES['manuscript']['name'], PATHINFO_EXTENSION);
                $original_filename = $request->manuscript->getClientOriginalName();

                if( !in_array($extension, $extensions) ) :
                    return redirect()->back()->with([
                        'alert_type' => 'danger',
                        'errors' => AdminHelpers::createMessageBag('File type not allowed.'),
                    ]);
                endif;

                $destinationPath = 'storage/coaching-timer-manuscripts/'; // upload path

                $time = time();
                $fileName = $time.'.'.$extension;//$original_filename; // rename document0
                $file = $destinationPath.$fileName;
                $request->manuscript->move($destinationPath, $fileName);

                $docObj = new \Docx2Text($destinationPath.$fileName);
                $docText= $docObj->convertToText();
                $word_count = FrontendHelpers::get_num_of_words($docText);

                $word_7500_price    = 690;
                $excess_word        = 0;
                $excess_word_price  = 0;

                // the initial calculated word is 7500 if excess then calculate the total excess price
                if ($word_count > 7500) {
                    $excess_word = $word_count - 7500;
                    // 69 is the price for every 1250 that is excess
                    $excess_word_price = ceil($excess_word/1250) * 69;
                }

                $price = $word_7500_price + $excess_word_price;
                $data['price'] = $data['price'] + $price;

            endif;

            // check if the admin wants to send an invoice to the user
            if (isset($data['send_invoice'])) {

                $title = 'Coaching time';
                if ($data['plan_type'] == 1) {
                    $title .= ' (1 time)';
                    $productID = 601355457;
                } else {
                    $title .= ' (0,5 time)';
                    $productID = 601355458;
                }

                $paymentMode = PaymentMode::findOrFail(3); // hardcoded faktura payment
                $paymentPlan = PaymentPlan::findOrFail(6);
                $payment_plan = ( $paymentMode->mode == "Paypal" ) ?  "Hele beløpet" : $paymentPlan->plan;

                $comment = '(Manuskript: ' . $title . ', ';
                $comment .= 'Betalingsmodus: ' . $paymentMode->mode . ', ';
                $comment .= 'Betalingsplan: 14 dager)';

                $dueDate = date("Y-m-d");
                $dueDate = Carbon::parse($dueDate);

                $dueDate->addDays(14);

                $dueDate = date_format(date_create($dueDate), 'Y-m-d');
                $price = $data['price'] * 100;

                $invoice_fields = [
                    'user_id'       => $user->id,
                    'first_name'    => $user->first_name,
                    'last_name'     => $user->last_name,
                    'netAmount'     => $price,
                    'dueDate'       => $dueDate,
                    'description'   => 'Kursordrefaktura',
                    'productID'     => $productID,
                    'email'         => $user->email,
                    'telephone'     => $user->telephone,
                    'address'       => $user->street,
                    'postalPlace'   => $user->city,
                    'postalCode'    => $user->zip,
                    'comment'       => $comment,
                    'payment_mode'  => $paymentMode->mode,
                ];

                $invoice = new FikenInvoice();
                $invoice->create_invoice($invoice_fields);
            }

            CoachingTimerManuscript::create([
                'user_id'           => $user_id,
                'file'              => $file,
                'payment_price'     => $data['price'],
                'plan_type'         => $data['plan_type'],
                'editor_id'         => $request->exists('editor_id') ? $data['editor_id'] : NULL,
                'is_approved'       => 1
            ]);

            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Coaching session added successfully.'),
                'alert_type' => 'success',
                'not-former-courses' => true
            ]);

        }

        return redirect()->route('admin.learner.index');
    }

    /**
     * Add diploma to user
     * @param $learner_id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addDiploma($learner_id, Request $request)
    {
        if ($learner = User::find($learner_id)) {
            $data = $request->except('_token');
            $extensions = ['pdf'];

            if ($request->hasFile('diploma') && $request->file('diploma')->isValid()) :
                $extension = pathinfo($_FILES['diploma']['name'], PATHINFO_EXTENSION);
                $original_filename = $request->diploma->getClientOriginalName();

                if( !in_array($extension, $extensions) ) :
                    return redirect()->back()->with([
                        'alert_type' => 'danger',
                        'errors' => AdminHelpers::createMessageBag('File type not allowed.'),
                        'not-former-courses' => true
                    ]);
                endif;

                $destinationPath = 'storage/diploma'; // upload path

                // check if path not exists then create it
                if(!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, $mode = 0777, true, true);
                }

                $filename = pathinfo($original_filename, PATHINFO_FILENAME);
                // check the file name and add/increment number if the filename already exists
                $file = AdminHelpers::checkFileName($destinationPath, $filename, $extension);

                $request->diploma->move($destinationPath, $file);

                $data['diploma'] = $file;

                $learner->diplomas()->create($data);

                return redirect()->back()->with([
                    'errors'                => AdminHelpers::createMessageBag('Diploma added successfully.'),
                    'alert_type'            => 'success',
                    'not-former-courses'    => true
                ]);
            endif;
        }

        return redirect()->route('admin.learner.index');
    }

    /**
     * Edit diploma details
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function editDiploma($id, Request $request)
    {
        if ($diploma = Diploma::find($id)) {
            $data = $request->except('_token');
            $extensions = ['pdf'];
            if ($request->hasFile('diploma') && $request->file('diploma')->isValid()) :
                $extension = pathinfo($_FILES['diploma']['name'], PATHINFO_EXTENSION);
                $original_filename = $request->diploma->getClientOriginalName();

                if( !in_array($extension, $extensions) ) :
                    return redirect()->back()->with([
                        'alert_type' => 'danger',
                        'errors' => AdminHelpers::createMessageBag('File type not allowed.'),
                        'not-former-courses' => true
                    ]);
                endif;

                $destinationPath = 'storage/diploma'; // upload path

                // check if path not exists then create it
                if(!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, $mode = 0777, true, true);
                }

                // remove the previous file from server
                if (File::exists($diploma->diploma)) {
                    File::delete($diploma->diploma);
                }

                $filename = pathinfo($original_filename, PATHINFO_FILENAME);
                // check the file name and add/increment number if the filename already exists
                $file = AdminHelpers::checkFileName($destinationPath, $filename, $extension);

                $request->diploma->move($destinationPath, $file);

                $data['diploma'] = $file;
            endif;

            $diploma->update($data);

            return redirect()->back()->with([
                'errors'                => AdminHelpers::createMessageBag('Diploma updated successfully.'),
                'alert_type'            => 'success',
                'not-former-courses'    => true
            ]);
        }
        return redirect()->route('admin.learner.index');
    }

    /**
     * Delete the diploma the file inclded
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteDiploma($id)
    {
        if ($diploma = Diploma::find($id)) {

            // check first if the file exists to prevent error on deleting file
            if (File::exists($diploma->diploma)) {
                File::delete($diploma->diploma);
            }

            $diploma->delete();
            return redirect()->back()->with([
                'errors'                => AdminHelpers::createMessageBag('Diploma deleted successfully.'),
                'alert_type'            => 'success',
                'not-former-courses'    => true
            ]);
        }

        return redirect()->route('admin.learner.index');
    }

    /**
     * Download the diploma
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadDiploma($id)
    {
        $shopManuscriptTaken = Diploma::find($id);
        if ($shopManuscriptTaken) {
            $filename = $shopManuscriptTaken->diploma;
            return response()->download(public_path($filename));
        }

        return redirect()->route('admin.learner.index');
    }

    /**
     * Approve a coaching timer
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approveCoachingTimer($id)
    {
        if($coachingTimer = CoachingTimerManuscript::find($id)) {
            $coachingTimer->is_approved = 1;
            $coachingTimer->save();
            return redirect()->back()->with([
                'errors'                => AdminHelpers::createMessageBag('Coaching timer approved successfully.'),
                'alert_type'            => 'success',
                'not-former-courses'    => true
            ]);
        }

        return redirect()->route('backend.dashboard');
    }

    /**
     * Update the note for a workshop taken
     * @param $workshop_taken_id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateWorkshopTakenNotes($workshop_taken_id, Request $request)
    {
        if($workshopTaken = WorkshopsTaken::find($workshop_taken_id)) {
            $workshopTaken->notes = $request->notes;
            $workshopTaken->save();

            return redirect()->back()->with([
                'errors'                => AdminHelpers::createMessageBag('Workshop note updated successfully.'),
                'alert_type'            => 'success',
                'not-former-courses'    => true
            ]);
        }

        return redirect()->back();
    }

    /**
     * Add secondary email to user
     * @param $learner_id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addSecondaryEmail($learner_id, Request $request)
    {
        $validator = Validator::make(($request->all()), [
            'email' => 'required|email|unique:users|unique:user_emails',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->with([
                    'alert_type'            => 'danger',
                    'not-former-courses'    => true
                ]);
        }

        UserEmail::create([
            'user_id' => $learner_id,
            'email' => $request->email
        ]);

        return redirect()->back()->with([
                'errors'                => AdminHelpers::createMessageBag('Email added successfully.'),
                'alert_type'            => 'success',
                'not-former-courses'    => true
            ]);
    }

    /**
     * Set a new primary email
     * @param $email_id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function setPrimaryEmail($email_id)
    {
        $userEmail  = UserEmail::find($email_id);
        $user       = $userEmail->users->first();
        $primary    = $userEmail->email;
        $secondary  = $user->email;
        if( !$user->update(['email' => $primary]))
        {
            \DB::rollback();
            return response()->json(['error' => 'Opss. Something went wrong'], 500);
        }

        if(! $userEmail->update(['email' => $secondary]))
        {
            \DB::rollback();
            return response()->json(['error' => 'Opss. Something went wrong'], 500);
        }
        \DB::commit();

        $searchEmail = $secondary;
        $result = AdminHelpers::getActiveCampaignDataByEmail($searchEmail);
        // check if exists in any list
        if (isset($result['lists'])) {
            // check if subscriber in list 40
            if (isset($result['lists'][40])) {
                $list_data = $result['lists'][40];
                $user_id = $list_data['subscriberid'];

                $newEmail = $primary;
                AdminHelpers::updateActiveCampaignContactEmailForList($user_id, $newEmail, 40);
            }
        }

        return redirect()->back()->with([
                'errors'                => AdminHelpers::createMessageBag('Secondary email set as primary.'),
                'alert_type'            => 'success',
                'not-former-courses'    => true
            ]);

    }

    public function removeSecondaryEmail($email_id)
    {
        $userEmail = UserEmail::findOrFail($email_id);
        $userEmail->delete();
        return redirect()->back()->with([
            'errors'                => AdminHelpers::createMessageBag('Secondary email removed successfully.'),
            'alert_type'            => 'success',
            'not-former-courses'    => true
        ]);
    }
}
