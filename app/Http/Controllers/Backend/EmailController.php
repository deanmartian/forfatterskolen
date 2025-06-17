<?php

namespace App\Http\Controllers\Backend;

use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Http\EmailReader;
use App\LearnerEmail;
use App\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\View\View;

class EmailController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'checkPageAccess:12',
        ];
    }

    /**
     * Display the corresponding view
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(): View
    {
        // check first if the user is already logged in to their email
        if (\Session::has('email_logged_in')) {
            $emails = \Session::get('inbox');

            return view('backend.emails.index', compact('emails'));
        } else {
            return view('backend.emails.login');
        }
    }

    public function show($id)
    {
        $user = \Session::get('email_user');
        $pass = \Session::get('email_pass');

        // login to the email server
        $email_reader = new EmailReader($user, $pass);
        if ($email_reader->connect()) {
            $email = $email_reader->get($id);
            $email_id = $email['header']->message_id;

            \Session::put('reply_email_id', $email_id);
            \Session::put('reply_from_email', $email['header']->to[0]->mailbox.'@'.$email['header']->to[0]->host);
            \Session::put('reply_to_email', $email['header']->from[0]->mailbox.'@'.$email['header']->from[0]->host);
            \Session::put('reply_email_subject', str_replace('_', ' ', mb_decode_mimeheader($email['header']->subject)));

            return view('backend.emails.show', compact('email', 'email_id'));
        } else {
            return redirect()->back();
        }
    }

    public function store(Request $request): RedirectResponse
    {
        session()->flash('message.level', 'success');
        session()->flash('message.content', 'Email sent successfully.');

        return redirect()->back();
    }

    /**
     * Login the user to the web server
     */
    public function login(Request $request): RedirectResponse
    {
        $user = $request->email;
        $pass = $request->password;

        \Session::put('email_user', $user);
        \Session::put('email_pass', $pass);

        $email_reader = new EmailReader($user, $pass);
        if ($email_reader->connect()) {
            \Session::put('inbox', $email_reader->inbox());
            \Session::put('email_logged_in', 1);
        } else {
            session()->flash('message.level', 'danger');
            session()->flash('message.content', 'Unable to login.');
        }

        return redirect()->back();
    }

    /**
     * Move the selected email from inbox to the learners
     *
     * @param  int  $id  email id
     */
    public function move(int $id): RedirectResponse
    {
        $user = \Session::get('email_user');
        $pass = \Session::get('email_pass');

        // login to the email server
        $email_reader = new EmailReader($user, $pass);
        if ($email_reader->connect()) {
            $email = $email_reader->get($id);
            $from = $email['header']->from[0]->mailbox.'@'.$email['header']->from[0]->host;
            $subject = str_replace('_', ' ', mb_decode_mimeheader($email['header']->subject));
            $structure = $email['structure'];
            $body = $email['readable_body'];
            $attachmentFile = null;

            $user = User::where('email', $from)->first();

            // check if email have attachment
            $attachments = $this->extract_attachments($email_reader->connection(), $id);

            foreach ($attachments as $attachment) {
                if ($attachment['is_attachment'] == 1) {
                    $destinationPath = 'storage/email_attachments/'; // upload path
                    $fileName = $attachment['filename']; // rename document
                    // check if directory exists
                    if (! \File::exists($destinationPath)) {
                        \File::makeDirectory($destinationPath);
                    }

                    file_put_contents(public_path().'/'.$destinationPath.$fileName, $attachment['attachment']);
                    $attachmentFile = $destinationPath.$fileName;
                }

            }

            $encodings = [
                0 => '7BIT',
                1 => '8BIT',
                2 => 'BINARY',
                3 => 'BASE64',
                4 => 'QUOTED-PRINTABLE',
                5 => 'OTHER',
            ];

            $encoding = $encodings[$structure->encoding];

            if ($encoding == 'BASE64') {
                $body = imap_base64($body);
            } elseif ($encoding == 'QUOTED-PRINTABLE') {
                $body = quoted_printable_decode($body);
            } elseif ($encoding == '8BIT') {
                $body = quoted_printable_decode(imap_8bit($body));
            } elseif ($encoding == '7BIT') {
                $body = $this->decode7Bit($body);
            }

            if ($user) {
                $learnerEmail = new LearnerEmail;
                $learnerEmail->user_id = $user->id;
                $learnerEmail->subject = $subject;
                $learnerEmail->email = $body;
                $learnerEmail->attachment = $attachmentFile;
                $learnerEmail->save();

                session()->flash('message.level', 'success');
                session()->flash('message.content', 'Email successfully moved to learner.');
            } else {
                session()->flash('message.level', 'danger');
                session()->flash('message.content', 'Email could not be moved. Learner does not exist.');
            }
        }

        return redirect()->back();
    }

    public function delete($id): RedirectResponse
    {
        $user = \Session::get('email_user');
        $pass = \Session::get('email_pass');

        $email_reader = new EmailReader($user, $pass);
        if ($email_reader->connect()) {
            $deleteEmail = $email_reader->delete($id);
            if ($deleteEmail) {
                $email_reader = new EmailReader($user, $pass);
                \Session::put('inbox', $email_reader->inbox());

                session()->flash('message.level', 'success');
                session()->flash('message.content', 'Email deleted successfully.');
            }
        }

        return redirect()->back();
    }

    /**
     * Reply to particular email
     */
    public function reply(Request $request): RedirectResponse
    {

        $from = \Session::get('reply_from_email');
        $email_id = \Session::get('reply_email_id');
        $to = \Session::get('reply_to_email');

        $headers = 'From: Forfatterskolen<'.$from.">\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= 'In-Reply-To: '.$email_id."\r\n";

        $subject = \Session::get('reply_email_subject');
        $content = $request->email_content;

        if (! $content) {
            AdminHelpers::addFlashMessage('danger', 'Message field is required.');
        } else {
            mail($to, $subject, $content, $headers);
            AdminHelpers::addFlashMessage('success', 'Email sent successfully.');
        }

        return redirect()->back();
    }

    public function forward($id, Request $request): RedirectResponse
    {
        $user = \Session::get('email_user');
        $pass = \Session::get('email_pass');

        // login to the email server
        $email_reader = new EmailReader($user, $pass);
        if ($email_reader->connect()) {
            $email = $email_reader->get($id);
            $from = $email['header']->from[0]->mailbox.'@'.$email['header']->from[0]->host;
            $subject = str_replace('_', ' ', mb_decode_mimeheader($email['header']->subject));
            $structure = $email['structure'];
            $body = $this->getBody($id, $email_reader->connection());
            $to = $request->to_email;

            $boundary = md5(uniqid(time()));

            $messageBody = $request->email_content;

            // check if email have attachment
            $attachments = $this->extract_attachments($email_reader->connection(), $id);
            $attachmentFile = null;

            foreach ($attachments as $attachment) {
                if ($attachment['is_attachment'] == 1) {
                    $destinationPath = 'storage/email_attachments/'; // upload path
                    $fileName = $attachment['filename']; // rename document
                    // check if directory exists
                    if (! \File::exists($destinationPath)) {
                        \File::makeDirectory($destinationPath);
                    }

                    file_put_contents(public_path().'/'.$destinationPath.$fileName, $attachment['attachment']);
                    $attachmentFile = $destinationPath.$fileName;
                }

            }

            $headers = 'From: Forfatterskolen<'.$from.">\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            // $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

            if (! is_null($attachmentFile)) {
                $file = public_path($attachmentFile);
                $fileExp = explode('/', $file);
                $fileName = end($fileExp);
                $file_size = filesize($file);
                $handle = fopen($file, 'r');
                $content = fread($handle, $file_size);
                fclose($handle);
                $content = chunk_split(base64_encode($content));
                $mimeType = mime_content_type($file);

                $message = '';

                $headers .= 'Content-Type: multipart/mixed; boundary="'.$boundary.'"'."\r\n";

                $message .= '--'.$boundary."\r\n";
                $message .= "Content-Type: text/html; charset=UTF-8\r\n";
                $message .= "Content-Transfer-Encoding: base64\r\n\r\n";
                $message .= chunk_split(base64_encode($messageBody.$body));

                $message .= '--'.$boundary."\r\n";
                $message .= 'Content-Type: '.$mimeType.'; name="'.$fileName.'"'."\r\n";
                $message .= 'Content-Transfer-Encoding: base64'."\r\n";
                $message .= 'Content-Disposition: attachment; filename="'.$fileName.'"'."\r\n";
                $message .= $content."\r\n";
                $message .= '--'.$boundary.'--';

                if (mail($to, $subject, $message, $headers)) {
                    AdminHelpers::addFlashMessage('success', 'Email forwarded successfully.');

                    return redirect()->back();
                }
            } else {
                $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

                if (mail($to, $subject, $messageBody.$body, $headers)) {
                    AdminHelpers::addFlashMessage('success', 'Email forwarded successfully.');

                    return redirect()->back();
                }
            }

        } else {
            return redirect()->back();
        }
    }

    /**
     * Get body of the email
     *
     * @param  $uid  int index id of the message
     * @param  $imap  resource imap connection
     * @return bool
     */
    public function getBody($uid, $imap)
    {
        $body = $this->get_part($imap, $uid, 'TEXT/HTML');
        // if HTML body is empty, try getting text body
        if ($body == '') {
            $body = $this->get_part($imap, $uid, 'TEXT/PLAIN');
        }

        return $body;
    }

    /**
     * Get the email parts
     *
     * @param  $imap  resource imap connection
     * @param  $uid  int message id
     * @param  $mimetype  string
     * @return bool|string
     */
    public function get_part($imap, $uid, $mimetype, bool $structure = false, bool $partNumber = false)
    {
        if (! $structure) {
            $structure = imap_fetchstructure($imap, $uid);
        }
        if ($structure) {
            if ($mimetype == $this->get_mime_type($structure)) {
                if (! $partNumber) {
                    $partNumber = 1;
                }
                $text = imap_fetchbody($imap, $uid, $partNumber);
                switch ($structure->encoding) {
                    case 3:
                        return imap_base64($text);
                    case 4:
                        return imap_qprint($text);
                    default:
                        return $text;
                }
            }

            // multipart
            if ($structure->type == 1) {
                foreach ($structure->parts as $index => $subStruct) {
                    $prefix = '';
                    if ($partNumber) {
                        $prefix = $partNumber.'.';
                    }
                    $data = $this->get_part($imap, $uid, $mimetype, $subStruct, $prefix.($index + 1));
                    if ($data) {
                        return $data;
                    }
                }
            }
        }

        return false;
    }

    /*
     * Get the mime type of the email
     */
    public function get_mime_type($structure)
    {
        $primaryMimetype = ['TEXT', 'MULTIPART', 'MESSAGE', 'APPLICATION', 'AUDIO', 'IMAGE', 'VIDEO', 'OTHER'];

        if ($structure->subtype) {
            return $primaryMimetype[(int) $structure->type].'/'.$structure->subtype;
        }

        return 'TEXT/PLAIN';
    }

    /**
     * Decodes 7-Bit text.
     *
     * PHP seems to think that most emails are 7BIT-encoded, therefore this
     * decoding method assumes that text passed through may actually be base64-
     * encoded, quoted-printable encoded, or just plain text. Instead of passing
     * the email directly through a particular decoding function, this method
     * runs through a bunch of common encoding schemes to try to decode everything
     * and simply end up with something *resembling* plain text.
     *
     * Results are not guaranteed, but it's pretty good at what it does.
     *
     * @param  $text  (string)
     *               7-Bit text to convert.
     * @return (string)
     *                  Decoded text.
     */
    public function decode7Bit($text)
    {
        // If there are no spaces on the first line, assume that the body is
        // actually base64-encoded, and decode it.
        $original_text = $text;
        $lines = explode("\r\n", $text);
        $first_line_words = explode(' ', $lines[0]);
        if ($first_line_words[0] == $lines[0]) {
            $text = base64_decode($text);
        }

        if (! strlen($text)) {
            $text = $original_text;
        }
        // Manually convert common encoded characters into their UTF-8 equivalents.
        $characters = [
            '=20' => ' ', // space.
            '=2C' => ',', // comma.
            '=E2=80=99' => "'", // single quote.
            '=0A' => "\r\n", // line break.
            '=0D' => "\r\n", // carriage return.
            '=A0' => ' ', // non-breaking space.
            '=B9' => '$sup1', // 1 superscript.
            '=C2=A0' => ' ', // non-breaking space.
            "=\r\n" => '', // joined line.
            '=E2=80=A6' => '&hellip;', // ellipsis.
            '=E2=80=A2' => '&bull;', // bullet.
            '=E2=80=93' => '&ndash;', // en dash.
            '=E2=80=94' => '&mdash;', // em dash.
        ];
        // Loop through the encoded characters and replace any that are found.
        foreach ($characters as $key => $value) {
            $text = str_replace($key, $value, $text);
        }

        return $text;
    }

    public function extract_attachments($connection, $message_number)
    {

        $attachments = [];
        $structure = imap_fetchstructure($connection, $message_number);

        if (isset($structure->parts) && count($structure->parts)) {

            for ($i = 0; $i < count($structure->parts); $i++) {

                $attachments[$i] = [
                    'is_attachment' => false,
                    'filename' => '',
                    'name' => '',
                    'attachment' => '',
                ];

                if ($structure->parts[$i]->ifdparameters) {
                    foreach ($structure->parts[$i]->dparameters as $object) {
                        if (strtolower($object->attribute) == 'filename') {
                            $attachments[$i]['is_attachment'] = true;
                            $attachments[$i]['filename'] = $object->value;
                        }
                    }
                }

                if ($structure->parts[$i]->ifparameters) {
                    foreach ($structure->parts[$i]->parameters as $object) {
                        if (strtolower($object->attribute) == 'name') {
                            $attachments[$i]['is_attachment'] = true;
                            $attachments[$i]['name'] = $object->value;
                        }
                    }
                }

                if ($attachments[$i]['is_attachment']) {
                    $attachments[$i]['attachment'] = imap_fetchbody($connection, $message_number, $i + 1);
                    if ($structure->parts[$i]->encoding == 3) { // 3 = BASE64
                        $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
                    } elseif ($structure->parts[$i]->encoding == 4) { // 4 = QUOTED-PRINTABLE
                        $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
                    }
                }

            }

        }

        return $attachments;

    }
}
