<?php

namespace App\Http\Controllers\Frontend;

use App\Address;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Mail\SubjectBodyEmail;
use App\Order;
use App\PaymentMode;
use App\Paypal;
use App\User;
use App\Workshop;
use App\WorkshopMenu;
use App\WorkshopsTaken;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Validator;

require app_path('/Http/PaypalIPN/PaypalIPN.php');
use App\Http\FikenInvoice;

class WorkshopController extends Controller
{
    public function index(): View
    {
        $workshops = Workshop::orderBy('faktura_date', 'ASC')->get();

        return view('frontend.workshop.index', compact('workshops'));
    }

    public function show($id): View
    {
        abort(404);
        $workshop = Workshop::findOrFail($id);

        return view('frontend.workshop.show', compact('workshop'));
    }

    public function checkout($id)
    {
        abort(404);
        $workshop = Workshop::findOrFail($id);
        if (! Auth::guest()) {
            $userWorkshops = Auth::user()->workshopsTaken()->get()->pluck('workshop_id')->toArray();
            if (in_array($id, $userWorkshops)) {
                return redirect()->route('learner.workshop');
            }
        }

        return view('frontend.workshop.checkout', compact('workshop'));
    }

    public function place_order($id, Request $request)
    {
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $workshop = Workshop::findOrFail($id);
        $menu_id = $request->menu_id ?: $workshop->menus->first()->id;
        $menu = WorkshopMenu::findOrFail($menu_id);
        if ($menu->workshop_id != $workshop->id) {
            return redirect()->back()->withInput()->withErrors(['Invalid menu']);
        }

        if (Auth::guest()) {
            $user = User::where('email', $request->email)->first();
            if ($user) {
                Auth::login($user);
                // return redirect()->back()->withInput()->withErrors(['The email you provided is already registered.
                // <a href="#" data-toggle="collapse" data-target="#checkoutLogin">Login Here</a>']);
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

        $courseWorkshops = 0;
        $isFree = 0;
        foreach (Auth::user()->coursesTaken as $courseTaken) {
            $courseWorkshops += $courseTaken->package->workshops;
        }

        // check if user have courses taken and workshops taken
        if (Auth::user()->workshopsTaken->count() == 0 && $courseWorkshops > 0) {
            $isFree = 1;
        }

        // check if the user already have this workshop
        $alreadyAvailWorkshop = WorkshopsTaken::where(['workshop_id' => $workshop->id, 'user_id' => Auth::user()->id])->get();
        if ($alreadyAvailWorkshop->count()) {
            return redirect()->route('learner.workshop');
        }

        $workshopTaken = new WorkshopsTaken;
        $workshopTaken->user_id = Auth::user()->id;
        $workshopTaken->workshop_id = $workshop->id;
        $workshopTaken->menu_id = $menu->id;
        $workshopTaken->notes = $request->notes;

        $workshopTaken->is_active = false;
        $workshopTaken->save();

        $newOrder['user_id'] = Auth::user()->id;
        $newOrder['item_id'] = $id;
        $newOrder['type'] = Order::WORKSHOP_TYPE;

        Order::create($newOrder);

        $paymentMode = PaymentMode::findOrFail($request->payment_mode_id);

        $price = $workshop->price;
        if (Auth::user()->coursesTakenNotOld2->count()) {
            $price = $price - 500;
        }

        $price = $price * 100;

        $payment_mode = $paymentMode->mode;
        if ($payment_mode == 'Faktura') {
            $payment_mode = 'Bankoverføring';
        }

        $comment = '(Workshop: '.$workshop->title.', ';
        $comment .= 'Betalingsmodus: '.$payment_mode.')';

        $dueDate = $workshop->faktura_date ?: date('Y-m-d');
        $dueDate = Carbon::parse($dueDate);
        if (! $workshop->faktura_date) {
            $dueDate->addDays(10);
        }
        $dueDate = date_format(date_create($dueDate), 'Y-m-d');

        $invoice_fields = [
            'user_id' => Auth::user()->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'netAmount' => $price,
            'dueDate' => $dueDate,
            'description' => 'Kursordrefaktura',
            'productID' => $workshop->fiken_product,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'address' => $request->street,
            'postalPlace' => $request->city,
            'postalCode' => $request->zip,
            'comment' => $comment,
            'payment_mode' => $paymentMode->mode,
        ];

        if ($isFree < 1) {
            $invoice = new FikenInvoice;
            $invoice->create_invoice($invoice_fields);
        }

        // if( $request->update_address ) :
        $address = Address::firstOrNew(['user_id' => Auth::user()->id]);
        $address->street = $request->street;
        $address->city = $request->city;
        $address->zip = $request->zip;
        $address->phone = $request->phone;
        $address->save();
        // endif;

        // send email to learner
        $user = Auth::user();

        $emailData['email_subject'] = $workshop->email_title;
        $emailData['email_message'] = nl2br($workshop->email_body);
        $emailData['from_name'] = null;
        $emailData['from_email'] = 'post@forfatterskolen.no';
        $emailData['attach_file'] = null;
        $user_email = $user->email;

        // add email to queue
        \Mail::to($user_email)->queue(new SubjectBodyEmail($emailData));

        // send email to admin
        $adminEmailData['email_subject'] = 'New Workshop Order';
        $adminEmailData['email_message'] = Auth::user()->first_name.' has ordered the workshop ';
        $adminEmailData['from_name'] = null;
        $adminEmailData['from_email'] = null;
        $adminEmailData['attach_file'] = null;

        // add email to queue
        \Mail::to('post@forfatterskolen.no')->queue(new SubjectBodyEmail($adminEmailData));

        if ($paymentMode->mode == 'Paypal') {
            $paypal = new Paypal;

            $response = $paypal->purchase([
                'amount' => ($price / 100),
                'transactionId' => $invoice->invoiceID,
                'currency' => 'NOK',
                'cancelUrl' => $paypal->getCancelUrl($invoice->invoiceID),
                'returnUrl' => $paypal->getReturnUrl($invoice->invoiceID, 'workshop'),
            ]);

            if ($response->isRedirect()) {
                $response->redirect();
            }

            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag($response->getMessage()),
            ]);
            /*echo '<form name="_xclick" id="paypal_form" style="display:none" action="https://www.paypal.com/cgi-bin/webscr" method="post">
                <input type="hidden" name="cmd" value="_xclick">
                <input type="hidden" name="business" value="post.forfatterskolen@gmail.com">
                <input type="hidden" name="currency_code" value="NOK">
                <input type="hidden" name="custom" value="'.$invoice->invoiceID.'">
                <input type="hidden" name="item_name" value="Course Order Invoice">
                <input type="hidden" name="amount" value="'.($price/100).'">
                <input type="hidden" name="return" value="'.route('front.shop.thankyou').'">
                <input type="image" name="submit" src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" align="right" alt="PayPal - The safer, easier way to pay online">
            </form>';
            echo '<script>document.getElementById("paypal_form").submit();</script>';
            return;*/
        }

        if ($paymentMode->mode == 'Vipps') {
            $orderId = $invoice->invoice_number;
            $transactionText = $workshop->title;
            $vippsData = [
                'amount' => $price,
                'orderId' => $orderId,
                'transactionText' => $transactionText,
            ];

            return $this->vippsInitiatePayment($vippsData);
        }

        return redirect(route('front.shop.thankyou', ['page' => 'workshop']));
    }

    public function validator($data)
    {
        return Validator::make($data, [
            'email' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'street' => 'required',
            'city' => 'required',
            'zip' => 'required',
            'payment_mode_id' => 'required',
        ]);
    }
}
