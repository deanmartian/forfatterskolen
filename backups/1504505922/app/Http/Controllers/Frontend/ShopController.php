<?php

namespace App\Http\Controllers\Frontend;

use App\Address;
use App\Course;
use App\CoursesTaken;
use App\Http\Controllers\Controller;
use App\Http\FikenInvoice;
use App\Http\FrontendHelpers;
use App\Http\Requests\OrderCreateRequest;
use App\Invoice;
use App\Package;
use App\PaymentMode;
use App\PaymentPlan;
use App\ShopManuscriptsTaken;
use App\Transaction;
use App\User;
use App\WorkshopsTaken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

require '../app/Http/PaypalIPN/PaypalIPN.php';
use Carbon\Carbon;
use PaypalIPN;

class ShopController extends Controller
{
    public function checkout($course_id)
    {
        $course = Course::findOrFail($course_id);
        if (! Auth::guest()) {
            $course_packages = $course->packages->pluck('id')->toArray();
            $courseTaken = CoursesTaken::where('user_id', Auth::user()->id)->whereIn('package_id', $course_packages)->first();
            if ($courseTaken) {
                return redirect(route('learner.course.show', ['id' => $courseTaken->id]));
            }
        }

        return view('frontend.shop.checkout', compact('course'));
    }

    public function place_order(OrderCreateRequest $request)
    {
        if (Auth::guest()) {
            $user = User::where('email', $request->email)->first();
            if ($user) {
                return redirect()->back()->withInput()->withErrors(['The email you provided is already registered. <a href="#" data-toggle="collapse" data-target="#checkoutLogin">Login Here</a>']);
            } else {
                // register new user
                $new_user = new User;
                $new_user->email = $request->email;
                $new_user->first_name = $request->first_name;
                $new_user->last_name = $request->last_name;
                $new_user->password = bcrypt($request->password);
                $new_user->save();
                Auth::login($new_user);
            }
        }

        $hasPaidCourse = false;
        foreach (Auth::user()->coursesTaken as $courseTaken) {
            if ($courseTaken->package->course->type != 'Free' && $courseTaken->is_active) {
                $hasPaidCourse = true;
                break;
            }
        }

        $paymentMode = PaymentMode::findOrFail($request->payment_mode_id);
        $paymentPlan = PaymentPlan::findOrFail($request->payment_plan_id);
        $package = Package::findOrFail($request->package_id);

        $payment_plan = ($paymentMode->mode == 'Paypal') ? 'Hele beløpet' : $paymentPlan->plan;

        $dueDate = date('Y-m-d');
        $dueDate = Carbon::parse($dueDate);
        $payment_plan = trim($payment_plan);
        if ($payment_plan == 'Hele beløpet') {
            $price = (int) $package->full_payment_price * 100;
            $product_ID = $package->full_price_product;
            $dueDate->addDays($package->full_price_due_date);
        } elseif ($payment_plan == '3 måneder') {
            $price = (int) $package->months_3_price * 100;
            $product_ID = $package->months_3_product;
            $dueDate->addDays($package->months_3_due_date);
        } elseif ($payment_plan == '6 måneder') {
            $price = (int) $package->months_6_price * 100;
            $product_ID = $package->months_6_product;
            $dueDate->addDays($package->months_6_due_date);
        }
        $dueDate = date_format(date_create($dueDate), 'Y-m-d');

        $payment_mode = $paymentMode->mode;
        if ($payment_mode == 'Faktura') {
            $payment_mode = 'Bankoverføring';
        }

        $comment = '(Kurs: '.$package->course->title.' ['.$package->variation.'], ';
        $comment .= 'Betalingsmodus: '.$payment_mode.', ';
        $comment .= 'Betalingsplan: '.$payment_plan.')';
        if ($hasPaidCourse && $package->course->type == 'Group') {
            $comment .= ' - Discount: Kr 1.500,00';
            $price = $price - ((int) 1500 * 100);
        }

        $invoice_fields = [
            'user_id' => Auth::user()->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'netAmount' => $price,
            'dueDate' => $dueDate,
            'description' => 'Kursordrefaktura',
            'productID' => $product_ID,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'address' => $request->street,
            'postalPlace' => $request->city,
            'postalCode' => $request->zip,
            'comment' => $comment,
        ];

        $invoice = new FikenInvoice;
        $invoice->create_invoice($invoice_fields);

        if ($request->update_address) {
            $address = Address::firstOrNew(['user_id' => Auth::user()->id]);
            $address->street = $request->street;
            $address->city = $request->city;
            $address->zip = $request->zip;
            $address->phone = $request->phone;
            $address->save();
        }

        $courseTaken = CoursesTaken::firstOrNew(['user_id' => Auth::user()->id, 'package_id' => $package->id]);
        $courseTaken->is_active = 0;
        $courseTaken->save();

        // Check for shop manuscripts
        if ($package->shop_manuscripts->count() > 0) {
            foreach ($package->shop_manuscripts as $shop_manuscript) {
                $shopManuscriptTaken = ShopManuscriptsTaken::firstOrNew(['user_id' => Auth::user()->id, 'shop_manuscript_id' => $shop_manuscript->shop_manuscript_id]);
                $shopManuscriptTaken->user_id = Auth::user()->id;
                $shopManuscriptTaken->shop_manuscript_id = $shop_manuscript->shop_manuscript_id;
                $shopManuscriptTaken->is_active = false;
                $shopManuscriptTaken->save();
            }
        }

        /*// Check for workshops
        if( $package->workshops->count() > 0 ) :
            foreach( $package->workshops as $workshop ) :
            $workshopTaken = WorkshopsTaken::firstOrNew(['user_id' => Auth::user()->id, 'workshop_id' => $workshop->workshop_id]);
            $workshopTaken->user_id = Auth::user()->id;
            $workshopTaken->workshop_id = $workshop->workshop_id;
            $workshopTaken->is_active = false;
            $workshopTaken->save();
            endforeach;
        endif;*/

        // Check for included courses
        if ($package->included_courses->count() > 0) {
            foreach ($package->included_courses as $included_course) {
                $includedCourse = CoursesTaken::firstOrNew(['user_id' => Auth::user()->id, 'package_id' => $included_course->included_package_id]);
                $includedCourse->is_active = false;
                $includedCourse->save();
            }
        }

        // Email to support
        mail('support@forfatterskolen.no', 'New Course Order', Auth::user()->first_name.' has ordered the course '.$package->course->title);

        // Send course email
        $actionText = 'Mine Kurs';
        $actionUrl = 'http:/www.forfatterskolen.no/account/course';
        $headers = "From: Forfatterskolen<post@forfatterskolen.no>\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $user = Auth::user();
        $email_content = $package->course->email;
        mail($user->email, $package->course->title, view('emails.course_order', compact('actionText', 'actionUrl', 'user', 'email_content')), $headers);

        if ($paymentMode->mode == 'Paypal') {
            echo '<form name="_xclick" id="paypal_form" style="display:none" action="https://www.paypal.com/cgi-bin/webscr" method="post">
                <input type="hidden" name="cmd" value="_xclick">
                <input type="hidden" name="business" value="post.forfatterskolen@gmail.com">
                <input type="hidden" name="currency_code" value="NOK">
                <input type="hidden" name="custom" value="'.$invoice->invoiceID.'">
                <input type="hidden" name="item_name" value="Course Order Invoice">
                <input type="hidden" name="amount" value="'.($price / 100).'">
                <input type="hidden" name="return" value="'.route('front.shop.thankyou').'?gateway=Paypal">
                <input type="image" name="submit" src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" align="right" alt="PayPal - The safer, easier way to pay online">
            </form>';
            echo '<script>document.getElementById("paypal_form").submit();</script>';

            return;
        }

        return redirect(route('front.shop.thankyou'));

    }

