<?php

namespace App\Services;

use App\Address;
use App\Course;
use App\CourseDiscount;
use App\CourseOrderAttachment;
use App\CoursesTaken;
use App\EmailOut;
use App\Events\AddToCampaignList;
use App\Http\AdminHelpers;
use App\Http\FikenInvoice;
use App\Http\FrontendHelpers;
use App\Jobs\AddMailToQueueJob;
use App\Jobs\AddToListJob;
use App\Jobs\CourseOrderJob;
use App\Order;
use App\Package;
use App\PaymentPlan;
use App\ShopManuscriptsTaken;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\SimpleType\DocProtect;

class CourseService
{
    protected $course;

    protected $user;

    /**
     * CourseService constructor.
     */
    public function __construct(Course $course, User $user)
    {
        $this->course = $course;
        $this->user = $user;
    }

    /**
     * Check if the coupon is valid and show discount
     */
    public function checkCouponDiscount($course_id, $coupon): JsonResponse
    {
        $course = $this->course->find($course_id);
        $discountCoupon = $course->discounts()->where('coupon', $coupon)->first();

        if (! $discountCoupon) {
            return response()->json([
                'error_message' => 'Invalid Coupon.',
            ], 422);
        }

        if ($discountCoupon->valid_to) {
            $valid_from = Carbon::parse($discountCoupon->valid_from)->format('Y-m-d');
            $valid_to = Carbon::parse($discountCoupon->valid_to)->format('Y-m-d');
            $today = Carbon::today()->format('Y-m-d');

            if (($today >= $valid_from) && ($today <= $valid_to)) {
                // valid date
            } else {
                return response()->json([
                    'error_message' => 'Coupon expired.',
                ], 422);
            }
        }

        return response()->json([
            'discountCoupon' => $discountCoupon,
        ], 200);
    }

    /**
     * Check if user is logged in or not and if it exists
     */
    public function evaluateUser($email, $password, $first_name, $last_name, $address)
    {
        if (\Auth::guest()) {
            $user = $this->user->where('email', $email)->first();
            if ($user) {
                \Auth::login($user);
            } else {
                $new_user = User::create([
                    'email' => $email,
                    'password' => bcrypt($password),
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                ]);
                \Auth::login($new_user);
                Address::create(array_merge($address, ['user_id' => $new_user->id]));
            }
        }
    }

    /**
     * Process the checkout
     *
     * @return \Illuminate\Http\JsonResponse|array
     */
    public function processCheckout(Request $request)
    {
        // this is for not logged in user
        $addressData = [
            'street' => $request->street,
            'zip' => $request->zip,
            'city' => $request->city,
            'phone' => $request->phone,
        ];
        $this->evaluateUser($request->email, $request->password, $request->first_name, $request->last_name, $addressData);

        // update address
        Address::updateOrCreate(
            ['user_id' => \Auth::user()->id],
            $request->only('street', 'zip', 'city', 'phone')
        );

        $package = Package::find($request->package_id);
        $course = $package->course;
        $course_packages = $course->packages->pluck('id')->toArray();
        $courseTaken = \Auth::user()->coursesTaken()->where('user_id', \Auth::user()->id)
            ->whereIn('package_id', $course_packages)->first();
        // check if the user is already on the course
        if ($courseTaken) {
            $course_link = route('learner.course.show', $courseTaken->id);

            return [
                'course_link' => $course_link,
            ];
        }

        if ($request->is_pay_later) {
            return $this->processPayLaterOrder($request);
        }

        return $this->generateSveaCheckout($request);
    }

    public function processPayLaterOrder(Request $request)
    {
        $package = Package::find($request->package_id);
        $course = $package->course;
        $calculatedPrice = $this->calculatePrice($course, $package, $request);

        // check if upgrade course
        if ($request->has('order_type') && $request->order_type === 6) {
            $calculatedPrice = $request->price;
        }

        $discount = $request->price - $calculatedPrice;
        $request->merge(['discount' => $discount]);

        $orderRecord = $this->createOrder($request);

        return [
            'redirect_url' => url('/thankyou?pl_ord='.$orderRecord->id),
        ];
    }

