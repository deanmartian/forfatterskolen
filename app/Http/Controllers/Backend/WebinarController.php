<?php

namespace App\Http\Controllers\Backend;

use App\Course;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Http\FrontendHelpers;
use App\Http\Requests\AddWebinarRequest;
use App\Jobs\WebinarScheduleRegistrationJob;
use App\Mail\SubjectBodyEmail;
use App\UserAutoRegisterToCourseWebinar;
use App\Webinar;
use App\WebinarEmailOut;
use App\WebinarRegistrant;
use App\WebinarScheduledRegistration;
use File;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WebinarController extends Controller
{
    public function store(AddWebinarRequest $request): RedirectResponse
    {
        $course = Course::findOrFail($request->course_id);
        Course::where('', '');
        $webinar = new Webinar;
        $webinar->course_id = $course->id;
        $webinar->title = $request->title;
        $webinar->description = $request->description;
        $webinar->host = $request->host;
        $webinar->start_date = $request->start_date;
        $webinar->link = $request->link;

        if ($request->hasFile('image')) {
            /*
             * original code for inserting image
             *
             * $destinationPath = 'storage/webinars/'; // upload path
            $extension = $request->image->extension(); // getting image extension
            $fileName = time().'.'.$extension; // renameing image
            $request->image->move($destinationPath, $fileName);
            // optimize image
            if ( strtolower( $extension ) == "png" ) :
                $image = imagecreatefrompng($destinationPath.$fileName);
                imagepng($image, $destinationPath.$fileName, 9);
            else :
                $image = imagecreatefromjpeg($destinationPath.$fileName);
                imagejpeg($image, $destinationPath.$fileName, 70);
            endif;
            $webinar->image = '/'.$destinationPath.$fileName;*/

            $fileExt = $request->image->extension(); // getting image extension
            $fileType = $request->image->getMimeType();
            $fileSize = $request->image->getSize();
            $fileTmp = $request->image->getPathName();
            $fileName = time().'.'.$fileExt; // renaming image

            $largeImageLoc = 'storage/webinars/'.$fileName; // upload path
            $thumbImageLoc = 'storage/webinars/thumb/'.$fileName; // upload path thumb

            if (move_uploaded_file($fileTmp, $largeImageLoc)) {
                // file permission
                chmod($largeImageLoc, 0777);

                // get dimensions of the original image
                [$width_org, $height_org] = getimagesize($largeImageLoc);

                // get image coords
                $x = (int) $request->x;
                $y = (int) $request->y;
                $width = (int) $request->w;
                $height = (int) $request->h;

                // define the final size of the cropped image
                $width_new = $width;
                $height_new = $height;

                $source = '';

                // crop and resize image
                $newImage = imagecreatetruecolor($width_new, $height_new);

                switch ($fileType) {
                    case 'image/gif':
                        $source = imagecreatefromgif($largeImageLoc);
                        break;
                    case 'image/pjpeg':
                    case 'image/jpeg':
                    case 'image/jpg':
                        $source = imagecreatefromjpeg($largeImageLoc);
                        break;
                    case 'image/png':
                    case 'image/x-png':
                        $source = imagecreatefrompng($largeImageLoc);
                        break;
                }

                imagecopyresampled($newImage, $source, 0, 0, $x, $y, $width_new, $height_new, $width, $height);

                // check if the folder exists if not then create the folder
                if (! file_exists('storage/webinars/thumb/')) {
                    File::makeDirectory('storage/webinars/thumb/', 0775, true);
                }

                switch ($fileType) {
                    case 'image/gif':
                        imagegif($newImage, $thumbImageLoc);
                        break;
                    case 'image/pjpeg':
                    case 'image/jpeg':
                    case 'image/jpg':
                        imagejpeg($newImage, $thumbImageLoc, 90);
                        break;
                    case 'image/png':
                    case 'image/x-png':
                        imagepng($newImage, $thumbImageLoc);
                        break;
                }
                imagedestroy($newImage);

                // remove large image
                unlink($largeImageLoc);

                $webinar->image = '/'.$thumbImageLoc;
            }

        }

        $webinar->save();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Webinar created successfully.'),
            'alert_type' => 'success',
        ]);
    }

    public function update($id, AddWebinarRequest $request)/* : RedirectResponse */
    {
        $webinar = Webinar::findOrFail($id);
        $webinar->title = $request->title;
        $webinar->description = $request->description;
        $webinar->host = $request->host;
        $webinar->start_date = $request->start_date;
        $webinar->link = $request->link;

        if ($request->hasFile('image')) {
            /*
             * Original Code
             *
             *  $image = substr($webinar->image, 1);
             if( File::exists($image) ) :
                 File::delete($image);
             endif;
             $destinationPath = 'storage/webinars/'; // upload path
             $extension = $request->image->extension(); // getting image extension
             $fileName = time().'.'.$extension; // renameing image
             $request->image->move($destinationPath, $fileName);
             // optimize image
             if ( strtolower( $extension ) == "png" ) :
                 $image = imagecreatefrompng($destinationPath.$fileName);
                 imagepng($image, $destinationPath.$fileName, 9);
             else :
                 $image = imagecreatefromjpeg($destinationPath.$fileName);
                 imagejpeg($image, $destinationPath.$fileName, 70);
             endif;
             $webinar->image = '/'.$destinationPath.$fileName;*/

            $fileExt = $request->image->extension(); // getting image extension
            $fileType = $request->image->getMimeType();
            $fileSize = $request->image->getSize();
            $fileTmp = $request->image->getPathName();
            $fileName = time().'.'.$fileExt; // renaming image

            $largeImageLoc = 'storage/webinars/'.$fileName; // upload path
            $thumbImageLoc = 'storage/webinars/thumb/'.$fileName; // upload path thumb

            if (move_uploaded_file($fileTmp, $largeImageLoc)) {
                // file permission
                chmod($largeImageLoc, 0777);

                // get dimensions of the original image
                [$width_org, $height_org] = getimagesize($largeImageLoc);

                // get image coords
                $x = (int) $request->x;
                $y = (int) $request->y;
                $width = (int) $request->w;
                $height = (int) $request->h;

                // define the final size of the cropped image
                $width_new = $width;
                $height_new = $height;

                $source = '';

                // crop and resize image
                $newImage = imagecreatetruecolor($width_new, $height_new);

                switch ($fileType) {
                    case 'image/gif':
                        $source = imagecreatefromgif($largeImageLoc);
                        break;
                    case 'image/pjpeg':
                    case 'image/jpeg':
                    case 'image/jpg':
                        $source = imagecreatefromjpeg($largeImageLoc);
                        break;
                    case 'image/png':
                    case 'image/x-png':
                        $source = imagecreatefrompng($largeImageLoc);
                        break;
                }

                imagecopyresampled($newImage, $source, 0, 0, $x, $y, $width_new, $height_new, $width, $height);

                // check if the folder exists if not then create the folder
                if (! file_exists('storage/webinars/thumb/')) {
                    File::makeDirectory('storage/webinars/thumb/', 0775, true);
                }

                switch ($fileType) {
                    case 'image/gif':
                        imagegif($newImage, $thumbImageLoc);
                        break;
                    case 'image/pjpeg':
                    case 'image/jpeg':
                    case 'image/jpg':
                        imagejpeg($newImage, $thumbImageLoc, 90);
                        break;
                    case 'image/png':
                    case 'image/x-png':
                        imagepng($newImage, $thumbImageLoc);
                        break;
                }
                imagedestroy($newImage);

                // remove large image
                unlink($largeImageLoc);

                $webinar->image = '/'.$thumbImageLoc;
            }

        }

        $webinar->save();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Webinar updated successfully.'),
            'alert_type' => 'success',
        ]);
    }

    public function updateField($id, Request $request): RedirectResponse
    {
        $field = $request->field;
        $value = $request->value;
        $webinar = Webinar::findOrFail($id);
        $webinar->$field = $value;
        $webinar->save();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Webinar hidden successfully.'),
            'alert_type' => 'success',
        ]);
    }

    public function destroy($id, Request $request): RedirectResponse
    {
        $webinar = Webinar::findOrFail($id);
        $webinar->forceDelete();

        return redirect()->back();
    }

    public function makeReplay($webinar_id, Request $request): RedirectResponse
    {
        $webinar = Webinar::find($webinar_id);
        if ($webinar) {
            $webinar->set_as_replay = $request->set_as_replay;
            $webinar->save();

            return redirect()->back();
        }

        return redirect()->route('admin.course.index');
    }

    public function setSchedule($webinar_id, Request $request): RedirectResponse
    {

        $scheduledRegistration = WebinarScheduledRegistration::firstOrCreate([
            'webinar_id' => $webinar_id,
        ]);

        $scheduledRegistration->date = $request->date;
        $scheduledRegistration->save();

        if ($request->has('run_cron')) {
            dispatch(new WebinarScheduleRegistrationJob($scheduledRegistration));
        }

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Webinar scheduled successfully.'),
            'alert_type' => 'success',
        ]);
    }

    /**
     * Save email out for webinar
     */
    public function webinarEmailOut($webinar_id, $course_id, Request $request): RedirectResponse
    {
        $webinar = Webinar::where('course_id', $course_id)->where('id', $webinar_id)->first();

        if (! $webinar) {
            return redirect()->back();
        }

        $request->validate([
            'send_date' => 'required|date',
            'message' => 'required',
            'subject' => 'required',
        ]);

        $emailOut = WebinarEmailOut::firstOrNew(['course_id' => $course_id, 'webinar_id' => $webinar_id]);
        $emailOut->subject = $request->get('subject');
        $emailOut->send_date = $request->get('send_date');
        $emailOut->message = $request->get('message');
        $emailOut->save();

        if ($request->test_email) {
            $user_email = $request->test_email;
            $register_link = "<a href='".route('front.goto-webinar.registration.email',
                [encrypt($webinar->link), encrypt($user_email)])."'>Registrer meg</a>";

            $extractLink = FrontendHelpers::getTextBetween($request->message, '[redirect]',
                '[/redirect]');
            $redirectLabel = FrontendHelpers::getTextBetween($request->message, '[redirect_label]',
                '[/redirect_label]');
            $encode_email = encrypt($request->test_email);
            $formatRedirectLink = route('auth.login.emailRedirect', [$encode_email, encrypt($extractLink)]);
            $redirectLink = "<a href='".$formatRedirectLink."'>".$redirectLabel.'</a>';
            $search_string = [
                '[redirect]'.$extractLink.'[/redirect]', '[redirect_label]'.$redirectLabel.'[/redirect_label]',
                '[register_link]',
            ];
            $replace_string = [
                $redirectLink, '', $register_link,
            ];
            $message = str_replace($search_string, $replace_string, $request->message);

            $emailData['email_subject'] = $request->subject;
            $emailData['email_message'] = $message;
            $emailData['from_name'] = null;
            $emailData['from_email'] = null;
            $emailData['attach_file'] = null;

            // add email to queue
            \Mail::to($user_email)->queue(new SubjectBodyEmail($emailData));

            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Webinar email save and email sent successfully.'),
                'alert_type' => 'success',
            ]);
        }

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Webinar email save successfully.'),
            'alert_type' => 'success',
        ]);
    }

    public function autoRegisterLearnersToWebinar($webinar_id, $course_id, Request $request): RedirectResponse
    {
        $webinar = Webinar::find($webinar_id);
        $autoRegisterLearners = UserAutoRegisterToCourseWebinar::where('course_id', $course_id)->get();

        $scheduledRegistration = WebinarScheduledRegistration::firstOrCreate([
            'webinar_id' => $webinar_id,
        ]);

        $scheduledRegistration->date = $request->date;
        $scheduledRegistration->save();

        /*$header[] = 'API-KEY: '.config('services.big_marker.api_key');
        $counter = 1;
        foreach ( $autoRegisterLearners as $learner ) {
            $user = $learner->user;
            $data = [
                'id'            => $webinar->link,
                'email'         => $user->email,
                'first_name'    => $user->first_name,
                'last_name'     => $user->last_name,
            ];
            $ch = curl_init();
            $url = config('services.big_marker.register_link');

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            $response = curl_exec($ch);
            $decoded_response = json_decode($response);

            if (array_key_exists('conference_url', $decoded_response)) {

                $registrant['user_id'] = $user->id;
                $registrant['webinar_id'] = $webinar->id;
                $webRegister = WebinarRegistrant::firstOrNew($registrant);
                $webRegister->join_url = $decoded_response->conference_url;
                $webRegister->save();
                echo "success ".$user->email." ".$counter. "<br/>";
            } else {
                echo $decoded_response->error." ".$user->email." ".$counter. "<br/>";
            }

            $counter++;
        }*/

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Webinar scheduled successfully.'),
            'alert_type' => 'success']);
    }

    public function registrantList($webinar_id): JsonResponse
    {
        $registrants = WebinarRegistrant::where('webinar_id', $webinar_id)->get();

        return response()->json($registrants);
    }

    public function removeRegistrant($registrant_id, Request $request)
    {
        $registrant = WebinarRegistrant::find($registrant_id);
        $user = $registrant->user;
        $webinar = $registrant->webinar;
        $webinar_key = $webinar->link;

        $data = [
            'id' => $webinar_key,
            'email' => $user->email,
        ];

        $url = config('services.big_marker.register_link');
        $ch = curl_init();
        $header[] = 'API-KEY: '.config('services.big_marker.api_key');
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $response = curl_exec($ch);
        $decoded_response = json_decode($response);

        if (! empty($decoded_response)) {
            return response()->json($decoded_response, 400);
        }

        $registrant->delete();

        if ($request->ajax()) {
            return response()->json($decoded_response);
        } else {
            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Learner removed from webinar successfully'),
                'alert_type' => 'success',
                'not-former-courses' => true,
            ]);
        }
    }
}
