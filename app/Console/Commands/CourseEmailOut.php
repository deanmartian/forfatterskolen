<?php

namespace App\Console\Commands;

use App\Course;
use App\CoursesTaken;
use App\CronLog;
use App\EmailAttachment;
use App\EmailOut;
use App\Http\AdminHelpers;
use App\Http\FrontendHelpers;
use App\Jobs\AddMailToQueueJob;
use App\Order;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

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
        $courses = Course::all()->pluck('id');
        $emailOutList = EmailOut::where('for_free_course', 0)->whereDate('delay', '=', $today)
            ->whereIn('course_id', $courses)
            ->where('send_immediately', 0)->get();
        $emailOutListSent = [];

        foreach ($emailOutList as $emailOut) {
            if (! in_array($emailOut->id, $emailOutListSent)) {
                $packages = $emailOut->allowed_package ? json_decode($emailOut->allowed_package) :
                       $emailOut->course->packages->pluck('id')->toArray();
                $emailRecipients = $emailOut->recipients->pluck('user_id')->toArray();

                $emailAttachment = EmailAttachment::where('hash', $emailOut->attachment_hash)->first();
                $attachmentText = '';
                if ($emailAttachment) {
                    $attachmentText = "<p style='margin-top: 10px'><b>Vedlegg:</b> 
                        <a href='".route('front.email-attachment', $emailAttachment->hash)."'>"
                        .AdminHelpers::extractFileName($emailAttachment->filename).'</a></p>';
                }

                if ($emailOut->send_to_learners_no_course || $emailOut->send_to_learners_with_unpaid_pay_later) {

                    // build once
                    $query = User::query();

                    $clauses = [];

                    if ($emailOut->send_to_learners_no_course) {
                        $clauses[] = function ($q) {
                            $q->doesntHave('coursesTakenNotOld')
                            ->doesntHave('shopManuscriptsTaken')
                            ->doesntHave('coachingTimers')
                            ->doesntHave('invoices')
                            ->whereNull('notes');
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

                        $userList = $query->whereNotIn('id', $emailRecipients)->get();
                    }

                    // loop the result and send email
                    foreach ($userList as $user) {
                        $toMail = $user->email;

                        $encode_email = encrypt($user->email);
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
                                $loginLink, $user->email, $password,
                            ];
                            $message = str_replace($search_string, $replace_string, $emailOut->message);
                        }

                        $emailData['email_subject'] = $emailOut->subject;
                        $emailData['email_message'] = $message.$attachmentText;
                        $emailData['from_name'] = $emailOut->from_name;
                        $emailData['from_email'] = $emailOut->from_email;
                        $emailData['attach_file'] = null;

                        if(!$user->is_disabled) {
                            // add email to queue
                            dispatch(new AddMailToQueueJob($toMail, $emailOut->subject, $message.$attachmentText,
                                $emailOut->from_email, $emailOut->from_name, null, 'learner', $user->id));

                            $emailOut->recipients()->updateOrCreate([
                                'user_id' => $user->id,
                            ]);

                            CronLog::create(['activity' => 'CourseEmailOut added to email queue '.$toMail]);
                        }
                    }

                } else {
                    $coursesTaken = CoursesTaken::whereIn('package_id', $packages)
                    ->whereHas('user')
                    ->whereNull('renewed_at')
                    ->whereNotIn('user_id', $emailRecipients)
                    ->where('can_receive_email', 1)
                    ->get();

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

                        if(!$courseTaken->user->is_disabled) {
                            // add email to queue
                            dispatch(new AddMailToQueueJob($toMail, $emailOut->subject, $message.$attachmentText,
                                $emailOut->from_email, $emailOut->from_name, null, 'courses-taken', $courseTaken->id));

                            $emailOut->recipients()->updateOrCreate([
                                'user_id' => $user->id,
                            ]);

                            CronLog::create(['activity' => 'CourseEmailOut added to email queue '.$toMail]);
                        }
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

                $emailAttachment = EmailAttachment::where('hash', $emailOut->attachment_hash)->first();
                $attachmentText = '';
                if ($emailAttachment) {
                    $attachmentText = "<p style='margin-top: 10px'><b>Vedlegg:</b> 
                        <a href='".route('front.email-attachment', $emailAttachment->hash)."'>"
                        .AdminHelpers::extractFileName($emailAttachment->filename).'</a></p>';
                }

                if ($emailOut->send_to_learners_no_course || $emailOut->send_to_learners_with_unpaid_pay_later) {

                    // build once
                    $query = User::query();

                    $clauses = [];

                    if ($emailOut->send_to_learners_no_course) {
                        $clauses[] = function ($q) {
                            $q->doesntHave('coursesTakenNotOld')
                            ->doesntHave('shopManuscriptsTaken')
                            ->doesntHave('coachingTimers')
                            ->doesntHave('invoices')
                            ->whereNull('notes');
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

                        $userList = $query->whereNotIn('id', $emailRecipients)->get();
                    }

                    // loop the result and send email
                    foreach ($userList as $user) {
                        $toMail = $user->email;

                        $encode_email = encrypt($user->email);
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
                                $loginLink, $user->email, $password,
                            ];
                            $message = str_replace($search_string, $replace_string, $emailOut->message);
                        }

                        $emailData['email_subject'] = $emailOut->subject;
                        $emailData['email_message'] = $message.$attachmentText;
                        $emailData['from_name'] = $emailOut->from_name;
                        $emailData['from_email'] = $emailOut->from_email;
                        $emailData['attach_file'] = null;

                        if(!$user->is_disabled) {
                            // add email to queue
                            dispatch(new AddMailToQueueJob($toMail, $emailOut->subject, $message.$attachmentText,
                                $emailOut->from_email, $emailOut->from_name, null, 'learner', $user->id));

                            $emailOut->recipients()->updateOrCreate([
                                'user_id' => $user->id,
                            ]);

                            CronLog::create(['activity' => 'CourseEmailOut added to email queue '.$toMail]);
                        }
                    }

                } else {
                    $coursesTaken = CoursesTaken::whereIn('package_id', $packages)
                    ->whereHas('user')
                    ->where(function ($query) use ($emailDate) {
                        $query->whereDate('started_at', '=', $emailDate);
                        $query->orWhereDate('start_date', '=', $emailDate);
                    })
                    ->whereNull('renewed_at')
                    ->whereNotIn('user_id', $emailRecipients)
                    ->where('can_receive_email', 1)
                    ->get();

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

                        if(!$courseTaken->user->is_disabled) {
                            // add email to queue
                            dispatch(new AddMailToQueueJob($toMail, $emailOut->subject, $message.$attachmentText,
                                $emailOut->from_email, $emailOut->from_name, null, 'courses-taken', $courseTaken->id));

                            $emailOut->recipients()->updateOrCreate([
                                'user_id' => $user->id,
                            ]);

                            CronLog::create(['activity' => 'CourseEmailOut added to email queue '.$toMail]);
                        }
                    }
                }
            }
            array_push($emailOutListDaySent, $emailOut->id);
        }

        CronLog::create(['activity' => 'CourseEmailOut CRON done running.']);

        return 'done';
    }
}
