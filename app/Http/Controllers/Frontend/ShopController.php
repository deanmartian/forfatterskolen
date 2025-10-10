<?php

namespace App\Http\Controllers\Frontend;

use App\Address;
use App\CheckoutLog;
use App\Course;
use App\CourseDiscount;
use App\CourseOrderAttachment;
use App\CourseShared;
use App\CourseSharedUser;
use App\CoursesTaken;
use App\Events\AddToCampaignList;
use App\Http\AdminHelpers;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Controller;
use App\Http\FikenInvoice;
use App\Http\FrontendHelpers;
use App\Http\Requests\OrderCreateRequest;
use App\Invoice;
use App\Jobs\CourseOrderJob;
use App\Jobs\SveaUpdateOrderDetailsJob;
use App\Mail\SubjectBodyEmail;
use App\Order;
use App\Package;
use App\PaymentMode;
use App\PaymentPlan;
use App\Paypal;
use App\Services\CourseService;
use App\ShopManuscriptsTaken;
use App\Transaction;
use App\User;
use App\WorkshopsTaken;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

require app_path('/Http/PaypalIPN/PaypalIPN.php');

use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use PaypalIPN;
use PhpOffice\PhpWord\SimpleType\DocProtect;

class ShopController extends Controller
{
    /**
     * @var CourseService
     */
    protected $courseService;

    /**
     * ShopController constructor.
     */
    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    public function checkout($course_id, Request $request)
    {
        abort(404);
        $countryCode = AdminHelpers::ip_info($request->ip(), 'Country Code');

        if ($countryCode === 'NO') {
            // return redirect()->route('front.course.checkout', $course_id);
        }

        $course = Course::findOrFail($course_id);

        if (! $course->is_free) { // added this condition to check if the course is for sale
            if (! FrontendHelpers::isCourseActive($course) || count($course->packages) == 0) { // Display 404 if Course has no Packages
                return abort(404);
            }
        }

        if (! Auth::guest()) {
            $course_packages = $course->packages->pluck('id')->toArray();
            $courseTaken = CoursesTaken::where('user_id', Auth::user()->id)->whereIn('package_id', $course_packages)->first();
            if ($courseTaken) {
                return redirect(route('learner.course.show', ['id' => $courseTaken->id]));
            }
        }

        if ($course->hide_price) {
            return redirect()->route('front.course.show', $course->id);
        }

        $packages = $course->packages()->isShow()->get();
        $coupon = \request()->has('c') ? \request()->get('c') : '';

        if (\request()->has('sp')) {
            // use try/catch to handle invalid payload
            try {
                $package_id = decrypt(\request('sp'));
                $packages = $course->packages()->where('id', $package_id)->get();
            } catch (DecryptException $e) {
                //
            }
        }

        return view('frontend.shop.checkout', compact('course', 'packages', 'coupon'));
    }

    /**
     * Checkout page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function sveaCheckout($course_id, Request $request)
    {
        $countryCode = AdminHelpers::ip_info($request->ip(), 'Country Code');

        $course = Course::findOrFail($course_id);

        if (! $course->is_free) { // added this condition to check if the course is for sale
            if (! FrontendHelpers::isCourseActive($course) || count($course->packages) == 0) { // Display 404 if Course has no Packages
                return abort(404);
            }
        }

        if (! Auth::guest() && ! auth()->user()->could_buy_course) {
            return redirect()->route('front.course.show', $course_id);
        }

        if ($course->pay_later_with_application) {
            return redirect()->route('front.course.application', $course_id);
        }

        $hasPaidCourse = false;
        if (! Auth::guest()) {
            foreach (\Auth::user()->coursesTakenNotOld as $courseTaken) {
                if ($courseTaken->package->course->type != 'Free' && $courseTaken->is_active) {
                    if ($courseTaken->package->course->is_free != 1) {
                        $hasPaidCourse = true;
                    }
                    break;
                }
            }

            $course_packages = $course->packages->pluck('id')->toArray();
            $courseTaken = CoursesTaken::where('user_id', Auth::user()->id)
                ->whereIn('package_id', $course_packages)
                ->first();
            if ($courseTaken) {
                return redirect(route('learner.course.show', ['id' => $courseTaken->id]));
            }
        }

        if ($course->hide_price) {
            return redirect()->route('front.course.show', $course->id);
        }

        $packages = $course->packages()->isShow()->where('variation', '!=', 'Editor Package')->get();
        $package_id = \Request::has('package') ? \Request::get('package') :
            (isset($packages[1]) ? $packages[1]['id'] : $packages[0]['id']);

        // If course_id = 115, override package_id
        if ($course_id == 115) {
            $package_id = 312;
        }

        // check if the course have set standard package
        if ($course->standardPackage) {
            $package_id = $course->standardPackage->id;
        }

        $coupon = \request()->has('c') ? \request()->get('c') : '';
        $startIndex = \request()->has('si') ? \request()->get('si') : 0;

        if (\request()->has('sp')) {
            // use try/catch to handle invalid payload
            try {
                $package_id = decrypt(\request('sp'));
                $packages = $course->packages()->where('id', $package_id)->get();
            } catch (DecryptException $e) {
                //
            }
        }

        $user = \Auth::user();

        if ($user) {
            $user['address'] = $user->address;
            $user->checkoutLogs()->firstOrCreate([
                'parent' => 'course',
                'parent_id' => $course->id,
            ]);
        }

        // old view svea-checkout
        return view('frontend.shop.checkout-update', compact('course', 'packages', 'package_id', 'coupon',
            'hasPaidCourse', 'user', 'startIndex', 'countryCode'));
    }

    public function processOrder($course_id, OrderCreateRequest $request)
    {
        // check if webinar-pakke
        // if ($course_id == 17) {
        $course = Course::findOrFail($course_id);
        $course_packages = $course->packages->pluck('id')->toArray();
        $courseTaken = CoursesTaken::where('user_id', Auth::user()->id)->whereIn('package_id', $course_packages)->first();
        // check if the user already avails this course
        if ($courseTaken) {
            return response()->json(['redirect_link' => route('learner.course.show', ['id' => $courseTaken->id])]);
        }
        // }
        $hasPaidCourse = $this->hasPaidCourse()->original; // get result of json

        $paymentMode = PaymentMode::findOrFail($request->payment_mode_id);
        $paymentPlan = PaymentPlan::findOrFail($request->payment_plan_id);
        $package = Package::findOrFail($request->package_id);
        $add_to_automation = 0;

        $payment_plan = ($paymentMode->mode == 'Paypal') ? 'Hele beløpet' : $paymentPlan->plan;

        /* check if there's an issue date set ir not then use today */
        $dueDate = date('Y-m-d');
        if ($package->issue_date && Carbon::parse($package->issue_date)->gt(Carbon::today())) {
            $dueDate = $package->issue_date;
        }
        $dueDate = Carbon::parse($dueDate);
        $payment_plan = trim($payment_plan);
        $price = 0;
        $product_ID = 0;
        $isStudentDiscounted = false;
        $saleDiscount = 0;
        $discount = 0;

        // check payment plan and set price, saleDiscount, product_id and due date
        if ($paymentPlan->division === 1) {
            $price = $package->full_payment_is_sale && $package->full_payment_sale_price
                ? (int) $package->full_payment_sale_price * 100
                : (int) $package->full_payment_price * 100;
            $saleDiscount = $package->full_payment_is_sale
                ? ($package->full_payment_price - $package->full_payment_sale_price) * 100 : 0;
            $product_ID = $package->full_price_product;
            $dueDate->addDays($package->full_price_due_date);
        } elseif ($paymentPlan->division === 3) {
            $price = $package->months_3_is_sale && $package->months_3_sale_price
                ? (int) $package->months_3_sale_price * 100
                : (int) $package->months_3_price * 100;
            $saleDiscount = $package->months_3_is_sale
                ? ($package->months_3_price - $package->months_3_sale_price) * 100 : 0;
            $product_ID = $package->months_3_product;
            $dueDate->addDays($package->months_3_due_date);
        } elseif ($paymentPlan->division === 6) {
            $price = $package->months_6_is_sale && $package->months_6_sale_price
                ? (int) $package->months_6_sale_price * 100
                : (int) $package->months_6_price * 100;
            $saleDiscount = $package->months_6_is_sale
                ? ($package->months_6_price - $package->months_6_sale_price) * 100 : 0;
            $product_ID = $package->months_6_product;
            $dueDate->addDays($package->months_6_due_date);
        } elseif ($paymentPlan->division === 12) {
            $price = $package->months_12_is_sale && $package->months_12_sale_price
                ? (int) $package->months_12_sale_price * 100
                : (int) $package->months_12_price * 100;
            $saleDiscount = $package->months_12_is_sale
                ? ($package->months_12_price - $package->months_12_sale_price) * 100 : 0;
            $product_ID = $package->months_12_product;
            $dueDate->addDays($package->months_12_due_date);
        }
        $dueDate = date_format(date_create($dueDate), 'Y-m-d');

        $payment_mode = $paymentMode->mode;
        if ($payment_mode == 'Faktura') {
            $payment_mode = 'Bankoverføring';
        }

        $comment = '(Kurs: '.$package->course->title.' ['.$package->variation.'], ';
        $comment .= 'Betalingsmodus: '.$payment_mode.', ';
        $comment .= 'Betalingsplan: '.$payment_plan.')';

        if ($request->coupon) {
            $discountCoupon = CourseDiscount::where('coupon', $request->coupon)->where('course_id', $course_id)->first();

            if ($discountCoupon->valid_to) {
                $valid_from = Carbon::parse($discountCoupon->valid_from)->format('Y-m-d');
                $valid_to = Carbon::parse($discountCoupon->valid_to)->format('Y-m-d');
                $today = Carbon::today()->format('Y-m-d');

                if (($today >= $valid_from) && ($today <= $valid_to)) {
                    // echo "valid date <br/>";
                } else {
                    return redirect()->back()->withInput()->with([
                        'errors' => AdminHelpers::createMessageBag('Rabattkupongen er ugyldig eller utløpt.'),
                    ]);
                }
            }

            if ($discountCoupon) {
                $discount = ((int) $discountCoupon->discount) * 100;
            }

        }

