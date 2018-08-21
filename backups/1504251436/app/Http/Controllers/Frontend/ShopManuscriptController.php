<?php
namespace App\Http\Controllers\Frontend;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\FrontendHelpers;
use App\User;
use App\Address;
use App\ShopManuscript;
use App\PaymentMode;
use App\PaymentPlan;
use App\ShopManuscriptsTaken;
use Validator;
use Carbon\Carbon;
use App\Http\FikenInvoice;


include_once($_SERVER['DOCUMENT_ROOT'].'/Docx2Text.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/Pdf2Text.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/Odt2Text.php');

class ShopManuscriptController extends Controller
{
   
    public function index()
    {
        $shopManuscripts = ShopManuscript::orderBy('price', 'asc')->get();
    	return view('frontend.shop-manuscript.index', compact('shopManuscripts'));
    }




    public function checkout($id)
    {
      $shopManuscript = ShopManuscript::findOrFail($id);
      return view('frontend.shop-manuscript.checkout', compact('shopManuscript'));
    }




    public function place_order($id, Request $request)
    {
      $validator = $this->validator($request->all());
      if( $validator->fails() ) :
        return redirect()->back()->withInput()->withErrors($validator);
      endif;

      $shopManuscript = ShopManuscript::findOrFail($id);

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

        $hasPaidCourse = false;
        foreach( Auth::user()->coursesTaken as $courseTaken ) :
            if( $courseTaken->package->course->type != "Free" && $courseTaken->is_active ) :
                $hasPaidCourse = true;
                break;
            endif;
        endforeach;

        $shopManuscriptTaken = new ShopManuscriptsTaken();
        $shopManuscriptTaken->user_id = Auth::user()->id;
        $shopManuscriptTaken->shop_manuscript_id = $shopManuscript->id;


        $extensions = ['pdf', 'docx', 'odt'];

        
        if ($request->hasFile('manuscript') && $request->file('manuscript')->isValid()) :
            $extension = pathinfo($_FILES['manuscript']['name'],PATHINFO_EXTENSION);
            $original_filename = $request->manuscript->getClientOriginalName();

            if( !in_array($extension, $extensions) ) :
                return redirect()->back();
            endif;

            $time = time();
            $destinationPath = 'storage/shop-manuscripts/';
            $fileName = $time.'.'.$extension; // rename document
            $request->manuscript->move($destinationPath, $fileName);
            if($extension == "pdf") :
              $pdf  =  new \PdfToText ( $destinationPath.$fileName ) ;
              $pdf_content = $pdf->Text; 
              $word_count = FrontendHelpers::get_num_of_words($pdf_content);
            elseif($extension == "docx") :
              $docObj = new \Docx2Text($destinationPath.$fileName);
              $docText= $docObj->convertToText();
              $word_count = FrontendHelpers::get_num_of_words($docText);
            elseif($extension == "odt") :
              $doc = odt2text($destinationPath.$fileName);
              $word_count = FrontendHelpers::get_num_of_words($doc);
            endif;
            $word_count = (int) $word_count;
            $shopManuscriptTaken->file = '/'.$destinationPath.$fileName;
            $shopManuscriptTaken->words = $word_count;
        endif;

        $shopManuscriptTaken->is_active = false;
        $shopManuscriptTaken->save();



        $paymentMode = PaymentMode::findOrFail($request->payment_mode_id);
        $paymentPlan = PaymentPlan::findOrFail($request->payment_plan_id);

        $payment_plan = ( $paymentMode->mode == "Paypal" ) ?  "Hele beløpet" : $paymentPlan->plan;
        $price = ( $payment_plan == "Hele beløpet" ) ? (int)$shopManuscript->price*100 : (int)$shopManuscript->split_payment_price*100;

        $comment = '(Manuskript: ' . $shopManuscript->title . ', ';
        $comment .= 'Betalingsmodus: ' . $paymentMode->mode . ', ';
        $comment .= 'Betalingsplan: ' . $payment_plan . ')';

        if( $hasPaidCourse ) :
            $discount = $price * 0.05;
            $price = $price - ( (int)$discount );
            $comment .= ' - Discount: '.FrontendHelpers::currencyFormat($discount/100);
        endif;

        $dueDate = date("Y-m-d");
        $dueDate = Carbon::parse($dueDate);
        $dueDate->addDays(10);
        $dueDate = date_format(date_create($dueDate), 'Y-m-d');

        $invoice_fields = [
            'user_id' => Auth::user()->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'netAmount' => $price,
            'dueDate' => $dueDate,
            'description' => 'Kursordrefaktura',
            'productID' => $shopManuscript->fiken_product,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'address' => $request->street,
            'postalPlace' => $request->city,
            'postalCode' => $request->zip,
            'comment' => $comment,
        ];

        $invoice = new FikenInvoice();
        $invoice->create_invoice($invoice_fields);

        if( $request->update_address ) :
            $address = Address::firstOrNew(['user_id' => Auth::user()->id]);
            $address->street = $request->street;
            $address->city = $request->city;
            $address->zip = $request->zip;
            $address->phone = $request->phone;
            $address->save();
        endif;

        
        if( $paymentMode->mode == "Paypal" ) :
            echo '<form name="_xclick" id="paypal_form" style="display:none" action="https://www.paypal.com/cgi-bin/webscr" method="post">
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
            return;
        endif;



        return redirect(route('front.shop.thankyou'));
    }





