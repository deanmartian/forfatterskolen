<?php
namespace App\Http\Controllers\Backend;

use App\Console\Commands\CourseEmailOut;
use App\Course;
use App\CoursesTaken;
use App\EmailAttachment;
use App\EmailOut;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Http\FrontendHelpers;
use App\Jobs\AddMailToQueueJob;
use App\Mail\SubjectBodyEmail;
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

        if ($request->has('for_free_course') &&
            $course->emailOut()->where('for_free_course', 1)->count() > 0) {
            return redirect()->back()->with([
               'errors' => AdminHelpers::createMessageBag('Only one email out for free course allowed.')
            ]);
        }

        $data = $request->except('_token');
        $data['course_id'] = $course_id;
        $data['for_free_course'] = $request->has('for_free_course') ? 1 : 0;
        $data['allowed_package'] = isset($request->allowed_package) ? json_encode($request->allowed_package) : NULL;

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

            $encode_email = encrypt($to);

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
                $content = str_replace($search_string, $replace_string, $request->message);
            }

            //AdminHelpers::send_email($subject, $from, $to, $content);
            $emailData = [
                'email_subject' => $subject,
                'email_message' => $content,
                'from_name' => '',
                'from_email' => $from,
                'attach_file' => NULL
            ];
            \Mail::to($to)->queue(new SubjectBodyEmail($emailData));
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
    public function update($course_id, $id, Request $request)
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

        $checkEmailOut = $course->emailOut()->where('for_free_course', 1)->first();

        if ($request->has('for_free_course') && $checkEmailOut && $checkEmailOut->id !== (int) $id) {
            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Only one email out for free course allowed.')
            ]);
        }

        $data = $request->except('_token');
        $data['course_id'] = $course_id;
        $data['for_free_course'] = $request->has('for_free_course') ? 1 : 0;
        $data['allowed_package'] = isset($request->allowed_package) ? json_encode($request->allowed_package) : NULL;

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

            $encode_email = encrypt($to);
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
                $content = str_replace($search_string, $replace_string, $request->message);
            }

            /*AdminHelpers::send_email($subject, $from, $to, $content);*/
            $emailData = [
                'email_subject' => $subject,
                'email_message' => $content,
                'from_name' => '',
                'from_email' => $from,
                'attach_file' => NULL
            ];
            \Mail::to($to)->queue(new SubjectBodyEmail($emailData));
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
    public function destroy($course_id, $id)
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

    public function sendEmailToLearners($course_id, $id)
    {
        $emailOut = EmailOut::find($id);
        $packages = $emailOut->allowed_package ? json_decode($emailOut->allowed_package) :
            $emailOut->course->packages->pluck('id')->toArray();
        $emailRecipients = $emailOut->recipients->pluck('user_id')->toArray();
        $coursesTaken = CoursesTaken::whereIn('package_id', $packages)
            ->whereNull('renewed_at')
            ->whereNotIn('user_id', $emailRecipients)
            ->get();

        $emailAttachment = EmailAttachment::where('hash', $emailOut->attachment_hash)->first();
        $attachmentText = '';
        if ($emailAttachment) {
            $attachmentText = "<p style='margin-top: 10px'><b>Vedlegg:</b> 
<a href='".route('front.email-attachment', $emailAttachment->hash)."'>"
                .AdminHelpers::extractFileName($emailAttachment->filename)."</a></p>";
        }

        // loop the result and send email
        foreach ($coursesTaken as $courseTaken) {
            $toMail = $courseTaken->user->email;

            $encode_email = encrypt($courseTaken->user->email);
            $user = $courseTaken->user;
            $loginLink = "<a href='".route('auth.login.email', $encode_email)."'>Klikk her for å logge inn</a>";
            $password = $user->need_pass_update ? 'Z5C5E5M2jv' : 'Skjult (kan endres inne i portalen eller via glemt passord)';
            if (strpos($emailOut->message, "[redirect]")) {
                $extractLink        = FrontendHelpers::getTextBetween($emailOut->message, "[redirect]", "[/redirect]");
                $formatRedirectLink = route('auth.login.emailRedirect',[$encode_email, encrypt($extractLink)]);
                $redirectLabel      =  FrontendHelpers::getTextBetween($emailOut->message, "[redirect_label]", "[/redirect_label]");
                $redirectLink       = "<a href='".$formatRedirectLink."'>".$redirectLabel."</a>";
                $search_string = [
                    '[redirect]'.$extractLink.'[/redirect]', '[redirect_label]'.$redirectLabel.'[/redirect_label]'
                ];
                $replace_string = [
                    $redirectLink, ''
                ];
                $message = str_replace($search_string, $replace_string, $emailOut->message);
            } else {
                $search_string = [
                    '[login_link]', '[username]', '[password]'
                ];
                $replace_string = [
                    $loginLink, $courseTaken->user->email, $password
                ];
                $message = str_replace($search_string, $replace_string, $emailOut->message);
            }

            $emailData['email_subject'] = $emailOut->subject;
            $emailData['email_message'] = $message.$attachmentText;
            $emailData['from_name'] = $emailOut->from_name;
            $emailData['from_email'] = $emailOut->from_email;
            $emailData['attach_file'] = NULL;

            // add email to queue
            dispatch(new AddMailToQueueJob($toMail, $emailOut->subject, $message.$attachmentText,
                $emailOut->from_email, $emailOut->from_name, null, 'courses-taken', $courseTaken->id));

            $emailOut->recipients()->updateOrCreate([
                'user_id' => $user->id
            ]);

        }

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Email out sent successfully.'),
            'alert_type' => 'success'
        ]);
    }

}