        if ($hasPaidCourse && $package->course->type == 'Group' && $package->has_student_discount) {
            $groupDiscount = 1000;
            $isStudentDiscounted = true;

            if ($groupDiscount > $discount) {
                $discount = ((int) $groupDiscount * 100);
            }

            if ($saleDiscount) {
                $discount = $discount + $saleDiscount;
            }

            $comment .= ' - Discount: Kr '.number_format($discount / 100, 2, ',', '.');
        }

        if ($hasPaidCourse && $package->course->type == 'Single' && $package->has_student_discount) {
            $singleDiscount = 500;
            $isStudentDiscounted = true;

            if ($singleDiscount > $discount) {
                $discount = ((int) $singleDiscount * 100);
            }

            if ($saleDiscount) {
                $discount = $discount + $saleDiscount;
            }

            $comment .= ' - Discount: Kr '.number_format($discount / 100, 2, ',', '.');
        }

        if ($saleDiscount && ! $isStudentDiscounted) {
            $comment .= ' - Discount: Kr '.number_format($saleDiscount / 100, 2, ',', '.');
        }

        $price = $price - $discount;
        $comment .= ' From course order';

        // check if it's part payment
        if ($paymentPlan->division > 1) {
            $division = $paymentPlan->division * 100; // multiply the split count to get the correct value
            $price = round($price / $division, 2); // round the value to the nearest tenths
            $price = (int) $price * 100;
            for ($i = 1; $i <= $paymentPlan->division; $i++) { // loop based on the split count
                /* Carbon::today() - this is the old instead of Carbon parse */
                $dueDate = $package->issue_date ?: date('Y-m-d');
                $dueDate = Carbon::parse($dueDate)->addMonth($i)->format('Y-m-d'); // due date on every month on the same day
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
                    'payment_mode' => $paymentMode->mode,
                    'index' => $i,
                ];

                $invoice = new FikenInvoice;
                $invoice->create_invoice($invoice_fields);
            }

        } else {
            // this is the original code without the split
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
                'payment_mode' => $paymentMode->mode,
            ];

            $invoice = new FikenInvoice;
            $invoice->create_invoice($invoice_fields);
        }

        // update the users address
        $address = Address::firstOrNew(['user_id' => Auth::user()->id]);
        $address->street = $request->street;
        $address->city = $request->city;
        $address->zip = $request->zip;
        $address->phone = $request->phone;
        $address->save();

        // create course taken record
        $course_status = $paymentMode->mode == 'Vipps' || $paymentMode->mode == 'Paypal' ? 1 : 0;
        $courseTaken = CoursesTaken::firstOrNew(['user_id' => Auth::user()->id, 'package_id' => $package->id]);
        $courseTaken->is_active = $course_status;
        $courseTaken->is_welcome_email_sent = 0;
        $courseTaken->save();

        $newOrder['user_id'] = Auth::user()->id;
        $newOrder['item_id'] = $course_id;
        $newOrder['type'] = Order::COURSE_TYPE;
        $newOrder['package_id'] = $package->id;
        $newOrder['plan_id'] = $paymentPlan->id;

        $order = Order::create($newOrder);

        // update the created log to mark it as ordered
        CheckoutLog::updateOrCreate([
            'user_id' => \auth()->id(),
            'parent' => 'course',
            'parent_id' => $course_id,
        ], [
            'is_ordered' => true,
        ]);

        // Check for shop manuscripts
        if ($package->shop_manuscripts->count() > 0) {
            foreach ($package->shop_manuscripts as $shop_manuscript) {
                // $shopManuscriptTaken = ShopManuscriptsTaken::firstOrNew(['user_id' => Auth::user()->id, 'shop_manuscript_id' => $shop_manuscript->shop_manuscript_id]);
                $shopManuscriptTaken = new ShopManuscriptsTaken;
                $shopManuscriptTaken->user_id = Auth::user()->id;
                $shopManuscriptTaken->shop_manuscript_id = $shop_manuscript->shop_manuscript_id;
                $shopManuscriptTaken->is_active = false;
                $shopManuscriptTaken->package_shop_manuscripts_id = $package->shop_manuscripts[0]->id;
                $shopManuscriptTaken->save();
            }
        }

        if ($package->included_courses->count() > 0) {
            foreach ($package->included_courses as $included_course) {
                if ($included_course->included_package_id == 29) { // check if webinar-pakke is included
                    $add_to_automation++;
                }

                // add user to the included course
                $courseIncluded = CoursesTaken::firstOrNew([
                    'user_id' => Auth::user()->id,
                    'package_id' => $included_course->included_package_id,
                ]);
                $courseIncluded->is_active = $course_status;
                $courseIncluded->save();
            }
        }

        if ($package->course->id == 17) { // check if webinar-pakke
            $add_to_automation++;
        }

        if ($add_to_automation > 0) {
            $user_email = Auth::user()->email;
            $automation_id = 73;
            $user_name = Auth::user()->first_name;

            AdminHelpers::addToAutomation($user_email, $automation_id, $user_name);
        }

        // check if the course has activecampaign list then add the user
        if ($package->course->auto_list_id > 0) {
            $list_id = $package->course->auto_list_id;
            $listData = [
                'email' => Auth::user()->email,
                'name' => Auth::user()->first_name,
                'last_name' => Auth::user()->last_name,
            ];

            // AdminHelpers::addToActiveCampaignList($list_id, $listData);
            event(new AddToCampaignList($list_id, $listData)); // fire the event
        }

        // Email to support
        $from = 'postmail@forfatterskolen.no';
        $headers1 = 'From: Forfatterskolen<'.$from.">\r\n";
        $headers1 .= "MIME-Version: 1.0\r\n";
        $headers1 .= "Content-Type: text/html; charset=UTF-8\r\n";
        // mail('support@forfatterskolen.no', 'New Course Order', Auth::user()->first_name . ' has ordered the course ' . $package->course->title, $headers1);
        $to = 'support@forfatterskolen.no'; //
        $emailData = [
            'email_subject' => 'New Course Order',
            'email_message' => Auth::user()->first_name.' has ordered the course '.$package->course->title,
            'from_name' => '',
            'from_email' => 'post@forfatterskolen.no',
            'attach_file' => null,
        ];
        \Mail::to($to)->queue(new SubjectBodyEmail($emailData));
        \Mail::to('post@forfatterskolen.no')->queue(new SubjectBodyEmail($emailData));
        /*AdminHelpers::send_email('New Course Order',
            'post@forfatterskolen.no', 'support@forfatterskolen.no', Auth::user()->first_name . ' has ordered the course ' . $package->course->title);*/

        // Send course email
        $headers = "From: Forfatterskolen<post@forfatterskolen.no>\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $user = Auth::user();

        $password = $user->need_pass_update ? 'Z5C5E5M2jv' : 'Skjult (kan endres inne i portalen eller via glemt passord)';

        $search_string = [
            '[username]', '[password]',
        ];
        $replace_string = [
            $courseTaken->user->email, $password,
        ];
        $email_content = str_replace($search_string, $replace_string, $package->course->email);

        $user_email = $user->email;

        $encode_email = encrypt($user_email);
        $redirectLink = encrypt(route('learner.course'));
        $actionUrl = route('auth.login.emailRedirect', [$encode_email, $redirectLink]);
        $actionText = 'Mine Kurs';
        $attachments = [asset($this->generateDocx($user->id, $package->id)),
            asset('/email-attachments/skjema-for-opplysninger-om-angrerett.docx')];

        dispatch(new CourseOrderJob($user_email, $package->course->title, $email_content,
            'postmail@forfatterskolen.no', 'Forfatterskolen', $attachments, 'courses-taken-order',
            $courseTaken->id, $actionText, $actionUrl, $user, $package->id));

        if ($paymentMode->mode == 'Paypal') {
            $paypal = new PayPal;

            $response = $paypal->purchase([
                'amount' => ($price / 100),
                'transactionId' => $invoice->invoiceID,
                'currency' => 'NOK',
                'cancelUrl' => $paypal->getCancelUrl($invoice->invoiceID),
                'returnUrl' => $paypal->getReturnUrl($invoice->invoiceID, 'course'),
            ]);

            if ($response->isRedirect()) {
                // $response->redirect();
                return response()->json(['redirect_link' => $response->getRedirectUrl()]);
            }

            return response()->json(['message' => $response->getMessage()], 400);
            // return response()->json(['redirect_link' => route('front.shop.thankyou')]);
        }

        // check if vipps payment mode and the current user id is 4
        if ($paymentMode->mode == 'Vipps') {
            // $orderId = $invoice->invoice_number;
            $orderId = $order->id.'-'.$user->id;
            $transactionText = $package->course->title;
            $vippsData = [
                'amount' => $price,
                'orderId' => $orderId,
                'transactionText' => $transactionText,
                'is_ajax' => true,
                'fallbackUrl' => route('front.shop.thankyou', 'iu='.encrypt($invoice->fikenUrl)),
            ];

            return response()->json(['redirect_link' => $this->vippsInitiatePayment($vippsData)]);
        }

        return response()->json(['redirect_link' => route('front.course.thank-you', $course_id)]);
    }

    /**
     * Check if user has paid course
     */
    public function hasPaidCourse(): JsonResponse
    {
        $hasPaidCourse = false;

        if (! Auth::guest()) {
            foreach (\Auth::user()->coursesTakenNotOld as $courseTaken) {
                if ($courseTaken->package->course->type != 'Free' && $courseTaken->is_active) {
                    if ($courseTaken->package->course->is_free != 1) {
                        $hasPaidCourse = true;
                        break;
                    }
                }
            }
        }

        return response()->json($hasPaidCourse);
    }

    public function validateCheckoutForm($course_id, Request $request): JsonResponse
    {
        /*$this->validate($request, [
            'email'         => 'required',
            'first_name'    => 'required',
            'last_name'     => 'required',
            'street'        => 'required',
            'zip'           => 'required',
            'city'          => 'required',
            'phone'         => 'required',
        ]);

        if (!\Auth::check()) {
            $this->validate($request,[
                'password' => 'required|min:3'
            ]);
        }*/

        $validation = [
            'email' => 'required|email',
            'first_name' => 'required',
            'last_name' => 'required',
            'street' => 'required',
            'zip' => 'required',
            'city' => 'required',
            'phone' => 'required',
            'terms' => 'accepted',
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
            $this->courseService->evaluateUser($request->email, $request->password, $request->first_name, $request->last_name, $addressData);
        }

        $user = \Auth::user();
        $user->checkoutLogs()->firstOrCreate([
            'parent' => 'course',
            'parent_id' => $course_id,
        ]);

        // return response()->json();

        return response()->json($this->courseService->processCheckout($request));
    }

    public function vippsCheckout($course_id, Request $request, CourseService $courseService, LoginController $loginController): JsonResponse
    {
        $package = Package::find($request->package_id);
        $course = $package->course;
        $calculatedPrice = $courseService->calculatePrice($course, $package, $request);
        $discount = $request->price - $calculatedPrice;

        $request->merge([
            'course_id' => $course_id,
            'item_type' => 'course',
            'discount' => $discount,
        ]);
        $checkoutDetails = collect($request->except('_token'));
        \Session::put('vipps_checkout', $checkoutDetails);

        return response()->json(['redirect_link' => $loginController->vippsLogin('checkout_state')]);
        /*$vipps = \Session::get('vipps_checkout');
        return response()->json(['redirect_link' => route('front.course.checkout.process-vipps',$vipps['course_id'])]);*/
    }

    public function processVipps(CourseService $courseService): RedirectResponse
    {
        $vippsCheckout = \Session::get('vipps_checkout');
        $package = Package::find($vippsCheckout['package_id']);
        $course = $package->course;
        $course_packages = $course->packages->pluck('id')->toArray();
        $courseTaken = \Auth::user()->coursesTaken()->where('user_id', \Auth::user()->id)
            ->whereIn('package_id', $course_packages)->first();
        // check if the user is already on the course
        if ($courseTaken) {
            $course_link = route('learner.course.show', $courseTaken->id);

            return redirect()->to($course_link);
            /*return [
                'course_link' => $course_link
            ];*/
        }

        $hasPaidCourse = false;

        // check if course bought is not expired yet
        foreach (Auth::user()->coursesTakenNotOld as $courseTaken) {
            if ($courseTaken->package->course->type != 'Free' && $courseTaken->is_active) {
                // check if course taken is not free
                if ($courseTaken->package->course->is_free != 1) {
                    $hasPaidCourse = true;
                    break;
                }
            }
        }

        $discount = $vippsCheckout['discount'];

        if ($hasPaidCourse && $package->course->type == 'Group' && $package->has_student_discount) {
            $groupDiscount = 1000;

            if ($groupDiscount > $discount) {
                $discount = $groupDiscount;
            }
        }

        if ($hasPaidCourse && $package->course->type == 'Single' && $package->has_student_discount) {
            $singleDiscount = 500;

            if ($singleDiscount > $discount) {
                $discount = $singleDiscount;
            }
        }

        $vippsCheckout['discount'] = $discount;
        $request = new \Illuminate\Http\Request;
        $request->replace($vippsCheckout->toArray());

        $orderRecord = $courseService->createOrder($request);

        $orderId = $orderRecord->id.'-'.$orderRecord->user_id;
        $price = $orderRecord->price - $orderRecord->discount;
        $transactionText = $package->course->title;
        $user = Auth::user();

        $vippsData = [
            'amount' => $price * 100,
            'orderId' => $orderId,
            'transactionText' => $transactionText,
            'is_ajax' => true,
            'vipps_phone_number' => $user->address->vipps_phone_number,
        ];

        return redirect()->to($this->vippsInitiatePayment($vippsData));
    }

    public function orderCancelled($course_id): View
    {
        return view('frontend.shop.cancelled-order', compact('course_id'));
    }

    public function checkoutTest($course_id)
    {
        $course = Course::findOrFail($course_id);
        if (! Auth::guest()) {
            $course_packages = $course->packages->pluck('id')->toArray();
            $courseTaken = CoursesTaken::where('user_id', Auth::user()->id)->whereIn('package_id', $course_packages)->first();
            if ($courseTaken) {
                return redirect(route('learner.course.show', ['id' => $courseTaken->id]));
            }
        }

        return view('frontend.shop.checkout-test', compact('course'));
    }

    /**
     * Checkout for the shared course
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function shareCourseCheckout($share_hash, Request $request)
    {
        $courseShare = CourseShared::where('hash', '=', $share_hash)->first();
        if (! $courseShare) {
            return redirect()->route('front.course.index');
        }
        $course = $courseShare->course;
        $package = $courseShare->package;

        if ($request->isMethod('post')) {
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

            $alreadyAvailCourse = CourseSharedUser::where(['user_id' => Auth::user()->id, 'course_shared_id' => $courseShare->id])->first();
            if ($alreadyAvailCourse) {
                return redirect(route('learner.course'));
            }

            // $courseTaken = CoursesTaken::firstOrNew(['user_id' => Auth::user()->id, 'package_id' => $package->id]);
            $courseTaken = new CoursesTaken;
            $courseTaken->user_id = Auth::user()->id;
            $courseTaken->package_id = $package->id;
            $courseTaken->is_active = 1;
            $courseTaken->is_free = 1;
            $courseTaken->save();

            $courseSharedUser['user_id'] = Auth::user()->id;
            $courseSharedUser['course_shared_id'] = $courseShare->id;
            CourseSharedUser::create($courseSharedUser);

            // Check for shop manuscripts
            if ($package->shop_manuscripts->count() > 0) {
                foreach ($package->shop_manuscripts as $shop_manuscript) {
                    // $shopManuscriptTaken = ShopManuscriptsTaken::firstOrNew(['user_id' => Auth::user()->id, 'shop_manuscript_id' => $shop_manuscript->shop_manuscript_id]);
                    $shopManuscriptTaken = new ShopManuscriptsTaken;
                    $shopManuscriptTaken->user_id = Auth::user()->id;
                    $shopManuscriptTaken->shop_manuscript_id = $shop_manuscript->shop_manuscript_id;
                    $shopManuscriptTaken->is_active = false;
                    $shopManuscriptTaken->package_shop_manuscripts_id = $package->shop_manuscripts[0]->id;
                    $shopManuscriptTaken->save();
                }
            }

            $add_to_automation = 0;
            if ($package->included_courses->count() > 0) {
                foreach ($package->included_courses as $included_course) {
                    if ($included_course->included_package_id == 29) { // check if webinar-pakke is included
                        $add_to_automation++;
                    }
                }
            }

            if ($package->course->id == 17) { // check if webinar-pakke
                $add_to_automation++;
            }

            if ($add_to_automation > 0) {
                $user_email = Auth::user()->email;
                $automation_id = 73;
                $user_name = Auth::user()->first_name;

                AdminHelpers::addToAutomation($user_email, $automation_id, $user_name);
            }

            // check if the course has activecampaign list then add the user
            if ($package->course->auto_list_id > 0) {
                $list_id = $package->course->auto_list_id;
                $listData = [
                    'email' => Auth::user()->email,
                    'name' => Auth::user()->first_name,
                    'last_name' => Auth::user()->last_name,
                ];

                AdminHelpers::addToActiveCampaignList($list_id, $listData);
            }

            return redirect(route('front.shop.thankyou'));
        }

        return view('frontend.shop.checkout-share', compact('course', 'package'));
    }

    /**
     * Apply discount page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function applyDiscount($course_id, $coupon)
    {
        return redirect()->to(route('front.course.checkout', $course_id).'?c='.$coupon);
        $course = Course::find($course_id);
        if (! $course) {
            return redirect()->route('front.course.index');
        }

        $discountData = $course->discounts()->where('coupon', '=', $coupon)->first();

        if (! $discountData) {
            return view('frontend.shop.applied-discount', compact('course', 'coupon', 'discountData'))
                ->with([
                    'errors' => AdminHelpers::createMessageBag('Invalid coupon code.'),
                ]);
            // return redirect()->route('front.course.checkout', $course_id);
        }

        if ($discountData->valid_to) {
            $valid_from = Carbon::parse($discountData->valid_from)->format('Y-m-d');
            $valid_to = Carbon::parse($discountData->valid_to)->format('Y-m-d');
            $today = Carbon::today()->format('Y-m-d');

            if (($today >= $valid_from) && ($today <= $valid_to)) {
                // echo "valid date <br/>";
            } else {
                return view('frontend.shop.applied-discount', compact('course', 'coupon', 'discountData'))
                    ->with([
                        'errors' => AdminHelpers::createMessageBag('Rabattkupongen er ugyldig eller utløpt.'),
                    ]);
            }
        }

        if (! Auth::guest()) {
            $course_packages = $course->packages->pluck('id')->toArray();
            $courseTaken = CoursesTaken::where('user_id', Auth::user()->id)->whereIn('package_id', $course_packages)->first();
            if ($courseTaken) {
                return redirect(route('learner.course.show', ['id' => $courseTaken->id]));
            }
        }

        $packages = $course->packages()->isShow()->get();

        return view('frontend.shop.applied-discount', compact('course', 'coupon', 'discountData',
            'packages'));
    }

    /**
     * Check the discount for the course
     */
    public function checkDiscount($course_id, Request $request): JsonResponse
    {

        if (Auth::guest()) {
            if ($request->coupon) {
                $package = Package::find($request->package_id);
                $payment_plan = PaymentPlan::find($request->payment_plan_id);
                $discountCoupon = CourseDiscount::where('course_id', $course_id)->where('coupon',
                    $request->coupon)->first();

                $full_payment_price = $package->full_payment_price;
                $months_3_price = $package->months_3_price;
                $months_6_price = $package->months_6_price;
                $months_12_price = $package->months_12_price;

                $full_payment_sale_price = $package->full_payment_sale_price;
                $months_3_sale_price = $package->months_3_sale_price;
                $months_6_sale_price = $package->months_6_sale_price;
                $months_12_sale_price = $package->months_12_sale_price;

                $today = \Carbon\Carbon::today()->format('Y-m-d');
                $fromFull = \Carbon\Carbon::parse($package->full_payment_sale_price_from)->format('Y-m-d');
                $toFull = \Carbon\Carbon::parse($package->full_payment_sale_price_to)->format('Y-m-d');
                $isBetweenFull = (($today >= $fromFull) && ($today <= $toFull)) ? 1 : 0;

                $fromMonths3 = \Carbon\Carbon::parse($package->months_3_sale_price_from)->format('Y-m-d');
                $toMonths3 = \Carbon\Carbon::parse($package->months_3_sale_price_to)->format('Y-m-d');
                $isBetweenMonths3 = (($today >= $fromMonths3) && ($today <= $toMonths3)) ? 1 : 0;

                $fromMonths6 = \Carbon\Carbon::parse($package->months_6_sale_price_from)->format('Y-m-d');
                $toMonths6 = \Carbon\Carbon::parse($package->months_6_sale_price_to)->format('Y-m-d');
                $isBetweenMonths6 = (($today >= $fromMonths6) && ($today <= $toMonths6)) ? 1 : 0;

                $fromMonths12 = \Carbon\Carbon::parse($package->months_12_sale_price_from)->format('Y-m-d');
                $toMonths12 = \Carbon\Carbon::parse($package->months_12_sale_price_to)->format('Y-m-d');
                $isBetweenMonths12 = (($today >= $fromMonths12) && ($today <= $toMonths12)) ? 1 : 0;

                $discountPrice = 0;
                if ($isBetweenFull && $package->full_payment_sale_price && $payment_plan->division === 1) {
                    $discountPrice = $full_payment_price - $full_payment_sale_price;
                }

                if ($isBetweenMonths3 && $package->months_3_sale_price && $payment_plan->division === 3) {
                    $discountPrice = $months_3_price - $months_3_sale_price;
                }

                if ($isBetweenMonths6 && $package->months_6_sale_price && $payment_plan->division === 6) {
                    $discountPrice = $months_6_price - $months_6_sale_price;
                }

                if ($isBetweenMonths12 && $package->months_12_sale_price && $payment_plan->division === 12) {
                    $discountPrice = $months_12_price - $months_12_sale_price;
                }

                $applyDiscount = $discountPrice + $discountCoupon->discount;
                $formattedDiscount = number_format($applyDiscount, 2, ',', '.');

                return response()->json(['discount' => $applyDiscount, 'discount_text' => 'Kr '.$formattedDiscount,
                    'discount_price' => $discountPrice]);
            }
        }

        $hasPaidCourse = false;
        foreach (Auth::user()->coursesTaken as $courseTaken) {
            if ($courseTaken->package->course->type != 'Free' && $courseTaken->is_active) {
                $hasPaidCourse = true;
                break;
            }
        }

        $package = Package::findOrFail($request->package_id);
        $price = 0;
        if ($hasPaidCourse && $package->course->type == 'Group' && $package->has_student_discount) {
            $price = ((int) 1500);
        }

        if ($hasPaidCourse && $package->course->type == 'Single' && $package->has_student_discount) {
            $price = ((int) 500);
        }

        if ($request->coupon) {
            $discountCoupon = CourseDiscount::where('course_id', $course_id)->where('coupon', $request->coupon)->first();
            $package = Package::find($request->package_id);
            $payment_plan = PaymentPlan::find($request->payment_plan_id);

            if ($discountCoupon) {
                $convertDiscount = ((int) $discountCoupon->discount);
                $applyDiscount = $discountCoupon->discount;

                if ($price > $convertDiscount) {
                    $applyDiscount = $price;
                }

                $full_payment_price = $package->full_payment_price;
                $months_3_price = $package->months_3_price;
                $months_6_price = $package->months_6_price;
                $months_12_price = $package->months_12_price;

                $full_payment_sale_price = $package->full_payment_sale_price;
                $months_3_sale_price = $package->months_3_sale_price;
                $months_6_sale_price = $package->months_6_sale_price;
                $months_12_sale_price = $package->months_12_sale_price;

                $today = \Carbon\Carbon::today()->format('Y-m-d');
                $fromFull = \Carbon\Carbon::parse($package->full_payment_sale_price_from)->format('Y-m-d');
                $toFull = \Carbon\Carbon::parse($package->full_payment_sale_price_to)->format('Y-m-d');
                $isBetweenFull = (($today >= $fromFull) && ($today <= $toFull)) ? 1 : 0;

                $fromMonths3 = \Carbon\Carbon::parse($package->months_3_sale_price_from)->format('Y-m-d');
                $toMonths3 = \Carbon\Carbon::parse($package->months_3_sale_price_to)->format('Y-m-d');
                $isBetweenMonths3 = (($today >= $fromMonths3) && ($today <= $toMonths3)) ? 1 : 0;

                $fromMonths6 = \Carbon\Carbon::parse($package->months_6_sale_price_from)->format('Y-m-d');
                $toMonths6 = \Carbon\Carbon::parse($package->months_6_sale_price_to)->format('Y-m-d');
                $isBetweenMonths6 = (($today >= $fromMonths6) && ($today <= $toMonths6)) ? 1 : 0;

                $fromMonths12 = \Carbon\Carbon::parse($package->months_12_sale_price_from)->format('Y-m-d');
                $toMonths12 = \Carbon\Carbon::parse($package->months_12_sale_price_to)->format('Y-m-d');
                $isBetweenMonths12 = (($today >= $fromMonths12) && ($today <= $toMonths12)) ? 1 : 0;

                $discountPrice = 0;
                if ($isBetweenFull && $package->full_payment_sale_price && $payment_plan->division === 1) {
                    $discountPrice = $full_payment_price - $full_payment_sale_price;
                }

                if ($isBetweenMonths3 && $package->months_3_sale_price && $payment_plan->division === 3) {
                    $discountPrice = $months_3_price - $months_3_sale_price;
                }

                if ($isBetweenMonths6 && $package->months_6_sale_price && $payment_plan->division === 6) {
                    $discountPrice = $months_6_price - $months_6_sale_price;
                }

                if ($isBetweenMonths12 && $package->months_12_sale_price && $payment_plan->division === 12) {
                    $discountPrice = $months_12_price - $months_12_sale_price;
                }

                $applyDiscount = $discountPrice + $applyDiscount;

                $formattedDiscount = number_format($applyDiscount, 2, ',', '.');

                return response()->json(['discount' => $applyDiscount, 'discount_text' => 'Kr '.$formattedDiscount]);
            }

        }

        return response()->json('', 404);
    }

    public function checkCouponDiscount($course_id, $coupon, CourseService $courseService): JsonResponse
    {
        $course = Course::find($course_id);
        if (! $course) {
            return response()->json([], 404);
        }

        return $courseService->checkCouponDiscount($course_id, $coupon);
    }

    public function place_order($course_id, OrderCreateRequest $request)
    {
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

        // check if webinar-pakke
        if ($course_id == 17) {
            $course = Course::findOrFail($course_id);
            $course_packages = $course->packages->pluck('id')->toArray();
            $courseTaken = CoursesTaken::where('user_id', Auth::user()->id)->whereIn('package_id', $course_packages)->first();
            // check if the user already avails this course
            if ($courseTaken) {
                return redirect(route('learner.course.show', ['id' => $courseTaken->id]));
            }
        }

        $hasPaidCourse = false;
        // check if course bought is not expired yet
        foreach (Auth::user()->coursesTakenNotOld as $courseTaken) {
            if ($courseTaken->package->course->type != 'Free' && $courseTaken->is_active) {
                // check if course taken is not free
                if ($courseTaken->package->course->is_free != 1) {
                    $hasPaidCourse = true;
                    break;
                }
            }
        }

        $paymentMode = PaymentMode::findOrFail($request->payment_mode_id);
        $paymentPlan = PaymentPlan::findOrFail($request->payment_plan_id);
        $package = Package::findOrFail($request->package_id);
        $add_to_automation = 0;

        $monthNumbers = [3 => 'months_3_enable', 6 => 'months_6_enable', 12 => 'months_12_enable'];
        // check if monthly payment is selected
        if (array_key_exists($paymentPlan->division, $monthNumbers)) {
            foreach ($monthNumbers as $month => $field) {
                // check if the payment plan selected is allowed
                if ($month == $paymentPlan->division && $package->$field == 0) {
                    return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Invalid payment plan')]);
                }
            }
        }

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

        /* check if there's an issue date set ir not then use today */
        $dueDate = date('Y-m-d');
        if ($package->issue_date && Carbon::parse($package->issue_date)->gt(Carbon::today())) {
            $dueDate = $package->issue_date;
        }
        $dueDate = Carbon::parse($dueDate);
        $payment_plan = trim($payment_plan);

        // this is use to check if the current date is within a sale date
        // for the 3 plans/payments
        $today = \Carbon\Carbon::today()->format('Y-m-d');
        $fromFull = \Carbon\Carbon::parse($package->full_payment_sale_price_from)->format('Y-m-d');
        $toFull = \Carbon\Carbon::parse($package->full_payment_sale_price_to)->format('Y-m-d');
        $isBetweenFull = (($today >= $fromFull) && ($today <= $toFull)) ? 1 : 0;

        $fromMonths3 = \Carbon\Carbon::parse($package->months_3_sale_price_from)->format('Y-m-d');
        $toMonths3 = \Carbon\Carbon::parse($package->months_3_sale_price_to)->format('Y-m-d');
        $isBetweenMonths3 = (($today >= $fromMonths3) && ($today <= $toMonths3)) ? 1 : 0;

        $fromMonths6 = \Carbon\Carbon::parse($package->months_6_sale_price_from)->format('Y-m-d');
        $toMonths6 = \Carbon\Carbon::parse($package->months_6_sale_price_to)->format('Y-m-d');
        $isBetweenMonths6 = (($today >= $fromMonths6) && ($today <= $toMonths6)) ? 1 : 0;

        // added 12th month
        $fromMonths12 = \Carbon\Carbon::parse($package->months_12_sale_price_from)->format('Y-m-d');
        $toMonths12 = \Carbon\Carbon::parse($package->months_12_sale_price_to)->format('Y-m-d');
        $isBetweenMonths12 = (($today >= $fromMonths12) && ($today <= $toMonths12)) ? 1 : 0;

        if ($payment_plan == 'Hele beløpet') {
            $price = $isBetweenFull && $package->full_payment_sale_price
                ? (int) $package->full_payment_sale_price * 100
                : (int) $package->full_payment_price * 100;
            $product_ID = $package->full_price_product;
            $dueDate->addDays($package->full_price_due_date);
        } elseif ($payment_plan == '3 måneder') {
            $price = $isBetweenMonths3 && $package->months_3_sale_price
                ? (int) $package->months_3_sale_price * 100
                : (int) $package->months_3_price * 100;
            $product_ID = $package->months_3_product;
            $dueDate->addDays($package->months_3_due_date);
        } elseif ($payment_plan == '6 måneder') {
            $price = $isBetweenMonths6 && $package->months_6_sale_price
                ? (int) $package->months_6_sale_price * 100
                : (int) $package->months_6_price * 100;
            $product_ID = $package->months_6_product;
            $dueDate->addDays($package->months_6_due_date);
        } elseif ($payment_plan == '12 måneder') {
            $price = $isBetweenMonths12 && $package->months_12_sale_price
                ? (int) $package->months_12_sale_price * 100
                : (int) $package->months_12_price * 100;
            $product_ID = $package->months_12_product;
            $dueDate->addDays($package->months_12_due_date);
        }
        $dueDate = date_format(date_create($dueDate), 'Y-m-d');

        $payment_mode = $paymentMode->mode;
        if ($payment_mode == 'Faktura') {
            $payment_mode = 'Bankoverføring';
        }

        $comment = '(Kurs: '.$package->course->title.' ['.$package->variation.'], ';
        $comment .= 'Betalingsmodus: '.$payment_mode.', ';
        $comment .= 'Betalingsplan: '.$payment_plan.')';

        $discount = 0;

        if ($request->coupon) {
            $discountCoupon = CourseDiscount::where('coupon', $request->coupon)->where('course_id', $course_id)->first();

            if ($discountCoupon->valid_to) {
                $valid_from = Carbon::parse($discountCoupon->valid_from)->format('Y-m-d');
                $valid_to = Carbon::parse($discountCoupon->valid_to)->format('Y-m-d');
                $today = Carbon::today()->format('Y-m-d');

                if (($today >= $valid_from) && ($today <= $valid_to)) {
                    // echo "valid date <br/>";
                } else {
                    return redirect()->back()->withInput()->with([
                        'errors' => AdminHelpers::createMessageBag('Rabattkupongen er ugyldig eller utløpt.'),
                    ]);
                }
            }

            if ($discountCoupon) {
                $discount = ((int) $discountCoupon->discount);
                $price = $price - ((int) $discount * 100);
            }

        }

        if ($hasPaidCourse && $package->course->type == 'Group' && $package->has_student_discount) {
            /* original code
             * $comment .= ' - Discount: Kr 1.500,00';
            $price = $price - ( (int)1500*100 );*/

            $groupDiscount = 1000;

            if ($groupDiscount > $discount) {
                $discount = $groupDiscount;
            }

            $comment .= ' - Discount: Kr '.number_format($discount, 2, ',', '.');
            $price = $price - ((int) $discount * 100);
        }

        if ($hasPaidCourse && $package->course->type == 'Single' && $package->has_student_discount) {
            /* original code
             * $comment .= ' - Discount: Kr 500,00';
            $price = $price - ( (int)500*100 );*/

            $singleDiscount = 500;

            if ($singleDiscount > $discount) {
                $discount = $singleDiscount;
            }

            $comment .= ' - Discount: Kr '.number_format($discount, 2, ',', '.');
            $price = $price - ((int) $discount * 100);
        }
        /*if( $hasPaidCourse && $package->course->type == 'Group' ) :
            $comment .= ' - Discount: Kr 1.500,00';
            $price = $price - ( (int)1500*100 );
        endif;*/

        /* original apply discount
         * if ($request->coupon) {
            $discountCoupon = CourseDiscount::where('coupon', $request->coupon)->first();

            if ($discountCoupon && !$hasPaidCourse) {
                $price = $price - ((int) $discountCoupon->discount * 100);
                $comment .= ' - Discount Coupon: Kr '.number_format($discountCoupon->discount, 2,',','.');
            }

        }*/

        $invoiceText = $package->variation;
        $comment .= ' From course order';

        // check if the course is taken and redirect the user to the course page before processing an invoice
        $alreadyAvailCourse = CoursesTaken::where(['user_id' => Auth::user()->id, 'package_id' => $package->id])->first();
        if ($alreadyAvailCourse) {
            return redirect(route('learner.course.show', ['id' => $alreadyAvailCourse->id]));
        }

        // check if the customer wants to split the invoice
        if ($paymentPlan->division > 1) { // isset($request->split_invoice) && $request->split_invoice
            $division = $paymentPlan->division * 100; // multiply the split count to get the correct value
            $price = round($price / $division, 2); // round the value to the nearest tenths
            $price = (int) $price * 100;
            for ($i = 1; $i <= $paymentPlan->division; $i++) { // loop based on the split count
                /* Carbon::today() - this is the old instead of Carbon parse */
                $dueDate = $package->issue_date ?: date('Y-m-d');
                $dueDate = Carbon::parse($dueDate)->addMonth($i)->format('Y-m-d'); // due date on every month on the same day
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
                    'payment_mode' => $paymentMode->mode,
                    'index' => $i,
                ];

                $invoice = new FikenInvoice;
                $invoice->create_invoice($invoice_fields);
            }

        } else {
            // this is the original code without the split
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
                'payment_mode' => $paymentMode->mode,
                /* 'issueDate' => $package->issue_date */
            ];

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

        $course_status = $paymentMode->mode == 'Vipps' || $paymentMode->mode == 'Paypal' ? 1 : 0;
        $course = $package->course;
        $start_date = $course->type === 'Group' ? $package->course->start_date : Carbon::today();

        $courseTaken = CoursesTaken::firstOrNew(['user_id' => Auth::user()->id, 'package_id' => $package->id]);
        $courseTaken->is_active = $course_status;
        $courseTaken->is_welcome_email_sent = 0;
        $courseTaken->end_date = Carbon::parse($start_date)->addYear();
        $courseTaken->save();

        $newOrder['user_id'] = Auth::user()->id;
        $newOrder['item_id'] = $course_id;
        $newOrder['type'] = Order::COURSE_TYPE;
        $newOrder['package_id'] = $package->id;
        $newOrder['plan_id'] = $paymentPlan->id;
        $newOrder['payment_mode_id'] = $paymentMode->id;

        $order = Order::create($newOrder);

        // Check for shop manuscripts
        if ($package->shop_manuscripts->count() > 0) {
            foreach ($package->shop_manuscripts as $shop_manuscript) {
                // $shopManuscriptTaken = ShopManuscriptsTaken::firstOrNew(['user_id' => Auth::user()->id, 'shop_manuscript_id' => $shop_manuscript->shop_manuscript_id]);
                $shopManuscriptTaken = new ShopManuscriptsTaken;
                $shopManuscriptTaken->user_id = Auth::user()->id;
                $shopManuscriptTaken->shop_manuscript_id = $shop_manuscript->shop_manuscript_id;
                $shopManuscriptTaken->is_active = false;
                $shopManuscriptTaken->package_shop_manuscripts_id = $package->shop_manuscripts[0]->id;
                $shopManuscriptTaken->save();
            }
        }

        if ($package->included_courses->count() > 0) {
            foreach ($package->included_courses as $included_course) {
                if ($included_course->included_package_id == 29) { // check if webinar-pakke is included
                    $add_to_automation++;
                }

                // add user to the included course
                $courseIncluded = CoursesTaken::firstOrNew([
                    'user_id' => Auth::user()->id,
                    'package_id' => $included_course->included_package_id,
                ]);
                $courseIncluded->is_active = $course_status;
                $courseIncluded->save();
            }

            // this means webinar-pakke is included
            if ($add_to_automation) {
                $user = Auth::user();
                $userCoursesTaken = $user->coursesTaken;
                foreach ($userCoursesTaken as $userCourseTaken) {
                    $userCourseTaken->end_date = Carbon::parse($start_date)->addYear();
                    $userCourseTaken->save();
                }
            }
        }

        if ($package->course->id == 17) { // check if webinar-pakke
            $add_to_automation++;
        }

        if ($add_to_automation > 0) {
            $user_email = Auth::user()->email;
            $automation_id = 73;
            $user_name = Auth::user()->first_name;

            AdminHelpers::addToAutomation($user_email, $automation_id, $user_name);
        }

        // check if the course has activecampaign list then add the user
        if ($package->course->auto_list_id > 0) {
            $list_id = $package->course->auto_list_id;
            $listData = [
                'email' => Auth::user()->email,
                'name' => Auth::user()->first_name,
                'last_name' => Auth::user()->last_name,
            ];

            AdminHelpers::addToActiveCampaignList($list_id, $listData);
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

        // Email to support
        $from = 'postmail@forfatterskolen.no';
        $headers1 = 'From: Forfatterskolen<'.$from.">\r\n";
        $headers1 .= "MIME-Version: 1.0\r\n";
        $headers1 .= "Content-Type: text/html; charset=UTF-8\r\n";
        // mail('support@forfatterskolen.no', 'New Course Order', Auth::user()->first_name . ' has ordered the course ' . $package->course->title, $headers1);
        $to = 'support@forfatterskolen.no'; //
        $emailData = [
            'email_subject' => 'New Course Order',
            'email_message' => Auth::user()->first_name.' has ordered the course '.$package->course->title,
            'from_name' => '',
            'from_email' => 'post@forfatterskolen.no',
            'attach_file' => null,
        ];
        \Mail::to($to)->queue(new SubjectBodyEmail($emailData));
        /*AdminHelpers::send_email('New Course Order',
            'post@forfatterskolen.no', 'support@forfatterskolen.no', Auth::user()->first_name . ' has ordered the course ' . $package->course->title);*/

        // Send course email
        $headers = "From: Forfatterskolen<post@forfatterskolen.no>\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $user = Auth::user();

        $password = $user->need_pass_update ? 'Z5C5E5M2jv' : 'Skjult (kan endres inne i portalen eller via glemt passord)';

        $search_string = [
            '[username]', '[password]',
        ];
        $replace_string = [
            $courseTaken->user->email, $password,
        ];
        $email_content = str_replace($search_string, $replace_string, $package->course->email);

        $user_email = $user->email;

        $encode_email = encrypt($user_email);
        $redirectLink = encrypt(route('learner.course'));
        $actionUrl = route('auth.login.emailRedirect', [$encode_email, $redirectLink]);
        $actionText = 'Mine Kurs';
        $attachments = [asset($this->generateDocx($user->id, $package->id)),
            asset('/email-attachments/skjema-for-opplysninger-om-angrerett.docx')];

        // mail($user->email, $package->course->title, view('emails.course_order', compact('actionText', 'actionUrl', 'user', 'email_content')), $headers);
        /*AdminHelpers::send_email($package->course->title,
            'postmail@forfatterskolen.no', $user_email,
            view('emails.course_order', compact('actionText', 'actionUrl', 'user', 'email_content')),
            'Forfatterskolen', $attachments);*/
        dispatch(new CourseOrderJob($user_email, $package->course->title, $email_content,
            'postmail@forfatterskolen.no', 'Forfatterskolen', $attachments, 'courses-taken-order',
            $courseTaken->id, $actionText, $actionUrl, $user, $package->id));

        if ($paymentMode->mode == 'Paypal') {
            $paypal = new PayPal;

            $response = $paypal->purchase([
                'amount' => ($price / 100),
                'transactionId' => $invoice->invoiceID,
                'currency' => 'NOK',
                'cancelUrl' => $paypal->getCancelUrl($invoice->invoiceID),
                'returnUrl' => $paypal->getReturnUrl($invoice->invoiceID, 'course'),
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
                <input type="hidden" name="return" value="'.route('front.shop.thankyou').'?gateway=Paypal">
                <input type="image" name="submit" src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" align="right" alt="PayPal - The safer, easier way to pay online">
            </form>';
            echo '<script>document.getElementById("paypal_form").submit();</script>';
            return;*/
        }

        // check if vipps payment mode and the current user id is 4
        if ($paymentMode->mode == 'Vipps') {
            $orderId = $order->id.'-'.$user->id;
            $transactionText = $package->course->title;
            $vippsData = [
                'amount' => $price,
                'orderId' => $orderId,
                'transactionText' => $transactionText,
            ];

            return $this->vippsInitiatePayment($vippsData);
        }

        return redirect(route('front.shop.thankyou'));

    }

    public function place_order_test($course_id, OrderCreateRequest $request)
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

        // check if webinar-pakke
        if ($course_id == 17) {
            $course = Course::findOrFail($course_id);
            $course_packages = $course->packages->pluck('id')->toArray();
            $courseTaken = CoursesTaken::where('user_id', Auth::user()->id)->whereIn('package_id', $course_packages)->first();
            // check if the user already avails this course
            if ($courseTaken) {
                return redirect(route('learner.course.show', ['id' => $courseTaken->id]));
            }
        }

        $hasPaidCourse = false;
        // check if course bought is not expired yet
        foreach (Auth::user()->coursesTakenNotOld as $courseTaken) {
            if ($courseTaken->package->course->type != 'Free' && $courseTaken->is_active) {
                $hasPaidCourse = true;
                break;
            }
        }

        $paymentMode = PaymentMode::findOrFail($request->payment_mode_id);
        $paymentPlan = PaymentPlan::findOrFail($request->payment_plan_id);
        $package = Package::findOrFail($request->package_id);
        $add_to_automation = 0;

        $monthNumbers = [3 => 'months_3_enable', 6 => 'months_6_enable', 12 => 'months_12_enable'];
        // check if monthly payment is selected
        if (array_key_exists($paymentPlan->division, $monthNumbers)) {
            foreach ($monthNumbers as $month => $field) {
                // check if the payment plan selected is allowed
                if ($month == $paymentPlan->division && $package->$field == 0) {
                    return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Invalid payment plan')]);
                }
            }
        }

        $payment_plan = ($paymentMode->mode == 'Paypal') ? 'Hele beløpet' : $paymentPlan->plan;

        /* check if there's an issue date set ir not then use today */
        $dueDate = $package->issue_date ?: date('Y-m-d');
        $dueDate = Carbon::parse($dueDate);
        $payment_plan = trim($payment_plan);

        // this is use to check if the current date is within a sale date
        // for the 3 plans/payments
        $today = \Carbon\Carbon::today()->format('Y-m-d');
        $fromFull = \Carbon\Carbon::parse($package->full_payment_sale_price_from)->format('Y-m-d');
        $toFull = \Carbon\Carbon::parse($package->full_payment_sale_price_to)->format('Y-m-d');
        $isBetweenFull = (($today >= $fromFull) && ($today <= $toFull)) ? 1 : 0;

        $fromMonths3 = \Carbon\Carbon::parse($package->months_3_sale_price_from)->format('Y-m-d');
        $toMonths3 = \Carbon\Carbon::parse($package->months_3_sale_price_to)->format('Y-m-d');
        $isBetweenMonths3 = (($today >= $fromMonths3) && ($today <= $toMonths3)) ? 1 : 0;

        $fromMonths6 = \Carbon\Carbon::parse($package->months_6_sale_price_from)->format('Y-m-d');
        $toMonths6 = \Carbon\Carbon::parse($package->months_6_sale_price_to)->format('Y-m-d');
        $isBetweenMonths6 = (($today >= $fromMonths6) && ($today <= $toMonths6)) ? 1 : 0;

        // added 12th month
        $fromMonths12 = \Carbon\Carbon::parse($package->months_12_sale_price_from)->format('Y-m-d');
        $toMonths12 = \Carbon\Carbon::parse($package->months_12_sale_price_to)->format('Y-m-d');
        $isBetweenMonths12 = (($today >= $fromMonths12) && ($today <= $toMonths12)) ? 1 : 0;

        if ($payment_plan == 'Hele beløpet') {
            $price = $isBetweenFull && $package->full_payment_sale_price
                ? (int) $package->full_payment_sale_price * 100
                : (int) $package->full_payment_price * 100;
            $product_ID = $package->full_price_product;
            $dueDate->addDays($package->full_price_due_date);
        } elseif ($payment_plan == '3 måneder') {
            $price = $isBetweenMonths3 && $package->months_3_sale_price
                ? (int) $package->months_3_sale_price * 100
                : (int) $package->months_3_price * 100;
            $product_ID = $package->months_3_product;
            $dueDate->addDays($package->months_3_due_date);
        } elseif ($payment_plan == '6 måneder') {
            $price = $isBetweenMonths6 && $package->months_6_sale_price
                ? (int) $package->months_6_sale_price * 100
                : (int) $package->months_6_price * 100;
            $product_ID = $package->months_6_product;
            $dueDate->addDays($package->months_6_due_date);
        } elseif ($payment_plan == '12 måneder') {
            $price = $isBetweenMonths12 && $package->months_12_sale_price
                ? (int) $package->months_12_sale_price * 100
                : (int) $package->months_12_price * 100;
            $product_ID = $package->months_12_product;
            $dueDate->addDays($package->months_12_due_date);
        }
        $dueDate = date_format(date_create($dueDate), 'Y-m-d');

        $payment_mode = $paymentMode->mode;
        if ($payment_mode == 'Faktura') {
            $payment_mode = 'Bankoverføring';
        }

        $comment = '(Kurs: '.$package->course->title.' ['.$package->variation.'], ';
        $comment .= 'Betalingsmodus: '.$payment_mode.', ';
        $comment .= 'Betalingsplan: '.$payment_plan.')';

        $discount = 0;

        if ($request->coupon) {
            $discountCoupon = CourseDiscount::where('coupon', $request->coupon)->where('course_id', $course_id)->first();

            if ($discountCoupon->valid_to) {
                $valid_from = Carbon::parse($discountCoupon->valid_from)->format('Y-m-d');
                $valid_to = Carbon::parse($discountCoupon->valid_to)->format('Y-m-d');
                $today = Carbon::today()->format('Y-m-d');

                if (($today >= $valid_from) && ($today <= $valid_to)) {
                    // echo "valid date <br/>";
                } else {
                    return redirect()->back()->withInput()->with([
                        'errors' => AdminHelpers::createMessageBag('Rabattkupongen er ugyldig eller utløpt.'),
                    ]);
                }
            }

            if ($discountCoupon) {
                $discount = ((int) $discountCoupon->discount);
                $price = $price - ((int) $discount * 100);
            }

        }

        if ($hasPaidCourse && $package->course->type == 'Group' && $package->has_student_discount) {
            /* original code
             * $comment .= ' - Discount: Kr 1.500,00';
            $price = $price - ( (int)1500*100 );*/

            $groupDiscount = 1000;

            if ($groupDiscount > $discount) {
                $discount = $groupDiscount;
            }

            $comment .= ' - Discount: Kr '.number_format($discount, 2, ',', '.');
            $price = $price - ((int) $discount * 100);
        }

        if ($hasPaidCourse && $package->course->type == 'Single' && $package->has_student_discount) {
            /* original code
             * $comment .= ' - Discount: Kr 500,00';
            $price = $price - ( (int)500*100 );*/

            $singleDiscount = 500;

            if ($singleDiscount > $discount) {
                $discount = $singleDiscount;
            }

            $comment .= ' - Discount: Kr '.number_format($discount, 2, ',', '.');
            $price = $price - ((int) $discount * 100);
        }
        /*if( $hasPaidCourse && $package->course->type == 'Group' ) :
            $comment .= ' - Discount: Kr 1.500,00';
            $price = $price - ( (int)1500*100 );
        endif;*/

        /* original apply discount
         * if ($request->coupon) {
            $discountCoupon = CourseDiscount::where('coupon', $request->coupon)->first();

            if ($discountCoupon && !$hasPaidCourse) {
                $price = $price - ((int) $discountCoupon->discount * 100);
                $comment .= ' - Discount Coupon: Kr '.number_format($discountCoupon->discount, 2,',','.');
            }

        }*/

        $invoiceText = $package->variation;
        $comment .= ' From course order';

        // check if the course is taken and redirect the user to the course page before processing an invoice
        $alreadyAvailCourse = CoursesTaken::where(['user_id' => Auth::user()->id, 'package_id' => $package->id])->first();
        if ($alreadyAvailCourse) {
            return redirect(route('learner.course.show', ['id' => $alreadyAvailCourse->id]));
        }

        // check if the customer wants to split the invoice
        if (isset($request->split_invoice) && $request->split_invoice) {
            $division = $paymentPlan->division * 100; // multiply the split count to get the correct value
            $price = round($price / $division, 2); // round the value to the nearest tenths
            $price = (int) $price * 100;
            for ($i = 1; $i <= $paymentPlan->division; $i++) { // loop based on the split count
                /* Carbon::today() - this is the old instead of Carbon parse */
                $dueDate = $package->issue_date ?: date('Y-m-d');
                $dueDate = Carbon::parse($dueDate)->addMonth($i)->format('Y-m-d'); // due date on every month on the same day
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
                    'comment' => $comment, 'payment_mode' => $paymentMode->mode,
                ];

                $invoice = new FikenInvoice;
                $invoice->create_invoice($invoice_fields);
            }

        } else {
            // this is the original code without the split
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
                'comment' => $comment, 'payment_mode' => $paymentMode->mode,
                /* 'issueDate' => $package->issue_date */
            ];

            $invoice = new FikenInvoice;
            $invoice->create_invoice($invoice_fields);
        }

        if ($request->update_address) {
            $address = Address::firstOrNew(['user_id' => Auth::user()->id]);
            $address->street = $request->street;
            $address->city = $request->city;
            $address->zip = $request->zip;
            $address->phone = $request->phone;
            // $address->save();
        }

        $courseTaken = CoursesTaken::firstOrNew(['user_id' => Auth::user()->id, 'package_id' => $package->id]);
        $courseTaken->is_active = 0;
        // $courseTaken->save();

        // Check for shop manuscripts
        if ($package->shop_manuscripts->count() > 0) {
            foreach ($package->shop_manuscripts as $shop_manuscript) {
                // $shopManuscriptTaken = ShopManuscriptsTaken::firstOrNew(['user_id' => Auth::user()->id, 'shop_manuscript_id' => $shop_manuscript->shop_manuscript_id]);
                $shopManuscriptTaken = new ShopManuscriptsTaken;
                $shopManuscriptTaken->user_id = Auth::user()->id;
                $shopManuscriptTaken->shop_manuscript_id = $shop_manuscript->shop_manuscript_id;
                $shopManuscriptTaken->is_active = false;
                $shopManuscriptTaken->package_shop_manuscripts_id = $package->shop_manuscripts[0]->id;
                $shopManuscriptTaken->save();
            }
        }

        if ($package->included_courses->count() > 0) {
            foreach ($package->included_courses as $included_course) {
                if ($included_course->included_package_id == 29) { // check if webinar-pakke is included
                    $add_to_automation++;
                }
            }
        }

        if ($package->course->id == 17) { // check if webinar-pakke
            $add_to_automation++;
        }

        if ($add_to_automation > 0) {
            $user_email = Auth::user()->email;
            $automation_id = 73;
            $user_name = Auth::user()->first_name;

            AdminHelpers::addToAutomation($user_email, $automation_id, $user_name);
        }

        // check if the course has activecampaign list then add the user
        if ($package->course->auto_list_id > 0) {
            $list_id = $package->course->auto_list_id;
            $listData = [
                'email' => Auth::user()->email,
                'name' => Auth::user()->first_name,
                'last_name' => Auth::user()->last_name,
            ];

            echo $list_id;
            print_r($listData);
            print_r(AdminHelpers::addToActiveCampaignListTest($list_id, $listData));
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

        // Email to support
        $from = 'postmail@forfatterskolen.no';
        $headers1 = 'From: Forfatterskolen<'.$from.">\r\n";
        $headers1 .= "MIME-Version: 1.0\r\n";
        $headers1 .= "Content-Type: text/html; charset=UTF-8\r\n";
        // mail('support@forfatterskolen.no', 'New Course Order', Auth::user()->first_name . ' has ordered the course ' . $package->course->title, $headers1);
        /*AdminHelpers::send_email('New Course Order',
            'post@forfatterskolen.no', 'support@forfatterskolen.no', Auth::user()->first_name . ' has ordered the course ' . $package->course->title);*/

        // Send course email
        $actionText = 'Mine Kurs';
        $actionUrl = 'http://www.forfatterskolen.no/account/course';
        $headers = "From: Forfatterskolen<postmail@forfatterskolen.no>\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $user = Auth::user();
        $email_content = $package->course->email;
        // mail($user->email, $package->course->title, view('emails.course_order', compact('actionText', 'actionUrl', 'user', 'email_content')), $headers);
        /*AdminHelpers::send_email($package->course->title,
            'post@forfatterskolen.no', $user->email,
            view('emails.course_order', compact('actionText', 'actionUrl', 'user', 'email_content')));*/

        if ($paymentMode->mode == 'Paypal') {
            $paypalForm = '<form name="_xclick" id="paypal_form" style="display:none" action="https://www.paypal.com/cgi-bin/webscr" method="post">
            <input type="hidden" name="cmd" value="_xclick">
            <input type="hidden" name="business" value="post.forfatterskolen@gmail.com">
            <input type="hidden" name="currency_code" value="NOK">
            <input type="hidden" name="custom" value="'.$invoice->invoiceID.'">
            <input type="hidden" name="item_name" value="Course Order Invoice">
            <input type="hidden" name="amount" value="'.($price / 100).'">
            <input type="hidden" name="return" value="'.route('front.shop.thankyou').'">
            <input type="image" name="submit" src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" align="right" alt="PayPal - The safer, easier way to pay online">
        </form>
        <script>document.getElementById("paypal_form").submit();</script>';

            return new Response($paypalForm);
        }

        // return redirect(route('front.shop.thankyou'));

    }

    public function thankyou(Request $request, CourseService $courseService): View
    {

        // check if from svea payment
        if ($request->has('svea_ord') || $request->has('pl_ord')) {

            $order_id = $request->input('svea_ord') ?? $request->input('pl_ord');
            $order = Order::find($order_id);

            Log::info('has svea_ord or pl_ord order_id = '.$order_id);
            if ($request->has('svea_ord')) {
                Log::info('inside has SVEA order '.$order_id);
                SveaUpdateOrderDetailsJob::dispatch($order->id)->delay(Carbon::now()->addMinute(1));
            }

            // add course to user
            if (! $order->is_processed) {

                if ($order->type === 6) {
                    $courseTaken = $courseService->upgradeCourseTaken($order);
                    $courseService->notifyUserForUpgrade($order, $courseTaken);
                } else {
                    $courseTaken = $courseService->addCourseToLearner($order->user_id, $order->package_id);
                    $courseTaken->is_pay_later = $order->is_pay_later;
                    $courseTaken->is_active = $order->is_pay_later ? 0 : 1;
                    $courseTaken->save();

                    $courseService->notifyUser($order->user_id, $order->package_id, $courseTaken, true, true);
                }

                $courseService->notifyAdmin($order->user_id, $order->package_id);
            }

            $order->is_processed = 1;
            $order->save();

            CheckoutLog::updateOrCreate([
                'user_id' => \auth()->id(),
                'parent' => 'course',
                'parent_id' => $order->package->course_id,
            ], [
                'is_ordered' => true,
            ]);
        }

        // check if fiken invoice url is set
        // this is set when vipps payment is cancelled
        if ($request->has('iu')) {
            $fikenUrl = decrypt($request->get('iu'));
            $fiken = new FikenInvoice;
            $fikenInvoice = $fiken->get_invoice_data($fikenUrl);
            $fiken->send_invoice($fikenInvoice);
        }

        \Session::remove('vipps_checkout');

        if ($request->has('pl_ord')) {
            return view('frontend.shop.pay_later_thankyou');
        }

        return view('frontend.shop.thankyou');
    }

    /**
     * Claim course reward
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function claimReward($course_id, Request $request)
    {
        $course = Course::find($course_id);
        if ($course->rewardCoupons()->count()) {
            if ($request->isMethod('post')) {
                $reward = $course->rewardCoupons()->where('coupon', $request->coupon)->first();

                if (! $reward) {
                    return redirect()->back()->withInput()->with(['errors' => AdminHelpers::createMessageBag('Invalid coupon.')]);
                }

                // check if the coupon is already been used
                if ($reward->is_used) {
                    return redirect()->back()->withInput()->with(['errors' => AdminHelpers::createMessageBag('Coupon is already used.')]);
                }

                $reward->is_used = 1;
                $reward->save();

                // if( $request->update_address ) :
                $address = Address::firstOrNew(['user_id' => Auth::user()->id]);
                $address->street = $request->street;
                $address->city = $request->city;
                $address->zip = $request->zip;
                $address->phone = $request->phone;
                $address->save();
                // endif;

                $course_packages = $course->packages->pluck('id')->toArray();
                $courseTaken = CoursesTaken::where('user_id', Auth::user()->id)->whereIn('package_id', $course_packages)->first();

                // check if the user already avails this course
                if ($courseTaken) {
                    $courseTaken->is_active = 1;
                    // add one month to the end date
                    $courseTaken->end_date = Carbon::parse($courseTaken->end_date)->addMonth(1); // Carbon::now()->addMonth(1);
                } else {
                    $package = Package::findOrFail($request->package_id);

                    if (! $package) {
                        return redirect()->back();
                    }

                    $courseTaken = CoursesTaken::firstOrNew(['user_id' => Auth::user()->id, 'package_id' => $package->id]);
                    $courseTaken->is_active = 1;
                    $courseTaken->started_at = Carbon::now();

                    // check if webinar-pakke or not to specify the correct end date
                    $courseTaken->end_date = ($course_id == 17) ? Carbon::now()->addMonth(1) : Carbon::now()->addYear(1);
                }

                $courseTaken->save();

                return redirect()->route('learner.course');
            }

            return view('frontend.shop.claim-reward', compact('course'));
        }

        return redirect()->route('front.course.index');
    }

    /**
     * Get the payment plan options to display in plan section
     */
    public function getPaymentPlanOptions($id): JsonResponse
    {
        $package = Package::find($id);
        $allowedPaymentMonth = [1];

        if ($package->months_3_enable) {
            array_push($allowedPaymentMonth, 3);
        }

        if ($package->months_6_enable) {
            array_push($allowedPaymentMonth, 6);
        }

        if ($package->months_12_enable) {
            array_push($allowedPaymentMonth, 12);
        }

        $paymentPlan = PaymentPlan::whereIn('division', $allowedPaymentMonth)
            ->orderBy('division', 'asc')
            ->get();

        return response()->json($paymentPlan);
    }

    public function getPaymentModeOptions(): JsonResponse
    {
        return response()->json(\App\Http\FrontendHelpers::paymentModes(true));
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

    public function proceedCheckout($course_id, Course $course, Request $request): JsonResponse
    {
        $apiKey = app('Bambora')->credentials;
        $course = $course->find($course_id);
        $package = $course->packages()->where('id', $request->package_id)->first();
        $data['course'] = $course;
        $data['request'] = $request->all();
        $data['package'] = $package;

        $hasPaidCourse = false;
        // check if course bought is not expired yet
        if (Auth::user()) {
            foreach (Auth::user()->coursesTakenNotOld as $courseTaken) {
                if ($courseTaken->package->course->type != 'Free' && $courseTaken->is_active) {
                    // check if course taken is not free
                    if ($courseTaken->package->course->is_free != 1) {
                        $hasPaidCourse = true;
                        break;
                    }
                }
            }
        }

        $price = $package->full_payment_price * 100;
        $data['price'] = $price;

        $curlRequest = [];
        $curlRequest['order'] = [];
        $curlRequest['order']['id'] = AdminHelpers::generateHash(6);
        $curlRequest['order']['amount'] = $price;
        $curlRequest['order']['currency'] = 'NOK';

        $curlRequest['url'] = [];
        $curlRequest['url']['accept'] = 'https://www.forfatterskolen.no/bambora/accept';
        $curlRequest['url']['cancel'] = 'https://www.forfatterskolen.no/thankyou';
        $curlRequest['url']['immediateredirecttoaccept'] = 1;
        /*$curlRequest["url"]["callbacks"] = array();
        $curlRequest["url"]["callbacks"][] = array("url" => "https://example.org/callback");*/

        $curlRequest['paymentwindow'] = [];
        $curlRequest['paymentwindow'] = ['id' => 1];
        // $curlRequest["paymentwindow"]["paymentmethod"] = array();
        /*$curlRequest["paymentwindow"]["paymentmethods"] = [
            ["id" => "paymentcard", "action" => "include"],
            ["id" => "invoice", "action" => "include"],
            ["id" => "vipps", "action" => "include"],
            ["id" => "ekspresbank", "action" => "include"]
        ];*/

        /*$curlRequest["paymentwindow"]["paymentgroup"] = array();
        $curlRequest["paymentwindow"]["paymentgroup"] = [
            ["id" => 1, "action" => "include"],
            ["id" => 11, "action" => "include"],
            ["id" => 4, "action" => "include"],
            ["id" => 16, "action" => "include"],
        ];

        $curlRequest["paymentwindow"]["paymenttype"] = array();
        $curlRequest["paymentwindow"]["paymenttype"] = [
            ["id" => 1, "action" => "include"],
            ["id" => 11, "action" => "include"],
            ["id" => 14, "action" => "include"]
        ];*/

        $checkoutUrl = 'https://api.v1.checkout.bambora.com/sessions';

        $requestJson = json_encode($curlRequest);
        $contentLength = isset($requestJson) ? strlen($requestJson) : 0;

        $headers = [
            'Content-Type: application/json',
            'Content-Length: '.$contentLength,
            'Accept: application/json',
            'Authorization: Basic '.$apiKey,
        ];

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $requestJson);
        curl_setopt($curl, CURLOPT_URL, $checkoutUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $rawResponse = curl_exec($curl);
        $response = json_decode($rawResponse);

        return response()->json($response);
    }

    public function generateDocx($user_id, $package_id)
    {
        $user = User::find($user_id);
        $address = $user->address;
        $package = Package::find($package_id);
        $course = $package->course;

        $parseDate = Carbon::today()->addDays(13);
        if ($course->type === 'Group' && Carbon::today()->lt(Carbon::parse($course->start_date))) {
            $parseDate = Carbon::parse($course->start_date)->addDays(13);
        }

        $expirationDate = $parseDate->format('d.m.Y');
        $expirationDay = FrontendHelpers::convertDayLanguage($parseDate->format('N'));

        $phpWord = new \PhpOffice\PhpWord\PhpWord;
        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(12);

        // prevent user from editing/copying from the file
        $documentProtection = $phpWord->getSettings()->getDocumentProtection();
        $documentProtection->setEditing(DocProtect::FORMS);

        $sectionStyle = [
            'marginTop' => 1150,
            'marginBottom' => 1150,
            'marginLeft' => 800,
            'marginRight' => 800,
        ];
        $section = $phpWord->addSection(
            $sectionStyle
        );

        $section->addText('Angreskjema',
            [
                'size' => 18,
            ],
            [
                'alignment' => 'center',
                'marginBottom' => 0,
                'space' => ['before' => 0, 'after' => 70],
            ]);

        $section->addText('ved kjøp av varer og tjenester som ikke er finansielle tjenester',
            ['size' => 10], [
                'alignment' => 'center',
                'space' => ['after' => 250],
            ]);

        $section->addText('Fyll ut og returner dette skjemaet dersom du ønsker å gå fra avtalen', [],
            [
                'alignment' => 'center',
                'space' => ['after' => 350],
            ]);

        $section->addText('Utfylt skjema sendes til:', [], [
            'space' => ['after' => 0],
        ]);
        $section->addText('(den næringsdrivende skal sette inn sitt navn, geografiske adresse og ev.'.
            'telefaksnummer og e-postadresse)', ['size' => 10], [
                'space' => ['after' => 350],
            ]);

        $width = 100 * 100;

        $table = $section->addTable([
            'width' => $width,
        ]);

        $table->addRow(0);
        $table->addCell($width, [
            'borderBottomSize' => 6,
            'height' => 1,
        ])->addText('Forfatterskolen, Postboks 9233, 3064 DRAMMEN', [
            'bgColor' => 'CCCCCC',
        ], [
            'space' => ['before' => 150, 'after' => 0],
            'indent' => 0.1,
        ]);

        $table->addRow(0);
        $table->addCell($width, [
            'borderBottomSize' => 6,
            'height' => 1,
        ])->addText('post@forfatterskolen.no', [
            'bgColor' => 'CCCCCC',
        ], [
            'space' => ['before' => 250, 'after' => 0],
            'indent' => 0.1,
        ]);

        $section->addTable($table);

        $listItemRun = $section->addTextRun([
            'space' => ['before' => 550],
        ]);
        $listItemRun->addText('Jeg/vi underretter herved om at jeg/vi ønsker å gå fra min/vår avtale om kjøp av følgende:');
        $listItemRun->addText(' (sett kryss)', ['size' => 10]);

        $checkBox = $section->addTextRun();
        $checkBox->addFormField('checkbox')->setValue(true);
        $checkBox->addText(' tjenester');
        $checkBox->addText(' (spesifiser på linjene nedenfor)', ['size' => 10]);

        $table = $section->addTable([
            'width' => $width,
        ]);
        $table->addRow(0);
        $table->addCell($width, [
            'borderBottomSize' => 6,
            'height' => 1,
        ])->addText('Gjelder kjøp av '.$course->title, [
            'bgColor' => 'CCCCCC',
        ], [
            'space' => ['before' => 150, 'after' => 0],
            'indent' => 0.1,
        ]);

        $table->addRow(0);
        $table->addCell($width, [
            'borderBottomSize' => 6,
            'height' => 1,
        ])->addText('Frist for avbestilling for  å kunne benytte angreretten: Innen klokken 23.59 '
            .$expirationDay.' '.$expirationDate, [
                'bgColor' => 'CCCCCC',
            ], [
                'space' => ['before' => 150, 'after' => 0],
                'indent' => 0.1,
            ]);

        $section->addText('Sett kryss og dato:', ['size' => 10], [
            'space' => ['before' => 400],
        ]);

        $textRun = $section->addTextRun();
        $textRun->addFormField('checkbox')->setValue(true);
        $textRun->addText(' Avtalen ble inngått den');
        $textRun->addText(' (dato)', ['size' => 10]);
        $textRun->addText('     '); // spacing
        $textRun->addText(Carbon::today()->format('d.m.Y'), [
            'bgColor' => 'CCCCCC',
            'underline' => 'single',
        ]);
        $textRun->addText(' (ved kjøp av tjenester)', ['size' => 10]);

        $table = $section->addTable([
            'width' => $width,
        ]);
        $table->addRow(0);
        $table->addCell($width, [
            'height' => 1,
        ])->addText('Forbrukerens/forbrukemesnavn:', ['size' => 10], [
            'space' => ['before' => 500],
        ]);

        $table->addRow(0);
        $table->addCell($width, [
            'borderBottomSize' => 6,
            'height' => 1,
        ])->addFormField('textinput', [
            'bgColor' => 'CCCCCC',
        ], [
            'space' => ['before' => 0, 'after' => 0],
            'indent' => 0.1,
        ])->setValue(' ');

        $table->addRow(0);
        $table->addCell($width, [
            'height' => 1,
        ])->addText('Forbrukerens/forbrukemes adresse:', ['size' => 10], [
            'space' => ['before' => 300, 'after' => 0],
        ]);

        $table->addRow(0);
        $table->addCell($width, [
            'borderBottomSize' => 6,
            'height' => 1,
        ])->addFormField('textinput', [
            'bgColor' => 'CCCCCC',
        ], [
            'space' => ['before' => 200, 'after' => 0],
            'indent' => 0.1,
        ])->setValue(' ');

        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell($width)->addTextRun([
            'space' => ['before' => 1800, 'after' => 0],
        ]);

        $cell->addText('Dato:', ['size' => 10]);
        $cell->addText('     '); // spacing
        $cell->addFormField('textinput', [
            'indent' => 2,
        ])->setValue('dd. dd. åååå');

        $table = $section->addTable();
        $table->addRow(0);
        $table->addCell($width, [
            'borderBottomSize' => 6,
        ])->addText('', [], [
            'space' => ['before' => 500, 'after' => 0],
        ]);

        $section->addText('Forbrukerens/forbrukemes underskrift (dersom papirskjema benyttes)',
            [
                'size' => 10,
            ],
            [
                'alignment' => 'center',
            ]);

        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        try {
            $objWriter->save(public_path('email-attachments/angrerettskjema.docx'));

            $courseOrderAttachmentCopy = '/storage/course-order-attachments/'.
                str_replace(':', '-', $course->title).'-'.$user_id.'.docx';
            $objWriter->save(public_path($courseOrderAttachmentCopy));

            CourseOrderAttachment::create([
                'user_id' => $user_id,
                'course_id' => $course->id,
                'package_id' => $package_id,
                'file_path' => $courseOrderAttachmentCopy,
            ]);

            return 'email-attachments/angrerettskjema.docx';
        } catch (\Exception $e) {
            return '';
        }
    }
}
