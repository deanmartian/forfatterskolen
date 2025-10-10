<?php

namespace App\Http\Controllers\Backend;

use App\CoachingTimerManuscript;
use App\CoachingTimerTaken;
use App\Course;
use App\CourseApplication;
use App\CourseCertificate;
use App\CourseExpiryReminder;
use App\CoursesTaken;
use App\EmailAttachment;
use App\EmailOutLog;
use App\Exports\CourseLearnerExport;
use App\Exports\GenericExport;
use App\FormerCourse;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Frontend\ShopController;
use App\Http\FrontendHelpers;
use App\Http\Requests\CourseCreateRequest;
use App\Http\Requests\CourseUpdateRequest;
use App\Jobs\AddMailToQueueJob;
use App\Jobs\CourseOrderJob;
use App\Jobs\WebinarScheduleRegistrationJob;
use App\Mail\SubjectBodyEmail;
use App\Order;
use App\Package;
use App\PackageCourse;
use App\Services\CourseService;
use App\SimilarCourse;
use App\User;
use App\Webinar;
use App\WebinarRegistrant;
use App\WebinarScheduledRegistration;
use Carbon\Carbon;
use File;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\View\View;
use Maatwebsite\Excel\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CourseController extends Controller
{

    public function __construct()
    {
        $this->middleware('checkPageAccess:1');
    }

    public function index(Request $request): View
    {
        if ($request->search && ! empty($request->search)) {
            $courses = Course::where('title', 'LIKE', '%'.$request->search.'%')->orderBy('created_at', 'desc')->paginate(25);
        } else {
            // display 0 value last
            $courses = Course::orderByRaw('display_order = 0, display_order')->orderBy('created_at', 'desc')->paginate(25);
        }

        return view('backend.course.index', compact('courses'));
    }

    public function show(Request $request, $id)
    {
        $section = isset($request->section) ? $request->section : 'overview';
        AdminHelpers::validateCourseSubpage($section);

        $course = Course::findOrFail($id);

        return $this->showSection($section, $course);
    }

    public function showSection($section, $course): View
    {
        return view('backend.course.'.$section, compact('course', 'section'));
    }

    public function create(): View
    {
        $course = [
            'title' => old('title'),
            'description' => old('description'),
            'description_simplemde' => old('description_simplemde'),
            'course_image' => '',
            'type' => '',
            'course_plan' => '',
            'course_plan_data' => '',
            'start_date' => '',
            'end_date' => '',
            'display_order' => '',
            'is_free' => '',
            'instructor' => '',
            'auto_list_id' => '',
            'photographer' => '',
            'hide_price' => '',
            'meta_title' => '',
            'meta_description' => '',
            'meta_image' => '',
            'pay_later_with_application' => '',
            'free_for_days' => '',
        ];

        return view('backend.course.create', compact('course'));
    }

    public function store(CourseCreateRequest $request): RedirectResponse
    {
        $free_for_days = $request->is_free ? $request->free_for_days : 0;
        $requestData = $request->toArray();
        $requestData['display_order'] = $requestData['display_order'] ? $requestData['display_order'] : 0;

        $course = new Course;
        $course->title = $request->title;
        $course->description = $request->description;
        $course->display_order = $requestData['display_order'];

        if ($request->hasFile('course_image')) {
            $destinationPath = 'storage/course-images/'; // upload path
            $extension = $request->course_image->extension(); // getting image extension
            $fileName = time().'.'.$extension; // renameing image
            $request->course_image->move($destinationPath, $fileName);
            // optimize image
            if (strtolower($extension) == 'png') {
                $image = imagecreatefrompng($destinationPath.$fileName);
                imagepng($image, $destinationPath.$fileName, 9);
            } else {
                $image = imagecreatefromjpeg($destinationPath.$fileName);
                imagejpeg($image, $destinationPath.$fileName, 70);
            }
            $course->course_image = '/'.$destinationPath.$fileName;
        }

        if ($request->hasFile('meta_image')) {
            if (! File::exists('storage/meta-images/')) {
                File::makeDirectory('meta-images');
            }
            $destinationPath = 'storage/meta-images/'; // upload path
            $extension = $request->meta_image->extension(); // getting image extension
            $fileName = time().'.'.$extension; // renaming image
            $request->meta_image->move($destinationPath, $fileName);
            // optimize image
            if (strtolower($extension) == 'png') {
                $image = imagecreatefrompng($destinationPath.$fileName);
                imagepng($image, $destinationPath.$fileName, 9);
            } else {
                $image = imagecreatefromjpeg($destinationPath.$fileName);
                imagejpeg($image, $destinationPath.$fileName, 70);
            }
            $course->meta_image = '/'.$destinationPath.$fileName;
        }

        $course->type = $request->type;
        $course->start_date = $request->start_date;
        $course->end_date = $request->end_date;
        $course->is_free = isset($request->is_free) ? 1 : 0;
        $course->instructor = $request->instructor;
        $course->auto_list_id = $request->auto_list_id ?: 0;
        $course->photographer = $request->photographer;
        $course->hide_price = isset($request->hide_price) ? 1 : 0;
        $course->pay_later_with_application = isset($request->pay_later_with_application) ? 1 : 0;
        $course->meta_title = $request->meta_title;
        $course->meta_description = $request->meta_description;
        $course->free_for_days = $free_for_days;
        $course->save();

        // create editor package
        $package = new Package;
        $package->course_id = $course->id;
        $package->variation = 'Editor Package';
        $package->description = 'Editor Package';
        $package->manuscripts_count = 0;
        $package->full_payment_sale_price_from = null;
        $package->full_payment_sale_price_to = null;
        $package->full_payment_other_sale_price_from = null;
        $package->full_payment_other_sale_price_to = null;
        $package->full_payment_sale_price = null;
        $package->full_payment_other_sale_price = null;
        $package->months_3_sale_price_from = null;
        $package->months_3_sale_price_to = null;
        $package->months_6_sale_price_from = null;
        $package->months_6_sale_price_to = null;
        $package->months_12_sale_price_from = null;
        $package->months_12_sale_price_to = null;
        $package->full_payment_price = 0;
        $package->is_standard = 0;
        $package->save();

        $display_order = $requestData['display_order'];
        $this->updateDisplayOrder($display_order, $course->id);

        return redirect(route('admin.course.show', $course->id));
    }

    public function edit($id): View
    {
        $course = Course::findOrFail($id)->toArray();

        return view('backend.course.edit', compact('course'));
    }

    public function update($id, CourseUpdateRequest $request): RedirectResponse
    {

        $free_for_days = $request->is_free ? $request->free_for_days : 0;
        $requestData = $request->toArray();
        $requestData['display_order'] = $requestData['display_order'] ? $requestData['display_order'] : 0;

        $course = Course::findOrFail($id);
        $course->title = $request->title;
        $course->description = $request->description;
        $course->course_plan = $request->course_plan;
        $course->course_plan_data = $request->course_plan_data;
        $course->display_order = $requestData['display_order'];

        if ($request->hasFile('course_image') && $request->file('course_image')->isValid()) {
            $checkImageExistCount = Course::where('course_image', 'LIKE', '%'.$course->course_image.'%')
                ->get()->count();
            $image = substr($course->course_image, 1);
            /* if( File::exists($image) && $checkImageExistCount < 2) :
                File::delete($image);
            endif; */
            $destinationPath = 'storage/course-images/'; // upload path
            $extension = $request->course_image->extension(); // getting image extension
            $fileName = time().'.'.$extension; // renameing image
            $request->course_image->move($destinationPath, $fileName);
            // optimize image
            if (strtolower($extension) == 'png') {
                $image = imagecreatefrompng($destinationPath.$fileName);
                imagepng($image, $destinationPath.$fileName, 9);
            } else {
                $image = imagecreatefromjpeg($destinationPath.$fileName);
                imagejpeg($image, $destinationPath.$fileName, 70);
            }
            $course->course_image = '/'.$destinationPath.$fileName;
        }

        if ($request->hasFile('meta_image')) {
            if (! File::exists('storage/meta-images/')) {
                File::makeDirectory('meta-images');
            }
            $destinationPath = 'storage/meta-images/'; // upload path
            $extension = $request->meta_image->extension(); // getting image extension
            $fileName = time().'.'.$extension; // renaming image
            $request->meta_image->move($destinationPath, $fileName);
            // optimize image
            if (strtolower($extension) == 'png') {
                $image = imagecreatefrompng($destinationPath.$fileName);
                imagepng($image, $destinationPath.$fileName, 9);
            } else {
                $image = imagecreatefromjpeg($destinationPath.$fileName);
                imagejpeg($image, $destinationPath.$fileName, 70);
            }
            $course->meta_image = '/'.$destinationPath.$fileName;
        }

        $course->type = $request->type;
        $course->start_date = $request->start_date;
        $course->end_date = $request->end_date;
        $course->instructor = $request->instructor;
        $course->auto_list_id = $request->auto_list_id ?: 0;
        $course->photographer = $request->photographer;
        $course->is_free = isset($request->is_free) ? 1 : 0;
        $course->hide_price = isset($request->hide_price) ? 1 : 0;
        $course->pay_later_with_application = isset($request->pay_later_with_application) ? 1 : 0;
        $course->meta_title = $request->meta_title;
        $course->meta_description = $request->meta_description;
        $course->free_for_days = $free_for_days;
        $course->save();

        $display_order = $requestData['display_order'];
        $this->updateDisplayOrder($display_order, $id);

        return redirect(route('admin.course.show', $course->id));
    }

    public function updateDisplayOrder($display_order, $id)
    {
        while ($course = Course::where('display_order', $display_order)->where('id', '!=', $id)->first()) {
            $lastCourse = Course::orderBy('display_order', 'DESC')->first();
            if ($course && $course->id !== $lastCourse->id) {
                $course->display_order = $display_order + 1;
                $course->save();
            } else {
                $lastDisplay = Course::where('display_order', $display_order)->get();
                // check if last display order is more than 1
                if ($lastDisplay->count() > 1) {
                    $lastCourse->display_order = $lastCourse->display_order + 1;
                    $lastCourse->save();
                }
            }

            $display_order++;
        }
    }

    public function destroy($id): RedirectResponse
    {
        $course = Course::findOrFail($id);
        $image = substr($course->course_image, 1);
        if (File::exists($image)) {
            File::delete($image);
        }
        $course->forceDelete();

        return redirect(route('admin.course.index'));
    }

    public function update_email($id, Request $request): RedirectResponse
    {
        $course = Course::findOrFail($id);
        $course->email = $request->email;
        $course->save();

        return redirect()->back();
    }

    public function sendWelcomeEmail($id, Request $request): RedirectResponse
    {
        $course = Course::find($id);

        foreach ($request->learners as $learner_id) {
            $user = User::find($learner_id);
            $to = $user->email;

            $emailData = [
                'email_subject' => $course->title,
                'email_message' => $course->email,
                'from_name' => '',
                'from_email' => 'post@forfatterskolen.no',
                'attach_file' => null,
            ];
            \Mail::to($to)->queue(new SubjectBodyEmail($emailData));
        }

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Email sent successfully.'),
            'alert_type' => 'success',
        ]);
    }

    public function clone_course($id, Request $request): RedirectResponse
    {
        $course = Course::findOrFail($id);
        $clone_course = $course->replicate();
        $clone_course->push();

        foreach ($course->lessons as $lesson) {
            $clone_lesson = $lesson->replicate();
            $clone_lesson->course_id = $clone_course->id;
            $clone_lesson->push();

            // clone the documents of the lesson
            foreach ($lesson->documents as $document) {
                // get parts of the file
                $parts = pathinfo($document->document);

                // check filename and copy the file
                $newDocumentName = AdminHelpers::checkFileName(ltrim($parts['dirname'], '/'), $parts['filename'], $parts['extension']);
                File::copy($document->document, $newDocumentName);

                $clone_document = $document->replicate();
                $clone_document->lesson_id = $clone_lesson->id;
                $clone_document->document = $newDocumentName;
                $clone_document->push();
            }
        }

        foreach ($course->packages as $package) {
            $clone_package = $package->replicate();
            $clone_package->course_id = $clone_course->id;
            $clone_package->push();
        }

        foreach ($course->webinars as $webinar) {

            $newImage = null;
            if ($webinar->image) {
                $parts = pathinfo($webinar->image);
                // check filename and copy the file
                $newImage = AdminHelpers::checkFileName(ltrim($parts['dirname'], '/'), $parts['filename'], $parts['extension']);
                File::copy(ltrim($webinar->image, '/'), $newImage); // use ltrim to remove first /
            }

            $clone_webinar = $webinar->replicate();
            $clone_webinar->course_id = $clone_course->id;
            $clone_webinar->image = $newImage;
            $clone_webinar->push();
        }

        foreach ($course->emailOut as $emailOut) {
            $clone_email_out = $emailOut->replicate();

            // check if email has attachment
            if ($emailOut->attachment) {
                $parts = pathinfo($emailOut->attachment);
                // check filename and copy the file
                $newAttachment = AdminHelpers::checkFileName(ltrim($parts['dirname'], '/'), $parts['filename'], $parts['extension']);
                File::copy(ltrim($emailOut->attachment, '/'), $newAttachment); // use ltrim to remove first /

                // create email-attachment record
                $emailAttach['filename'] = $newAttachment;
                $emailAttach['hash'] = substr(md5(microtime()), 0, 6);
                $emailAttachment = EmailAttachment::create($emailAttach);

                $clone_email_out->attachment = $newAttachment;
                $clone_email_out->attachment_hash = $emailAttachment->hash;
            }
            $clone_email_out->allowed_package = null;
            $clone_email_out->course_id = $clone_course->id;
            $clone_email_out->push();
        }

        return redirect(route('admin.course.show', $clone_course->id));
    }

    public function add_similar_course($id, Request $request): RedirectResponse
    {
        $course = Course::findOrFail($id);
        $similar_course_id = Course::findOrFail($request->similar_course_id);

        $similar_course = new SimilarCourse;
        $similar_course->course_id = $course->id;
        $similar_course->similar_course_id = $similar_course_id->id;
        $similar_course->save();

        return redirect()->back();
    }

    public function remove_similar_course($similar_course_id): RedirectResponse
    {
        $similar_course = SimilarCourse::findOrFail($similar_course_id);
        $similar_course->forceDelete();

        return redirect()->back();
    }

    public function updateStatus(Request $request): JsonResponse
    {

        $course = Course::find($request->course_id);
        $success = false;

        if ($course) {
            $course->status = $request->status;
            $course->save();
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
        $course = Course::find($request->course_id);
        $success = false;

        if ($course) {
            $course->for_sale = $request->for_sale;
            $course->save();
            $success = true;
        }

        return response()->json([
            'data' => [
                'success' => $success,
            ],
        ]);
    }

    /**
     * Update is free field
     */
    public function updateIsFreeStatus(Request $request): JsonResponse
    {
        $course = Course::find($request->course_id);
        $success = false;

        if ($course) {
            $course->is_free = $request->is_free;
            $course->save();
            $success = true;
        }

        return response()->json([
            'data' => [
                'success' => $success,
            ],
        ]);
    }

    public function togglePaymentPlan(Request $request, Course $course): JsonResponse
    {
        $validated = $request->validate([
            'payment_plan_id' => 'required|exists:payment_plans,id',
            'is_active' => 'required|boolean',
        ]);

        $paymentPlanIds = collect($course->payment_plan_ids ?? [])
            ->map(static fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->all();

        $paymentPlanId = (int) $validated['payment_plan_id'];

        if ($request->boolean('is_active')) {
            if (! in_array($paymentPlanId, $paymentPlanIds, true)) {
                $paymentPlanIds[] = $paymentPlanId;
            }
        } else {
            $paymentPlanIds = array_values(array_filter($paymentPlanIds, static function ($id) use ($paymentPlanId) {
                return (int) $id !== $paymentPlanId;
            }));
        }

        $course->payment_plan_ids = $paymentPlanIds;
        $course->save();

        return response()->json([
            'status' => 'success',
            'payment_plan_ids' => $course->payment_plan_ids ?? [],
        ]);
    }

    public function sendEmailToLearners($id, Request $request): RedirectResponse
    {
        $course = Course::find($id);
        if ($course) {

            $request->validate([
                'subject' => 'required',
                'message' => 'required',
            ]);

            $learners = isset($request->check_all) || isset($request->learners) ?
                $course->learners->whereIn('user_id', $request->learners)->get()
                : $course->learners->get();
            $subject = $request->subject;
            $message = '';
            $from_email = $request->from_email ?: 'postmail@forfatterskolen.no';
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

            foreach ($learners as $learner) {
                /*$email = $learner->user->email;
                AdminHelpers::send_email($subject,
                    $from_email, $email, $message, $from_name);*/

                $encode_email = encrypt($learner->user->email);
                $user = $learner->user;
                $loginLink = "<a href='".route('auth.login.email', $encode_email)."'>Klikk her for å logge inn</a>";
                $password = $user->need_pass_update ? 'Z5C5E5M2jv' : 'Skjult (kan endres inne i portalen eller via glemt passord)';

                if (strpos($request->message, '[redirect]')) {
                    $extractLink = FrontendHelpers::getTextBetween($request->message, '[redirect]', '[/redirect]');
                    $formatRedirectLink = route('auth.login.emailRedirect', [$encode_email, encrypt($extractLink)]);
                    $redirectLabel = FrontendHelpers::getTextBetween($request->message, '[redirect_label]', '[/redirect_label]');
                    $redirectLink = "<a href='".$formatRedirectLink."'>".$redirectLabel.'</a>';
                    $search_string = [
                        '[redirect]'.$extractLink.'[/redirect]', '[redirect_label]'.$redirectLabel.'[/redirect_label]',
                    ];
                    $replace_string = [
                        $redirectLink, '',
                    ];
                    $message = str_replace($search_string, $replace_string, $request->message);
                } else {
                    $search_string = [
                        '[login_link]', '[username]', '[password]',
                    ];
                    $replace_string = [
                        $loginLink, $learner->user->email, $password,
                    ];
                    $message = str_replace($search_string, $replace_string, $request->message);
                }

                $email = $learner->user->email;
                $emailData['email_subject'] = $subject;
                $emailData['email_message'] = $message.$attachmentText;
                $emailData['from_name'] = $from_name;
                $emailData['from_email'] = $from_email;
                $emailData['attach_file'] = null;
                // \Mail::to($email)->queue(new SubjectBodyEmail($emailData));
                if (!$user->is_disabled) {
                    dispatch(new AddMailToQueueJob($email, $subject, $message.$attachmentText, $from_email,
                    $from_name, null, 'courses-taken', $learner->id));
                }
            }

            $selected_learners = null;
            if (isset($request->check_all) || isset($request->learners)) {
                $selected_learners = json_encode($request->learners);
            }

            $emailOutLog = [
                'course_id' => $id,
                'subject' => $subject,
                'message' => $message,
                'learners' => $selected_learners,
                'from_name' => $from_name,
                'from_email' => $from_email,
                'attachment' => $attachment,
            ];
            EmailOutLog::create($emailOutLog);

            return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Mail sent successfully.'),
                'alert_type' => 'success']);
        }

        return redirect()->back();
    }

    /**
     * Send email to the learners that haven't started the free course yet
     */
    public function notStartedCourseReminder($course_id, Request $request): RedirectResponse
    {
        $course = Course::find($course_id);
        if ($course) {

            $request->validate([
                'subject' => 'required',
                'message' => 'required',
            ]);

            // check courses taken that's not yet started with the specified course id
            $coursesTaken = CoursesTaken::whereHas('package', function ($query) use ($course_id) {
                $query->where('course_id', $course_id);
            })
                ->whereNull('started_at')
                ->get();

            // get the other courses of the learner
            $learnerOtherCourse = CoursesTaken::whereIn('user_id', $coursesTaken->pluck('user_id')->toArray())
                ->whereNotNull('started_at')
                ->get()->pluck('user_id')->toArray();

            $learners = [];
            // check if send to has value or if it's for testing purpose
            if ($request->send_to) {
                $email = $request->send_to;
                $user = User::where('email', $email)->first();

                if ($user) {
                    $encode_email = encrypt($email);
                    $loginLink = "<a href='".route('auth.login.email', $encode_email)."'>Klikk her for å logge inn</a>";
                    $password = $user->need_pass_update ? 'Z5C5E5M2jv' : 'Skjult (kan endres inne i portalen eller via glemt passord)';

                    $search_string = [
                        '[login_link]', '[username]', '[password]',
                    ];
                    $replace_string = [
                        $loginLink, $email, $password,
                    ];
                    $convert_message = str_replace($search_string, $replace_string, $request->message);

                    $emailData['email_subject'] = $request->subject;
                    $emailData['email_message'] = $convert_message;
                    $emailData['from_name'] = null;
                    $emailData['from_email'] = null;
                    $emailData['attach_file'] = null;
                    \Mail::to($email)->queue(new SubjectBodyEmail($emailData));
                }
            } else {
                foreach ($coursesTaken as $courseTaken) {
                    if (! in_array($courseTaken->user_id, $learnerOtherCourse)) {
                        $encode_email = encrypt($courseTaken->user->email);
                        $user = $courseTaken->user;
                        $loginLink = "<a href='".route('auth.login.email', $encode_email)."'>Klikk her for å logge inn</a>";
                        $password = $user->need_pass_update ? 'Z5C5E5M2jv' : 'Skjult (kan endres inne i portalen eller via glemt passord)';

                        $search_string = [
                            '[login_link]', '[username]', '[password]',
                        ];
                        $replace_string = [
                            $loginLink, $courseTaken->user->email, $password,
                        ];
                        $convert_message = str_replace($search_string, $replace_string, $request->message);

                        $email = $courseTaken->user->email;
                        $emailData['email_subject'] = $request->subject;
                        $emailData['email_message'] = $convert_message;
                        $emailData['from_name'] = null;
                        $emailData['from_email'] = null;
                        $emailData['attach_file'] = null;
                        \Mail::to($email)->queue(new SubjectBodyEmail($emailData));

                        array_push($learners, $courseTaken->user->id);
                    }
                }

                $emailOutLog = [
                    'course_id' => $course_id,
                    'subject' => $request->subject,
                    'message' => $request->message,
                    'learners' => json_encode($learners),
                ];
                EmailOutLog::create($emailOutLog);
            }

            return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Mail sent successfully.'),
                'alert_type' => 'success']);
        }

        return redirect()->back();
    }

    public function setCourseTakenEndDate($courseId, Request $request): RedirectResponse
    {
        $learners = Course::find($courseId)->learners;
        $learners->update([
            'end_date' => $request->date,
        ]);

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('End Date save successfully.'),
                'alert_type' => 'success']);
    }

    /**
     * Export the learners to excel
     */
    public function learnerListExcel($course_id, $type = 'email')
    {
        $course = Course::find($course_id);
        if ($course) {
            $excel = \App::make('excel');

            // for new version of excel
            return $excel->download(new CourseLearnerExport($course_id, $type), $course->title.' Learners.xlsx');
            /*$learners       = $course->learners->get();
            $learnerList    = [];

            if ($type === 'address') {
                $learnerList[]  = ['id', 'learner', 'street', 'postnumber', 'city']; // first row in excel
            } else {
                $learnerList[]  = ['id', 'learner', $type]; // first row in excel
            }

            // loop all the learners
            foreach ($learners as $learner) {
                $value = $type === 'email' ? $learner->user->email : $learner->user->fullAddress;

                if ($type === 'email') {
                    $learnerList[] = [$learner->user->id, $learner->user->full_name, $value];
                } else {
                    $learnerAddress = $learner->user->address;
                    $street = $learnerAddress ? $learnerAddress->street : '';
                    $zip = $learnerAddress ? $learnerAddress->zip : '';
                    $city = $learnerAddress ? $learnerAddress->city : '';
                    $learnerList[] = [$learner->user->id, $learner->user->full_name, $street, $zip, $city];
                }
            }
            $excel->store($course->title.' Learners', function($excel) use ($learnerList) {

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($learnerList) {
                // prevent inserting an empty first row
                $sheet->fromArray($learnerList, null, 'A1', false, false);
            });
            })->download('xlsx');*/
        }

        return redirect()->back();
    }

    /**
     * Course with learners from included package
     */
    public function learnerActiveListExcel($course_id): RedirectResponse
    {
        $course = Course::find($course_id);
        if ($course) {
            $excel = \App::make('excel');
            $packageIdsOfCourse = $course->packages()->pluck('id')->toArray();
            $packageCourses = PackageCourse::whereIn('included_package_id', $packageIdsOfCourse)->get()
                ->pluck('package_id')
                ->toArray();
            $packageCourses[] = 29; // add the actual package id of webinar-pakke

            $learnerWithCourse = CoursesTaken::whereIn('package_id', $packageCourses)
                ->where('end_date', '>=', Carbon::now())
                ->groupBy('user_id')
                ->orderBy('updated_at', 'desc')
                ->get();

            $learnerList = [];
            $headers = ['id', 'learner', 'email']; // first row in excel

            // loop all the learners that have the course (included from other course)
            foreach ($learnerWithCourse as $learner) {
                $learnerList[] = [$learner->user->id, $learner->user->full_name, $learner->user->email];
            }

            return $excel->download(new GenericExport($learnerList, $headers), $course->title.' Active Learners.xlsx');
            /*$excel->create($course->title.' Active Learners', function($excel) use ($learnerList) {

                // Build the spreadsheet, passing in the payments array
                $excel->sheet('sheet1', function($sheet) use ($learnerList) {
                    // prevent inserting an empty first row
                    $sheet->fromArray($learnerList, null, 'A1', false, false);
                });
            })->download('xlsx');*/
        }

        return redirect()->back();
    }

    public function exportPayLaterLearners($course_id)
    {
        $course = Course::find($course_id);

        if ($course) {
            $packages = Package::where('course_id', $course_id)->get()->pluck('id')->toArray();
            $coursesTaken = CoursesTaken::whereIn('package_id', $packages)->get()->pluck('user_id')->toArray();
            $payLaterOrders = Order::where([
                'is_pay_later' => 1,
                'is_processed' => 1,
                'is_invoice_sent' => 0,
                'is_order_withdrawn' => 0,
            ])
            ->whereIn('package_id', $packages)
            ->whereIn('user_id', $coursesTaken)
            ->get();

            $excel = \App::make('excel');

            $learnerList = [];
            $headers = ['learner', 'email', 'package', 'price', 'discount', 'amount']; // first row in excel

            foreach($payLaterOrders as $order) {
                $learnerList[] = [
                    $order->user->full_name,
                    $order->user->email,
                    $order->package->variation,
                    $order->price,
                    $order->discount,
                    $order->price - $order->discount,
                ];
            }

            return $excel->download(new GenericExport($learnerList, $headers), $course->title.' Pay Later Orders.xlsx');
        }
        
        return redirect()->back();
    }

    /**
     * Save expiration email reminder
     */
    public function expirationReminder($course_id, Request $request): RedirectResponse
    {
        $request->validate([
            'subject_28_days' => 'required',
            'message_28_days' => 'required',
            'subject_1_week' => 'required',
            'message_1_week' => 'required',
            'subject_1_day' => 'required',
            'message_1_day' => 'required',
        ]);

        $expiryReminderEmail = CourseExpiryReminder::firstOrNew(['course_id' => $course_id]);
        $expiryReminderEmail->subject_28_days = $request->subject_28_days;
        $expiryReminderEmail->message_28_days = $request->message_28_days;
        $expiryReminderEmail->subject_1_week = $request->subject_1_week;
        $expiryReminderEmail->message_1_week = $request->message_1_week;
        $expiryReminderEmail->subject_1_day = $request->subject_1_day;
        $expiryReminderEmail->message_1_day = $request->message_1_day;
        $expiryReminderEmail->save();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Expiration email reminder saved.'),
            'alert_type' => 'success',
        ]);
    }

    /**
     * Add all learners to all webinars
     */
    public function addLearnersToWebinars($course_id, Request $request): RedirectResponse
    {
        if (! $request->webinar_id) {
            return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Webinar id is required.'),
                'alert_type' => 'danger']);
        }

        $course = Course::find($course_id);
        $webinar = $course->webinars()->where('id', $request->webinar_id)->first();
        $learners = $course->learners->get();

        $scheduledRegistration = WebinarScheduledRegistration::firstOrCreate([
            'webinar_id' => $request->webinar_id,
        ]);

        $scheduledRegistration->date = $request->date;
        $scheduledRegistration->save();

        // run the cron
        if ($request->has('run_cron')) {
            dispatch(new WebinarScheduleRegistrationJob($scheduledRegistration));
        }

        /*
         * old code before saving to schedule
         * $header[] = 'API-KEY: '.config('services.big_marker.api_key');
        $counter = 1;
        foreach ( $learners as $learner ) {
            $user = $learner->user;

            //foreach($webinars as $webinar) {

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

            //}

        }*/

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Webinar scheduled successfully.'),
            'alert_type' => 'success']);

    }

    public function resendWelcomeEmailToUser($package_id, $user_id, $courseTakenID, ShopController $shopController)
    {
        $user = User::findOrFail($user_id);
        $package = Package::findOrFail($package_id);
        $courseTaken = CoursesTaken::findOrFail($courseTakenID); // this is fixed for the certain user

        $user_email = $user->email;
        $password = $user->need_pass_update ? 'Z5C5E5M2jv' : 'Skjult (kan endres inne i portalen eller via glemt passord)';

        $search_string = [
            '[username]', '[password]',
        ];
        $replace_string = [
            $user_email, $password,
        ];
        $email_content = str_replace($search_string, $replace_string, $package->course->email);

        $encode_email = encrypt($user_email);
        $redirectLink = encrypt(route('learner.course'));
        $actionUrl = route('auth.login.emailRedirect', [$encode_email, $redirectLink]);
        $actionText = 'Mine Kurs';
        $attachments = [asset($shopController->generateDocx($user->id, $package->id)),
            asset('/email-attachments/skjema-for-opplysninger-om-angrerett.docx')];

        dispatch(new CourseOrderJob($user_email, $package->course->title, $email_content,
            'postmail@forfatterskolen.no', 'Forfatterskolen', $attachments, 'courses-taken-order',
            $courseTaken->id, $actionText, $actionUrl, $user, $package->id));
    }

    public function updateCertificateDates($course_id, Request $request): RedirectResponse
    {
        $course = Course::find($course_id);
        $course->completed_date = $request->completed_date ?: null;
        $course->issue_date = $request->issue_date ?: null;
        $course->save();

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Certificate dates updated successfully.'),
            'alert_type' => 'success']);
    }

    public function saveCertificateTemplate($course_id, Request $request): RedirectResponse
    {

        CourseCertificate::updateOrCreate([
            'course_id' => $course_id,
        ], [
            'template' => $request->template,
        ]);

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Certificate saved successfully.'),
            'alert_type' => 'success']);

    }

    public function savePackageCertificateTemplate($course_id, $package_id, Request $request): RedirectResponse
    {

        CourseCertificate::updateOrCreate([
            'course_id' => $course_id,
            'package_id' => $package_id,
        ], [
            'template' => $request->template,
        ]);

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Certificate saved successfully.'),
            'alert_type' => 'success']);
    }

    public function addCoachingTime(Request $request)
    {
        $coursesTaken = CoursesTaken::whereIn('package_id', $request->packages)->get();

        foreach($coursesTaken as $coursesTaken) {
            CoachingTimerManuscript::create([
                'user_id' => $coursesTaken->user_id,
                'file' => null,
                'plan_type' => 1
            ]);

            CoachingTimerTaken::create([
                'user_id' => $coursesTaken->user_id,
                'course_taken_id' => $coursesTaken->id,
            ]);
        }

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Coaching Time added to learners.'),
            'alert_type' => 'success']);
    }

    public function exportHiddenWebinars($course_id)
    {
        $course = Course::find($course_id);
        if ($course) {
            $headers = ['title', 'description', 'date', 'webinar id'];
            $webinars = [];

            foreach ($course->webinars()->where('status', 0)->get() as $webinar) {
                $webinars[] = [$webinar->title, $webinar->description, $webinar->start_date, $webinar->link];
            }
            $excel = \App::make('excel');

            return $excel->download(new GenericExport($webinars, $headers), 'Hidden Webinars.xlsx');
        }

        return redirect()->back();
    }

    public function exportFormerLearners()
    {
        $formerCourses = FormerCourse::join('packages', 'former_courses.package_id', '=', 'packages.id')
            ->whereIn('packages.course_id', [47, 68, 74])
            ->select('former_courses.*')
            ->get();

        $headers = ['name', 'email'];
        $learners = [];

        foreach ($formerCourses as $formerCourse) {
            $learners[] = [$formerCourse->user->full_name, $formerCourse->user->email];
        }

        $excel = \App::make('excel');

        return $excel->download(new GenericExport($learners, $headers), 'Former Learners.xlsx');
    }

    public function exportCurrentLearners()
    {
        $coursesTaken = CoursesTaken::join('packages', 'courses_taken.package_id', '=', 'packages.id')
            ->whereIn('packages.course_id', [47, 68, 74])
            ->select('courses_taken.*')
            ->get();

        $headers = ['name', 'email'];
        $learners = [];

        foreach ($coursesTaken as $courseTaken) {
            $learners[] = [$courseTaken->user->full_name, $courseTaken->user->email];
        }

        $excel = \App::make('excel');

        return $excel->download(new GenericExport($learners, $headers), 'Current Learners.xlsx');
    }

    public function applicationDetails($application_id): View
    {
        $application = CourseApplication::find($application_id);

        return view('backend.course.partials._application-details', compact('application'));
    }

    public function applicationDownload($application_id): BinaryFileResponse
    {
        $application = CourseApplication::find($application_id);

        $zipFileName = $application->user->full_name.'.zip';
        $public_dir = public_path('storage');
        $zip = new \ZipArchive;

        if ($zip->open($public_dir.'/'.$zipFileName, \ZIPARCHIVE::CREATE | \ZIPARCHIVE::OVERWRITE) !== true) {
            exit('An error occurred creating your ZIP file.');
        }

        $pdf = \App::make('dompdf.wrapper');
        $pdfContent = $pdf->loadHTML(view('frontend.pdf.course-application', compact('application')))->output();
        $zip->addFromString('application.pdf', $pdfContent);

        $filePaths = explode(', ', $application->file_path);
        foreach ($filePaths as $applicationFile) {
            $filePath = trim($applicationFile);
            // get the correct filename
            $expFileName = explode('/', $filePath);
            $file = str_replace('\\', '/', public_path());

            // physical file location and name of the file
            $zip->addFile($file.$filePath, end($expFileName));
        }

        /* //get the correct filename
        $expFileName = explode('/', $application->file_path);
        $file = str_replace('\\', '/', public_path());

        // physical file location and name of the file
        $zip->addFile($file.$application->file_path, end($expFileName)); */

        $zip->close();

        $fileToPath = $public_dir.'/'.$zipFileName;

        return response()->download($fileToPath)->deleteFileAfterSend(true);
    }

    public function applicationApprove($application_id, CourseService $courseService): RedirectResponse
    {
        $application = CourseApplication::find($application_id);
        $courseTaken = $courseService->addCourseToLearner($application->user_id, $application->package_id);
        $courseTaken->is_active = 1;
        $courseTaken->save();

        $courseService->notifyUser($application->user_id, $application->package_id, $courseTaken, true, true);
        $application->approved_date = now();
        $application->save();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Application approved.'),
            'alert_type' => 'success',
        ]);
    }

    public function applicationDelete($application_id): RedirectResponse
    {
        CourseApplication::find($application_id)->delete();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Application deleted.'),
            'alert_type' => 'success',
        ]);
    }

    public function canReceiveEmailUpdate($course_taken_id, Request $request): JsonResponse
    {
        $courseTaken = CoursesTaken::find($course_taken_id);
        $success = false;

        if ($courseTaken) {
            $courseTaken->can_receive_email = $request->can_receive_email;
            $courseTaken->save();
            $success = true;
        }

        return response()->json([
            'data' => [
                'success' => $success,
            ],
        ]);
    }

    public function inFacebookGroupUpdate($course_taken_id, Request $request): JsonResponse
    {
        $courseTaken = CoursesTaken::find($course_taken_id);
        $success = false;

        if ($courseTaken) {
            $courseTaken->in_facebook_group = $request->in_facebook_group;
            $courseTaken->save();
            $success = true;
        }

        return response()->json([
            'data' => [
                'success' => $success,
            ],
        ]);
    }

    public function excludeInScheduledRegistration($course_taken_id, Request $request): JsonResponse
    {
        $courseTaken = CoursesTaken::find($course_taken_id);
        $success = false;

        if ($courseTaken) {
            $courseTaken->exclude_in_scheduled_registration = $request->exclude_in_scheduled_registration;
            $courseTaken->save();
            $success = true;
        }

        return response()->json([
            'data' => [
                'success' => $success,
            ],
        ]);
    }

    public function certificate($course_id, $package_id)
    {
        $course = Course::find($course_id);
        $package = Package::find($package_id);
        $section = 'certificate';
        $certificate = CourseCertificate::where('course_id', $course_id)
            ->where('package_id', $package_id)->first();

        if (! $certificate) {
            $certificate = view('backend.course.partials.certificate-template')->render();
        } else {
            $certificate = $certificate->template;
        }

        return view('backend.course.certificate.form', compact('course', 'package', 'section', 'certificate'));
        // return response()->json($certificate);
    }

    /**
     * @return mixed
     */
    public function downloadCertificate($course_id)
    {
        $course = Course::find($course_id);
        $certificate = $course->certificate;
        $pdf = \App::make('dompdf.wrapper');
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
        $pdf->setPaper('letter', 'landscape');
        $pdf->loadHTML($certificate->template);

        return $pdf->download($course->title.' certificate.pdf');
    }

    public function downloadPackageCertificate($course_id, $package_id)
    {
        $course = Course::find($course_id);
        $certificate = CourseCertificate::where([
            'course_id' => $course_id,
            'package_id' => $package_id,
        ])->first();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
        $pdf->setPaper('letter', 'landscape');
        $pdf->loadHTML($certificate->template);
        
        /* return response($certificate->template)
        ->header('Content-Type', 'text/html'); */

        return $pdf->download($course->title.' certificate.pdf');
    }

    public function getAllPaidLearners()
    {
        $courses = Course::where('is_free', 0)->get()->pluck('id')->toArray();
        $packages = Package::whereIn('course_id', $courses)->get()->pluck('id')->toArray();

        $coursesTaken = CoursesTaken::whereYear('created_at', 2021)
            // ->where('is_free', 0)
            ->whereIn('package_id', $packages)
            ->get();

        $learnerList = [];
        $headers = ['learner', 'email', 'course']; // first row in excel

        foreach ($coursesTaken as $courseTaken) {
            $learnerList[] = [$courseTaken->user->full_name, $courseTaken->user->email, $courseTaken->package->course->title];
        }

        $excel = \App::make('excel');

        return $excel->download(new GenericExport($learnerList, $headers), 'Course Buyers 2021.xlsx');
    }

    public function allUpcomingWebinars(): View
    {
        $webinars = Webinar::where('start_date', '>=', now()->format('Y-m-d H:i:s'))
            ->oldest('start_date')
            ->paginate(20);

        return view('backend.course.webinars.upcoming', compact('webinars'));
    }

    public function exportCoursesWithNoCertificate()
    {
        $courses = Course::whereNotIn('id', function ($query) {
            $query->select('course_id')
                ->from('course_certificates');
        })->get();

        $courseList = [];
        $headers = ['id', 'title'];

        foreach ($courses as $course) {
            $courseList[] = [$course->id, $course->title];
        }

        $excel = \App::make('excel');

        return $excel->download(new GenericExport($courseList, $headers), 'Course without Certificate.xlsx');
    }

    public function copyPackageLearners(Request $request)
    {
        $coursesTaken = CoursesTaken::where('package_id', $request->from_package)->get();
        $toPackage = Package::findOrFail($request->to_package);
        
        foreach ($coursesTaken as $course) {
            $newCourse = $course->replicate(); // clone all attributes except primary key
            $newCourse->package_id = $request->to_package; // change package_id
            $newCourse->save();
        }

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Learners copied to ' 
                . $toPackage->course->title . ' - ' . $toPackage->variation . '.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }

    public function importPackageLearners(Request $request)
    {
        $fromPackage = Package::findOrFail($request->from_package);
        $coursesTaken = CoursesTaken::where('package_id', $request->from_package)->get();
        
        foreach ($coursesTaken as $course) {
            $newCourse = $course->replicate(); // clone all attributes except primary key
            $newCourse->package_id = $request->to_package;
            $newCourse->save();
        }

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Learners copied from ' 
                . $fromPackage->course->title . ' - ' . $fromPackage->variation . '.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }

    public function copyPackageAndLearners(Request $request)
    {
        $package = Package::findOrFail($request->from_package);
        $course = Course::findOrFail($request->course_id);
        $coursesTaken = CoursesTaken::where('package_id', $request->from_package)->get();

        // copy package and change the course_id
        $newPackage = $package->replicate();
        $newPackage->course_id = $request->course_id;
        $newPackage->is_standard = 0;
        $newPackage->save();

        // copy package learners and change to new package
        foreach ($coursesTaken as $courseTaken) {
            $newCourse = $courseTaken->replicate();
            $newCourse->package_id = $newPackage->id;
            $newCourse->save();
        }

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Package and Learners copied to ' 
                . $course->title . ' - ' . $newPackage->variation . '.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }
}