    public function thankyou()
    {
        return view('frontend.shop.thankyou');
    }

    public function paypalIPN(Request $request)
    {
        $ipn = new PaypalIPN;

        // $ipn->useSandbox();

        $verified = $ipn->verifyIPN();

        if ($verified) {
            // Create new transaction
            $invoice = Invoice::findOrFail($request->custom);
            $transaction = new Transaction;
            $transaction->invoice_id = $invoice->id;
            $transaction->mode = 'Paypal';
            $transaction->mode_transaction = $request->txn_id;
            $transaction->amount = $request->mc_gross;
            $transaction->save();

            $fiken_invoice = FrontendHelpers::FikenConnect($invoice->fiken_url);
            $balance = (float) $fiken_invoice->gross / 100;

            if ($invoice->payment_plan->division == 1 && ($balance - $invoice->transactions->sum('amount')) <= 0) {
                $courseTaken = CoursesTaken::where('user_id', $invoice->user_id)->where('package_id', $invoice->package_id)->first();
                if (! $courseTaken) {
                    $courseTaken = new CoursesTaken;
                    $courseTaken->user_id = $invoice->user_id;
                    $courseTaken->package_id = $invoice->package_id;
                }
                $courseTaken->is_active = 1;
                $courseTaken->save();
            }
        }

        return header('HTTP/1.1 200 OK');
    }
}
