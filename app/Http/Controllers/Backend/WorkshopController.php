<?php

namespace App\Http\Controllers\Backend;

use App\Course;
use App\CoursesTaken;
use App\EmailAttachment;
use App\Exports\GenericExport;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddWorkshopRequest;
use App\Http\TFPDF\TFPDF;
use App\Mail\SubjectBodyEmail;
use App\User;
use App\Workshop;
use App\WorkshopEmailLog;
use App\WorkshopsTaken;
use File;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\View\View;

class WorkshopController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkPageAccess:3');
    }

    public function index(): View
    {
        $workshops = Workshop::orderBy('created_at', 'desc')->paginate(25);

        return view('backend.workshop.index', compact('workshops'));
    }

    public function show($id): View
    {
        $workshop = Workshop::findOrFail($id);
        $emailLog = $workshop->emailLog()->paginate(5);
        $courses = Course::with('packages')->get();

        return view('backend.workshop.show', compact('workshop', 'emailLog', 'courses'));
    }

    public function store(AddWorkshopRequest $request): RedirectResponse
    {
        $request->validate([
            'description' => 'required',
        ]);
        $workshop = new Workshop;
        $workshop->title = $request->title;
        $workshop->description = $request->description;
        $workshop->price = $request->price;
        $workshop->date = $request->date;
        $workshop->faktura_date = $request->faktura_date;
        $workshop->duration = $request->duration;
        $workshop->fiken_product = $request->fiken_product;
        $workshop->seats = $request->seats;
        $workshop->location = $request->location;
        $workshop->gmap = $request->gmap;
        $workshop->is_free = $request->is_free == 'on' ? 1 : 0;

        if ($request->hasFile('image')) {
            $destinationPath = 'storage/workshops/'; // upload path
            $extension = $request->image->extension(); // getting image extension
            $fileName = time().'.'.$extension; // renameing image
            $request->image->move($destinationPath, $fileName);
            // optimize image
            if (strtolower($extension) == 'png') {
                $image = imagecreatefrompng($destinationPath.$fileName);
                imagepng($image, $destinationPath.$fileName, 9);
            } else {
                $image = imagecreatefromjpeg($destinationPath.$fileName);
                imagejpeg($image, $destinationPath.$fileName, 70);
            }
            $workshop->image = '/'.$destinationPath.$fileName;
        }

        $workshop->save();

        return redirect(route('admin.workshop.show', $workshop->id));
    }

    public function update($id, AddWorkshopRequest $request): RedirectResponse
    {
        $request->validate([
            'description' => 'required',
        ]);
        $workshop = Workshop::findOrFail($id);
        $workshop->title = $request->title;
        $workshop->description = $request->description;
        $workshop->price = $request->price;
        $workshop->date = $request->date;
        $workshop->faktura_date = $request->faktura_date;
        $workshop->duration = $request->duration;
        $workshop->fiken_product = $request->fiken_product;
        $workshop->seats = $request->seats;
        $workshop->location = $request->location;
        $workshop->gmap = $request->gmap;
        $workshop->is_free = $request->is_free == 'on' ? 1 : 0;

        if ($request->hasFile('image')) {
            $image = substr($workshop->image, 1);
            if (File::exists($image)) {
                File::delete($image);
            }
            $destinationPath = 'storage/workshops/'; // upload path
            $extension = $request->image->extension(); // getting image extension
            $fileName = time().'.'.$extension; // renameing image
            $request->image->move($destinationPath, $fileName);
            // optimize image
            if (strtolower($extension) == 'png') {
                $image = imagecreatefrompng($destinationPath.$fileName);
                imagepng($image, $destinationPath.$fileName, 9);
            } else {
                $image = imagecreatefromjpeg($destinationPath.$fileName);
                imagejpeg($image, $destinationPath.$fileName, 70);
            }
            $workshop->image = '/'.$destinationPath.$fileName;
        }

        $workshop->save();

        return redirect()->back();
    }

    public function destroy($id, Request $request): RedirectResponse
    {
        $workshop = Workshop::findOrFail($id);
        $workshop->forceDelete();

        return redirect(route('admin.workshop.index'));
    }

    /**
     * Update the email to be sent when approved for this workshop
     */
    public function update_email($id, Request $request): RedirectResponse
    {
        $workshop = Workshop::findOrFail($id);
        $workshop->email_title = $request->email_title;
        $workshop->email_body = $request->email_body;
        $workshop->save();

        return redirect()->back();
    }

    public function removeAttendee($workshop_taken_id, $attendee_id, Request $request): RedirectResponse
    {
        $workshopTaken = WorkshopsTaken::findOrFail($workshop_taken_id);
        $user = User::findOrFail($attendee_id);
        $workshopTaken->forceDelete();

        return redirect()->back();
    }

    public function downloadAttendees($id)
    {
        $workshop = Workshop::findOrFail($id);
        $pdf = new TFPDF;
        $pdf->AddPage();

        foreach ($workshop->taken as $taken) {
            $pdf->Ln();
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(0, 5, $taken->user->full_name, 0, 1);
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(0, 5, 'Menu: '.$taken->menu->title, 0, 1);
            $pdf->Cell(0, 5, 'Notes: '.$taken->notes, 0, 1);
        }

        $pdf->Output($workshop->title.'-attendees.pdf', 'D');
    }

    public function downloadAttendeesExcel($id)
    {
        $workshop = Workshop::findOrFail($id);
        $learnerList = [];
        // $learnerList[]  = ['First Name', 'Last Name', 'Email']; // first row in excel
        $headers = ['First Name', 'Last Name', 'Email'];

        foreach ($workshop->taken as $taken) {
            $learnerList[] = [$taken->user->first_name, $taken->user->last_name, $taken->user->email];
        }

        $excel = \App::make('excel');

        return $excel->download(new GenericExport($learnerList, $headers), $workshop->title.' Learners.xlsx');
        /*$excel->create($workshop->title.' Learners', function($excel) use ($learnerList) {

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($learnerList) {
                // prevent inserting an empty first row
                $sheet->fromArray($learnerList, null, 'A1', false, false);
            });
        })->download('xlsx');*/
    }

    public function addLearnersToCourse($id, Request $request): RedirectResponse
    {
        $workshop = Workshop::findOrFail($id);

        foreach ($workshop->taken as $taken) {
            $learner = $taken->user;
            $course = Course::findOrFail($request->course_id);
            $packageIds = $course->packages->pluck('id')->toArray();
            $courseTaken = CoursesTaken::where('user_id', $learner->id)
                ->whereIn('package_id', $packageIds)->first();

            if (! $courseTaken) {
                $courseTaken = new CoursesTaken;
                $courseTaken->user_id = $learner->id;
                $courseTaken->package_id = $request->package_id;
            }

            $courseTaken->started_at = null;
            $courseTaken->is_active = 1;

            if ($course->is_free) {
                $courseTaken->is_free = 1;
            }

            $courseTaken->save();
        }

        return redirect()->back();
    }

    /**
     * Send email to the attendees of the workshop
     *
     * @param  $id  int workshop id
     */
    public function sendEmailToAttendees($id, Request $request): RedirectResponse
    {
        $workshop = Workshop::find($id);
        if ($workshop) {

            $request->validate([
                'subject' => 'required',
                'message' => 'required',
            ]);

            $attendees = isset($request->check_all) || isset($request->learners) ?
                $workshop->attendees->whereIn('user_id', $request->learners)
                : $workshop->attendees;

            $subject = $request->subject;
            $message = $request->message;
            $from_email = $request->from_email ?: 'post@forfatterskolen.no';
            $from_name = $request->from_name ?: 'Forfatterskolen';

            // check for attachment
            // save the file first before attaching it on email
            $attachment = null;
            $attachmentText = '';
            if ($request->hasFile('attachment')) {
                $destinationPath = 'storage/email_attachments'; // upload path

                if (! \File::exists($destinationPath)) {
                    \File::makeDirectory($destinationPath);
                }

                $extension = $request->attachment->extension(); // getting image extension
                $uploadedFile = $request->attachment->getClientOriginalName();
                $actual_name = pathinfo($uploadedFile, PATHINFO_FILENAME);
                // remove spaces to avoid error on attachment
                $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension); // rename document
                $request->attachment->move($destinationPath, $fileName);

                $attachment = '/'.$fileName;
                $emailAttach['filename'] = $attachment;
                $emailAttach['hash'] = substr(md5(microtime()), 0, 6);
                $emailAttachment = EmailAttachment::create($emailAttach);
                $attachmentText = "<p style='margin-top: 10px'><b>Vedlegg:</b> 
<a href='".route('front.email-attachment', $emailAttachment->hash)."'>"
                    .AdminHelpers::extractFileName($attachment).'</a></p>';
            }

            foreach ($attendees as $attendee) {
                // AdminHelpers::send_email($subject, $from_email, $email, $message, $from_name);

                $email = $attendee->user->email;
                $emailData['email_subject'] = $subject;
                $emailData['email_message'] = $message.$attachmentText;
                $emailData['from_name'] = $from_name;
                $emailData['from_email'] = $from_email;
                $emailData['attach_file'] = null;
                \Mail::to($email)->queue(new SubjectBodyEmail($emailData));
            }

            $selected_attendees = null;
            if (isset($request->check_all) || isset($request->learners)) {
                $selected_attendees = json_encode($request->learners);
            }

            $emailLog = [
                'workshop_id' => $id,
                'subject' => $subject,
                'message' => $message,
                'learners' => $selected_attendees,
                'from_name' => $from_name,
                'from_email' => $from_email,
                'attachment' => $attachment,
            ];
            WorkshopEmailLog::create($emailLog);
        }

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Email sent successfully.'),
            'alert_type' => 'success']);
    }

    /**
     * View the log of emails
     */
    public function viewEmailLogAttendees($log_id): JsonResponse
    {
        $log = WorkshopEmailLog::find($log_id);
        $attendees = [];
        foreach (json_decode($log->learners) as $learner) {
            $user = User::find($learner);
            $attendees[route('admin.learner.show', $user->id)] = $user ? $user->full_name : '';
        }

        return response()->json($attendees);
    }

    /**
     * Update the status of the workshop
     */
    public function updateStatus(Request $request): JsonResponse
    {

        $workshop = Workshop::find($request->workshop_id);
        $success = false;

        if ($workshop) {
            $workshop->is_active = $request->is_active;
            $workshop->save();
            $success = true;
        }

        return response()->json([
            'data' => [
                'success' => $success,
            ],
        ]);
    }

    public function updateForSaleStatus(Request $request): JsonResponse
    {
        $workshop = Workshop::find($request->workshop_id);
        $success = false;

        if ($workshop) {
            $workshop->is_free = $request->is_free;
            $workshop->save();
            $success = true;
        }

        return response()->json([
            'data' => [
                'success' => $success,
            ],
        ]);
    }
}
