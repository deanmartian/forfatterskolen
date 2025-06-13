<?php

namespace App\Http\Middleware;

use App\Http\AdminHelpers;
use App\Http\FikenInvoice;
use App\Mail\SubjectBodyEmail;
use App\Order;
use App\Package;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAutoRenewCourses
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            foreach (Auth::user()->coursesTaken as $courseTaken) {
                $package = Package::find($courseTaken->package_id);
                if ($package && $package->course_id == 17 && $courseTaken->started_at) {

                    $checkDate = date('Y-m-d', strtotime($courseTaken->started_at));
                    if ($courseTaken->end_date) {
                        $checkDate = date('Y-m-d', strtotime($courseTaken->end_date));
                    }

                    // check if the date is in the past or today
                    // and if the user wants to auto renew the courses
                    if (Carbon::now()->gt(Carbon::parse($checkDate)) && \Auth::user()->auto_renew_courses) {
                        $user = \Auth::user();
                        $payment_mode = 'Bankoverføring';
                        $price = (int) 1490 * 100;
                        $product_ID = $package->full_price_product;
                        $send_to = $user->email;
                        $dueDate = date('Y-m-d');

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
                        $invoice = new FikenInvoice;
                        $invoice->create_invoice($invoice_fields);

                        foreach (\Auth::user()->coursesTaken as $coursesTaken) {
                            // check if course taken have set end date and add one year to it
                            if ($coursesTaken->end_date) {
                                $addYear = date('Y-m-d', strtotime(date('Y-m-d', strtotime($coursesTaken->end_date)).' + 1 year'));
                                $coursesTaken->end_date = $addYear;
                            }

                            $coursesTaken->renewed_at = Carbon::now();
                            // $coursesTaken->started_at = Carbon::now();
                            $coursesTaken->save();
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
                        $user_email = \Auth::user()->email;
                        $automation_id = 73;
                        $user_name = \Auth::user()->first_name;

                        // uncomment if needed
                        // AdminHelpers::addToAutomation($user_email,$automation_id,$user_name);

                        // Email to support
                        $emailData = [
                            'email_subject' => 'All Courses Renewed',
                            'email_message' => Auth::user()->first_name.' has renewed all the courses',
                            'from_name' => null,
                            'from_email' => null,
                            'attach_file' => null,
                        ];
                        \Mail::to('support@forfatterskolen.no')->queue(new SubjectBodyEmail($emailData));
                        // mail('support@forfatterskolen.no', 'All Courses Renewed', Auth::user()->first_name . ' has renewed all the courses');
                    }
                }
            }
        }

        return $next($request);
    }
}
