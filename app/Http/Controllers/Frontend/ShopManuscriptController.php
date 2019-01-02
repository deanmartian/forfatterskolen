<?php
namespace App\Http\Controllers\Frontend;

use App\Editor;
use App\Http\AdminHelpers;
use App\ShopManuscriptUpgrade;
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
use App\FreeManuscript;
use App\Log;
use Illuminate\Support\Facades\Session;
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
        $shopManuscripts = ShopManuscript::orderBy('full_payment_price', 'asc')->get();
        $editors = Editor::orderBy('id', 'ASC')->get();
    	return view('frontend.shop-manuscript.index', compact('shopManuscripts', 'editors'));
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
        $shopManuscriptTaken->user_id               = Auth::user()->id;
        $shopManuscriptTaken->genre                 = $request->genre;
        $shopManuscriptTaken->description           = $request->description;
        $shopManuscriptTaken->shop_manuscript_id    = $shopManuscript->id;


        $extensions = ['pdf', 'doc' ,'docx', 'odt'];
        $word_count = 0;
        
        if ($request->hasFile('manuscript') && $request->file('manuscript')->isValid()) :
            $extension = pathinfo($_FILES['manuscript']['name'],PATHINFO_EXTENSION);
            $original_filename = $request->manuscript->getClientOriginalName();

            if( !in_array($extension, $extensions) ) :
                return redirect()->back()->withInput()->with(
                    'manuscript_test_error', 'Invalid file format. Allowed formats are PDF, DOC, DOCX, ODT'
                );
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
            elseif($extension == "doc") :
                $docText = $this->readWord($destinationPath.$fileName);
                $word_count = FrontendHelpers::get_num_of_words($docText);
            elseif($extension == "odt") :
              $doc = odt2text($destinationPath.$fileName);
              $word_count = FrontendHelpers::get_num_of_words($doc);
            endif;
            $word_count = (int) $word_count;
            $shopManuscriptTaken->file = '/'.$destinationPath.$fileName;
            $shopManuscriptTaken->words = $word_count;

            // Admin notification
            $message = Auth::user()->full_name.' submitted a manuscript for shop manuscript '.$shopManuscriptTaken->shop_manuscript->title;
            $toMail = 'Camilla@forfatterskolen.no'; //post@forfatterskolen.no
            AdminHelpers::send_email('New manuscript submitted for shop manuscript',
                'post@forfatterskolen.no',$toMail, $message);
            //mail($toMail, 'New manuscript submitted for shop manuscript', $message);
        endif;

        if ($request->hasFile('synopsis') && $request->file('synopsis')->isValid()) :
            $extension = pathinfo($_FILES['synopsis']['name'],PATHINFO_EXTENSION);

            if( !in_array($extension, $extensions) ) :
                return redirect()->back();
            endif;

            $time = time();
            $destinationPath = 'storage/shop-manuscripts-synopsis/';
            $fileName = $time.'.'.$extension; // rename document
            $request->synopsis->move($destinationPath, $fileName);
            $shopManuscriptTaken->synopsis = '/'.$destinationPath.$fileName;
        endif;

        // check if the uploaded file exceeds the plan max words
        if ($word_count > $shopManuscript->max_words) {
            // get the plan that meets the word count uploaded
            $nextPlan = ShopManuscript::where('max_words','>=',$word_count)->first();
            return redirect()->back()->withErrors(['exceed' => 'Ditt manus er '.$word_count
                .' ord, du må bestille <a href="'.route('front.shop-manuscript.checkout', $nextPlan->id).'">'
                .$nextPlan->title.'</a>.']);
        }

        $paymentMode = PaymentMode::findOrFail($request->payment_mode_id);
        $paymentPlan = PaymentPlan::findOrFail($request->payment_plan_id);
        $payment_plan = ( $paymentMode->mode == "Paypal" ) ?  "Hele beløpet" : $paymentPlan->plan;

        $comment = '(Manuskript: ' . $shopManuscript->title . ', ';
        $comment .= 'Betalingsmodus: ' . $paymentMode->mode . ', ';
        $comment .= 'Betalingsplan: ' . $payment_plan . ')';

        $dueDate = date("Y-m-d");
        $dueDate = Carbon::parse($dueDate);
        $payment_plan = trim($payment_plan);
        if( $payment_plan == "Hele beløpet" ) :
            $price = (int)$shopManuscript->full_payment_price*100;
            $product_ID = $shopManuscript->full_price_product;
            $dueDate->addDays($shopManuscript->full_price_due_date);
        elseif( $payment_plan == "3 måneder" ) :
            $price = (int)$shopManuscript->months_3_price*100;
            $product_ID = $shopManuscript->months_3_product;
            $dueDate->addDays($shopManuscript->months_3_due_date);
        endif;
        $dueDate = date_format(date_create($dueDate), 'Y-m-d');


        if( $hasPaidCourse ) :
            $discount = $price * 0.05;
            $price = $price - ( (int)$discount );
            $comment .= ' - Discount: '.FrontendHelpers::currencyFormat($discount/100);
        endif;

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

        // wait for the invoice to be saved first before saving the shop manuscript taken
        $shopManuscriptTaken->is_active = false;
        $shopManuscriptTaken->save();

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



    public function upload_manuscript($id, Request $request)
    {
        $shopManuscriptTaken = ShopManuscriptsTaken::where('id', $id)->where('user_id', Auth::user()->id)->firstOrFail();
        $extensions = ['pdf', 'doc', 'docx', 'odt'];

        
        if ($request->hasFile('manuscript') && $request->file('manuscript')->isValid()) :
            $extension = pathinfo($_FILES['manuscript']['name'],PATHINFO_EXTENSION);
            $original_filename = $request->manuscript->getClientOriginalName();

            if( !in_array($extension, $extensions) ) :
                return redirect()->back()->with(
                    'manuscript_test_error', 'Invalid file format. Allowed formats are PDF, DOC, DOCX, ODT'
                );
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
            elseif($extension == "doc") :
                $docText = $this->readWord($destinationPath.$fileName);
                $word_count = FrontendHelpers::get_num_of_words($docText);
            elseif($extension == "odt") :
              $doc = odt2text($destinationPath.$fileName);
              $word_count = FrontendHelpers::get_num_of_words($doc);
            endif;
            $word_count = (int) $word_count;
            $shopManuscriptTaken->file = '/'.$destinationPath.$fileName;
            $shopManuscriptTaken->words = $word_count;
        endif;

        if ($request->hasFile('synopsis') && $request->file('synopsis')->isValid()) :
            $extension = pathinfo($_FILES['synopsis']['name'],PATHINFO_EXTENSION);

            if( !in_array($extension, $extensions) ) :
                return redirect()->back()->with(
                    'manuscript_test_error', 'Invalid file format. Allowed formats are PDF, DOC, DOCX, ODT'
                );
            endif;

            $time = time();
            $destinationPath = 'storage/shop-manuscripts-synopsis/';
            $fileName = $time.'.'.$extension; // rename document
            $request->synopsis->move($destinationPath, $fileName);
            $shopManuscriptTaken->synopsis = '/'.$destinationPath.$fileName;
        endif;

        if ($word_count > 17500) {
            $price = 0;

            /*
             * original hard coded price
             * switch ($word_count) {
                case $word_count <= 35000:
                    $price = 1400;
                    break;
                case $word_count <= 52500:
                    $price = 2250;
                    break;
                case $word_count <= 70000:
                    $price = 3000;
                    break;
                case $word_count <= 105000:
                    $price = 4000;
                    break;
                case $word_count <= 140000:
                    $price = 5000;
                    break;
            }*/

            $nextPlan = ShopManuscript::where('max_words','>=',$word_count)
                ->orderBy('max_words', 'ASC')->first();

            // get the upgrade price based on the current shop manuscript and the suggested shop manuscript
            $upgradePlan = ShopManuscriptUpgrade::where('shop_manuscript_id', $shopManuscriptTaken->shop_manuscript_id)
                ->where('upgrade_shop_manuscript_id', $nextPlan->id)->first();
            if ($upgradePlan) {
                $price = $upgradePlan->price;
                return redirect()->back()->with(['exceed' => $price, 'plan' => $nextPlan->id, 'max_words' => $nextPlan->max_words]);
            }
            return redirect()->back();
        } else {
        $shopManuscriptTaken->genre         = $request->genre;
        $shopManuscriptTaken->description   = $request->description;
        $shopManuscriptTaken->manuscript_uploaded_date = Carbon::now()->toDateTimeString();
        $shopManuscriptTaken->save();

        Log::create([
            'activity' => '<strong>'.Auth::user()->full_name.'</strong> submitted a manuscript for shop manuscript  '.$shopManuscriptTaken->shop_manuscript->title
        ]);
        // Admin notification
        $message = Auth::user()->full_name.' submitted a manuscript for shop manuscript '.$shopManuscriptTaken->shop_manuscript->title;
        $toMail = 'Camilla@forfatterskolen.no'; //post@forfatterskolen.no
        //mail($toMail, 'New manuscript submitted for shop manuscript', $message);
            AdminHelpers::send_email('New manuscript submitted for shop manuscript',
                'post@forfatterskolen.no', $toMail, $message);
        return redirect()->back();
        }
    }

    /**
     * Update the manuscript uploaded by the learner
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateUploadedManuscript($id, Request $request)
    {
        $shopManuscriptTaken = ShopManuscriptsTaken::where('id', $id)->where('user_id', Auth::user()->id)->first();
        $extensions = ['pdf', 'doc', 'docx', 'odt'];


        if ($request->hasFile('manuscript') && $request->file('manuscript')->isValid()) :
            $extension = pathinfo($_FILES['manuscript']['name'],PATHINFO_EXTENSION);
            $original_filename = $request->manuscript->getClientOriginalName();

            if( !in_array($extension, $extensions) ) :
                return redirect()->back()->with(
                    'manuscript_test_error', 'Invalid file format. Allowed formats are PDF, DOC, DOCX, ODT'
                );
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
            elseif($extension == "doc") :
                $docText = $this->readWord($destinationPath.$fileName);
                $word_count = FrontendHelpers::get_num_of_words($docText);
            elseif($extension == "odt") :
                $doc = odt2text($destinationPath.$fileName);
                $word_count = FrontendHelpers::get_num_of_words($doc);
            endif;
            $word_count = (int) $word_count;
            $shopManuscriptTaken->file = '/'.$destinationPath.$fileName;
            $shopManuscriptTaken->words = $word_count;
        endif;

        if ($request->hasFile('synopsis') && $request->file('synopsis')->isValid()) :
            $extension = pathinfo($_FILES['synopsis']['name'],PATHINFO_EXTENSION);

            if( !in_array($extension, $extensions) ) :
                return redirect()->back()->with(
                    'manuscript_test_error', 'Invalid file format. Allowed formats are PDF, DOC, DOCX, ODT'
                );
            endif;

            $time = time();
            $destinationPath = 'storage/shop-manuscripts-synopsis/';
            $fileName = $time.'.'.$extension; // rename document
            $request->synopsis->move($destinationPath, $fileName);
            $shopManuscriptTaken->synopsis = '/'.$destinationPath.$fileName;
        endif;

        if ($word_count > 17500) {
            $price = 0;

            /*switch ($word_count) {
                case $word_count <= 35000:
                    $price = 1400;
                    break;
                case $word_count <= 52500:
                    $price = 2250;
                    break;
                case $word_count <= 70000:
                    $price = 3000;
                    break;
                case $word_count <= 105000:
                    $price = 4000;
                    break;
                case $word_count <= 140000:
                    $price = 5000;
                    break;
            }*/

            $nextPlan = ShopManuscript::where('max_words','>=',$word_count)
                ->orderBy('max_words', 'ASC')->first();

            // get the upgrade price based on the current shop manuscript and the suggested shop manuscript
            $upgradePlan = ShopManuscriptUpgrade::where('shop_manuscript_id', $shopManuscriptTaken->shop_manuscript_id)
                ->where('upgrade_shop_manuscript_id', $nextPlan->id)->first();
            $price = $upgradePlan->price;

            return redirect()->back()->with(['exceed' => $price, 'plan' => $nextPlan->id, 'max_words' => $nextPlan]);
        } else {
            $shopManuscriptTaken->genre         = $request->genre;
            $shopManuscriptTaken->description   = $request->description;
            $shopManuscriptTaken->save();

            Log::create([
                'activity' => '<strong>'.Auth::user()->full_name.'</strong> submitted a manuscript for shop manuscript  '.$shopManuscriptTaken->shop_manuscript->title
            ]);
            // Admin notification
            $message = Auth::user()->full_name.' submitted a manuscript for shop manuscript '.$shopManuscriptTaken->shop_manuscript->title;
            //mail('post@forfatterskolen.no', 'New manuscript submitted for shop manuscript', $message);
            $toMail = 'Camilla@forfatterskolen.no'; //post@forfatterskolen.no
            AdminHelpers::send_email('New manuscript submitted for shop manuscript',
                'post@forfatterskolen.no', $toMail, $message);
            return redirect()->back();
        }
    }

    public function deleteUploadedManuscript($id)
    {
        $shopManuscriptTaken = ShopManuscriptsTaken::where('id', $id)->where('user_id', Auth::user()->id)->first();
        $shopManuscriptTaken->file                  = NULL;
        $shopManuscriptTaken->words                 = NULL;
        $shopManuscriptTaken->genre                 = 0;
        $shopManuscriptTaken->description           = NULL;
        $shopManuscriptTaken->is_manuscript_locked  = 0;
        $shopManuscriptTaken->synopsis              = NULL;
        $shopManuscriptTaken->save();
        return redirect()->back();
    }


    public function test_manuscript(Request $request)
    {
        $extensions = ['pdf', 'doc', 'docx', 'odt'];

    	if( $request->hasFile('manuscript') &&  $request->file('manuscript')->isValid() ) :
    		$extension = pathinfo($_FILES['manuscript']['name'],PATHINFO_EXTENSION);
    		$original_filename = $request->manuscript->getClientOriginalName();

    		if( !in_array($extension, $extensions) ) :
                return redirect()->back()->with(
                    'manuscript_test_error', '<h3>Invalid file format. Allowed formats are PDF, DOC, DOCX, ODT</h3>'
                );
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
            elseif($extension == "doc") :
                $docText = $this->readWord($destinationPath.$fileName);
                $word_count = FrontendHelpers::get_num_of_words($docText);
            elseif($extension == "odt") :
              $doc = odt2text($destinationPath.$fileName);
              $word_count = FrontendHelpers::get_num_of_words($doc);
            endif;
            $word_count = (int) $word_count;

            /*
             * original code for price
             * if($word_count <= 5000) :
                $price = '1,500 KR';
                $button_link = 'http://www.forfatterskolen.no/shop-manuscript/9/checkout';
            elseif($word_count <= 17500) :
                $price = '2,900 KR';
                $button_link = 'http://www.forfatterskolen.no/shop-manuscript/3/checkout';
            elseif($word_count <= 35000) :
                $price = '3,900 KR';
                $button_link = 'http://www.forfatterskolen.no/shop-manuscript/4/checkout';
            elseif($word_count <= 52500) :
                $price = '5,000 KR';
                $button_link = 'http://www.forfatterskolen.no/shop-manuscript/5/checkout';
            elseif($word_count <= 70000) :
                $price = '6,100 KR';
                $button_link = 'http://www.forfatterskolen.no/shop-manuscript/6/checkout';
            elseif($word_count <= 105000) :
                $price = '7,800 KR';
                $button_link = 'http://www.forfatterskolen.no/shop-manuscript/7/checkout';
            elseif($word_count <= 140000) :
                $price = '9,500 KR';
                $button_link = 'http://www.forfatterskolen.no/shop-manuscript/8/checkout';
            endif;*/

            $suggestedPlan = ShopManuscript::where('max_words','>=', $word_count)
                ->orderBy('max_words', 'ASC')->first();
            if ($suggestedPlan) {
                $price = $suggestedPlan->full_payment_price;
                $button_link = route('front.shop-manuscript.checkout', $suggestedPlan->id);
            }

    	endif;

        $message = 'Manuset ditt er på '.$word_count.' ord <br />
        <h3  class="no-margin-top">Prisen for ditt manus er kroner: '.$price.'</h3>
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

    public function upgradeValidator($data)
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

    public function freeManuscriptShow()
    {
        return view('frontend.shop-manuscript.free-manuscript');
    }



    public function freeManuscriptShowSuccess()
    {
        return view('frontend.shop-manuscript.free-manuscript-success');
    }

    /**
     * Display the checkout page for upgrading manuscript
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function checkoutUpgradeManuscript($id)
    {
        $shopManuscript         = ShopManuscript::findOrFail($id);
        $shopManuscriptTaken    = Auth::user()->shopManuscriptsTaken;
        $upgradeManuscript      = ShopManuscriptUpgrade::where('shop_manuscript_id', $shopManuscriptTaken[0]->shop_manuscript->id)
        ->where('upgrade_shop_manuscript_id', $id)->first();


        return view('frontend.shop-manuscript.upgrade', compact('shopManuscript', 'upgradeManuscript'));
    }

    public function upgradeManuscript($id, Request $request)
    {
        $validator = $this->upgradeValidator($request->all());
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

        $previousManuscript = ShopManuscript::where('max_words','<',$shopManuscript->max_words)->first();

        //$shopManuscriptTaken = ShopManuscriptsTaken::where('shop_manuscript_id',$previousManuscript->id)->where('user_id',Auth::user()->id)->first();
        $shopManuscriptTaken    = Auth::user()->shopManuscriptsTaken;
        $upgradeManuscript      = ShopManuscriptUpgrade::where('shop_manuscript_id', $shopManuscriptTaken[0]->shop_manuscript->id)
            ->where('upgrade_shop_manuscript_id', $id)->first();


        $extensions = ['pdf', 'doc', 'docx', 'odt'];
        $shopManuscriptTaken->shop_manuscript_id = $shopManuscript->id;


        if ($request->hasFile('manuscript') && $request->file('manuscript')->isValid()) :
            $extension = pathinfo($_FILES['manuscript']['name'],PATHINFO_EXTENSION);
            $original_filename = $request->manuscript->getClientOriginalName();

            if( !in_array($extension, $extensions) ) :
                return redirect()->back()->withInput()->with(
                    'manuscript_test_error', 'Invalid file format. Allowed formats are PDF, DOC, DOCX, ODT'
                );
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
            elseif($extension == "doc") :
                $docText = $this->readWord($destinationPath.$fileName);
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
        $paymentPlan = PaymentPlan::findOrFail(6);
        $payment_plan = ( $paymentMode->mode == "Paypal" ) ?  "Hele beløpet" : $paymentPlan->plan;

        $comment = '(Manuskript: ' . $shopManuscript->title . ', ';
        $comment .= 'Betalingsmodus: ' . $paymentMode->mode . ', ';
        $comment .= 'Betalingsplan: ' . $payment_plan . ')';

        $dueDate = date("Y-m-d");
        $dueDate = Carbon::parse($dueDate);

        $price = (int)$upgradeManuscript->price*100;
        $dueDate->addDays($shopManuscript->full_price_due_date);

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


    public function freeManuscriptWordCount(Request $request)
    {
        \Session::put('wordcount', $request->wordcount);
        return response()->json(['data' => $request->wordcount]);
    }


    public function freeManuscriptSend( Request $request )
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'genre' => 'required',
            'content' => 'required',
        ]);

        $wordcount = Session::get('wordcount');

        if ($wordcount > 500) {
            return redirect()->back()->withInput()->with([
                'errors' => AdminHelpers::createMessageBag('The content may not be greater than 500 words.')
            ]);
        }

        $existing_emails = FreeManuscript::all()->pluck('email')->toArray();
        // prevent user from sending multiple manuscript
        if (in_array($request->email, $existing_emails)) {
            return redirect()->back()->withInput()->with([
                'errors' => AdminHelpers::createMessageBag('Beklager, men du har allerede benyttet deg av dette gratistilbudet
Er det feil må du sende en mail til <a href="mailto:post@forfatterskolen.no">post@forfatterskolen.no</a>')
            ]);
        }

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

            //mail('post@forfatterskolen.no', 'Free Manuscript', view('emails.free-manuscript', compact('name', 'email', 'content', 'word_count')), $headers);
            AdminHelpers::send_email('Free Manuscript',
                'post@forfatterskolen.no', 'post@forfatterskolen.no',
                view('emails.free-manuscript', compact('name', 'email', 'content', 'word_count')));
            FreeManuscript::create([
                'name' => $request->name,
                'email' => $request->email,
                'genre' => $request->genre,
                'content' => $request->content
            ]);

            // forget the wordcount
            Session::forget('wordcount');
            return redirect(route('front.free-manuscript.success'));
        endif;
    }

    function readWord($filename) {
        if(file_exists($filename))
        {
            if(($fh = fopen($filename, 'r')) !== false )
            {
                $headers = fread($fh, 0xA00);

                // 1 = (ord(n)*1) ; Document has from 0 to 255 characters
                $n1 = ( ord($headers[0x21C]) - 1 );

                // 1 = ((ord(n)-8)*256) ; Document has from 256 to 63743 characters
                $n2 = ( ( ord($headers[0x21D]) - 8 ) * 256 );

                // 1 = ((ord(n)*256)*256) ; Document has from 63744 to 16775423 characters
                $n3 = ( ( ord($headers[0x21E]) * 256 ) * 256 );

                // 1 = (((ord(n)*256)*256)*256) ; Document has from 16775424 to 4294965504 characters
                $n4 = ( ( ( ord($headers[0x21F]) * 256 ) * 256 ) * 256 );

                // Total length of text in the document
                $textLength = ($n1 + $n2 + $n3 + $n4);

                $extracted_plaintext = fread($fh, $textLength);

                // if you want to see your paragraphs in a new line, do this
                // return nl2br($extracted_plaintext);
                return $extracted_plaintext;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

}
