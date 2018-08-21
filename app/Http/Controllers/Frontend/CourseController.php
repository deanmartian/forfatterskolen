<?php
namespace App\Http\Controllers\Frontend;

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

}
