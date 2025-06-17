<?php

namespace App\Http\Controllers\Frontend;

use App\Address;
use App\CheckoutLog;
use App\Editor;
use App\Events\AddToCampaignList;
use App\Exports\GenericExport;
use App\FreeManuscript;
use App\Http\AdminHelpers;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Controller;
use App\Http\FikenInvoice;
use App\Http\FrontendHelpers;
use App\Invoice;
use App\Jobs\AddMailToQueueJob;
use App\Jobs\SveaUpdateOrderDetailsJob;
use App\Log;
use App\Mail\SubjectBodyEmail;
use App\Order;
use App\OrderShopManuscript;
use App\PaymentMode;
use App\PaymentPlan;
use App\Paypal;
use App\Services\CourseService;
use App\Services\ShopManuscriptService;
use App\ShopManuscript;
use App\ShopManuscriptsTaken;
use App\ShopManuscriptUpgrade;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator as FacadeValidator;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Str;
use Validator;

include_once $_SERVER['DOCUMENT_ROOT'].'/Docx2Text.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/Pdf2Text.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/Odt2Text.php';

class ShopManuscriptController extends Controller
{
    public function index(): View
    {
        $shopManuscripts = ShopManuscript::orderBy('full_payment_price', 'asc')->get();
        $editors = Editor::orderBy('id', 'ASC')->get();
        $checkoutRoute = 'front.shop-manuscript.checkout';

        return view('frontend.shop-manuscript.index', compact('shopManuscripts', 'editors', 'checkoutRoute'));
    }

    public function checkout($id): View
    {
        $shopManuscript = ShopManuscript::findOrFail($id);
        $user = \Auth::user();
        $userHasPaidCourse = FrontendHelpers::userHasPaidCourse();

        if ($user) {
            $user['address'] = $user->address;
            $user->checkoutLogs()->firstOrCreate([
                'parent' => 'shop-manuscript',
                'parent_id' => $shopManuscript->id,
            ]);
        } else {
            return view('frontend.shop-manuscript.login');
        }
        $assignmentTypes = FrontendHelpers::assignmentType();
        $originalPrice = $shopManuscript->full_payment_price;
        if (! Str::contains($shopManuscript->title, 'Start') && ! Str::contains($shopManuscript->title, '1')) {
            $extra_price = ($shopManuscript->max_words - 17500) * FrontendHelpers::manuscriptExcessPerWordPrice();
            $originalPrice = $shopManuscript->full_payment_price + $extra_price;
        }

        return view('frontend.shop-manuscript.checkout-svea', compact('shopManuscript', 'user', 'assignmentTypes',
            'userHasPaidCourse', 'originalPrice'));
    }

