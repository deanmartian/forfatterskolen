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
    	$courses = Course::orderBy('created_at', 'desc')
        ->whereHas('packages', function($query){
            return count($query) > 0;
        })
        ->get();
    	return view('frontend.course.index', compact('courses'));
    }


    public function show($id)
    {
    	$course = Course::findOrFail($id);

        $in_cart = FrontendHelpers::InCart('course_id', $id); // Check if already in cart
        $cartIndex = NULL;

        if( $in_cart ) :
            $cartIndex = FrontendHelpers::cartIndex('course_id', $id);
        endif;

        if( count($course->packages) == 0 ) : // Display 404 if Course has no Packages
            return abort(404);
        endif;

    	return view('frontend.course.show', compact('course', 'in_cart', 'cartIndex'));
    }

}
