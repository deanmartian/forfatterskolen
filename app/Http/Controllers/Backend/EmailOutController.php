<?php
namespace App\Http\Controllers\Backend;

use App\Course;
use App\EmailAttachment;
use App\EmailOut;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use \Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

class EmailOutController extends Controller {

    /**
     * Create new email out
     * @param $course_id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store($course_id, Request $request)
    {
        $course = Course::find($course_id);

        if (!$course) {
            return redirect()->back();
        }

        $this->validate($request,[
           'subject' => 'required',
           'message' => 'required',
           'delay' => 'required'
        ]);

        $data = $request->except('_token');
        $data['course_id'] = $course_id;

        if ($request->hasFile('attachment')) :
            $destinationPath = 'storage/course-email-out-attachments'; // upload path

            if (!\File::exists($destinationPath)) {
                \File::makeDirectory($destinationPath);
            }

            $extension = $request->attachment->extension(); // getting image extension
            $uploadedFile = $request->attachment->getClientOriginalName();
            $actual_name = pathinfo($uploadedFile, PATHINFO_FILENAME);
            //remove spaces to avoid error on attachment
            $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension);// rename document
            $request->attachment->move($destinationPath, $fileName);

            $data['attachment'] = '/'.$fileName;

            $emailAttach['filename'] =  $data['attachment'];
            $emailAttach['hash'] = substr(md5(microtime()), 0, 6);
            $emailAttachment = EmailAttachment::create($emailAttach);
            $data['attachment_hash'] = $emailAttachment->hash;
        endif;


        EmailOut::create($data);

        $notif = AdminHelpers::createMessageBag('Email out updated successfully.');
        if ($request->send_to) {
            $subject = $request->subject;
            $from = 'post@forfatterskolen.no';
            $to = $request->send_to;
            $content = $request->message;
            $messageBag = new MessageBag();
            $messageBag->add('errors', 'Email out updated successfully.');
            $messageBag->add('errors', "Email sent to ".$to);
            $notif = $messageBag;
            AdminHelpers::send_email($subject, $from, $to, $content);
        }

        return redirect()->back()->with([
            'errors' => $notif,
            'alert_type' => 'success'
        ]);
    }

    /**
     * Update email out record
     * @param $id
     * @param $course_id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id, $course_id, Request $request)
    {
        $course = Course::find($course_id);
        $email_out = EmailOut::find($id);

        if (!$course || !$email_out) {
            return redirect()->back();
        }

        $this->validate($request,[
            'subject' => 'required',
            'message' => 'required',
            'delay' => 'required'
        ]);

        $data = $request->except('_token');

        if ($request->hasFile('attachment')) :
            $destinationPath = 'storage/course-email-out-attachments'; // upload path

            if (!\File::exists($destinationPath)) {
                \File::makeDirectory($destinationPath);
            }

            $extension = $request->attachment->extension(); // getting image extension
            $uploadedFile = $request->attachment->getClientOriginalName();
            $actual_name = pathinfo($uploadedFile, PATHINFO_FILENAME);
            //remove spaces to avoid error on attachment
            $fileName = str_replace(' ','_', AdminHelpers::checkFileName($destinationPath, $actual_name, $extension));// rename document
            $request->attachment->move($destinationPath, $fileName);

            $data['attachment'] = '/'.$fileName;
        endif;

        $email_out->update($data);
        $email_out->save();

        $notif = AdminHelpers::createMessageBag('Email out updated successfully.');
        if ($request->send_to) {
            $subject = $email_out->subject;
            $from = 'post@forfatterskolen.no';
            $to = $request->send_to;
            $content = $email_out->message;
            $messageBag = new MessageBag();
            $messageBag->add('errors', 'Email out updated successfully.');
            $messageBag->add('errors', "Email sent to ".$to);
            $notif = $messageBag;
            AdminHelpers::send_email($subject, $from, $to, $content);
        }

        return redirect()->back()->with([
            'errors' => $notif,
            'alert_type' => 'success'
        ]);
    }

    /**
     * Delete email out record
     * @param $id
     * @param $course_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id, $course_id)
    {
        $course = Course::find($course_id);
        $email_out = EmailOut::find($id);

        if (!$course || !$email_out) {
            return redirect()->back();
        }

        $email_out->delete();
        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Email out deleted successfully.'),
            'alert_type' => 'success'
        ]);
    }

}