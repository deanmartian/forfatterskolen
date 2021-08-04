<?php

namespace App\Services;

use App\Address;
use App\CourseDiscount;
use App\GiftPurchase;
use App\Helpers\SveaConfig;
use App\Http\AdminHelpers;
use App\Http\FrontendHelpers;
use App\Order;
use App\Package;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GiftService {

    public function processCheckout( Request $request, $type = 'course' )
    {
        // update address
        Address::updateOrCreate(
            ['user_id' => \Auth::user()->id],
            $request->only('street', 'zip', 'city', 'phone')
        );

        return $this->sveaCheckout($request, $type);
    }

    /**
     * @param Request $request
     * @param string $type
     * @return \Illuminate\Http\JsonResponse
     */
    public function sveaCheckout( Request $request, $type = 'course' )
    {
        $discountedPrice = floatval($request->price);
        $merchantDataTitle = '';
        $checkoutUri = '';
        $confirmationUri = '';

        if ($type === 'course') {
            $package = Package::find($request->package_id);
            $course =  $package->course;
            $discountedPrice = $this->calculateCourseDiscountedPrice($course, $package, $request);
            $merchantDataTitle = $course->title;
            $checkoutUri = '/gift/course/' . $course->id . '/checkout';
            $confirmationUri = '/gift/course/' . $course->id;
        }

        $discount = $request->price - $discountedPrice;
        $request->merge(['discount' => $discount]);
        $orderRecord = $this->createOrder($request, $type);
        $checkoutMerchantId = config('services.svea.checkoutid_test');
        $checkoutSecret = config('services.svea.checkout_secret_test');

        //set endpoint url. Eg. test or prod
        $baseUrl = \Svea\Checkout\Transport\Connector::TEST_BASE_URL;

        $connector = \Svea\Checkout\Transport\Connector::init($checkoutMerchantId, $checkoutSecret, $baseUrl);
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
            $data = array(
                "countryCode" => config('services.svea.country_code'),
                "currency" => config('services.svea.currency'),
                "locale" => config('services.svea.locale'),
                "clientOrderNumber" => config('services.svea.identifier').$orderRecord->id,//rand(10000,30000000),
                "merchantData" => $merchantDataTitle." order",
                "cart" => array(
                    "items" => array(
                        array(
                            "name" => str_limit($merchantDataTitle, 35),
                            "quantity" => 100,
                            "unitPrice" => $discountedPrice*100,
                            "unit" => "pc"
                        )
                    )
                ),
                "presetValues" => array(
                    array(
                        "typeName" => "emailAddress",
                        "value" => $request->email,
                        "isReadonly" => false
                    ),
                    array(
                        "typeName" => "postalCode",
                        "value" => $request->zip,
                        "isReadonly" => false
                    ),
                    array(
                        "typeName" => "PhoneNumber",
                        "value" => $request->phone,
                        "isReadonly" => false
                    )
                ),
                "merchantSettings" => array(
                    "termsUri" => url('/terms'),
                    "checkoutUri" => url($checkoutUri), // load checkout
                    "confirmationUri" => url($confirmationUri . '/thankyou?svea_ord='.$orderRecord->id),
                    "pushUri" => url('/svea-callback?svea_order_id={checkout.order.uri}')
                )
            );

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
     *
     * @param $course
     * @param $package
     * @param Request $request
     * @return int
     */
    public function calculateCourseDiscountedPrice($course, $package, Request $request)
    {

        $hasPaidCourse = false;

        foreach( \Auth::user()->coursesTakenNotOld as $courseTaken ) {
            if( $courseTaken->package->course->type != "Free" && $courseTaken->is_active ) :
                if ($courseTaken->package->course->is_free != 1) {
                    $hasPaidCourse = true;
                }
                break;
            endif;
        }

        $today 			= \Carbon\Carbon::today()->format('Y-m-d');
        $fromFull 		= \Carbon\Carbon::parse($package->full_payment_sale_price_from)->format('Y-m-d');
        $toFull 		= \Carbon\Carbon::parse($package->full_payment_sale_price_to)->format('Y-m-d');
        $isBetweenFull 	= (($today >= $fromFull) && ($today <= $toFull)) ? 1 : 0;

        $fromMonths3 			= \Carbon\Carbon::parse($package->months_3_sale_price_from)->format('Y-m-d');
        $toMonths3 			= \Carbon\Carbon::parse($package->months_3_sale_price_to)->format('Y-m-d');
        $isBetweenMonths3 	= (($today >= $fromMonths3) && ($today <= $toMonths3)) ? 1 : 0;

        $fromMonths6 			= \Carbon\Carbon::parse($package->months_6_sale_price_from)->format('Y-m-d');
        $toMonths6 			= \Carbon\Carbon::parse($package->months_6_sale_price_to)->format('Y-m-d');
        $isBetweenMonths6 	= (($today >= $fromMonths6) && ($today <= $toMonths6)) ? 1 : 0;

        // added 12th month
        $fromMonths12 			= \Carbon\Carbon::parse($package->months_12_sale_price_from)->format('Y-m-d');
        $toMonths12 			= \Carbon\Carbon::parse($package->months_12_sale_price_to)->format('Y-m-d');
        $isBetweenMonths12 	= (($today >= $fromMonths12) && ($today <= $toMonths12)) ? 1 : 0;

        switch ($request->payment_plan_id) {
            case 1:
                $price = $isBetweenMonths3 && $package->months_3_sale_price
                    ? (int)$package->months_3_sale_price
                    : (int)$package->months_3_price;
                break;

            case 2:
                $price = $isBetweenMonths6 && $package->months_6_sale_price
                    ? (int)$package->months_6_sale_price
                    : (int)$package->months_6_price;
                break;

            case 4:
                $price = $isBetweenMonths12 && $package->months_12_sale_price
                    ? (int)$package->months_12_sale_price
                    : (int)$package->months_12_price;
                break;

            default:
                $price = $isBetweenFull && $package->full_payment_sale_price
                    ? (int)$package->full_payment_sale_price
                    : (int)$package->full_payment_price;
                break;
        }

        // check if the user has a paid course and the selected package have student discount
        if ($hasPaidCourse && $package->has_student_discount) {
            $price = $price - $course->student_discount;
        }

        if ($request->coupon) {
            $discountCoupon = CourseDiscount::where('coupon', $request->coupon)->where('course_id', $course->id)->first();

            if ($discountCoupon) {
                if ($discountCoupon->valid_to) {
                    $valid_from = Carbon::parse($discountCoupon->valid_from)->format('Y-m-d');
                    $valid_to   = Carbon::parse($discountCoupon->valid_to)->format('Y-m-d');
                    $today      = Carbon::today()->format('Y-m-d');

                    if ( ($today >= $valid_from) && ($today <= $valid_to)) {
                        //echo "valid date <br/>";
                    } else {
                        return $price;
                    }
                }

                $discount = ( (int) $discountCoupon->discount);
                $price = $price - ( (int)$discount );
            }

        }

        return $price;
    }

    /**
     * @param Request $request
     * @param string $parent
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public function createOrder( Request $request, $parent = 'course' )
    {
        $plan_id = $request->payment_plan_id;
        $totalPrice = $request->price;

        $item_id = 0;
        $type = '';
        $package_id = 0;
        if ($parent === 'course') {
            $package = Package::find($request->package_id);
            $item_id = $package->course_id;
            $type = Order::COURSE_TYPE;
            $package_id = $package->id;
        }

        $newOrder['user_id']    = \Auth::user()->id;
        $newOrder['item_id']    = $item_id;
        $newOrder['type']       = $type;
        $newOrder['package_id'] = $package_id;
        $newOrder['plan_id']    = $plan_id;
        $newOrder['price']      = $totalPrice;
        $newOrder['discount']   = $request->discount;
        $newOrder['payment_mode_id']   = $request->payment_mode_id;
        $newOrder['is_processed'] = 0;
        $newOrder['is_gift'] = 1;

        return Order::create($newOrder);
    }

    public function addGiftPurchase( $user_id, $parent, $parent_id )
    {

        do {
            $redeemCode = FrontendHelpers::generateUniqueCode(8);
        } while (GiftPurchase::where('redeem_code', $redeemCode)->first());

       return GiftPurchase::create([
            'user_id' => $user_id,
            'parent' => $parent,
            'parent_id' => $parent_id,
            'redeem_code' => $redeemCode
        ]);

    }

    public function notifyGiftBuyer( $giftPurchase )
    {
        $user = $giftPurchase->user;
        $user_email = $user->email;

        $emailTemplate = AdminHelpers::emailTemplate('Gift Purchase');
        $emailContent = str_replace([
            ':redeem_code'
        ], [
            $giftPurchase->redeem_code
        ], $emailTemplate->email_content);
        return $emailContent;
    }

}