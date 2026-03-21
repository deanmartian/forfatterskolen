<?php

namespace App\Console\Commands;

use App\AssignmentManuscript;
use App\Course;
use App\CoursesTaken;
use App\CronLog;
use App\EmailAttachment;
use App\EmailOut;
use App\Http\AdminHelpers;
use App\Http\FrontendHelpers;
use App\Jobs\AddMailToQueueJob;
use App\Mail\BrandedCourseMail;
use App\Order;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CourseEmailOut extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'courseemailout:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Course email out';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today()->format('Y-m-d');
        CronLog::create(['activity' => 'CourseEmailOut CRON running.']);
        $courses = Course::pluck('id');
        $emailOutList = EmailOut::where('for_free_course', 0)->whereDate('delay', '=', $today)
            ->whereIn('course_id', $courses)
            ->where('send_immediately', 0)->get();
        $emailOutListSent = [];

        foreach ($emailOutList as $emailOut) {
            if (! in_array($emailOut->id, $emailOutListSent)) {
                $packages = $emailOut->allowed_package ? json_decode($emailOut->allowed_package) :
                       $emailOut->course->packages->pluck('id')->toArray();
                $emailRecipients = $emailOut->recipients->pluck('user_id')->toArray();

                $attachmentText = $this->buildAttachmentText($emailOut);

                if ($emailOut->send_to_learners_no_course || $emailOut->send_to_learners_with_unpaid_pay_later) {

                    // build once
                    $query = User::query();

                    $clauses = [];

                    if ($emailOut->send_to_learners_no_course) {
                        $coursesTakenUserIds = CoursesTaken::query()
                            ->withoutGlobalScopes()                // removes "courses_taken.deleted_at is null"
                            ->from('courses_taken as ct')
                            ->whereNull('ct.deleted_at')           // re-apply using alias

                            ->whereIn('ct.package_id', $packages)

                            ->whereExists(function ($q) {
                                $q->select(DB::raw(1))
                                ->from('users')
                                ->whereColumn('users.id', 'ct.user_id')
                                ->whereNull('users.deleted_at');
                            })

                            ->where('ct.is_free', 1)

                            ->whereNotExists(function ($q) use ($today) {
                                $q->select(DB::raw(1))
                                ->from('courses_taken as ct2')
                                ->whereColumn('ct2.user_id', 'ct.user_id')
                                ->whereColumn('ct2.id', '!=', 'ct.id')
                                ->whereNull('ct2.deleted_at')
                                ->where('ct2.package_id', 29)
                                ->where(function ($qq) use ($today) {
                                    $qq->whereNull('ct2.end_date')
                                        ->orWhereDate('ct2.end_date', '>', $today);
                                });
                            })

                            ->distinct()
                            ->pluck('ct.user_id')
                            ->toArray();

                        $clauses[] = function ($q) use ($coursesTakenUserIds) {
                            $q->whereIn('id', $coursesTakenUserIds);
                        };
                    }

                    if ($emailOut->send_to_learners_with_unpaid_pay_later) {
                        $packageIds = Course::find($emailOut->course_id)->packages()->pluck('id');
                        $userIds = Order::whereIn('package_id', $packageIds)
                            ->where([
                                'is_processed' => 1,
                                'is_pay_later' => 1,
                                'is_invoice_sent' => 0,
                                'is_order_withdrawn' => 0,
                            ])->pluck('user_id');

                        $clauses[] = function ($q) use ($userIds) {
                            $q->whereIn('id', $userIds);
                        };
                    }

                    if (empty($clauses)) {
                        $userList = collect();
                    } else {
                        $query->where(function ($q) use ($clauses) {
                            foreach ($clauses as $i => $apply) {
                                if ($i === 0) {
                                    $apply($q);                   // first clause
                                } else {
                                    $q->orWhere(function ($qq) use ($apply) { $apply($qq); }); // OR the rest
                                }
                            }
                        });

                        if ($emailOut->exclude_free_manuscript_learners) {
                            $query->whereNotIn('email', function ($subquery) {
                                $subquery->select('email')->from('free_manuscripts');
                            });
                        }

                        if ($emailOut->send_to_learners_with_unpaid_pay_later) {
                            $query->whereNotIn('id', $emailRecipients);
                        }

                        $userList = $query->get();
                    }

                    // loop the result and send email
                    foreach ($userList as $user) {
                        if ($user->is_disabled) {
                            continue;
                        }

                        $message = $this->replaceMessagePlaceholders($emailOut->message, $user);
                        $this->sendBrandedOrLegacy($emailOut, $user, $message, $attachmentText, 'learner', $user->id);

                        $emailOut->recipients()->updateOrCreate([
                            'user_id' => $user->id,
                        ]);

                        CronLog::create(['activity' => 'CourseEmailOut added to email queue '.$user->email]);
                    }

                } else {
                    if ($emailOut->include_former_learners) {
                        $course = Course::find($emailOut->course_id);
                        $coursesTakenQuery = $course->learnersWithExpired;
                        if ($emailOut->exclude_free_manuscript_learners) {
                            $coursesTakenQuery->whereHas('user', function ($query) {
                                $query->whereNotIn('email', function ($subquery) {
                                    $subquery->select('email')->from('free_manuscripts');
                                });
                            });
                        }
                        $coursesTaken = $coursesTakenQuery->get();
                    } else {
                        $coursesTaken = CoursesTaken::whereIn('package_id', $packages)
                        ->whereHas('user', function ($query) use ($emailOut) {
                            if ($emailOut->exclude_free_manuscript_learners) {
                                $query->whereNotIn('email', function ($subquery) {
                                    $subquery->select('email')->from('free_manuscripts');
                                });
                            }
                        })
                        //->whereNull('renewed_at')
                        ->whereNotIn('user_id', $emailRecipients)
                        ->where('can_receive_email', 1)
                        ->get();
                    }

                    // loop the result and send email
                    foreach ($coursesTaken as $courseTaken) {
                        $user = $courseTaken->user;
                        if ($user->is_disabled) {
                            continue;
                        }

                        $message = $this->replaceMessagePlaceholders($emailOut->message, $user);
                        $this->sendBrandedOrLegacy($emailOut, $user, $message, $attachmentText, 'courses-taken', $courseTaken->id);

                        $emailOut->recipients()->updateOrCreate([
                            'user_id' => $user->id,
                        ]);

                        CronLog::create(['activity' => 'CourseEmailOut added to email queue '.$user->email]);
                    }
                }
            }
            array_push($emailOutListSent, $emailOut->id);
        }

        $emailOutListDay = EmailOut::where('for_free_course', 0)->where('delay', 'NOT LIKE', '%-%')
            ->whereIn('course_id', $courses)
            ->where('send_immediately', 0)
            ->get();
        $emailOutListDaySent = [];
        foreach ($emailOutListDay as $emailOut) {
            if (! in_array($emailOut->id, $emailOutListDaySent)) {
                $emailDate = Carbon::now()->subDays($emailOut->delay)->format('Y-m-d');
                $packages = $emailOut->allowed_package ? json_decode($emailOut->allowed_package) :
                    $emailOut->course->packages->pluck('id')->toArray();
                $emailRecipients = $emailOut->recipients->pluck('user_id')->toArray();

                $attachmentText = $this->buildAttachmentText($emailOut);

                if ($emailOut->send_to_learners_no_course || $emailOut->send_to_learners_with_unpaid_pay_later) {

                    // build once
                    $query = User::query();

                    $clauses = [];

                    if ($emailOut->send_to_learners_no_course) {
                        $today = now()->toDateString();

                        $coursesTakenUserIds = CoursesTaken::query()
                            ->withoutGlobalScopes()
                            ->from('courses_taken as ct')
                            ->whereNull('ct.deleted_at')

                            ->whereIn('ct.package_id', $packages)

                            ->whereExists(function ($q) {
                                $q->select(DB::raw(1))
                                ->from('users')
                                ->whereColumn('users.id', 'ct.user_id')
                                ->whereNull('users.deleted_at');
                            })

                            ->where(function ($query) use ($emailDate) {
                                $query->whereDate('ct.started_at', $emailDate)
                                    ->orWhereDate('ct.start_date', $emailDate);
                            })

                            ->where('ct.is_free', 1)

                            // exclude ONLY if user has an ACTIVE package_id=29
                            ->whereNotExists(function ($q) use ($today) {
                                $q->select(DB::raw(1))
                                ->from('courses_taken as ct2')
                                ->whereColumn('ct2.user_id', 'ct.user_id')
                                ->whereColumn('ct2.id', '!=', 'ct.id')
                                ->whereNull('ct2.deleted_at')
                                ->where('ct2.package_id', 29)
                                ->where(function ($qq) use ($today) {
                                    $qq->whereNull('ct2.end_date')
                                        ->orWhereDate('ct2.end_date', '>', $today);
                                });
                            })

                            ->distinct()
                            ->pluck('ct.user_id')
                            ->toArray();

                        $clauses[] = function ($q) use ($coursesTakenUserIds) {
                            $q->whereIn('id', $coursesTakenUserIds);
                        };
                    }

                    if ($emailOut->send_to_learners_with_unpaid_pay_later) {
                        $packageIds = Course::find($emailOut->course_id)->packages()->pluck('id');
                        $userIds = Order::whereIn('package_id', $packageIds)
                            ->where([
                                'is_processed' => 1,
                                'is_pay_later' => 1,
                                'is_invoice_sent' => 0,
                                'is_order_withdrawn' => 0,
                            ])->pluck('user_id');

                        $clauses[] = function ($q) use ($userIds) {
                            $q->whereIn('id', $userIds);
                        };
                    }

                    if (empty($clauses)) {
                        $userList = collect();
                    } else {
                        $query->where(function ($q) use ($clauses) {
                            foreach ($clauses as $i => $apply) {
                                if ($i === 0) {
                                    $apply($q);                   // first clause
                                } else {
                                    $q->orWhere(function ($qq) use ($apply) { $apply($qq); }); // OR the rest
                                }
                            }
                        });

                        if ($emailOut->exclude_free_manuscript_learners) {
                            $query->whereNotIn('email', function ($subquery) {
                                $subquery->select('email')->from('free_manuscripts');
                            });
                        }

                        if ($emailOut->send_to_learners_with_unpaid_pay_later) {
                            $query->whereNotIn('id', $emailRecipients);
                        }

                        $userList = $query->get();
                    }

                    // loop the result and send email
                    foreach ($userList as $user) {
                        if ($user->is_disabled) {
                            continue;
                        }

                        $message = $this->replaceMessagePlaceholders($emailOut->message, $user);
                        $this->sendBrandedOrLegacy($emailOut, $user, $message, $attachmentText, 'learner', $user->id);

                        $emailOut->recipients()->updateOrCreate([
                            'user_id' => $user->id,
                        ]);

                        CronLog::create(['activity' => 'CourseEmailOut added to email queue '.$user->email]);
                    }

                } else {
                    if ($emailOut->include_former_learners) {
                        $course = Course::find($emailOut->course_id);
                        $coursesTakenQuery = $course->learnersWithExpired;
                        if ($emailOut->exclude_free_manuscript_learners) {
                            $coursesTakenQuery->whereHas('user', function ($query) {
                                $query->whereNotIn('email', function ($subquery) {
                                    $subquery->select('email')->from('free_manuscripts');
                                });
                            });
                        }
                        $coursesTaken = $coursesTakenQuery->get();
                    } else {
                        $coursesTaken = CoursesTaken::whereIn('package_id', $packages)
                        ->whereHas('user', function ($query) use ($emailOut) {
                            if ($emailOut->exclude_free_manuscript_learners) {
                                $query->whereNotIn('email', function ($subquery) {
                                    $subquery->select('email')->from('free_manuscripts');
                                });
                            }
                        })
                        ->where(function ($query) use ($emailDate) {
                            $query->whereDate('started_at', '=', $emailDate);
                            $query->orWhereDate('start_date', '=', $emailDate);
                        })
                        //->whereNull('renewed_at')
                        ->whereNotIn('user_id', $emailRecipients)
                        ->where('can_receive_email', 1)
                        ->get();
                    }

                    // loop the result and send email
                    foreach ($coursesTaken as $courseTaken) {
                        $user = $courseTaken->user;
                        if ($user->is_disabled) {
                            continue;
                        }

                        $message = $this->replaceMessagePlaceholders($emailOut->message, $user);
                        $this->sendBrandedOrLegacy($emailOut, $user, $message, $attachmentText, 'courses-taken', $courseTaken->id);

                        $emailOut->recipients()->updateOrCreate([
                            'user_id' => $user->id,
                        ]);

                        CronLog::create(['activity' => 'CourseEmailOut added to email queue '.$user->email]);
                    }
                }
            }
            array_push($emailOutListDaySent, $emailOut->id);
        }

        CronLog::create(['activity' => 'CourseEmailOut CRON done running.']);

        return 'done';
    }

    /**
     * Replace placeholders in the email message with user-specific values.
     */
    private function replaceMessagePlaceholders(string $messageTemplate, User $user): string
    {
        $encode_email = encrypt($user->email);
        $loginLink = "<a href='".route('auth.login.email', $encode_email)."'>Klikk her for å logge inn</a>";
        $password = $user->need_pass_update
            ? 'Skjult (kan endres inne i portalen eller via glemt passord)'
            : 'Skjult (kan endres inne i portalen eller via glemt passord)';

        if (strpos($messageTemplate, '[redirect]') !== false) {
            $extractLink = FrontendHelpers::getTextBetween($messageTemplate, '[redirect]', '[/redirect]');
            $formatRedirectLink = route('auth.login.emailRedirect', [$encode_email, encrypt($extractLink)]);
            $redirectLabel = FrontendHelpers::getTextBetween($messageTemplate, '[redirect_label]', '[/redirect_label]');
            $redirectLink = "<a href='".$formatRedirectLink."'>".$redirectLabel.'</a>';
            $search_string = [
                '[redirect]'.$extractLink.'[/redirect]', '[redirect_label]'.$redirectLabel.'[/redirect_label]',
            ];
            $replace_string = [
                $redirectLink, '',
            ];

            return str_replace($search_string, $replace_string, $messageTemplate);
        }

        $search_string = [
            '[login_link]', '[username]', '[password]',
        ];
        $replace_string = [
            $loginLink, $user->email, $password,
        ];

        return str_replace($search_string, $replace_string, $messageTemplate);
    }

    /**
     * Send email via branded template or legacy AddMailToQueueJob
     */
    private function sendBrandedOrLegacy(EmailOut $emailOut, User $user, string $message, string $attachmentText, ?string $trackType = null, $trackId = null): void
    {
        // Smart filter: skip users who already submitted for reminder/deadline emails
        if ($this->shouldSkipForSubmitted($emailOut, $user)) {
            return;
        }

        // Respekter brukerens e-postvarsel-preferanser
        $templateNotificationMap = [
            'feedback_ready' => 'feedback_ready',
            'assignment_reminder' => 'task_reminder',
            'assignment_deadline' => 'task_reminder',
        ];
        $notifType = $templateNotificationMap[$emailOut->template_type] ?? null;
        if ($notifType && !$user->wantsNotification($notifType)) {
            return;
        }

        if ($emailOut->template_type) {
            $course = $emailOut->course;
            if ($course) {
                Mail::to($user->email)->queue(new BrandedCourseMail($emailOut, $user, $course));
            }
        } else {
            dispatch(new AddMailToQueueJob(
                $user->email, $emailOut->subject, $message . $attachmentText,
                $emailOut->from_email, $emailOut->from_name, null, $trackType, $trackId
            ));
        }
    }

    /**
     * Build attachment text HTML for an email out entry.
     */
    private function buildAttachmentText(EmailOut $emailOut): string
    {
        $emailAttachment = EmailAttachment::where('hash', $emailOut->attachment_hash)->first();
        if ($emailAttachment) {
            return "<p style='margin-top: 10px'><b>Vedlegg:</b>
                <a href='".route('front.email-attachment', $emailAttachment->hash)."'>"
                .AdminHelpers::extractFileName($emailAttachment->filename).'</a></p>';
        }

        return '';
    }

    /**
     * Check if user has already submitted the assignment — skip reminder/deadline if so.
     */
    private function shouldSkipForSubmitted(EmailOut $emailOut, User $user): bool
    {
        if (!in_array($emailOut->template_type, ['assignment_reminder', 'assignment_deadline'])) {
            return false;
        }

        $tplData = is_array($emailOut->template_data) ? $emailOut->template_data : (json_decode($emailOut->template_data, true) ?? []);
        $assignmentTitle = $tplData['assignmentTitle'] ?? null;
        if (!$assignmentTitle) {
            return false;
        }

        // Find the assignment by title and course
        $assignment = \App\Assignment::where('course_id', $emailOut->course_id)
            ->where('title', $assignmentTitle)
            ->first();

        if (!$assignment) {
            return false;
        }

        return AssignmentManuscript::where('assignment_id', $assignment->id)
            ->where('user_id', $user->id)
            ->exists();
    }
}
