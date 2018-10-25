<?php
namespace App\Http\Controllers\Frontend;

use App\Http\AdminHelpers;
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
        ->get();
    	return view('frontend.course.index', compact('courses'));
    }


    public function show($id)
    {
    	$course = Course::findOrFail($id);

        if( !FrontendHelpers::isCourseAvailable($course) || count($course->packages) == 0 ) : // Display 404 if Course has no Packages
            return abort(404);
        endif;

    	return view('frontend.course.show', compact('course', 'in_cart', 'cartIndex'));
    }

    public function getFreeCourse(Request $request)
    {
        $allowedTypes = [1 ,2];
        if (in_array($request->type, $allowedTypes)) {
            if ($request->type == 1) {
                $this->validate($request, ['email' => 'required|email|unique:users',
                    'first_name' => 'required|alpha_spaces', 'last_name' => 'required|alpha_spaces']);

                // register new user
                $new_user               = new User();
                $new_user->email        = $request->email;
                $new_user->first_name   = $request->first_name;
                $new_user->last_name    = $request->last_name;
                $new_user->password     = bcrypt('Z5C5E5M2jv');
                $new_user->save();
                Auth::login($new_user);

                // send email
            }
        }
        return redirect()->back();
    }

}
