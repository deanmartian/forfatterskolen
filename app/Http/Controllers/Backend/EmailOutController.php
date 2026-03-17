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
        if (!$emailOut || !$emailOut->template_type) {
            return 'Ingen branded mal for denne e-posten.';
        }

        $course = Course::find($course_id);
        $user = new User([
            'first_name' => 'Ola',
            'last_name' => 'Nordmann',
            'email' => 'ola@eksempel.no',
        ]);

        $mail = new \App\Mail\BrandedCourseMail($emailOut, $user, $course);
        return $mail->render();
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
        $excludeTitles = ['Kursplan', 'Repriser'];
        $lessons = $course->lessons()->orderBy('order', 'asc')
            ->whereNotIn('title', $excludeTitles)
            ->get();
        $startDate = $course->start_date ? Carbon::parse($course->start_date) : null;

        // Generer modul-e-poster per leksjon
        $lessonOrder = 0;
        foreach ($lessons as $lesson) {
            $lessonOrder++;

            // Beregn delay-dato fra lesson delay/period + course start_date
            $delayValue = $this->calculateLessonDelay($lesson, $startDate);

            $emailOut = EmailOut::firstOrCreate(
                [
                    'course_id' => $course_id,
                    'template_type' => 'module_available',
                    'lesson_id' => $lesson->id,
                ],
                [
                    'subject' => 'Modul ' . $lessonOrder . ' er klar: ' . $lesson->title,
                    'message' => '',
                    'delay' => $delayValue,
                    'from_name' => 'Forfatterskolen',
                    'from_email' => 'post@forfatterskolen.no',
                    'auto_generated' => true,
                    'status' => 'active',
                    'template_data' => [
                        'lessonOrder' => $lessonOrder,
                        'lessonTitle' => $lesson->title,
                        'lessonDescription' => $lesson->description_simplemde ?? '',
                        'hasAssignment' => false,
                    ],
                ]
            );

            if ($emailOut->wasRecentlyCreated) {
                $created++;
            }
        }

        // Generer oppgave-e-poster
        $assignments = Assignment::where('course_id', $course_id)
            ->where(function ($q) {
                $q->whereNull('parent')->orWhere('parent', 'course');
            })
            ->orderBy('available_date', 'asc')
            ->get();

        foreach ($assignments as $assignment) {
            $availableDate = $assignment->getRawOriginal('available_date');
            $submissionDate = $assignment->getRawOriginal('submission_date');

            // Oppgave tilgjengelig
            if ($availableDate) {
                $emailOut = EmailOut::firstOrCreate(
                    [
                        'course_id' => $course_id,
                        'template_type' => 'assignment_available',
                        'template_data->assignmentId' => $assignment->id,
                    ],
                    [
                        'subject' => 'Ny oppgave: ' . $assignment->title,
                        'message' => '',
                        'delay' => $availableDate,
                        'from_name' => 'Forfatterskolen',
                        'from_email' => 'post@forfatterskolen.no',
                        'auto_generated' => true,
                        'status' => 'active',
                        'template_data' => [
                            'assignmentId' => $assignment->id,
                            'assignmentTitle' => $assignment->title,
                            'assignmentDescription' => $assignment->description ?? '',
                            'submissionDate' => $submissionDate ? Carbon::parse($submissionDate)->format('d.m.Y') : '',
                        ],
                    ]
                );
                if ($emailOut->wasRecentlyCreated) $created++;
            }

            // Påminnelse 3 dager før frist
            if ($submissionDate && !is_numeric($submissionDate)) {
                $reminderDate = Carbon::parse($submissionDate)->subDays(3)->format('Y-m-d');

                $emailOut = EmailOut::firstOrCreate(
                    [
                        'course_id' => $course_id,
                        'template_type' => 'assignment_reminder',
                        'template_data->assignmentId' => $assignment->id,
                    ],
                    [
                        'subject' => 'Påminnelse: ' . $assignment->title . ' — frist om 3 dager',
                        'message' => '',
                        'delay' => $reminderDate,
                        'from_name' => 'Forfatterskolen',
                        'from_email' => 'post@forfatterskolen.no',
                        'auto_generated' => true,
                        'status' => 'active',
                        'template_data' => [
                            'assignmentId' => $assignment->id,
                            'assignmentTitle' => $assignment->title,
                            'submissionDate' => Carbon::parse($submissionDate)->format('d.m.Y'),
                        ],
                    ]
                );
                if ($emailOut->wasRecentlyCreated) $created++;

                // Frist-dag e-post
                $deadlineDate = Carbon::parse($submissionDate)->format('Y-m-d');

                $emailOut = EmailOut::firstOrCreate(
                    [
                        'course_id' => $course_id,
                        'template_type' => 'assignment_deadline',
                        'template_data->assignmentId' => $assignment->id,
                    ],
                    [
                        'subject' => 'Siste frist i dag: ' . $assignment->title,
                        'message' => '',
                        'delay' => $deadlineDate,
                        'from_name' => 'Forfatterskolen',
                        'from_email' => 'post@forfatterskolen.no',
                        'auto_generated' => true,
                        'status' => 'active',
                        'template_data' => [
                            'assignmentId' => $assignment->id,
                            'assignmentTitle' => $assignment->title,
                            'submissionDate' => Carbon::parse($submissionDate)->format('d.m.Y'),
                        ],
                    ]
                );
                if ($emailOut->wasRecentlyCreated) $created++;
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

}
