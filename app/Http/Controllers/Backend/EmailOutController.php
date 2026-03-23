<?php

namespace App\Http\Controllers\Backend;

use App\Assignment;
use App\Course;
use App\CoursesTaken;
use App\EmailAttachment;
use App\EmailOut;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Http\FrontendHelpers;
use App\Jobs\AddMailToQueueJob;
use App\Lesson;
use App\Mail\SubjectBodyEmail;
use App\Order;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

class EmailOutController extends Controller
{
    /**
     * Create new email out
     */
    public function store($course_id, Request $request): RedirectResponse
    {
        $course = Course::find($course_id);

        if (! $course) {
            return redirect()->back();
        }

        $request->validate([
            'subject' => 'required',
            'message' => 'required',
            'delay' => 'required',
        ]);

        if ($request->has('for_free_course') &&
            $course->emailOut()->where('for_free_course', 1)->count() > 0) {
            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Only one email out for free course allowed.'),
            ]);
        }

        $data = $request->except('_token');
        $data['course_id'] = $course_id;
        $data['for_free_course'] = $request->has('for_free_course') ? 1 : 0;
        $data['send_immediately'] = boolval($request->has('send_immediately'));
        $data['send_to_learners_no_course'] = boolval($request->has('send_to_learners_no_course'));
        $data['send_to_learners_with_unpaid_pay_later'] = boolval($request->has('send_to_learners_with_unpaid_pay_later'));
        $data['include_former_learners'] = boolval($request->has('include_former_learners'));
        $data['exclude_free_manuscript_learners'] = boolval($request->has('exclude_free_manuscript_learners'));
        $data['allowed_package'] = isset($request->allowed_package) ? json_encode($request->allowed_package) : null;

        if ($request->hasFile('attachment')) {
            $destinationPath = 'storage/course-email-out-attachments'; // upload path

            if (! \File::exists($destinationPath)) {
                \File::makeDirectory($destinationPath);
            }

            $extension = $request->attachment->extension(); // getting image extension
            $uploadedFile = $request->attachment->getClientOriginalName();
            $actual_name = pathinfo($uploadedFile, PATHINFO_FILENAME);
            // remove spaces to avoid error on attachment
            $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension); // rename document
            $request->attachment->move($destinationPath, $fileName);

            $data['attachment'] = '/'.$fileName;

            $emailAttach['filename'] = $data['attachment'];
            $emailAttach['hash'] = substr(md5(microtime()), 0, 6);
            $emailAttachment = EmailAttachment::create($emailAttach);
            $data['attachment_hash'] = $emailAttachment->hash;
        }

        EmailOut::create($data);
        $totalSent = 0;

        $notif = AdminHelpers::createMessageBag('Email out created successfully.');
        if ($request->send_to_learners_no_course) {
            $excludeFreeManuscriptLearners = false;

            $users = $this->getNonPayingLearners($excludeFreeManuscriptLearners);
            //$totalSent += $this->sendCustomEmailToUsers($users, $request);
        }

        if ($request->send_to_learners_with_unpaid_pay_later) {
            $users = $this->getUnpaidPayLaterLearners($course_id);
            //$totalSent += $this->sendCustomEmailToUsers($users, $request);
        }

        if ($request->send_to) {
            $subject = $request->subject;
            $from = 'post@forfatterskolen.no';
            $to = $request->send_to;
            $content = $request->message;
            $messageBag = new MessageBag;
            $messageBag->add('errors', 'Email out updated successfully.');
            $messageBag->add('errors', 'Email sent to '.$to);
            $notif = $messageBag;

            $encode_email = encrypt($to);

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
                $content = str_replace($search_string, $replace_string, $request->message);
            }

            // AdminHelpers::send_email($subject, $from, $to, $content);
            $emailData = [
                'email_subject' => $subject,
                'email_message' => $content,
                'from_name' => '',
                'from_email' => $from,
                'attach_file' => null,
            ];
            \Mail::to($to)->queue(new SubjectBodyEmail($emailData));
        }

        if ($totalSent) {
            $notif = AdminHelpers::createMessageBag("Email out created successfully.");
        }

        return redirect()->back()->with([
            'errors' => $notif,
            'alert_type' => 'success',
        ]);
    }

    /**
     * Update email out record
     */
    public function update($course_id, $id, Request $request): RedirectResponse
    {
        $course = Course::find($course_id);
        $email_out = EmailOut::find($id);

        if (! $course || ! $email_out) {
            return redirect()->back();
        }

        $request->validate([
            'subject' => 'required',
            'message' => 'required',
            'delay' => 'required',
        ]);

        $checkEmailOut = $course->emailOut()->where('for_free_course', 1)->first();

        if ($request->has('for_free_course') && $checkEmailOut && $checkEmailOut->id !== (int) $id) {
            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Only one email out for free course allowed.'),
            ]);
        }

        $data = $request->except('_token');
        $data['course_id'] = $course_id;
        $data['for_free_course'] = $request->has('for_free_course') ? 1 : 0;
        $data['send_immediately'] = boolval($request->has('send_immediately'));
        $data['send_to_learners_no_course'] = boolval($request->has('send_to_learners_no_course'));
        $data['send_to_learners_with_unpaid_pay_later'] = boolval($request->has('send_to_learners_with_unpaid_pay_later'));
        $data['include_former_learners'] = boolval($request->has('include_former_learners'));
        $data['exclude_free_manuscript_learners'] = boolval($request->has('exclude_free_manuscript_learners'));
        $data['allowed_package'] = isset($request->allowed_package) ? json_encode($request->allowed_package) : null;

        if ($request->hasFile('attachment')) {
            $destinationPath = 'storage/course-email-out-attachments'; // upload path

            if (! \File::exists($destinationPath)) {
                \File::makeDirectory($destinationPath);
            }

            $extension = $request->attachment->extension(); // getting image extension
            $uploadedFile = $request->attachment->getClientOriginalName();
            $actual_name = pathinfo($uploadedFile, PATHINFO_FILENAME);
            // remove spaces to avoid error on attachment
            $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension); // rename document
            $request->attachment->move($destinationPath, $fileName);

            $data['attachment'] = '/'.$fileName;

            $emailAttach['filename'] = $data['attachment'];
            $emailAttach['hash'] = substr(md5(microtime()), 0, 6);
            $emailAttachment = EmailAttachment::create($emailAttach);
            $data['attachment_hash'] = $emailAttachment->hash;
        }

        $email_out->update($data);
        $email_out->save();

        $notif = AdminHelpers::createMessageBag('Email out updated successfully.');
        if ($request->send_to_learners_no_course) {
            $excludeFreeManuscriptLearners = false;
            if ($id == 2666) {
                $excludeFreeManuscriptLearners = true;
            }

            /* $users = $this->getNonPayingLearners($excludeFreeManuscriptLearners);

            $userCounter = 0;
            foreach ($users as $user) {
                $subject = $email_out->subject;
                $from = 'post@forfatterskolen.no';
                $to = $user->email;
                $content = $email_out->message;

                $encode_email = encrypt($to);
                if (strpos($request->message, '[redirect]')) {
                    $extractLink = FrontendHelpers::getTextBetween($request->message, '[redirect]', '[/redirect]');
                    $formatRedirectLink = route('auth.login.emailRedirect', [$encode_email, encrypt($extractLink)]);
                    $redirectLabel = FrontendHelpers::getTextBetween($request->message, '[redirect_label]',
                        '[/redirect_label]');
                    $redirectLink = "<a href='".$formatRedirectLink."'>".$redirectLabel.'</a>';
                    $search_string = [
                        '[redirect]'.$extractLink.'[/redirect]', '[redirect_label]'.$redirectLabel.'[/redirect_label]',
                    ];
                    $replace_string = [
                        $redirectLink, '',
                    ];
                    $content = str_replace($search_string, $replace_string, $request->message);
                }

                $emailData = [
                    'email_subject' => $subject,
                    'email_message' => $content,
                    'from_name' => '',
                    'from_email' => $from,
                    'attach_file' => null,
                ];
                \Mail::to($to)->queue(new SubjectBodyEmail($emailData));

                $userCounter++;
            } */

            $notif = AdminHelpers::createMessageBag('Email out updated successfully. ');
        }

        if ($request->send_to) {
            $subject = $email_out->subject;
            $from = 'post@forfatterskolen.no';
            $to = $request->send_to;
            $content = $email_out->message;
            $messageBag = new MessageBag;
            $messageBag->add('errors', 'Email out updated successfully.');
            $messageBag->add('errors', 'Email sent to '.$to);
            $notif = $messageBag;

            $encode_email = encrypt($to);
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
                $content = str_replace($search_string, $replace_string, $request->message);
            }

            /* AdminHelpers::send_email($subject, $from, $to, $content); */
            $emailData = [
                'email_subject' => $subject,
                'email_message' => $content,
                'from_name' => '',
                'from_email' => $from,
                'attach_file' => null,
            ];
            \Mail::to($to)->queue(new SubjectBodyEmail($emailData));
        }

        return redirect()->back()->with([
            'errors' => $notif,
            'alert_type' => 'success',
        ]);
    }

    /**
     * Delete email out record
     */
    public function destroy($course_id, $id): RedirectResponse
    {
        $course = Course::find($course_id);
        $email_out = EmailOut::find($id);

        if (! $course || ! $email_out) {
            return redirect()->back();
        }

        $email_out->delete();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Email out deleted successfully.'),
            'alert_type' => 'success',
        ]);
    }

    public function sendEmailToLearners($course_id, $id): RedirectResponse
    {
        $emailOut = EmailOut::find($id);
        $packages = $emailOut->allowed_package ? json_decode($emailOut->allowed_package) :
            $emailOut->course->packages->pluck('id')->toArray();
        $emailRecipients = $emailOut->recipients->pluck('user_id')->toArray();
        $coursesTaken = CoursesTaken::whereHas('user')->whereIn('package_id', $packages)
            ->whereNull('renewed_at')
            ->whereNotIn('user_id', $emailRecipients)
            ->where('can_receive_email', 1)
            ->get();

        $emailAttachment = EmailAttachment::where('hash', $emailOut->attachment_hash)->first();
        $attachmentText = '';
        if ($emailAttachment) {
            $attachmentText = "<p style='margin-top: 10px'><b>Vedlegg:</b> 
<a href='".route('front.email-attachment', $emailAttachment->hash)."'>"
                .AdminHelpers::extractFileName($emailAttachment->filename).'</a></p>';
        }

        // loop the result and send email
        foreach ($coursesTaken as $courseTaken) {
            $toMail = $courseTaken->user->email;

            $encode_email = encrypt($courseTaken->user->email);
            $user = $courseTaken->user;
            $loginLink = "<a href='".route('auth.login.email', $encode_email)."'>Klikk her for å logge inn</a>";
            $password = $user->need_pass_update ? 'Z5C5E5M2jv' : 'Skjult (kan endres inne i portalen eller via glemt passord)';
            if (strpos($emailOut->message, '[redirect]')) {
                $extractLink = FrontendHelpers::getTextBetween($emailOut->message, '[redirect]', '[/redirect]');
                $formatRedirectLink = route('auth.login.emailRedirect', [$encode_email, encrypt($extractLink)]);
                $redirectLabel = FrontendHelpers::getTextBetween($emailOut->message, '[redirect_label]', '[/redirect_label]');
                $redirectLink = "<a href='".$formatRedirectLink."'>".$redirectLabel.'</a>';
                $search_string = [
                    '[redirect]'.$extractLink.'[/redirect]', '[redirect_label]'.$redirectLabel.'[/redirect_label]',
                ];
                $replace_string = [
                    $redirectLink, '',
                ];
                $message = str_replace($search_string, $replace_string, $emailOut->message);
            } else {
                $search_string = [
                    '[login_link]', '[username]', '[password]',
                ];
                $replace_string = [
                    $loginLink, $courseTaken->user->email, $password,
                ];
                $message = str_replace($search_string, $replace_string, $emailOut->message);
            }

            $emailData['email_subject'] = $emailOut->subject;
            $emailData['email_message'] = $message.$attachmentText;
            $emailData['from_name'] = $emailOut->from_name;
            $emailData['from_email'] = $emailOut->from_email;
            $emailData['attach_file'] = null;

            if (!$user->is_disabled) {
                // add email to queue
                dispatch(new AddMailToQueueJob($toMail, $emailOut->subject, $message.$attachmentText,
                    $emailOut->from_email, $emailOut->from_name, null, 'courses-taken', $courseTaken->id));

                $emailOut->recipients()->updateOrCreate([
                    'user_id' => $user->id,
                ]);
            }

        }

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Email out sent successfully.'),
            'alert_type' => 'success',
        ]);
    }

    /**
     * Preview en branded e-post i nettleseren
     */
    public function previewBranded($course_id, $id)
    {
        $emailOut = EmailOut::find($id);
        if (!$emailOut) {
            return 'E-post ikke funnet.';
        }

        $course = Course::find($course_id);
        $user = new User([
            'first_name' => 'Ola',
            'last_name' => 'Nordmann',
            'email' => 'ola@eksempel.no',
        ]);

        // Branded template
        if ($emailOut->template_type) {
            $mail = new \App\Mail\BrandedCourseMail($emailOut, $user, $course);
            return $mail->render();
        }

        // Legacy: vis i branded layout med demo-variabler
        $message = $emailOut->message;
        $message = str_replace([':firstname', ':name'], ['Ola', 'Ola Nordmann'], $message);
        $message = str_replace(':redirect_link', '<a href="#" style="display:inline-block;padding:14px 32px;background-color:#862736;color:#ffffff;text-decoration:none;border-radius:6px;font-weight:600;">', $message);
        $message = str_replace(':end_redirect_link', '</a>', $message);

        return view('emails.mail_to_queue', [
            'email_message' => $message,
            'track_code' => 'preview-' . $id,
        ])->render();
    }

    /**
     * Preview alle 8 branded system-maler (ikke Email Out)
     */
    public function previewSystemMail($template)
    {
        $demoData = [
            'order-confirmation' => [
                'view' => 'emails.branded.order-confirmation',
                'data' => [
                    'orderNumber' => 'FS-2026-001',
                    'firstName' => 'Ola',
                    'courseName' => 'Romankurset',
                    'packageName' => 'Premium',
                    'courseStartDate' => '20. april 2026',
                    'totalAmount' => '8 900',
                    'portalUrl' => config('app.url') . '/learner/dashboard',
                ],
            ],
            'invoice' => [
                'view' => 'emails.branded.invoice',
                'data' => [
                    'rateNumber' => 2,
                    'totalRates' => 3,
                    'dueDate' => '15. mai 2026',
                    'amount' => '2 967',
                    'firstName' => 'Ola',
                    'courseName' => 'Romankurset',
                    'orderNumber' => 'FS-2026-001',
                    'remaining' => '2 967',
                    'payUrl' => config('app.url') . '/learner/invoices',
                ],
            ],
            'magic-link' => [
                'view' => 'emails.branded.magic-link',
                'data' => [
                    'loginUrl' => config('app.url') . '/login/demo',
                ],
            ],
            'welcome' => [
                'view' => 'emails.branded.welcome',
                'data' => [
                    'firstName' => 'Ola',
                ],
            ],
            'manuscript-received' => [
                'view' => 'emails.branded.manuscript-received',
                'data' => [
                    'filename' => 'mitt-manus.docx',
                    'wordCount' => '45 000',
                    'genre' => 'Roman',
                    'expectedDelivery' => '15. juni 2026',
                ],
            ],
            'feedback-ready' => [
                'view' => 'emails.branded.feedback-ready',
                'data' => [
                    'firstName' => 'Ola',
                    'feedbackUrl' => config('app.url') . '/learner/manuscripts',
                ],
            ],
            'webinar-registration' => [
                'view' => 'emails.branded.webinar-registration',
                'data' => [
                    'webinarTitle' => 'Skriv din første roman',
                    'webinarDay' => '25',
                    'webinarMonth' => 'Januar',
                    'webinarTime' => '20:00',
                    'webinarDayName' => 'Søndag',
                    'webinarDescription' => 'Lær hvordan du kommer i gang med din første roman.',
                    'calendarUrl' => '#',
                ],
            ],
            'course-start-reminder' => [
                'view' => 'emails.branded.course-start-reminder',
                'data' => [
                    'courseName' => 'Romankurset',
                    'courseStartDate' => '20. april 2026',
                    'firstName' => 'Ola',
                ],
            ],
            'module-available' => [
                'view' => 'emails.branded.module-available',
                'data' => [
                    'lessonOrder' => 3,
                    'lessonTitle' => 'Plott og struktur',
                    'courseName' => 'Romankurset',
                    'firstName' => 'Ola',
                    'lessonDescription' => 'I denne modulen lærer du om plottstruktur, spenningskurve og hvordan du holder leseren engasjert.',
                    'hasAssignment' => true,
                    'progressPercent' => 37,
                    'totalLessons' => 8,
                    'portalUrl' => config('app.url') . '/learner/dashboard',
                ],
            ],
            'assignment-available' => [
                'view' => 'emails.branded.assignment-available',
                'data' => [
                    'assignmentTitle' => 'Første innlevering',
                    'courseName' => 'Romankurset',
                    'firstName' => 'Ola',
                    'submissionDate' => '05.05.2026',
                    'assignmentDescription' => 'Lever de første 10 sidene av manuset ditt.',
                    'portalUrl' => config('app.url') . '/learner/dashboard',
                ],
            ],
            'assignment-reminder' => [
                'view' => 'emails.branded.assignment-reminder',
                'data' => [
                    'assignmentTitle' => 'Første innlevering',
                    'courseName' => 'Romankurset',
                    'firstName' => 'Ola',
                    'submissionDate' => '05.05.2026',
                    'portalUrl' => config('app.url') . '/learner/dashboard',
                ],
            ],
            'assignment-deadline' => [
                'view' => 'emails.branded.assignment-deadline',
                'data' => [
                    'assignmentTitle' => 'Første innlevering',
                    'courseName' => 'Romankurset',
                    'firstName' => 'Ola',
                    'submissionDate' => '05.05.2026',
                    'portalUrl' => config('app.url') . '/learner/dashboard',
                ],
            ],
            'weekly-update' => [
                'view' => 'emails.branded.weekly-update',
                'data' => [
                    'weekNumber' => 3,
                    'courseName' => 'Romankurset',
                    'firstName' => 'Ola',
                    'weekModules' => [
                        ['order' => 3, 'title' => 'Plott og struktur', 'description' => 'I denne modulen lærer du om plottstruktur og spenningskurve.'],
                    ],
                    'weekAssignments' => [
                        ['title' => 'Første innlevering', 'type' => 'deadline', 'deadline' => '05.05.2026'],
                    ],
                    'quote' => ['text' => 'Skriv det du vil lese.', 'author' => 'Toni Morrison'],
                    'portalUrl' => config('app.url') . '/learner/dashboard',
                ],
            ],
        ];

        if (!isset($demoData[$template])) {
            return 'Ukjent mal: ' . $template . '. Tilgjengelige: ' . implode(', ', array_keys($demoData));
        }

        $config = $demoData[$template];
        return view($config['view'], $config['data']);
    }

    /**
     * Auto-generer branded e-poster for alle moduler og oppgaver i kurset
     */
    public function autoGenerate($course_id): RedirectResponse
    {
        $course = Course::find($course_id);
        if (!$course) {
            return redirect()->back();
        }

        $created = 0;

        // Velkomst-e-post (send umiddelbart ved kjøp)
        $welcomeEmail = EmailOut::firstOrCreate(
            [
                'course_id' => $course_id,
                'template_type' => 'welcome',
            ],
            [
                'subject' => 'Velkommen til ' . $course->title . '!',
                'message' => '',
                'delay' => 0,
                'from_name' => 'Forfatterskolen',
                'from_email' => 'post@forfatterskolen.no',
                'send_immediately' => 1,
                'auto_generated' => true,
                'status' => 'active',
                'template_data' => [
                    'courseName' => $course->title,
                ],
            ]
        );
        if ($welcomeEmail->wasRecentlyCreated) $created++;

        $excludeTitles = ['Kursplan', 'Repriser'];
        $lessons = $course->lessons()->orderBy('order', 'asc')
            ->whereNotIn('title', $excludeTitles)
            ->get();
        $startDate = $course->start_date ? Carbon::parse($course->start_date) : null;

        // Grupper moduler etter delay-dato (samme dato = én e-post)
        $lessonsByDate = [];
        $lessonOrder = 0;
        foreach ($lessons as $lesson) {
            $lessonOrder++;
            $delayValue = $this->calculateLessonDelay($lesson, $startDate);
            $lessonsByDate[$delayValue][] = [
                'lesson' => $lesson,
                'order' => $lessonOrder,
            ];
        }

        // Hent oppgaver for kurset
        $assignments = Assignment::where('course_id', $course_id)
            ->where(function ($q) {
                $q->whereNull('parent')->orWhere('parent', 'course');
            })
            ->orderBy('available_date', 'asc')
            ->get();

        // Hent webinarer for kurset
        $webinars = \App\Webinar::where('course_id', $course_id)
            ->where('status', 1)
            ->orderBy('start_date', 'asc')
            ->get();

        // Generer ukentlige oppdateringer (én samlet e-post per uke med alt innhold)
        if ($startDate) {
            $created += $this->generateWeeklyUpdates($course, $lessons, $assignments, $lessonsByDate, $startDate, $webinars);
        }

        // Generer påminnelser og frister (separate e-poster)
        $existingAssignmentEmails = EmailOut::where('course_id', $course_id)
            ->whereIn('template_type', ['assignment_reminder', 'assignment_deadline'])
            ->where('auto_generated', true)
            ->get()
            ->groupBy('template_type');

        foreach ($assignments as $assignment) {
            $submissionDate = $assignment->getRawOriginal('submission_date');
            $allowedPackage = $assignment->getRawOriginal('allowed_package');

            // Påminnelse 3 dager før frist
            if ($submissionDate && !is_numeric($submissionDate)) {
                $reminderDate = Carbon::parse($submissionDate)->subDays(3)->format('Y-m-d');

                $existsReminder = ($existingAssignmentEmails->get('assignment_reminder') ?? collect())
                    ->contains(fn($e) => ($e->template_data['assignmentId'] ?? null) == $assignment->id);

                if (!$existsReminder) {
                    EmailOut::create([
                        'course_id' => $course_id,
                        'template_type' => 'assignment_reminder',
                        'subject' => 'Påminnelse: ' . $assignment->title . ' — frist om 3 dager',
                        'message' => '',
                        'delay' => $reminderDate,
                        'from_name' => 'Forfatterskolen',
                        'from_email' => 'post@forfatterskolen.no',
                        'auto_generated' => true,
                        'status' => 'active',
                        'allowed_package' => $allowedPackage,
                        'template_data' => [
                            'assignmentId' => $assignment->id,
                            'assignmentTitle' => $assignment->title,
                            'submissionDate' => Carbon::parse($submissionDate)->format('d.m.Y'),
                        ],
                    ]);
                    $created++;
                }

                // Frist-dag e-post
                $deadlineDate = Carbon::parse($submissionDate)->format('Y-m-d');

                $existsDeadline = ($existingAssignmentEmails->get('assignment_deadline') ?? collect())
                    ->contains(fn($e) => ($e->template_data['assignmentId'] ?? null) == $assignment->id);

                if (!$existsDeadline) {
                    EmailOut::create([
                        'course_id' => $course_id,
                        'template_type' => 'assignment_deadline',
                        'subject' => 'Siste frist i dag: ' . $assignment->title,
                        'message' => '',
                        'delay' => $deadlineDate,
                        'from_name' => 'Forfatterskolen',
                        'from_email' => 'post@forfatterskolen.no',
                        'auto_generated' => true,
                        'status' => 'active',
                        'allowed_package' => $allowedPackage,
                        'template_data' => [
                            'assignmentId' => $assignment->id,
                            'assignmentTitle' => $assignment->title,
                            'submissionDate' => Carbon::parse($submissionDate)->format('d.m.Y'),
                        ],
                    ]);
                    $created++;
                }
            }
        }

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag($created . ' e-poster ble auto-generert.'),
            'alert_type' => 'success',
        ]);
    }

    /**
     * Beregn delay-verdi for en leksjon basert på delay/period og kurs start_date
     */
    protected function calculateLessonDelay(Lesson $lesson, ?Carbon $startDate): string
    {
        // Hvis lesson har en numerisk delay (antall dager), bruk det direkte
        if (is_numeric($lesson->delay)) {
            if ($startDate) {
                return $startDate->copy()->addDays((int) $lesson->delay)->format('Y-m-d');
            }
            return (string) $lesson->delay;
        }

        // Hvis lesson delay er en dato-string
        if ($lesson->delay && strpos($lesson->delay, '-') !== false) {
            return $lesson->delay;
        }

        // Fallback: bruk period som dager
        if (is_numeric($lesson->period) && $startDate) {
            return $startDate->copy()->addDays((int) $lesson->period)->format('Y-m-d');
        }

        // Standard fallback
        return $startDate ? $startDate->format('Y-m-d') : '0';
    }

    /**
     * Generer ukentlige oppdateringer fra Kristine (én per mandag fra kursstart)
     */
    protected function generateWeeklyUpdates(Course $course, $lessons, $assignments, array $lessonsByDate, Carbon $startDate, $webinars = null): int
    {
        $created = 0;

        // Finn siste modul-dato (ikke oppgaver — de kan gå lenge etter kursinnhold)
        $lastDate = $startDate->copy();
        foreach (array_keys($lessonsByDate) as $dateStr) {
            try {
                $d = Carbon::parse($dateStr);
                if ($d->gt($lastDate)) $lastDate = $d->copy();
            } catch (\Exception $e) {}
        }

        // Legg til 1 uke etter siste modul
        $endDate = $lastDate->copy()->addWeek();

        // Bygg oppslag: dato -> moduler og oppgaver for rask lookup
        $modulesByDate = [];
        $lessonOrder = 0;
        foreach ($lessons as $lesson) {
            $lessonOrder++;
            $delayValue = $this->calculateLessonDelay($lesson, $startDate);
            $modulesByDate[$delayValue][] = [
                'order' => $lessonOrder,
                'title' => $lesson->title,
                'description' => $lesson->description_simplemde ?? '',
            ];
        }

        $assignmentsByDate = [];
        foreach ($assignments as $assignment) {
            $avail = $assignment->getRawOriginal('available_date');
            if ($avail && !is_numeric($avail)) {
                $assignmentsByDate[$avail][] = [
                    'title' => $assignment->title,
                    'type' => 'available',
                ];
            }
            $sub = $assignment->getRawOriginal('submission_date');
            if ($sub && !is_numeric($sub)) {
                $assignmentsByDate[$sub][] = [
                    'title' => $assignment->title,
                    'type' => 'deadline',
                    'deadline' => Carbon::parse($sub)->format('d.m.Y'),
                ];
            }
        }

        // Bygg oppslag: webinarer etter dato
        $webinarsByDate = [];
        if ($webinars) {
            foreach ($webinars as $webinar) {
                $wDate = Carbon::parse($webinar->start_date)->format('Y-m-d');
                $webinarsByDate[$wDate][] = [
                    'title' => $webinar->title,
                    'host' => $webinar->host ?? '',
                    'startTime' => Carbon::parse($webinar->start_date)->format('d.m.Y H:i'),
                    'link' => $webinar->link ?? '',
                ];
            }
        }

        // Sitater for ukebrev
        $quotes = [
            ['text' => 'Skriv det du vil lese.', 'author' => 'Toni Morrison'],
            ['text' => 'Det finnes ingen regler. Det er slik det er mulig.', 'author' => 'Virginia Woolf'],
            ['text' => 'Du trenger ikke se hele trappen, bare ta det forste steget.', 'author' => 'Martin Luther King Jr.'],
            ['text' => 'Start der du er. Bruk det du har. Gjor det du kan.', 'author' => 'Arthur Ashe'],
            ['text' => 'En forfatter er en som skriver.', 'author' => 'Anne Enright'],
            ['text' => 'Skriv hardt og klart om det som gjor vondt.', 'author' => 'Ernest Hemingway'],
            ['text' => 'Du kan alltid redigere en darlig side. Du kan ikke redigere en blank side.', 'author' => 'Jodi Picoult'],
            ['text' => 'Inspirasjonen finnes, men den ma finne deg i arbeid.', 'author' => 'Pablo Picasso'],
        ];

        // Start på første mandag fra kursstart
        $monday = $startDate->copy()->startOfWeek(Carbon::MONDAY);
        if ($monday->lt($startDate)) {
            $monday->addWeek();
        }

        $weekNumber = 1;
        while ($monday->lte($endDate)) {
            $weekStart = $monday->copy();
            $weekEnd = $monday->copy()->endOfWeek(Carbon::SUNDAY);

            // Finn moduler, oppgaver og webinarer som faller i denne uken
            $weekModules = [];
            $weekAssignments = [];
            $weekWebinars = [];

            foreach ($modulesByDate as $dateStr => $mods) {
                try {
                    $d = Carbon::parse($dateStr);
                    if ($d->gte($weekStart) && $d->lte($weekEnd)) {
                        $weekModules = array_merge($weekModules, $mods);
                    }
                } catch (\Exception $e) {}
            }

            foreach ($assignmentsByDate as $dateStr => $assigns) {
                try {
                    $d = Carbon::parse($dateStr);
                    if ($d->gte($weekStart) && $d->lte($weekEnd)) {
                        $weekAssignments = array_merge($weekAssignments, $assigns);
                    }
                } catch (\Exception $e) {}
            }

            foreach ($webinarsByDate as $dateStr => $webs) {
                try {
                    $d = Carbon::parse($dateStr);
                    if ($d->gte($weekStart) && $d->lte($weekEnd)) {
                        $weekWebinars = array_merge($weekWebinars, $webs);
                    }
                } catch (\Exception $e) {}
            }

            $quote = $quotes[$weekNumber % count($quotes)];

            $subject = 'Uke ' . $weekNumber . ': ' . $course->title;
            if (!empty($weekModules)) {
                $modTitles = array_column($weekModules, 'title');
                $subject = 'Uke ' . $weekNumber . ': ' . implode(' og ', array_slice($modTitles, 0, 2));
                if (count($modTitles) > 2) $subject .= ' m.fl.';
            }

            // Mentormøte-tips: inkluder i ukebrev at elever kan booke mentortime
            $hasMentorInfo = ($weekNumber >= 2); // Fra uke 2 og utover

            $emailOut = EmailOut::firstOrCreate(
                [
                    'course_id' => $course->id,
                    'template_type' => 'weekly_update',
                    'delay' => $weekStart->format('Y-m-d'),
                ],
                [
                    'subject' => $subject,
                    'message' => '',
                    'delay' => $weekStart->format('Y-m-d'),
                    'from_name' => 'Kristine S. Henningsen',
                    'from_email' => 'post@forfatterskolen.no',
                    'auto_generated' => true,
                    'status' => 'active',
                    'template_data' => [
                        'weekNumber' => $weekNumber,
                        'weekModules' => $weekModules,
                        'weekAssignments' => $weekAssignments,
                        'weekWebinars' => $weekWebinars,
                        'hasMentorInfo' => $hasMentorInfo,
                        'quote' => $quote,
                    ],
                ]
            );
            if ($emailOut->wasRecentlyCreated) $created++;

            $monday->addWeek();
            $weekNumber++;
        }

        return $created;
    }

    public function getNonPayingLearners($excludeFreeManuscriptLearners = false)
    {
        $users = User::doesntHave('coursesTakenNotOld')
            ->doesntHave('shopManuscriptsTaken')
            ->doesntHave('coachingTimers')
            ->doesntHave('invoices');

        if ($excludeFreeManuscriptLearners) {
            $users->whereNotIn('email', function ($query) {
                $query->select('email')->from('free_manuscripts');
            });
        }

        return $users->whereNull('notes')->get();
    }

    protected function getUnpaidPayLaterLearners($course_id)
    {
        $packageIds = Course::find($course_id)->packages()->pluck('id');
        $userIds = Order::whereIn('package_id', $packageIds)
            ->where([
                'is_processed' => 1,
                'is_pay_later' => 1,
                'is_invoice_sent' => 0,
                'is_order_withdrawn' => 0,
            ])
            ->pluck('user_id');

        return User::whereIn('id', $userIds)->get();
    }

    protected function sendCustomEmailToUsers($users, $request)
    {
        $count = 0;
        foreach ($users as $user) {
            $to = $user->email;
            $content = $this->buildRedirectContent($request->message, $to);
            $emailData = [
                'email_subject' => $request->subject,
                'email_message' => $content,
                'from_name' => '',
                'from_email' => 'post@forfatterskolen.no',
                'attach_file' => null,
            ];
            \Mail::to($to)->queue(new SubjectBodyEmail($emailData));
            $count++;
        }
        return $count;
    }

    protected function buildRedirectContent($message, $email)
    {
        if (!str_contains($message, '[redirect]')) return $message;

        $encode = encrypt($email);
        $link = FrontendHelpers::getTextBetween($message, '[redirect]', '[/redirect]');
        $label = FrontendHelpers::getTextBetween($message, '[redirect_label]', '[/redirect_label]');
        $redirect = "<a href='" . route('auth.login.emailRedirect', [$encode, encrypt($link)]) . "'>$label</a>";

        $search_string = [
            '[redirect]'.$link.'[/redirect]', '[redirect_label]'.$label.'[/redirect_label]',
        ];
        $replace_string = [
            $redirect, '',
        ];

        return str_replace($search_string, $replace_string, $message);
    }

    public function previewWeeklyDigest($user_id = null)
    {
        $user = $user_id ? User::findOrFail($user_id) : auth()->user();
        $mailable = new \App\Mail\WeeklyDigestMail($user);
        $data = $mailable->buildDigestData();

        if (empty($data)) {
            return 'Ingen innhold å vise for denne brukeren denne uken. (Ingen aktive kurs, webinarer, moduler eller frister.)';
        }

        return view('emails.branded.weekly-digest', $data);
    }

    public function bulkAction($course_id, Request $request): RedirectResponse
    {
        $emailIds = $request->input('email_ids', []);
        $action = $request->input('action');

        if (empty($emailIds) || !$action) {
            return redirect()->back();
        }

        $count = EmailOut::where('course_id', $course_id)
            ->whereIn('id', $emailIds)
            ->delete();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag($count . ' e-poster ble slettet.'),
            'alert_type' => 'success',
        ]);
    }

}
