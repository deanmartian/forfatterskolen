<?php
namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\Course;
use App\FreeCourse;
use App\Package;
use App\Http\FikenInvoice;

class HomeController extends Controller
{
   
    public function index()
    {
        $popular_courses = Course::whereIn('id', [9, 11, 16])->orderBy('id', 'desc')->get();
        $free_courses = FreeCourse::orderBy('created_at', 'desc')->get();

        return view('frontend.home', compact('popular_courses', 'free_courses'));
    }



    public function contact_us()
    {
        return view('frontend.contact-us');
    }
    

    public function faq()
    {
        return view('frontend.faq');
    }
}
