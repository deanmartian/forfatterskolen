<?php

namespace App\Console\Commands;

use App\CoursesTaken;
use App\CronLog;
use App\Http\AdminHelpers;
use App\Http\FikenInvoice;
use App\Mail\SubjectBodyEmail;
use App\Order;
use App\Package;
use Carbon\Carbon;
use Illuminate\Console\Command;

class WebinarPakkeExpiresInAWeek extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webinarpakkeexpiresinaweek:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for learners that have a webinar pakke course that would expire in a week.';

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
    public function handle(): void
    {
        CronLog::create(['activity' => 'WebinarPakkeExpiresInAWeek CRON running.']);
        $dateAddDays = Carbon::now()->addDays(7);
        $monthDate = $dateAddDays->format('Y-m-d');
        $yearDate = $dateAddDays->subYear(1)->format('Y-m-d'); // subYear to get the correct started_at

        // get courses taken by end date
        $coursesTaken = CoursesTaken::whereHas('package', function ($query) {
            $query->where('course_id', 17);
        })->whereNotNull('end_date')->where('end_date', $monthDate)
            ->where('send_expiry_reminder', 1)->get();

        // get courses taken by started at field
        $coursesTakenByStartDate = CoursesTaken::whereHas('package', function ($query) {
            $query->where('course_id', 17);
        })
            ->whereNotNull('started_at')
            ->whereNull('end_date')
            ->whereDate('started_at', $yearDate)
            ->where('send_expiry_reminder', 1)
            ->get();

        // merge the collections
        $coursesTaken = $coursesTaken->merge($coursesTakenByStartDate);

        foreach ($coursesTaken->all() as $courseTaken) {

            // check if auto renew courses is set
            if ($courseTaken->user->auto_renew_courses) {
                $user = $courseTaken->user;
                $package = Package::findOrFail($courseTaken->package_id);
                $payment_mode = 'Bankoverføring';
                $price = (int) 1490 * 100;
                $product_ID = $package->full_price_product;
                $send_to = $user->email;
                $end_date = $courseTaken->end_date ? $courseTaken->end_date : date('Y-m-d');
                // add 10 days from today
                // $dueDate        = date('Y-m-d', strtotime(date("Y-m-d") . " +10 days"));
                $dueDate = date('Y-m-d', strtotime($end_date));

                $comment = '(Kurs: '.$package->course->title.' ['.$package->variation.'], ';
                $comment .= 'Betalingsmodus: '.$payment_mode.')';

                $invoice_fields = [
                    'user_id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'netAmount' => $price,
                    'dueDate' => $dueDate,
                    'description' => 'Kursordrefaktura',
                    'productID' => $product_ID,
                    'email' => $send_to,
                    'telephone' => $user->address->phone,
                    'address' => $user->address->street,
                    'postalPlace' => $user->address->city,
                    'postalCode' => $user->address->zip,
                    'comment' => $comment,
                    'payment_mode' => 'Faktura',
                ];

                if (!$user->is_disabled) {
                    $invoice = new FikenInvoice;
                    $invoice->create_invoice($invoice_fields);

                    // update all the started at of each courses taken
                    foreach ($courseTaken->user->coursesTaken as $coursesTaken) {
                        $formerCourse = $courseTaken->user->coursesTakenOld()->pluck('id')->toArray();

                        if (! in_array($coursesTaken->id, $formerCourse)) {
                            // check if course taken have set end date and add one year to it
                            if ($coursesTaken->end_date) {
                                $addYear = date('Y-m-d', strtotime(date('Y-m-d', strtotime($coursesTaken->end_date)).' + 1 year'));
                                $coursesTaken->end_date = $addYear;
                            }

                            // $coursesTaken->started_at = Carbon::now();
                            $coursesTaken->renewed_at = Carbon::now();
                            $coursesTaken->save();
                        }
                    }

                    // create order record
                    $newOrder['user_id'] = $courseTaken->user->id;
                    $newOrder['item_id'] = $package->course_id;
                    $newOrder['type'] = Order::COURSE_TYPE;
                    $newOrder['package_id'] = $package->id;
                    $newOrder['plan_id'] = 8; // Full payment
                    $newOrder['price'] = $price / 100;
                    $newOrder['discount'] = 0;
                    $newOrder['payment_mode_id'] = 3; // Faktura
                    $newOrder['is_processed'] = 1;
                    $order = Order::create($newOrder);

                    // add to automation
                    $user_email = $courseTaken->user->email;
                    $automation_id = 73;
                    $user_name = $courseTaken->user->first_name;

                    AdminHelpers::addToAutomation($user_email, $automation_id, $user_name);

                    // Email to support
                    $from = 'postmail@forfatterskolen.no';
                    $to = 'support@forfatterskolen.no';
                    /*AdminHelpers::send_email('All Courses Renewed',
                        $from, $to,
                        $user_name . ' has renewed all the courses');*/
                    $emailData = [
                        'email_subject' => 'All Courses Renewed',
                        'email_message' => $user_name.' has renewed all the courses',
                        'from_name' => '',
                        'from_email' => $from,
                        'attach_file' => null,
                    ];
                    \Mail::to($to)->queue(new SubjectBodyEmail($emailData));
                    CronLog::create(['activity' => 'WebinarPakkeExpiresInAWeek CRON renewed the courses for user '.$user->id]);
                }
            }
        }

        CronLog::create(['activity' => 'WebinarPakkeExpiresInAWeek CRON done running.']);
    }
}
