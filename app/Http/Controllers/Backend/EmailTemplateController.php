<?php
namespace App\Http\Controllers\Backend;

use App\EmailTemplate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EmailTemplateController extends Controller
{

    public function index()
    {
        $templates = EmailTemplate::all();
        return view('backend.email-template.index', compact('templates'));
    }

    public function addEmailTemplate(Request $request)
    {
        $this->validate($request, [
            'page_name' => 'required|unique:email_template,page_name',
            'email_content' => 'required'
        ]);

        EmailTemplate::create([
            'page_name' => $request->page_name,
            'subject' => $request->subject,
            'from_email' => $request->from_email,
            'email_content' => $request->email_content
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
}