    /**
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function validateOrder($shop_manuscript_id, Request $request, ShopManuscriptService $shopManuscriptService)
    {
        if (! $request->has('is_manuscript_only')) {
            $request->validate([
                'manuscript' => 'required',
                'genre' => 'required',
            ]);
        }

        if ($request->hasFile('manuscript')) {
            $file = $request->file('manuscript');
            $extension = $file->getClientOriginalExtension();

            if (! in_array($extension, ['docx'])) { // 'odt', 'pdf', 'doc',
                $customErrors = ['manuscript' => ['The manuscript must be a file of type: docx.']]; // odt, pdf, doc,
                $validator = FacadeValidator::make([], []);
                $validator->validate(); // Perform validation without rules
                $validator->errors()->merge($customErrors);

                throw new ValidationException($validator);
            }
        }

        if ($request->has('synopsis')) {
            $request->validate([
                'synopsis' => 'mimes:pdf,doc,docx,odt',
            ]);
        }

        $uploadedManuscript = $shopManuscriptService->uploadManuscriptTest($request);
        if ($uploadedManuscript['word_count'] == 0) {
            $customErrors = ['manuscript' => ['The manuscript word count is invalid.']];
            $validator = FacadeValidator::make([], []);
            $validator->validate(); // Perform validation without rules
            $validator->errors()->merge($customErrors);

            throw new ValidationException($validator);
        }

        $shopManuscript = ShopManuscript::find($shop_manuscript_id);
        $word_count = $uploadedManuscript['word_count'];
        // $word_to_deduct = $word_count * 0.02;
        $new_word_count = $word_count; // ceil($word_count - $word_to_deduct);
        $excess_words = $new_word_count - 17500; // deduct the manusutvikling 1 max words

        // check if the uploaded file exceeds the plan max words
        if ($new_word_count > $shopManuscript->max_words) {
            // get the plan that meets the word count uploaded
            $nextPlan = ShopManuscript::where('max_words', '>=', $new_word_count)->first();

            return response()->json([
                'message' => 'Ditt manus er '.$word_count
                    .' ord, du må bestille <a href="'.route('front.shop-manuscript.checkout', $nextPlan->id).'"
                     style="color: #000; font-weight: bold">'
                    .$nextPlan->title.'</a>.',
            ], 400);
        }

        $excessPerWordAmount = FrontendHelpers::manuscriptExcessPerWordPrice();

        $request->merge([
            'manuscript_file' => $uploadedManuscript['manuscript_file'],
            'word_count' => $uploadedManuscript['word_count'],
            'excess_words' => $excess_words,
            'excess_words_amount' => $excess_words > 0 ? $excess_words * $excessPerWordAmount : 0,
            'price' => $shopManuscript->full_payment_price,
        ]);

        return $request->all();
    }

    public function validateForm($shop_manuscript_id, Request $request, CourseService $courseService,
        ShopManuscriptService $shopManuscriptService): JsonResponse
    {
        $validation = [
            'email' => 'required|email',
            'first_name' => 'required',
            'last_name' => 'required',
            'street' => 'required',
            'zip' => 'required',
            'city' => 'required',
            'phone' => 'required',
        ];

        if (! \Auth::check()) {
            $validation['password'] = 'required|min:3';
        }

        $validator = \Validator::make($request->all(), $validation);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (! \Auth::check()) {
            $addressData = [
                'street' => $request->street,
                'zip' => $request->zip,
                'city' => $request->city,
                'phone' => $request->phone,
            ];
            $courseService->evaluateUser($request->email, $request->password, $request->first_name, $request->last_name, $addressData);
        }

        $user = \Auth::user();
        $user->checkoutLogs()->firstOrCreate([
            'parent' => 'shop-manuscript',
            'parent_id' => $shop_manuscript_id,
        ]);

        return response()->json($shopManuscriptService->processCheckout($request));
    }

    public function vippsCheckout($shop_manuscript_id, Request $request, ShopManuscriptService $shopManuscriptService,
        LoginController $loginController)
    {
        $validatedOrder = $this->validateOrder($shop_manuscript_id, $request, $shopManuscriptService);

        if (is_array($validatedOrder)) {

            $request->merge([
                'item_type' => 'shop-manuscript',
                'manuscript_file' => $validatedOrder['manuscript_file'],
                'word_count' => $validatedOrder['word_count'],
                'synopsis_file' => $shopManuscriptService->uploadSynopsis($request),
            ]);
            $data = $request->except('_token', 'synopsis', 'manuscript');

            $checkoutDetails = collect($data);
            \Session::put('vipps_checkout', $checkoutDetails);

            return response()->json(['redirect_link' => $loginController->vippsLogin('checkout_state')]);
            /*$vipps = \Session::get('vipps_checkout');
            return response()->json(['redirect_link' => route('front.shop-manuscript.checkout.process-vipps',$vipps['shop_manuscript_id'])]);*/
        }

        return $validatedOrder;
    }

    public function processVipps(ShopManuscriptService $shopManuscriptService): RedirectResponse
    {
        $vippsCheckout = \Session::get('vipps_checkout');
        $request = new \Illuminate\Http\Request;
        $request->replace($vippsCheckout->toArray());

        $orderRecord = $shopManuscriptService->createOrder($request);

        if (! $request->has('order_type') ||
            ($request->has('order_type') && $request->order_type === Order::MANUSCRIPT_TYPE)) {

            OrderShopManuscript::create([
                'order_id' => $orderRecord->id,
                'genre' => $request->genre,
                'file' => '/'.$vippsCheckout['manuscript_file'],
                'words' => $vippsCheckout['word_count'],
                'description' => $request->description,
                'synopsis' => $shopManuscriptService->uploadSynopsis($request),
                'coaching_time_later' => filter_var($request->coaching_time_later, FILTER_VALIDATE_BOOLEAN),
                'send_to_email' => filter_var($request->send_to_email, FILTER_VALIDATE_BOOLEAN),
            ]);

        }

        $price = $orderRecord->price - $orderRecord->discount;
        $user = Auth::user();

        $vippsData = [
            'amount' => $price * 100,
            'orderId' => $orderRecord->id.'-'.$user->id,
            'transactionText' => $orderRecord->item,
            'is_ajax' => true,
            'vipps_phone_number' => $user->address->vipps_phone_number,
            'fallbackUrl' => url('/shop-manuscript/'.$orderRecord->item_id.'/thankyou'),
        ];

        return redirect()->to($this->vippsInitiatePayment($vippsData));

    }

    public function orderCancelled($manuscript_id): View
    {
        return view('frontend.shop-manuscript.cancelled-order', compact('manuscript_id'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function thankyou($id, Request $request, ShopManuscriptService $shopManuscriptService): View
    {
        // check if from svea payment
        if ($request->has('svea_ord') || $request->has('pl_ord')) {
            $order_id = $request->input('svea_ord') ?? $request->input('pl_ord');
            $order = Order::find($order_id);

            if ($request->has('svea_ord')) {
                SveaUpdateOrderDetailsJob::dispatch($order->id)->delay(Carbon::now()->addMinute(1));
            }

            // add shop manuscript to user
            if (! $order->is_processed) {
                $shopManuscriptTaken = $shopManuscriptService->addShopManuscriptToLearner($order);
                $shopManuscriptService->notifyAdmin($order);
                $shopManuscriptService->notifyUser($order, $shopManuscriptTaken);
            }

            $order->is_processed = 1;
            $order->save();

            CheckoutLog::updateOrCreate([
                'user_id' => \auth()->id(),
                'parent' => 'shop-manuscript',
                'parent_id' => $id,
            ], [
                'is_ordered' => true,
            ]);
        }

        \Session::remove('vipps_checkout');

        return view('frontend.shop-manuscript.thankyou');
    }

    public function place_order($id, Request $request)
    {
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $shopManuscript = ShopManuscript::findOrFail($id);

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

        $hasPaidCourse = false;
        foreach (Auth::user()->coursesTaken as $courseTaken) {
            if ($courseTaken->package->course->type != 'Free' && $courseTaken->is_active && ! $courseTaken->is_free) {
                $hasPaidCourse = true;
                break;
            }
        }

        $paymentMode = PaymentMode::findOrFail($request->payment_mode_id);
        $paymentPlan = PaymentPlan::findOrFail($request->payment_plan_id);
        $payment_plan = ($paymentMode->mode == 'Paypal') ? 'Hele beløpet' : $paymentPlan->plan;

        // additional checking if the user selects correct payment mode for the selected plan
        // not faktura and payment plan is not full payment or split invoice
        if ($paymentMode->id !== 3 && ($paymentPlan->id != 8 || isset($request->split_invoice))) {
            return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Invalid payment mode for the selected plan')]);
        } else {
            // payment is faktura and wants to split invoice
            if ($paymentPlan->id == 8 && (isset($request->split_invoice) && $request->split_invoice)) {
                return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Invalid payment mode for the selected plan')]);
            }
        }

        $shopManuscriptTaken = new ShopManuscriptsTaken;
        $shopManuscriptTaken->user_id = Auth::user()->id;
        $shopManuscriptTaken->genre = $request->genre;
        $shopManuscriptTaken->description = $request->description;
        $shopManuscriptTaken->shop_manuscript_id = $shopManuscript->id;

        $extensions = ['pdf', 'doc', 'docx', 'odt'];
        $word_count = 0;

        if ($request->hasFile('manuscript') && $request->file('manuscript')->isValid()) {
            $extension = pathinfo($_FILES['manuscript']['name'], PATHINFO_EXTENSION);
            $original_filename = $request->manuscript->getClientOriginalName();

            if (! in_array($extension, $extensions)) {
                return redirect()->back()->withInput()->with(
                    'manuscript_test_error', 'Invalid file format. Allowed formats are PDF, DOC, DOCX, ODT'
                );
            }

            $time = time();
            $destinationPath = 'storage/shop-manuscripts';
            $filePath = AdminHelpers::checkFileName($destinationPath, Auth::user()->id, $extension); // rename document
            $expFileName = explode('/', $filePath);

            $request->manuscript->move($destinationPath, end($expFileName));
            if ($extension == 'pdf') {
                $pdf = new \PdfToText($filePath);
                $pdf_content = $pdf->Text;
                $word_count = FrontendHelpers::get_num_of_words($pdf_content);
            } elseif ($extension == 'docx') {
                $docObj = new \Docx2Text($filePath);
                $docText = $docObj->convertToText();
                $word_count = FrontendHelpers::get_num_of_words($docText);
            } elseif ($extension == 'doc') {
                $docText = $this->readWord($filePath);
                $word_count = FrontendHelpers::get_num_of_words($docText);
            } elseif ($extension == 'odt') {
                $doc = odt2text($filePath);
                $word_count = FrontendHelpers::get_num_of_words($doc);
            }
            $word_count = FrontendHelpers::wordCountByMargin((int) $word_count);
            $shopManuscriptTaken->file = '/'.$filePath;
            $shopManuscriptTaken->words = $word_count;

            // Admin notification
            $message = Auth::user()->full_name.' submitted a manuscript for shop manuscript '.$shopManuscriptTaken->shop_manuscript->title;
            $toMail = 'post@forfatterskolen.no'; // post@forfatterskolen.no
            /*AdminHelpers::send_email('New manuscript submitted for shop manuscript',
                'post@forfatterskolen.no',$toMail, $message);*/
            $to = 'post@forfatterskolen.no'; //
            $emailData = [
                'email_subject' => 'New manuscript submitted for shop manuscript',
                'email_message' => $message,
                'from_name' => '',
                'from_email' => 'post@forfatterskolen.no',
                'attach_file' => null,
            ];
            \Mail::to($to)->queue(new SubjectBodyEmail($emailData));
            // mail($toMail, 'New manuscript submitted for shop manuscript', $message);
        }

        if ($request->hasFile('synopsis') && $request->file('synopsis')->isValid()) {
            $extension = pathinfo($_FILES['synopsis']['name'], PATHINFO_EXTENSION);

            if (! in_array($extension, $extensions)) {
                return redirect()->back();
            }

            $time = time();
            $destinationPath = 'storage/shop-manuscripts-synopsis/';
            $fileName = $time.'.'.$extension; // rename document
            $request->synopsis->move($destinationPath, $fileName);
            $shopManuscriptTaken->synopsis = '/'.$destinationPath.$fileName;
        }

        // check if the uploaded file exceeds the plan max words
        if ($word_count > $shopManuscript->max_words) {
            // get the plan that meets the word count uploaded
            $nextPlan = ShopManuscript::where('max_words', '>=', $word_count)->first();

            return redirect()->back()->withErrors(['exceed' => 'Ditt manus er '.$word_count
                .' ord, du må bestille <a href="'.route('front.shop-manuscript.checkout', $nextPlan->id).'">'
                .$nextPlan->title.'</a>.']);
        }

        $comment = '(Manuskript: '.$shopManuscript->title.', ';
        $comment .= 'Betalingsmodus: '.$paymentMode->mode.', ';
        $comment .= 'Betalingsplan: '.$payment_plan.')';

        $dueDate = date('Y-m-d');
        $dueDate = Carbon::parse($dueDate);
        $payment_plan = trim($payment_plan);
        if ($payment_plan == 'Hele beløpet') {
            $price = (int) $shopManuscript->full_payment_price * 100;
            $product_ID = $shopManuscript->full_price_product;
            $dueDate->addDays($shopManuscript->full_price_due_date);
        } elseif ($payment_plan == '3 måneder') {
            $price = (int) $shopManuscript->months_3_price * 100;
            $product_ID = $shopManuscript->months_3_product;
            $dueDate->addDays($shopManuscript->months_3_due_date);
        }
        $dueDate = date_format(date_create($dueDate), 'Y-m-d');

        if ($hasPaidCourse) {
            $discount = $price * 0.05;
            $price = $price - ((int) $discount);
            $comment .= ' - Discount: '.FrontendHelpers::currencyFormat($discount / 100);
        }

        if ((int) $request->genre === 10) {
            $price = $price + ($price * .50);
        }

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
            'payment_mode' => $paymentMode->mode,
        ];

        $invoice = new FikenInvoice;
        $invoice->create_invoice($invoice_fields);

        // wait for the invoice to be saved first before saving the shop manuscript taken
        $shopManuscriptTaken->is_active = false;
        $shopManuscriptTaken->coaching_time_later = $request->has('coaching_time_later') ? 1 : 0;
        $shopManuscriptTaken->is_welcome_email_sent = 0;
        $shopManuscriptTaken->save();

        $newOrder['user_id'] = Auth::user()->id;
        $newOrder['item_id'] = $id;
        $newOrder['type'] = Order::MANUSCRIPT_TYPE;
        $newOrder['plan_id'] = $paymentPlan->id;

        $order = Order::create($newOrder);

        // Send Email
        $user_email = Auth::user()->email;
        $emailTemplate = AdminHelpers::emailTemplate('Shop Manuscript Welcome Email');
        $emailContent = AdminHelpers::formatEmailContent($emailTemplate->email_content, $user_email,
            Auth::user()->first_name, 'shop-manuscripts-taken');

        dispatch(new AddMailToQueueJob($user_email, $emailTemplate->subject, $emailContent,
            $emailTemplate->from_email, null, null, 'shop-manuscripts-taken', $shopManuscriptTaken->id));

        // if( $request->update_address ) :
        $address = Address::firstOrNew(['user_id' => Auth::user()->id]);
        $address->street = $request->street;
        $address->city = $request->city;
        $address->zip = $request->zip;
        $address->phone = $request->phone;
        $address->save();
        // endif;

        if ($paymentMode->mode == 'Paypal') {
            // redirect to another page to process paypal payment
            // it have an error when the function is placed here because of the file upload
            return redirect()->route('front.shop-manuscript.paypal-payment', encrypt($invoice->invoiceID));
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
            // $orderId = $invoice->invoice_number;
            $orderId = $order->id.'-'.Auth::user()->id;
            $transactionText = $shopManuscript->title;
            $vippsData = [
                'amount' => $price,
                'orderId' => $orderId,
                'transactionText' => $transactionText,
            ];

            return $this->vippsInitiatePayment($vippsData);
        }

        return redirect(route('front.shop.thankyou', ['page' => 'manuscript']));
    }

    /**
     * Paypal payment
     */
    public function paypalPayment($invoice_id): RedirectResponse
    {
        $invoice = Invoice::find(decrypt($invoice_id));

        if (! $invoice) {
            return redirect()->route('front.shop-manuscript.index');
        }

        $paypal = new Paypal;

        $response = $paypal->purchase([
            'amount' => ($invoice->gross / 100),
            'transactionId' => $invoice->id,
            'currency' => 'NOK',
            'cancelUrl' => $paypal->getCancelUrl($invoice->id),
            'returnUrl' => $paypal->getReturnUrl($invoice->id, 'manuscript'),
        ]);

        if ($response->isRedirect()) {
            $response->redirect();
        }

        return redirect()->route('front.shop-manuscript.index');
    }

    public function upload_manuscript($id, Request $request): RedirectResponse
    {
        $shopManuscriptTaken = ShopManuscriptsTaken::where('id', $id)->where('user_id', Auth::user()->id)->firstOrFail();
        $extensions = ['pdf', 'doc', 'docx', 'odt'];

        $word_count = 0;
        if ($request->hasFile('manuscript') && $request->file('manuscript')->isValid()) {
            $extension = pathinfo($_FILES['manuscript']['name'], PATHINFO_EXTENSION);
            $original_filename = $request->manuscript->getClientOriginalName();

            if (! in_array($extension, $extensions)) {
                return redirect()->back()->with(
                    'manuscript_test_error', 'Invalid file format. Allowed formats are PDF, DOC, DOCX, ODT'
                );
            }

            $time = time();
            $destinationPath = 'storage/shop-manuscripts';
            $filePath = AdminHelpers::checkFileName($destinationPath, Auth::user()->id, $extension); // rename document
            $expFileName = explode('/', $filePath);

            $request->manuscript->move($destinationPath, end($expFileName));
            if ($extension == 'pdf') {
                $pdf = new \PdfToText($filePath);
                $pdf_content = $pdf->Text;
                $word_count = FrontendHelpers::get_num_of_words($pdf_content);
            } elseif ($extension == 'docx') {
                $docObj = new \Docx2Text($filePath);
                $docText = $docObj->convertToText();
                $word_count = FrontendHelpers::get_num_of_words($docText);
            } elseif ($extension == 'doc') {
                $docText = $this->readWord($filePath);
                $word_count = FrontendHelpers::get_num_of_words($docText);
            } elseif ($extension == 'odt') {
                $doc = odt2text($filePath);
                $word_count = FrontendHelpers::get_num_of_words($doc);
            }
            $word_count = FrontendHelpers::wordCountByMargin((int) $word_count);
            $shopManuscriptTaken->file = '/'.$filePath;
            $shopManuscriptTaken->words = $word_count;
        }

        if ($request->hasFile('synopsis') && $request->file('synopsis')->isValid()) {
            $extension = pathinfo($_FILES['synopsis']['name'], PATHINFO_EXTENSION);

            if (! in_array($extension, $extensions)) {
                return redirect()->back()->with(
                    'manuscript_test_error', 'Invalid file format. Allowed formats are PDF, DOC, DOCX, ODT'
                );
            }

            $time = time();
            $destinationPath = 'storage/shop-manuscripts-synopsis/';
            $fileName = $time.'.'.$extension; // rename document
            $request->synopsis->move($destinationPath, $fileName);
            $shopManuscriptTaken->synopsis = '/'.$destinationPath.$fileName;
        }

        $currentPlanWords = $shopManuscriptTaken->shop_manuscript->max_words;

        if ($word_count > $currentPlanWords) { // $word_count > 17500
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

            $nextPlan = ShopManuscript::where('max_words', '>=', $word_count)
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
            $shopManuscriptTaken->genre = $request->genre;
            $shopManuscriptTaken->description = $request->description;
            $shopManuscriptTaken->manuscript_uploaded_date = Carbon::now()->toDateTimeString();
            $shopManuscriptTaken->save();

            Log::create([
                'activity' => '<strong>'.Auth::user()->full_name.'</strong> submitted a manuscript for shop manuscript  '.$shopManuscriptTaken->shop_manuscript->title,
            ]);
            // Admin notification
            $message = Auth::user()->full_name.' submitted a manuscript for shop manuscript '.$shopManuscriptTaken->shop_manuscript->title;
            $toMail = 'post@forfatterskolen.no'; // post@forfatterskolen.no
            // mail($toMail, 'New manuscript submitted for shop manuscript', $message);
            /*AdminHelpers::send_email('New manuscript submitted for shop manuscript',
                'post@forfatterskolen.no', $toMail, $message);*/
            $to = $toMail; //
            $emailData = [
                'email_subject' => 'New manuscript submitted for shop manuscript',
                'email_message' => $message,
                'from_name' => '',
                'from_email' => 'post@forfatterskolen.no',
                'attach_file' => null,
            ];
            \Mail::to($to)->queue(new SubjectBodyEmail($emailData));

            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag(trans('site.learner.upload-manuscript-success')),
                'alert_type' => 'success',
            ]);
        }
    }

    /**
     * Upload synopsis
     */
    public function upload_synopsis($id, Request $request): RedirectResponse
    {
        $shopManuscriptTaken = ShopManuscriptsTaken::where('id', $id)
            ->where('user_id', Auth::user()->id)->firstOrFail();
        $extensions = ['pdf', 'doc', 'docx', 'odt'];
        if ($request->hasFile('synopsis') && $request->file('synopsis')->isValid()) {
            $extension = pathinfo($_FILES['synopsis']['name'], PATHINFO_EXTENSION);

            if (! in_array($extension, $extensions)) {
                return redirect()->back()->with(
                    'manuscript_test_error', 'Invalid file format. Allowed formats are PDF, DOC, DOCX, ODT'
                );
            }

            $time = time();
            $destinationPath = 'storage/shop-manuscripts-synopsis/';
            $fileName = $time.'.'.$extension; // rename document
            $request->synopsis->move($destinationPath, $fileName);
            $shopManuscriptTaken->synopsis = '/'.$destinationPath.$fileName;
        }

        $shopManuscriptTaken->save();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag(trans('site.learner.upload-manuscript-success')),
            'alert_type' => 'success',
        ]);
    }

    /**
     * Update the manuscript uploaded by the learner
     */
    public function updateUploadedManuscript($id, Request $request): RedirectResponse
    {
        $shopManuscriptTaken = ShopManuscriptsTaken::where('id', $id)->where('user_id', Auth::user()->id)->first();
        $extensions = ['pdf', 'doc', 'docx', 'odt'];

        $word_count = 0;
        if ($request->hasFile('manuscript') && $request->file('manuscript')->isValid()) {
            $extension = pathinfo($_FILES['manuscript']['name'], PATHINFO_EXTENSION);
            $original_filename = $request->manuscript->getClientOriginalName();

            if (! in_array($extension, $extensions)) {
                return redirect()->back()->with(
                    'manuscript_test_error', 'Invalid file format. Allowed formats are PDF, DOC, DOCX, ODT'
                );
            }

            $time = time();
            $destinationPath = 'storage/shop-manuscripts';
            $filePath = AdminHelpers::checkFileName($destinationPath, Auth::user()->id, $extension); // rename document
            $expFileName = explode('/', $filePath);

            $request->manuscript->move($destinationPath, end($expFileName));
            if ($extension == 'pdf') {
                $pdf = new \PdfToText($filePath);
                $pdf_content = $pdf->Text;
                $word_count = FrontendHelpers::get_num_of_words($pdf_content);
            } elseif ($extension == 'docx') {
                $docObj = new \Docx2Text($filePath);
                $docText = $docObj->convertToText();
                $word_count = FrontendHelpers::get_num_of_words($docText);
            } elseif ($extension == 'doc') {
                $docText = $this->readWord($filePath);
                $word_count = FrontendHelpers::get_num_of_words($docText);
            } elseif ($extension == 'odt') {
                $doc = odt2text($filePath);
                $word_count = FrontendHelpers::get_num_of_words($doc);
            }
            $word_count = FrontendHelpers::wordCountByMargin((int) $word_count);
            $shopManuscriptTaken->file = '/'.$filePath;
            $shopManuscriptTaken->words = $word_count;
        }

        if ($request->hasFile('synopsis') && $request->file('synopsis')->isValid()) {
            $extension = pathinfo($_FILES['synopsis']['name'], PATHINFO_EXTENSION);

            if (! in_array($extension, $extensions)) {
                return redirect()->back()->with(
                    'manuscript_test_error', 'Invalid file format. Allowed formats are PDF, DOC, DOCX, ODT'
                );
            }

            $time = time();
            $destinationPath = 'storage/shop-manuscripts-synopsis/';
            $fileName = $time.'.'.$extension; // rename document
            $request->synopsis->move($destinationPath, $fileName);
            $shopManuscriptTaken->synopsis = '/'.$destinationPath.$fileName;
        }

        $currentPlanWords = $shopManuscriptTaken->shop_manuscript->max_words;

        if ($word_count > $currentPlanWords) { // $word_count > 17500
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

            $nextPlan = ShopManuscript::where('max_words', '>=', $word_count)
                ->orderBy('max_words', 'ASC')->first();

            // get the upgrade price based on the current shop manuscript and the suggested shop manuscript
            $upgradePlan = ShopManuscriptUpgrade::where('shop_manuscript_id', $shopManuscriptTaken->shop_manuscript_id)
                ->where('upgrade_shop_manuscript_id', $nextPlan->id)->first();
            $price = $upgradePlan->price;

            return redirect()->back()->with(['exceed' => $price, 'plan' => $nextPlan->id, 'max_words' => $nextPlan]);
        } else {
            $shopManuscriptTaken->genre = $request->genre;
            $shopManuscriptTaken->description = $request->description;
            $shopManuscriptTaken->coaching_time_later = $request->has('coaching_time_later') ? 1 : 0;
            $shopManuscriptTaken->save();

            Log::create([
                'activity' => '<strong>'.Auth::user()->full_name.'</strong> submitted a manuscript for shop manuscript  '.$shopManuscriptTaken->shop_manuscript->title,
            ]);
            // Admin notification
            $message = Auth::user()->full_name.' submitted a manuscript for shop manuscript '.$shopManuscriptTaken->shop_manuscript->title;
            // mail('post@forfatterskolen.no', 'New manuscript submitted for shop manuscript', $message);
            $toMail = 'post@forfatterskolen.no'; // post@forfatterskolen.no
            /*AdminHelpers::send_email('New manuscript submitted for shop manuscript',
                'post@forfatterskolen.no', $toMail, $message);*/
            $to = $toMail; //
            $emailData = [
                'email_subject' => 'New manuscript submitted for shop manuscript',
                'email_message' => $message,
                'from_name' => '',
                'from_email' => 'post@forfatterskolen.no',
                'attach_file' => null,
            ];
            \Mail::to($to)->queue(new SubjectBodyEmail($emailData));

            return redirect()->back();
        }
    }

    public function deleteUploadedManuscript($id): RedirectResponse
    {
        $shopManuscriptTaken = ShopManuscriptsTaken::where('id', $id)->where('user_id', Auth::user()->id)->first();
        $shopManuscriptTaken->file = null;
        $shopManuscriptTaken->words = null;
        $shopManuscriptTaken->genre = 0;
        $shopManuscriptTaken->description = null;
        $shopManuscriptTaken->is_manuscript_locked = 0;
        $shopManuscriptTaken->synopsis = null;
        $shopManuscriptTaken->expected_finish = null;
        $shopManuscriptTaken->save();

        return redirect()->back();
    }

    public function test_manuscript(Request $request): RedirectResponse
    {
        $extensions = ['pdf', 'doc', 'docx', 'odt'];
        $word_count = 0;
        $price = 20000;

        if ($request->hasFile('manuscript') && $request->file('manuscript')->isValid()) {
            $extension = pathinfo($_FILES['manuscript']['name'], PATHINFO_EXTENSION);
            $original_filename = $request->manuscript->getClientOriginalName();

            if (! in_array($extension, $extensions)) {
                return redirect()->back()->with(
                    'manuscript_test_error', '<h3>Invalid file format. Allowed formats are PDF, DOC, DOCX, ODT</h3>'
                );
            }

            $time = time();
            $destinationPath = 'storage/manuscript-tests/'; // upload path
            $fileName = $time.'.'.$extension; // rename document
            $request->manuscript->move($destinationPath, $fileName);
            if ($extension == 'pdf') {
                $pdf = new \PdfToText($destinationPath.$fileName);
                $pdf_content = $pdf->Text;
                $word_count = FrontendHelpers::get_num_of_words($pdf_content);
            } elseif ($extension == 'docx') {
                $docObj = new \Docx2Text($destinationPath.$fileName);
                $docText = $docObj->convertToText();
                $word_count = FrontendHelpers::get_num_of_words($docText);
            } elseif ($extension == 'doc') {
                $docText = $this->readWord($destinationPath.$fileName);
                $word_count = FrontendHelpers::get_num_of_words($docText);
            } elseif ($extension == 'odt') {
                $doc = odt2text($destinationPath.$fileName);
                $word_count = FrontendHelpers::get_num_of_words($doc);
            }
            $word_count = FrontendHelpers::wordCountByMargin((int) $word_count);
            $word_to_deduct = $word_count * 0.02;
            $new_word_count = ceil($word_count - $word_to_deduct);

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

            $checkoutRoute = 'front.shop-manuscript.checkout';
            $prevRoute = app('router')->getRoutes()->match(app('request')->create(\URL::previous()))->getName();
            if ($prevRoute === 'front.gift.shop-manuscript') {
                $checkoutRoute = 'front.gift.shop-manuscript.checkout';
            }

            $suggestedPlan = ShopManuscript::where('max_words', '>=', $new_word_count)
                ->orderBy('max_words', 'ASC')->first();
            if ($suggestedPlan) {

                $price = $suggestedPlan->full_payment_price;
                if ($new_word_count > 17500) {
                    $excessPerWordAmount = FrontendHelpers::manuscriptExcessPerWordPrice();
                    $excess_words = $new_word_count - 17500;
                    $price = $suggestedPlan->full_payment_price + ($excess_words * $excessPerWordAmount);
                }
                $button_link = route($checkoutRoute, $suggestedPlan->id);
            }

        }

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

    public function freeManuscriptShow(): View
    {
        $action = 'front.free-manuscript.send'; // default

        return view('frontend.shop-manuscript.free-manuscript', compact('action'));
    }

    public function freeManuscriptShowOther(): View
    {
        $action = 'front.free-manuscript.send-other'; // from other site

        return view('frontend.shop-manuscript.free-manuscript', compact('action'));
    }

    public function freeManuscriptShowSuccess(): View
    {
        return view('frontend.shop-manuscript.free-manuscript-success');
    }

    /**
     * Display the checkout page for upgrading manuscript
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function checkoutUpgradeManuscript($id): View
    {
        $shopManuscript = ShopManuscript::findOrFail($id);
        $shopManuscriptTaken = Auth::user()->shopManuscriptsTaken;
        $upgradeManuscript = ShopManuscriptUpgrade::where('shop_manuscript_id', $shopManuscriptTaken[0]->shop_manuscript->id)
            ->where('upgrade_shop_manuscript_id', $id)->first();

        return view('frontend.shop-manuscript.upgrade', compact('shopManuscript', 'upgradeManuscript'));
    }

    public function upgradeManuscript($id, Request $request): RedirectResponse
    {
        $validator = $this->upgradeValidator($request->all());
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $shopManuscript = ShopManuscript::findOrFail($id);

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

        $previousManuscript = ShopManuscript::where('max_words', '<', $shopManuscript->max_words)->first();

        // $shopManuscriptTaken = ShopManuscriptsTaken::where('shop_manuscript_id',$previousManuscript->id)->where('user_id',Auth::user()->id)->first();
        $shopManuscriptTaken = Auth::user()->shopManuscriptsTaken;
        $upgradeManuscript = ShopManuscriptUpgrade::where('shop_manuscript_id', $shopManuscriptTaken[0]->shop_manuscript->id)
            ->where('upgrade_shop_manuscript_id', $id)->first();

        $extensions = ['pdf', 'doc', 'docx', 'odt'];
        $shopManuscriptTaken->shop_manuscript_id = $shopManuscript->id;

        if ($request->hasFile('manuscript') && $request->file('manuscript')->isValid()) {
            $extension = pathinfo($_FILES['manuscript']['name'], PATHINFO_EXTENSION);
            $original_filename = $request->manuscript->getClientOriginalName();

            if (! in_array($extension, $extensions)) {
                return redirect()->back()->withInput()->with(
                    'manuscript_test_error', 'Invalid file format. Allowed formats are PDF, DOC, DOCX, ODT'
                );
            }

            $time = time();
            $destinationPath = 'storage/shop-manuscripts/';
            $fileName = $time.'.'.$extension; // rename document
            $request->manuscript->move($destinationPath, $fileName);
            if ($extension == 'pdf') {
                $pdf = new \PdfToText($destinationPath.$fileName);
                $pdf_content = $pdf->Text;
                $word_count = FrontendHelpers::get_num_of_words($pdf_content);
            } elseif ($extension == 'docx') {
                $docObj = new \Docx2Text($destinationPath.$fileName);
                $docText = $docObj->convertToText();
                $word_count = FrontendHelpers::get_num_of_words($docText);
            } elseif ($extension == 'doc') {
                $docText = $this->readWord($destinationPath.$fileName);
                $word_count = FrontendHelpers::get_num_of_words($docText);
            } elseif ($extension == 'odt') {
                $doc = odt2text($destinationPath.$fileName);
                $word_count = FrontendHelpers::get_num_of_words($doc);
            }
            $word_count = (int) $word_count;
            $shopManuscriptTaken->file = '/'.$destinationPath.$fileName;
            $shopManuscriptTaken->words = $word_count;
        }

        $shopManuscriptTaken->is_active = false;
        $shopManuscriptTaken->save();

        $paymentMode = PaymentMode::findOrFail($request->payment_mode_id);
        $paymentPlan = PaymentPlan::findOrFail(6);
        $payment_plan = ($paymentMode->mode == 'Paypal') ? 'Hele beløpet' : $paymentPlan->plan;

        $comment = '(Manuskript: '.$shopManuscript->title.', ';
        $comment .= 'Betalingsmodus: '.$paymentMode->mode.', ';
        $comment .= 'Betalingsplan: '.$payment_plan.')';

        $dueDate = date('Y-m-d');
        $dueDate = Carbon::parse($dueDate);

        $price = (int) $upgradeManuscript->price * 100;
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
            'payment_mode' => $paymentMode->mode,
        ];

        $invoice = new FikenInvoice;
        $invoice->create_invoice($invoice_fields);

        // if( $request->update_address ) :
        $address = Address::firstOrNew(['user_id' => Auth::user()->id]);
        $address->street = $request->street;
        $address->city = $request->city;
        $address->zip = $request->zip;
        $address->phone = $request->phone;
        $address->save();
        // endif;

        if ($paymentMode->mode == 'Paypal') {
            echo '<form name="_xclick" id="paypal_form" style="display:none" action="https://www.paypal.com/cgi-bin/webscr" method="post">
                <input type="hidden" name="cmd" value="_xclick">
                <input type="hidden" name="business" value="post.forfatterskolen@gmail.com">
                <input type="hidden" name="currency_code" value="NOK">
                <input type="hidden" name="custom" value="'.$invoice->invoiceID.'">
                <input type="hidden" name="item_name" value="Course Order Invoice">
                <input type="hidden" name="amount" value="'.($price / 100).'">
                <input type="hidden" name="return" value="'.route('front.shop.thankyou').'">
                <input type="image" name="submit" src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" align="right" alt="PayPal - The safer, easier way to pay online">
            </form>';
            echo '<script>document.getElementById("paypal_form").submit();</script>';

            return;
        }

        return redirect(route('front.shop.thankyou'));
    }

    public function freeManuscriptWordCount(Request $request): JsonResponse
    {
        \Session::put('wordcount', $request->wordcount);

        return response()->json(['data' => $request->wordcount]);
    }

    public function freeManuscriptSend(Request $request)
    {
        $request->merge([
            'from' => 'FS',
            'ac_list_id' => 199,
        ]);

        return $this->processFreeManuscript($request);
    }

    public function freeManuscriptSendOther(Request $request)
    {
        $request->merge([
            'from' => 'Giutbok',
            'ac_list_id' => 200,
        ]);

        return $this->processFreeManuscript($request);
    }

    public function processFreeManuscript(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|alpha_spaces',
            'last_name' => 'required|alpha_spaces',
            'email' => 'required|email',
            'genre' => 'required',
            'manuscript_content' => 'required|no_links',
        ]);

        $wordcount = Session::get('wordcount');

        if ($wordcount > 500) {
            return redirect()->back()->withInput()->with([
                'errors' => AdminHelpers::createMessageBag('The content may not be greater than 500 words.'),
            ]);
        }

        $existing_emails = FreeManuscript::all()->pluck('email')->toArray();
        // prevent user from sending multiple manuscript
        if (in_array($request->email, $existing_emails)) {
            return redirect()->back()->withInput()->with([
                'errors' => AdminHelpers::createMessageBag('Beklager, men du har allerede benyttet deg av dette gratistilbudet
Er det feil må du sende en mail til <a href="mailto:post@forfatterskolen.no">post@forfatterskolen.no</a>'),
            ]);
        }

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $name = $request->name;
        $last_name = $request->last_name;
        $email = $request->email;
        $content = $request->manuscript_content;
        $word_count = FrontendHelpers::get_num_of_words($request->manuscript_content);

        if ($word_count > 0) {
            // Send email
            $actionText = 'View Our Courses';
            $actionUrl = 'http://dev.forfatterskolen.no/course';
            $headers = "From: Forfatterskolen<no-reply@forfatterskolen.no>\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

            // mail('post@forfatterskolen.no', 'Free Manuscript', view('emails.free-manuscript', compact('name', 'email', 'content', 'word_count')), $headers);
            FreeManuscript::create([
                'name' => $request->name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'genre' => $request->genre,
                'from' => $request->from,
                'content' => $request->manuscript_content,
                'deadline' => Carbon::today()->addDays(6),
            ]);
            /*AdminHelpers::send_email('Free Manuscript',
                'post@forfatterskolen.no', 'post@forfatterskolen.no',
                view('emails.free-manuscript', compact('name', 'email', 'content', 'word_count')));*/
            $to = 'post@forfatterskolen.no'; //
            $emailData = [
                'email_subject' => 'Free Manuscript',
                'email_message' => view('emails.free-manuscript', compact('name', 'last_name', 'email', 'content', 'word_count'))->render(),
                'from_name' => '',
                'from_email' => 'post@forfatterskolen.no',
                'attach_file' => null,
            ];
            \Mail::to($to)->queue(new SubjectBodyEmail($emailData));
            if ($request->from === 'Giutbok') {
                \Mail::to('terje@giutbok.no')->queue(new SubjectBodyEmail($emailData));
            }

            $userEmail = $request->email;
            $emailTemplate = AdminHelpers::emailTemplate('Free Manuscript Received');
            $emailContent = AdminHelpers::formatEmailContent($emailTemplate->email_content, $userEmail,
                $request->name, '');
            $userEmailData = [
                'email_subject' => $emailTemplate->subject,
                'email_message' => $emailContent,
                'from_name' => '',
                'from_email' => $emailTemplate->from_email,
                'attach_file' => null,
            ];
            \Mail::to($userEmail)->queue(new SubjectBodyEmail($userEmailData));

            $list_id = $request->ac_list_id;
            $activeCampaign['email'] = $request->email;
            $activeCampaign['name'] = $request->name;
            $activeCampaign['last_name'] = $request->last_name;
            event(new AddToCampaignList($list_id, $activeCampaign));
            // AdminHelpers::addToActiveCampaignList($list_id, $activeCampaign);

            // forget the wordcount
            Session::forget('wordcount');

            return redirect()->route('front.free-manuscript.success');
        }
    }

    public function exportSingleBought()
    {
        $manuscriptsTaken = ShopManuscriptsTaken::where('package_shop_manuscripts_id', 0)
            ->whereNotNull('file')->get();
        $userList = [];
        foreach ($manuscriptsTaken as $manu) {
            $shopManuscriptOrder = Order::where('type', 2)
                ->where('item_id', $manu->shop_manuscript_id)
                ->where('user_id', $manu->user_id)
                ->whereNull('svea_order_id')->first();
            if ($shopManuscriptOrder) {
                $userList[] = [
                    'name' => $manu->user->full_name,
                    'email' => $manu->user->email,
                    'date' => FrontendHelpers::formatDate($manu->created_at),
                ];
            }
        }

        $headers = ['name', 'email', 'date'];
        $excel = \App::make('excel');

        return $excel->download(new GenericExport($userList, $headers), 'Learner with single shop manuscripts.xlsx');
    }

    public function readWord($filename)
    {
        if (file_exists($filename)) {
            if (($fh = fopen($filename, 'r')) !== false) {
                $headers = fread($fh, 0xA00);

                // 1 = (ord(n)*1) ; Document has from 0 to 255 characters
                $n1 = (ord($headers[0x21C]) - 1);

                // 1 = ((ord(n)-8)*256) ; Document has from 256 to 63743 characters
                $n2 = ((ord($headers[0x21D]) - 8) * 256);

                // 1 = ((ord(n)*256)*256) ; Document has from 63744 to 16775423 characters
                $n3 = ((ord($headers[0x21E]) * 256) * 256);

                // 1 = (((ord(n)*256)*256)*256) ; Document has from 16775424 to 4294965504 characters
                $n4 = (((ord($headers[0x21F]) * 256) * 256) * 256);

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
