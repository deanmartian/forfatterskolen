<?php
namespace App\Http\Controllers\Frontend;

use App\CoursesTaken;
use App\Events\AddToCampaignList;
use App\FreeCourseDelayedEmail;
use App\Http\AdminHelpers;
use App\Http\FikenInvoice;
use App\Mail\FreeCourseNewUserEmail;
use App\Order;
use App\Services\CourseService;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Course;
use App\CourseApplication;
use App\Http\FrontendHelpers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CourseController extends Controller
{
   
    public function index()
    {
    	$courses = Course::where('status', 1)
            //->orderBy('created_at', 'desc')
            // display the 0 last
         ->select(['*', \DB::raw('IF(display_order > 0, display_order, 1000000) display_order')])
        ->orderBy('display_order', 'asc')
        ->whereHas('packages')
        /*->whereHas('packages', function($query){
            return count($query) > 0;
        })*/
        ->get()
        ->filter(function($item) {
            return $item->is_active || $item->is_free;
        }); // the original don't have this filter
        $showRoute = 'front.course.show';
    	return view('frontend.course.index', compact('courses', 'showRoute'));
    }


    public function show($id)
    {
    	$course = Course::findOrFail($id);

    	if (!$course->is_free): // added this condition to display page if it's free
            if( !FrontendHelpers::isCourseActive($course) || count($course->packages) == 0 ) : // Display 404 if Course has no Packages
                return abort(404);
            endif;
        endif;
        $checkoutRoute = 'front.course.checkout';
        if ($course->pay_later_with_application) {
            $checkoutRoute = 'front.course.application';
        }
    	return view('frontend.course.show', compact('course', 'checkoutRoute'));
    }

    public function application($id)
    {
        $course = Course::findOrFail($id);

        if (!$course->is_free): // added this condition to check if the course is for sale
            if( !FrontendHelpers::isCourseActive($course) || count($course->packages) == 0 ) : // Display 404 if Course has no Packages
                return abort(404);
            endif;
        endif;

        if (!$course->pay_later_with_application) {
            return redirect()->route('front.course.checkout', $id);
        }

        return view('frontend.course.application', compact('course'));
    }

    public function processApplication($course_id, Request $request)
    {
        
        $course = Course::findOrFail($course_id);

        if (!$course->is_free): // added this condition to check if the course is for sale
            if( !FrontendHelpers::isCourseActive($course) || count($course->packages) == 0 ) : // Display 404 if Course has no Packages
                return abort(404);
            endif;
        endif;

        if (!$course->pay_later_with_application) {
            return redirect()->route('front.course.checkout', $course_id);
        }
        
        $this->validate($request, [
            'email'                 => 'required',
            'first_name'            => 'required|alpha_spaces',
            'last_name'             => 'required|alpha_spaces',
            'phone'                 => 'required',
            'manuscript'            => 'required'
        ]);
        
        if ($request->hasFile('manuscript')) {
            $file = $request->file('manuscript');
            $extension = $file->getClientOriginalExtension();

            if (!in_array($extension, ['odt', 'pdf', 'doc', 'docx'])) {
                $customErrors = ['manuscript' => 'The manuscript must be a file of type: odt, pdf, doc, docx.'];
                $validator = Validator::make([], []); 
                $validator->validate(); // Perform validation without rules
                $validator->errors()->merge($customErrors);

                throw new ValidationException($validator);
            }
        }
        
        if( Auth::guest() ) :
            $user = User::where('email', $request->email)->first();
            if( $user ) :
                Auth::login($user);
            else :
                // register new user
                $new_user = new User();
                $new_user->email = $request->email;
                $new_user->first_name = $request->first_name;
                $new_user->last_name = $request->last_name;
                $new_user->password = bcrypt($request->password);
                $new_user->save();
                Auth::login($new_user);
            endif;
        endif;

        $package_id = $course->packagesIsShow[0]->id;
        $user_id = Auth::user()->id;
        $file = FrontendHelpers::saveFile($request, 'course-application', 'manuscript');
        $request->merge([
            'package_id' => $package_id,
            'file_path' => $file,
            'user_id' => $user_id
        ]);

        $courseApplication = CourseApplication::where('user_id', $user_id)->where('package_id', $package_id)->first();
        if ($courseApplication) {
            $customErrors = ['user' => 'You already sent an application for this course.'];
            $validator = Validator::make([], []); 
            $validator->validate(); // Perform validation without rules
            $validator->errors()->merge($customErrors);

            throw new ValidationException($validator);
        }

        CourseApplication::create($request->except('_token', 'manuscript'));
        return redirect()->route('front.course.application.thank-you', $course_id);
    }

    public function applicationThankyou($course_id)
    {
        return view('frontend.course.application-thankyou');
    }

    /**
     * Free course
     * @param $course_id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function getFreeCourse($course_id, Request $request)
    {
        $course = Course::find($course_id);
        if ($course && $course->is_free && $course->status) {
            $package = $course->packages()->first();

            if (Auth::guest()) {
                $this->validate($request,
                    [
                        'email'         => 'required|email',
                        'first_name'    => 'required|alpha_spaces',
                        'last_name'     => 'required|alpha_spaces'
                    ]);

                // manually check if email already exists to display the login modal on the page
                $checkEmail = User::where('email', $request->get('email'))->first();
                if ($checkEmail) {
                    return redirect()->back()->with(['email_exist' => 1]);
                }

                // register new user
                $new_user               = new User();
                $new_user->email        = $request->email;
                $new_user->first_name   = $request->first_name;
                $new_user->last_name    = $request->last_name;
                $new_user->password     = bcrypt('Z5C5E5M2jv');
                $new_user->need_pass_update = 1;
                $new_user->save();
                Auth::login($new_user);

                // send email
                /*$email_data['email_message'] = $course->email;
                $email_data['email_subject'] = $course->title;
                $toEmail = $request->email;
                \Mail::to($toEmail)->queue(new FreeCourseNewUserEmail($email_data));*/

                // add to delayed email instead of sending email directly
                $delayedEmail['user_id'] = $new_user->id;
                $delayedEmail['course_id'] = $course->id;
                $delayedEmail['send_at'] = Carbon::now()->addMinute(10);
                FreeCourseDelayedEmail::create($delayedEmail);
            }

            // check if the course is taken and redirect the user to the course page before processing the free course
            $alreadyAvailCourse = CoursesTaken::where(['user_id' => Auth::user()->id, 'package_id' => $package->id])->first();
            if ($alreadyAvailCourse) {
                return redirect(route('learner.course.show', ['id' => $alreadyAvailCourse->id]));
            }

            // create new course taken
            $courseTaken = CoursesTaken::firstOrNew(['user_id' => Auth::user()->id, 'package_id' => $package->id]);
            $courseTaken->is_active = 1;
            $courseTaken->is_free = 1;
            $courseTaken->save();

            if ($package->course->auto_list_id > 0) {
                $list_id = $package->course->auto_list_id;
                $listData = [
                    'email' => Auth::user()->email,
                    'name' => Auth::user()->first_name,
                    'last_name' => Auth::user()->last_name
                ];
                //AdminHelpers::addToActiveCampaignList($list_id, $listData);
                event(new AddToCampaignList($list_id, $listData)); // fire the event
            }

            return redirect()->route('front.thank-you');
        }
        return redirect()->back();
    }

    public function thankyou( $course_id, Request $request, CourseService $courseService )
    {
        // check if from svea payment
        if ($request->has('svea_ord')) {
            $order_id = $request->get('svea_ord');
            $order = Order::find($order_id);

            // add course to user
            if (!$order->is_processed) {
                $courseTaken = $courseService->addCourseToLearner($order->user_id, $order->package_id);
                $courseService->notifyAdmin($order->user_id, $order->package_id);
                $courseService->notifyUser($order->user_id, $order->package_id, $courseTaken);
            }

            $order->is_processed = 1;
            $order->save();
        }

        // check if fiken invoice url is set
        // this is set when vipps payment is cancelled
        if ($request->has('iu')) {
            $fikenUrl = decrypt($request->get('iu'));
            $fiken = new FikenInvoice();
            $fikenInvoice   = $fiken->get_invoice_data($fikenUrl);
            $fiken->send_invoice($fikenInvoice);
        }

        return view('frontend.shop.thankyou');
    }

}
