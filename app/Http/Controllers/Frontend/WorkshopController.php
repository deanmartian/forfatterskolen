<?php
namespace App\Http\Controllers\Frontend;

use App\Http\AdminHelpers;
use App\Paypal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Workshop;
use App\WorkshopsTaken;
use App\WorkshopMenu;
use App\PaymentMode;
use App\Address;
use App\User;
use App\Http\FrontendHelpers;
use Validator;
use Carbon\Carbon;
require app_path('/Http/PaypalIPN/PaypalIPN.php');
use PaypalIPN;
use App\Http\FikenInvoice;

class WorkshopController extends Controller
{
   
    public function index()
    {
        $workshops = Workshop::orderBy('faktura_date', 'ASC')->get();
        return view('frontend.workshop.index', compact('workshops'));
    }


    public function show($id)
    {
        $workshop = Workshop::findOrFail($id);
        return view('frontend.workshop.show', compact('workshop'));
    }



    public function checkout($id)
    {
        $workshop = Workshop::findOrFail($id);
        return view('frontend.workshop.checkout', compact('workshop'));
    }


    public function place_order($id, Request $request)
    {
      $validator = $this->validator($request->all());
      if( $validator->fails() ) :
        return redirect()->back()->withInput()->withErrors($validator);
      endif;

      $workshop = Workshop::findOrFail($id);
      $menu = WorkshopMenu::findOrFail($request->menu_id);
      if( $menu->workshop_id != $workshop->id ) return redirect()->back()->withInput()->withErrors(['Invalid menu']);

      if( Auth::guest() ) :
            $user = User::where('email', $request->email)->first();
            if( $user ) :
                return redirect()->back()->withInput()->withErrors(['The email you provided is already registered. <a href="#" data-toggle="collapse" data-target="#checkoutLogin">Login Here</a>']);
            else :
                // register new user
                $new_user = new User();
                $new_user->email = $request->email;
                $new_user->first_name = $request->first_name;
                $new_user->last_name = $request->last_name;
                $new_user->password = bcrypt($request->password);
                $new_user->save();
                Auth::login($new_user);
            endif;
        endif;

        $courseWorkshops = 0;
        $isFree = 0;
        foreach( Auth::user()->coursesTaken as $courseTaken ) {
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

        $workshopTaken = new WorkshopsTaken();
        $workshopTaken->user_id = Auth::user()->id;
        $workshopTaken->workshop_id = $workshop->id;
        $workshopTaken->menu_id = $menu->id;
        $workshopTaken->notes = $request->notes;


        $workshopTaken->is_active = false;
        $workshopTaken->save();



        $paymentMode = PaymentMode::findOrFail($request->payment_mode_id);

        $price = $workshop->price*100;


        $payment_mode = $paymentMode->mode;
        if( $payment_mode == 'Faktura' ) :
            $payment_mode = 'Bankoverføring';
        endif;

        $comment = '(Workshop: ' . $workshop->title . ', ';
        $comment .= 'Betalingsmodus: ' . $payment_mode . ')';

        $dueDate = $workshop->faktura_date ?: date("Y-m-d");
        $dueDate = Carbon::parse($dueDate);
        if (!$workshop->faktura_date) {
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
        ];

        if ($isFree < 1) {
            $invoice = new FikenInvoice();
            $invoice->create_invoice($invoice_fields);
        }

        if( $request->update_address ) :
            $address = Address::firstOrNew(['user_id' => Auth::user()->id]);
            $address->street = $request->street;
            $address->city = $request->city;
            $address->zip = $request->zip;
            $address->phone = $request->phone;
            $address->save();
        endif;

        
        if( $paymentMode->mode == "Paypal" ) :
            $paypal = new Paypal;

            $response = $paypal->purchase([
                'amount' => ($price/100),
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
        endif;



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
