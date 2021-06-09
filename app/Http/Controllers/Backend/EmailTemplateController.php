<?php
namespace App\Http\Controllers\Backend;

use App\EmailTemplate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Course;
use Illuminate\Support\Str;
use Validator;
use App\Http\AdminHelpers;

class EmailTemplateController extends Controller
{

    public function index()
    {
        $templates = EmailTemplate::all();
        $templates->map(function ($item) {
            if($item->page_name === 'COURSE-FOR-SALE'){
                $course = Course::find($item->course_id)?Course::find($item->course_id)->title:'';
                $item->page_name = $item->page_name.':'.$course.':'.$item->course_type;
            }
            return $item;
        });
        $courses = Course::all();
        return view('backend.email-template.index', compact('templates','courses'));
    }

    public function addEmailTemplate(Request $request)
    {
        $this->validate($request, [
            'email_content' => 'required'
        ]);

        $page_name = $request->page_name;
        $type = null;

        if($request['is_course_for_sale']){
            $course = Course::find($request->course_id);
            $this->validate($request, [
                'course_id' => 'required'
            ]);
            if($course->type === 'Group'){
                $type = 'GROUP';
                if($request['group-course-multi-invioce-email']){
                    $type = 'GROUP-MULTI-INVOICE';
                }
            }else{
                $type = 'SINGLE';
            }

            $page_name = 'COURSE-FOR-SALE';

            // check if nana ba na course & type
            if(EmailTemplate::where('course_id', $course->id)->where('course_type', $type)->first()){
                return redirect()->back()->with([
                    'errors' => AdminHelpers::createMessageBag('Email template already exists.'),
                    'alert_type' => 'warning'
                ]);
            }
        }else{
            $this->validate($request, [
                'page_name' => 'required|unique:email_template'
            ]);
        }

        EmailTemplate::create([
            'page_name' => $page_name,
            'subject' => $request->subject,
            'from_email' => $request->from_email,
            'email_content' => $request->email_content,
            'course_id' => is_numeric($request->course_id) ? $request->course_id : NULL,
            'course_type' => $type
        ]);

        return redirect()->back();
    }

    public function editEmailTemplate($id, Request $request)
    {
        $emailtemplate = EmailTemplate::find($id);
        if ($emailtemplate) {
            $emailtemplate->page_name = $request->page_name ?: $emailtemplate->page_name;
            $emailtemplate->subject = $request->subject ?: $emailtemplate->subject;
            $emailtemplate->from_email = $request->from_email ? $request->from_email : $emailtemplate->from_email;
            $emailtemplate->email_content = $request->email_content;
            $emailtemplate->save();
        }
        return redirect()->back();
    }

    public function courseEditAdd($courseId, Request $request)
    {
        $course = Course::find($courseId);
        $emailtemplate = null;

        if($course->type == 'Single'){
            $emailtemplate = EmailTemplate::where('course_id', $courseId)->where('course_type', 'SINGLE')->first();
        }else{
            if($request->group_course_multi_invioce_email){
                $emailtemplate = EmailTemplate::where('course_id', $courseId)->where('course_type', 'GROUP-MULTI-INVOICE')->first();
            }else{
                $emailtemplate = EmailTemplate::where('course_id', $courseId)->where('course_type', 'GROUP')->first();
            }
        }

        if($emailtemplate){ // edit

            $emailtemplate->page_name = 'COURSE-FOR-SALE';
            $emailtemplate->subject = $request->subject ?: $emailtemplate->subject;
            $emailtemplate->from_email = $request->from_email ? $request->from_email : $emailtemplate->from_email;
            $emailtemplate->email_content = $request->email_content;
            $emailtemplate->save();
    
        }else{ //create

            $this->validate($request, [
                'email_content' => 'required'
            ]);

            $type = null;
    
            if($course->type === 'Group'){
                $type = 'GROUP';
                if($request['group-course-multi-invioce-email']){
                    $type = 'GROUP-MULTI-INVOICE';
                }
            }else{
                $type = 'SINGLE';
            }

            $page_name = 'COURSE-FOR-SALE';

            EmailTemplate::create([
                'page_name' => $page_name,
                'subject' => $request->subject,
                'from_email' => $request->from_email,
                'email_content' => $request->email_content,
                'course_id' => $course->id,
                'course_type' => $type
            ]);

        }

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Email template saved.'),
            'alert_type' => 'success'
        ]);
        
    }
}