    public function test_manuscript(Request $request)
    {
        $extensions = ['pdf', 'docx', 'odt'];

    	if( $request->hasFile('manuscript') &&  $request->file('manuscript')->isValid() ) :
    		$extension = pathinfo($_FILES['manuscript']['name'],PATHINFO_EXTENSION);
    		$original_filename = $request->manuscript->getClientOriginalName();

    		if( !in_array($extension, $extensions) ) :
                return redirect()->back();
            endif;

            $time = time();
            $destinationPath = 'storage/manuscript-tests/'; // upload path
            $fileName = $time.'.'.$extension; // rename document
            $request->manuscript->move($destinationPath, $fileName);
			if($extension == "pdf") :
              $pdf  =  new \PdfToText ( $destinationPath.$fileName ) ;
              $pdf_content = $pdf->Text; 
              $word_count = FrontendHelpers::get_num_of_words($pdf_content);
            elseif($extension == "docx") :
              $docObj = new \Docx2Text($destinationPath.$fileName);
              $docText= $docObj->convertToText();
              $word_count = FrontendHelpers::get_num_of_words($docText);
            elseif($extension == "odt") :
              $doc = odt2text($destinationPath.$fileName);
              $word_count = FrontendHelpers::get_num_of_words($doc);
            endif;
            $word_count = (int) $word_count;
            if($word_count <= 5000) :
                $price = '1,250 KR';
                $button_link = 'http://www.forfatterskolen.no/shop-manuscript/9/checkout';
            elseif($word_count <= 17500) :
                $price = '2,900 KR';
                $button_link = 'http://www.forfatterskolen.no/shop-manuscript/3/checkout';
            elseif($word_count <= 35000) :
                $price = '3,500 KR';
                $button_link = 'http://www.forfatterskolen.no/shop-manuscript/4/checkout';
            elseif($word_count <= 52500) :
                $price = '4,350 KR';
                $button_link = 'http://www.forfatterskolen.no/shop-manuscript/5/checkout';
            elseif($word_count <= 70000) :
                $price = '5,000 KR';
                $button_link = 'http://www.forfatterskolen.no/shop-manuscript/6/checkout';
            elseif($word_count <= 105000) :
                $price = '6,000 KR';
                $button_link = 'http://www.forfatterskolen.no/shop-manuscript/7/checkout';
            elseif($word_count <= 140000) :
                $price = '7,000 KR';
                $button_link = 'http://www.forfatterskolen.no/shop-manuscript/8/checkout';
            endif;
    	endif;
        $message = $word_count.' ORD <br />
        <h3  class="no-margin-top">'.$price.'</h3>
        <a href="'.$button_link.'" class="btn btn-theme">Bestill nå</a>';
    	return redirect()->back()->with('manuscript_test', $message);
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
        'payment_plan_id' => 'required',
      ]);
    }



    public function freeManuscriptShow()
    {
        return view('frontend.shop-manuscript.free-manuscript');
    }



    public function freeManuscriptShowSuccess()
    {
        return view('frontend.shop-manuscript.free-manuscript-success');
    }

    



    public function freeManuscriptSend( Request $request )
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'content' => 'required',
        ]);

        if( $validator->fails() ) :
            return redirect()->back()->withInput()->withErrors($validator);
        endif;

        $name = $request->name;
        $email = $request->email;
        $content = $request->content;
        $word_count = FrontendHelpers::get_num_of_words($request->content);

        if( $word_count > 0 ) :
            // Send email
            $actionText = 'View Our Courses';
            $actionUrl = 'http://dev.forfatterskolen.no/course';
            $headers = "From: Forfatterskolen<no-reply@forfatterskolen.no>\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

            mail('post@forfatterskolen.no', 'Free Manuscript', view('emails.free-manuscript', compact('name', 'email', 'content', 'word_count')), $headers);
            return redirect(route('front.free-manuscript.success'));
        endif;
    }

}
