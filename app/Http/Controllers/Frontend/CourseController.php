<?php
namespace App\Http\Controllers\Frontend;

use App\CoursesTaken;
use App\Http\AdminHelpers;
use App\Mail\FreeCourseNewUserEmail;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Course;
use App\Http\FrontendHelpers;

class CourseController extends Controller
{
   
    public function index()
    {
    	$courses = Course::where('status', 1)
            //->orderBy('created_at', 'desc')
            // display the 0 last
         ->select(['*', \DB::raw('IF(display_order > 0, display_order, 1000000) display_order')])
        ->orderBy('display_order', 'asc')
        ->whereHas('packages', function($query){
            return count($query) > 0;
        })
        ->get()
        ->filter(function($item) {
            return $item->is_available || $item->is_free;
        }); // the original don't have this filter
    	return view('frontend.course.index', compact('courses'));
    }


    public function show($id)
    {
    	$course = Course::findOrFail($id);

    	if (!$course->is_free): // added this condition to display page if it's free
            if( !FrontendHelpers::isCourseAvailable($course) || count($course->packages) == 0 ) : // Display 404 if Course has no Packages
                return abort(404);
            endif;
        endif;

    	return view('frontend.course.show', compact('course', 'in_cart', 'cartIndex'));
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
        if ($course && $course->is_free) {
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
                $new_user->save();
                Auth::login($new_user);

                // send email
                $email_data['email_message'] = $course->email;
                $toEmail = $request->email;
                \Mail::to($toEmail)->queue(new FreeCourseNewUserEmail($email_data));
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
            return redirect()->route('front.thank-you');
        }
        return redirect()->back();
    }

}