    /**
     * Generate checkout from Svea
     */
    public function generateSveaCheckout(Request $request)
    {
        $package = Package::find($request->package_id);
        $course = $package->course;
        $calculatedPrice = $this->calculatePrice($course, $package, $request);

        // check if upgrade course
        if ($request->has('order_type') && $request->order_type === 6) {
            $calculatedPrice = $request->price;
        }

        $discount = $request->price - $calculatedPrice;
        $request->merge(['discount' => $discount]);

        $orderRecord = $this->createOrder($request);

        Log::info('inside generate SVEA checkout');
        $checkoutMerchantId = config('services.svea.checkoutid');
        $checkoutSecret = config('services.svea.checkout_secret');

        // set endpoint url. Eg. test or prod
        $baseUrl = \Svea\Checkout\Transport\Connector::PROD_BASE_URL;

        try {
            /**
             * Create Connector object
             *
             * Exception \Svea\Checkout\Exception\SveaConnectorException will be returned if
             * some of fields $merchantId, $sharedSecret and $baseUrl is missing
             *
             *
             * Create Order
             *
             * Possible Exceptions are:
             * \Svea\Checkout\Exception\SveaInputValidationException - if $orderId is missing
             * \Svea\Checkout\Exception\SveaApiException - is there is some problem with api connection or
             *      some error occurred with data validation on API side
             * \Exception - for any other error
             */
            $conn = \Svea\Checkout\Transport\Connector::init($checkoutMerchantId, $checkoutSecret, $baseUrl);
            $checkoutClient = new \Svea\Checkout\CheckoutClient($conn);

            /**
             * create order
             */
            $data = [
                'countryCode' => config('services.svea.country_code'),
                'currency' => config('services.svea.currency'),
                'locale' => config('services.svea.locale'),
                'clientOrderNumber' => config('services.svea.identifier').$orderRecord->id, // rand(10000,30000000),
                'merchantData' => $course->title.' order',
                'cart' => [
                    'items' => [
                        [
                            'name' => \Illuminate\Support\Str::limit($course->title, 35),
                            'quantity' => 100,
                            'unitPrice' => $calculatedPrice * 100,
                            'unit' => 'pc',
                        ],
                    ],
                ],
                'presetValues' => [
                    [
                        'typeName' => 'emailAddress',
                        'value' => $request->email,
                        'isReadonly' => false,
                    ],
                    [
                        'typeName' => 'postalCode',
                        'value' => $request->zip,
                        'isReadonly' => false,
                    ],
                    [
                        'typeName' => 'PhoneNumber',
                        'value' => $request->phone,
                        'isReadonly' => false,
                    ],
                ],
                'merchantSettings' => [
                    'termsUri' => url('/terms/course-terms'),
                    'checkoutUri' => url('/course/'.$course->id.'/checkout?t=1'), // load checkout
                    'confirmationUri' => url('/thankyou?svea_ord='.$orderRecord->id),
                    'pushUri' => url('/svea-callback?svea_order_id={checkout.order.uri}'),
                    // "https://localhost:51925/push.php?svea_order_id={checkout.order.uri}",
                ],
            ];

            $response = $checkoutClient->create($data);
            $orderId = $response['OrderId'];
            $guiSnippet = $response['Gui']['Snippet'];
            $orderStatus = $response['Status'];
            $orderRecord->svea_order_id = $orderId;
            $orderRecord->save(); // update the checkout and save the order id from svea

            return $guiSnippet;

        } catch (\Svea\Checkout\Exception\SveaApiException $ex) {
            return response()->json($ex->getMessage(), 400);
        } catch (\Svea\Checkout\Exception\SveaConnectorException $ex) {
            return response()->json($ex->getMessage(), 400);
        } catch (\Svea\Checkout\Exception\SveaInputValidationException $ex) {
            return response()->json($ex->getMessage(), 400);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), 400);
        }
    }

    /**
     * Calculate the price with the coupon
     */
    public function calculatePrice($course, $package, Request $request): int
    {
        $hasPaidCourse = false;

        if (Auth::user()) {
            foreach (\Auth::user()->coursesTakenNotOld as $courseTaken) {
                if ($courseTaken->package->course->type != 'Free' && $courseTaken->is_active) {
                    if ($courseTaken->package->course->is_free != 1) {
                        $hasPaidCourse = true;
                    }
                    break;
                }
            }
        }

        $today = \Carbon\Carbon::today()->format('Y-m-d');
        $fromFull = \Carbon\Carbon::parse($package->full_payment_sale_price_from)->format('Y-m-d');
        $toFull = \Carbon\Carbon::parse($package->full_payment_sale_price_to)->format('Y-m-d');
        $isBetweenFull = (($today >= $fromFull) && ($today <= $toFull)) ? 1 : 0;

        $fromFullOther = \Carbon\Carbon::parse($package->full_payment_other_sale_price_from)->format('Y-m-d');
        $toFullOther = \Carbon\Carbon::parse($package->full_payment_other_sale_price_to)->format('Y-m-d');
        $isBetweenFullOther = (($today >= $fromFullOther) && ($today <= $toFullOther)) ? 1 : 0;

        $price = $package->full_payment_price;
        if ($isBetweenFullOther && $package->full_payment_other_sale_price) {
            $price = (int) $package->full_payment_other_sale_price;
        }

        if ($isBetweenFull && $package->full_payment_sale_price) {
            $price = (int) $package->full_payment_sale_price;
        }
        /* $price = $isBetweenFull && $package->full_payment_sale_price
            ? (int)$package->full_payment_sale_price
            : (int)$package->full_payment_price; */

        // check if the user has a paid course
        if ($hasPaidCourse && $package->has_student_discount) {
            $studentDiscount = 500;
            if ($course->type === 'Group') {
                $studentDiscount = 1000;
            }

            $price = $price - $studentDiscount;
        }

        // check if coupon is submitted
        if ($request->coupon) {
            $discountCoupon = CourseDiscount::where('coupon', $request->coupon)
                ->where('course_id', $course->id)->first();

            // check if coupon belongs to the course
            if ($discountCoupon) {
                if ($discountCoupon->valid_to) {
                    $valid_from = Carbon::parse($discountCoupon->valid_from)->format('Y-m-d');
                    $valid_to = Carbon::parse($discountCoupon->valid_to)->format('Y-m-d');
                    $today = Carbon::today()->format('Y-m-d');

                    if (($today >= $valid_from) && ($today <= $valid_to)) {
                        // echo "valid date <br/>";
                    } else {
                        return $price;
                    }
                }

                $discount = ((int) $discountCoupon->discount);

                /* $packageDiscount = ($isBetweenFull && $package->full_payment_sale_price)
                ? (int)$package->full_payment_price - (int)$package->full_payment_sale_price : 0; */
                $packageDiscount = 0;
                if ($isBetweenFullOther && $package->full_payment_other_sale_price) {
                    $packageDiscount = (int) $package->full_payment_price - (int) $package->full_payment_other_sale_price;
                }

                if ($isBetweenFull && $package->full_payment_sale_price) {
                    $packageDiscount = (int) $package->full_payment_price - (int) $package->full_payment_sale_price;
                }

                if ($discountCoupon->type === 1) {
                    $price -= $discount - $packageDiscount;
                }

                if ($discountCoupon->type === 0) {
                    $price -= $discount;
                }
            }

        }

        return $price;
    }

    /**
     * Create order record
     *
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public function createOrder(Request $request)
    {
        $plan_id = $request->payment_plan_id;
        if ($request->campaign_months > 1) {
            $plan = PaymentPlan::where('division', $request->campaign_months)->first();
            $plan_id = $plan ? $plan->id : 3; // if no plan found then just use full payment
        }

        $sveaPrice = $request->campaign_initial_fee + ($request->campaign_admin_fee * $request->campaign_months);
        $totalPrice = $request->price + $sveaPrice;

        $orderType = Order::COURSE_TYPE;
        $discount = $request->discount;
        if ($request->has('order_type')) {
            $orderType = $request->order_type;

            if ($orderType === 6) {
                $discount = 0;
            }
        }

        $package = Package::find($request->package_id);
        $newOrder['user_id'] = \Auth::user()->id;
        $newOrder['item_id'] = $package->course_id;
        $newOrder['type'] = $orderType;
        $newOrder['package_id'] = $package->id;
        $newOrder['plan_id'] = $plan_id;
        $newOrder['price'] = $totalPrice;
        $newOrder['discount'] = $discount;
        $newOrder['payment_mode_id'] = $request->payment_mode_id;
        $newOrder['is_processed'] = 0;
        $newOrder['is_pay_later'] = $request->is_pay_later ?? 0;

        $order = Order::create($newOrder);

        if ($orderType === 6) {
            $order->upgrade()->create([
                'parent' => $request->parent,
                'parent_id' => $request->parent_id,
            ]);
        }

        return $order;
    }

    /**
     * Add the course taken to learner including the shop-manuscript or included courses if there's any
     * The function is get from Frontend\ShopController\place_order()
     */
    public function addCourseToLearner($user_id, $package_id, $start_course = false): Model
    {
        Log::info('inside addCourseToLearner user = '.$user_id.', package_id = '.$package_id);
        $course_status = 1;
        $package = Package::find($package_id);
        $course = $package->course;

        $start_date = $course->type === 'Group' ? $package->course->start_date : Carbon::today();

        $courseTaken = CoursesTaken::firstOrNew(['user_id' => $user_id, 'package_id' => $package_id]);
        if ($start_course) {
            $courseTaken->started_at = $start_date;
        }
        $courseTaken->is_active = $course_status;
        $courseTaken->is_welcome_email_sent = 0;
        $courseTaken->is_free = $course->is_free;

        if ($course->is_free) {
            $started_at = now();
            $dayCount = $course->free_for_days == 0 ? 30 : $course->free_for_days;
            $end_date = Carbon::today()->addDays($dayCount)->format('Y-m-d');
            $courseTaken->started_at = $started_at;
            $courseTaken->end_date = $end_date;
        } else {
            $courseTaken->end_date = Carbon::parse($start_date)->addYear();
        }

        $courseTaken->save();

        // Check for shop manuscripts
        if ($package->shop_manuscripts->count() > 0) {
            foreach ($package->shop_manuscripts as $shop_manuscript) {
                $shopManuscriptTaken = new ShopManuscriptsTaken;
                $shopManuscriptTaken->user_id = $user_id;
                $shopManuscriptTaken->shop_manuscript_id = $shop_manuscript->shop_manuscript_id;
                $shopManuscriptTaken->is_active = false;
                $shopManuscriptTaken->package_shop_manuscripts_id = $package->shop_manuscripts[0]->id;
                $shopManuscriptTaken->save();
            }
        }

        $add_to_automation = 0;
        $user = $this->user->find($user_id);

        if ($package->included_courses->count() > 0) {
            foreach ($package->included_courses as $included_course) {
                if ($included_course->included_package_id == 29) { // check if webinar-pakke is included
                    $add_to_automation++;
                }

                // add user to the included course
                $courseIncluded = CoursesTaken::firstOrNew([
                    'user_id' => $user_id,
                    'package_id' => $included_course->included_package_id,
                ]);
                $courseIncluded->is_active = $course_status;
                $courseIncluded->save();
            }

            // this means webinar-pakke is included
            if ($add_to_automation) {
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

        // add user to automation
        if ($add_to_automation > 0) {
            Log::info('inside addCourseToLearner add_to_automation.');
            $user_email = $user->email;
            $automation_id = 73;
            $user_name = $user->first_name;

            AdminHelpers::addToAutomation($user_email, $automation_id, $user_name);
        }

        // check if the course has activecampaign list then add the user
        if ($package->course->auto_list_id > 0) {
            Log::info('inside addCourseToLearner checking of auto_list_id.');
            $list_id = $package->course->auto_list_id;
            $listData = [
                'email' => $user->email,
                'name' => $user->first_name,
                'last_name' => $user->last_name,
            ];

            event(new AddToCampaignList($list_id, $listData));
        }

        /* use this instead of the AddToCampaignList if zagomail would be used
        if ($package->course->auto_list_id) {
            $list_id = $package->course->auto_list_id;
            $listData = [
                'email' => $user->email,
                'fname' => $user->first_name,
                'lname' => $user->last_name
            ];

            dispatch(new AddToListJob($list_id, $listData));
        } */
        Log::info('inside addCourseToLearner after all of the saving.');

        return $courseTaken;

    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
    public function upgradeCourseTaken($order)
    {

        $orderUpgrade = $order->upgrade;
        $courseTaken = CoursesTaken::find($orderUpgrade->parent_id);

        $package = Package::findOrFail($order->package_id);
        $courseTaken->package_id = $package->id;
        $courseTaken->save();

        $add_to_automation = 0;

        // Check for shop manuscripts
        if ($package->shop_manuscripts->count() > 0) {
            foreach ($package->shop_manuscripts as $shop_manuscript) {
                $shopManuscriptTaken = ShopManuscriptsTaken::firstOrNew(['user_id' => $order->user_id, 'shop_manuscript_id' => $shop_manuscript->shop_manuscript_id]);
                $shopManuscriptTaken->user_id = $order->user_id;
                $shopManuscriptTaken->shop_manuscript_id = $shop_manuscript->shop_manuscript_id;
                $shopManuscriptTaken->is_active = false;
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
            $user = User::find($order->user_id);
            $user_email = $user->email;
            $automation_id = 73;
            $user_name = $user->first_name;

            AdminHelpers::addToAutomation($user_email, $automation_id, $user_name);
        }

        return $courseTaken;
    }

    /**
     * Send Email to admin
     */
    public function notifyAdmin($user_id, $package_id)
    {
        $user = $this->user->find($user_id);
        $package = Package::find($package_id);

        $to = 'support@forfatterskolen.no';
        $from = 'post@forfatterskolen.no';
        $subject = 'New Course Order';
        $message = $user->first_name.
            ' has ordered the course '.$package->course->title;

        AdminHelpers::queue_mail($to, $subject, $message, $from);
    }

    /**
     * Send email to user
     */
    public function notifyUser($user_id, $package_id, $courseTaken, $hasRegretForm = true, $isEmailOut = false)
    {
        $user = $this->user->find($user_id);
        $package = Package::find($package_id);

        $user_email = $user->email;

        $password = $user->need_pass_update ? 'Z5C5E5M2jv' : 'Skjult (kan endres inne i portalen eller via glemt passord)';

        $encode_email = encrypt($user_email);
        $redirectLink = encrypt(route('learner.course'));
        $actionUrl = route('auth.login.emailRedirect', [$encode_email, $redirectLink]);
        $actionText = 'Mine Kurs';
        $attachments = [];

        if ($isEmailOut) {
            $welcomEmail = $this->getEmailOutWelcomeEmail($package->course_id, $encode_email, $user);
            $email_content = $welcomEmail['message'];

            if (isset($welcomEmail['attachment']) && $welcomEmail['attachment']) {
                $publicPath = $welcomEmail['attachment']; // since this is stored in storage folder via symlink
                // Convert to real path
                $relativePath = str_replace('/storage/', '', $publicPath);
                $fullPath = storage_path('app/public/' . $relativePath);
                $attachments[] = $fullPath;
            }
        } else {
            $search_string = [
                '[username]', '[password]',
            ];
            $replace_string = [
                $user->email, $password,
            ];
            $email_content = str_replace($search_string, $replace_string, $package->course->email);
        }

        if ($hasRegretForm) {
            $attachments[] = public_path($this->generateDocx($user->id, $package->id));
            $attachments[] = public_path('/email-attachments/skjema-for-opplysninger-om-angrerett.docx');
        }

        if ($isEmailOut) {
            dispatch(new AddMailToQueueJob($user_email, $package->course->title, $email_content,
                'postmail@forfatterskolen.no', 'Forfatterskolen', $attachments,
                'courses-taken-order', $courseTaken->id));
        } else {
            dispatch(new CourseOrderJob($user_email, $package->course->title, $email_content,
                'postmail@forfatterskolen.no', 'Forfatterskolen', $attachments, 'courses-taken-order',
                $courseTaken->id, $actionText, $actionUrl, $user, $package->id));
        }
    }

    public function notifyUserForUpgrade($order, $courseTaken)
    {
        $package = $order->package;
        $user = User::find($order->user_id);
        $user_email = $user->email;
        $email_content = $package->course->email;
        $actionText = 'Mine Kurs';
        $actionUrl = 'http://www.forfatterskolen.no/account/course';

        dispatch(new CourseOrderJob($user_email, $package->course->title, $email_content,
            'postmail@forfatterskolen.no', 'Forfatterskolen', null, 'courses-taken-upgrade',
            $courseTaken->id, $actionText, $actionUrl, $user, $package->id));
    }

    public function getEmailOutWelcomeEmail($course_id, $encode_email, $user)
    {
        $emailOut = EmailOut::where('course_id', $course_id)->where('send_immediately', 1)->first();
        if ($emailOut) {
            $extractLink = FrontendHelpers::getTextBetween($emailOut->message, '[redirect]', '[/redirect]');
            $formatRedirectLink = route('auth.login.emailRedirect', [$encode_email, encrypt($extractLink)]);
            $redirectLabel = FrontendHelpers::getTextBetween($emailOut->message, '[redirect_label]', '[/redirect_label]');
            $redirectLink = "<a href='".$formatRedirectLink."'>".$redirectLabel.'</a>';

            $password = $user->need_pass_update ? 'Z5C5E5M2jv' : 'Skjult (kan endres inne i portalen eller via glemt passord)';

            $search_string = [
                '[username]', '[password]', '[redirect]'.$extractLink.'[/redirect]', '[redirect_label]'.$redirectLabel.'[/redirect_label]',
            ];
            $replace_string = [
                $user->email, $password, $redirectLink, '',
            ];
            $message = str_replace($search_string, $replace_string, $emailOut->message);

            $emailOut->recipients()->updateOrCreate([
                'user_id' => $user->id,
            ]);

            return [
                'message' => $message,
                'attachment' => $emailOut->attachment
            ];
        }

        $course = Course::find($course_id);
        
        return [
            'message' => $course->email
        ];
    }

    public function createInvoiceFromOder($order)
    {
        $user = $order->user;
        $package = $order->package;
        $product_ID = $package->full_price_product;

        $dueDate = date('Y-m-d');
        if ($package->issue_date && Carbon::parse($package->issue_date)->gt(Carbon::today())) {
            $dueDate = $package->issue_date;
        }
        $dueDate = Carbon::parse($dueDate);
        $dueDate->addDays($package->full_price_due_date);
        $dueDate = $dueDate->format('Y-m-d');

        $price = $order->price - $order->discount;

        $comment = '(Kurs: '.$package->course->title.' ['.$package->variation.'], ';
        $comment .= 'Betalingsmodus: Vipps, ';
        $comment .= 'Betalingsplan: Hele beløpet)';

        $invoice_fields = [
            'user_id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'netAmount' => $price * 100,
            'dueDate' => $dueDate,
            'description' => 'Kursordrefaktura',
            'productID' => $product_ID,
            'email' => $user->email,
            'telephone' => $user->address->phone,
            'address' => $user->address->street,
            'postalPlace' => $user->address->city,
            'postalCode' => $user->address->zip,
            'comment' => $comment,
            'payment_mode' => 'Vipps',
        ];

        $invoice = new FikenInvoice;
        $invoice->create_invoice($invoice_fields);
    }

    /**
     * Generate docx attached to the email with user and order info
     */
    public function generateDocx($user_id, $package_id): string
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
        ])->addText('Forfatterskolen, Lihagen 21, 3029 DRAMMEN', [
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
