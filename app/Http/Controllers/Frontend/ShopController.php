<?php
namespace App\Http\Controllers\Frontend;

use App\CourseDiscount;
use App\CourseShared;
use App\CourseSharedUser;
use App\Helpers\ApiException;
use App\Helpers\ApiResponse;
use App\Http\AdminHelpers;
use App\Mail\SubjectBodyEmail;
use App\Paypal;
use App\Repositories\VippsRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\OrderCreateRequest;
use App\Http\FrontendHelpers;
use App\Http\FikenInvoice;
use App\Package;
use App\Address;
use App\PaymentMode;
use App\PaymentPlan;
use App\Transaction;
use App\Invoice;
use App\Course;
use App\User;
use App\CoursesTaken;
use App\ShopManuscriptsTaken;
use App\WorkshopsTaken;
require app_path('/Http/PaypalIPN/PaypalIPN.php');
use PaypalIPN;
use Carbon\Carbon;

class ShopController extends Controller
{


    public function checkout($course_id)
    {
        $course = Course::findOrFail($course_id);
        if( !Auth::guest() ) :
            $course_packages = $course->packages->pluck('id')->toArray(); 
            $courseTaken = CoursesTaken::where('user_id', Auth::user()->id)->whereIn('package_id', $course_packages)->first();
            if($courseTaken) return redirect(route('learner.course.show', ['id' => $courseTaken->id]));
        endif;

        if ($course->hide_price) {
            return redirect()->route('front.course.show', $course->id);
        }

    	return view('frontend.shop.checkout', compact('course'));
    }

    public function checkoutTest($course_id)
    {
        $course = Course::findOrFail($course_id);
        if( !Auth::guest() ) :
            $course_packages = $course->packages->pluck('id')->toArray();
            $courseTaken = CoursesTaken::where('user_id', Auth::user()->id)->whereIn('package_id', $course_packages)->first();
            if($courseTaken) return redirect(route('learner.course.show', ['id' => $courseTaken->id]));
        endif;

        return view('frontend.shop.checkout-test', compact('course'));
    }

    /**
     * Checkout for the shared course
     * @param $share_hash
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function shareCourseCheckout($share_hash, Request $request)
    {
        $courseShare = CourseShared::where('hash', '=', $share_hash)->first();
        if (!$courseShare) {
            return redirect()->route('front.course.index');
        }
        $course = $courseShare->course;
        $package = $courseShare->package;

        if ($request->isMethod('post')) {
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

            $alreadyAvailCourse = CourseSharedUser::where(['user_id' => Auth::user()->id, 'course_shared_id' => $courseShare->id])->first();
            if ($alreadyAvailCourse) {
                return redirect(route('learner.course'));
            }

            //$courseTaken = CoursesTaken::firstOrNew(['user_id' => Auth::user()->id, 'package_id' => $package->id]);
            $courseTaken = new CoursesTaken();
            $courseTaken->user_id = Auth::user()->id;
            $courseTaken->package_id = $package->id;
            $courseTaken->is_active = 1;
            $courseTaken->is_free = 1;
            $courseTaken->save();

            $courseSharedUser['user_id'] = Auth::user()->id;
            $courseSharedUser['course_shared_id'] = $courseShare->id;
            CourseSharedUser::create($courseSharedUser);

            // Check for shop manuscripts
            if( $package->shop_manuscripts->count() > 0 ) :
                foreach( $package->shop_manuscripts as $shop_manuscript ) :
                    //$shopManuscriptTaken = ShopManuscriptsTaken::firstOrNew(['user_id' => Auth::user()->id, 'shop_manuscript_id' => $shop_manuscript->shop_manuscript_id]);
                    $shopManuscriptTaken = new ShopManuscriptsTaken();
                    $shopManuscriptTaken->user_id = Auth::user()->id;
                    $shopManuscriptTaken->shop_manuscript_id = $shop_manuscript->shop_manuscript_id;
                    $shopManuscriptTaken->is_active = false;
                    $shopManuscriptTaken->package_shop_manuscripts_id = $package->shop_manuscripts[0]->id;
                    $shopManuscriptTaken->save();
                endforeach;
            endif;

            $add_to_automation = 0;
            if ($package->included_courses->count() > 0) {
                foreach ($package->included_courses as $included_course) {
                    if ($included_course->included_package_id == 29) { // check if webinar-pakke is included
                        $add_to_automation++;
                    }
                }
            }

            if ($package->course->id == 17) { //check if webinar-pakke
                $add_to_automation++;
            }

            if ($add_to_automation > 0) {
                $user_email = Auth::user()->email;
                $automation_id = 73;
                $user_name = Auth::user()->first_name;

                AdminHelpers::addToAutomation($user_email,$automation_id,$user_name);
            }

            // check if the course has activecampaign list then add the user
            if ($package->course->auto_list_id > 0) {
                $list_id = $package->course->auto_list_id;
                $listData = [
                    'email' => Auth::user()->email,
                    'name' => Auth::user()->first_name,
                    'last_name' => Auth::user()->last_name
                ];

                AdminHelpers::addToActiveCampaignList($list_id, $listData);
            }

            return redirect(route('front.shop.thankyou'));
        }
        return view('frontend.shop.checkout-share', compact('course', 'package'));
    }

    /**
     * Apply discount page
     * @param $course_id
     * @param $coupon
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function applyDiscount($course_id, $coupon)
    {
        $course = Course::find($course_id);
        if (!$course) {
            return redirect()->route('front.course.index');
        }

        $discountData = $course->discounts()->where('coupon', '=', $coupon)->first();

        if (!$discountData) {
            return view('frontend.shop.applied-discount', compact('course', 'coupon', 'discountData'))
                ->with([
                    'errors' => AdminHelpers::createMessageBag('Invalid coupon code.')
                ]);
            //return redirect()->route('front.course.checkout', $course_id);
        }

        if ($discountData->valid_to) {
            $valid_from = Carbon::parse($discountData->valid_from)->format('Y-m-d');
            $valid_to   = Carbon::parse($discountData->valid_to)->format('Y-m-d');
            $today      = Carbon::today()->format('Y-m-d');

            if ( ($today >= $valid_from) && ($today <= $valid_to)) {
                //echo "valid date <br/>";
            } else {
                return view('frontend.shop.applied-discount', compact('course', 'coupon', 'discountData'))
                    ->with([
                    'errors' => AdminHelpers::createMessageBag('Rabattkupongen er ugyldig eller utløpt.')
                ]);
            }
        }

        if( !Auth::guest() ) :
            $course_packages = $course->packages->pluck('id')->toArray();
            $courseTaken = CoursesTaken::where('user_id', Auth::user()->id)->whereIn('package_id', $course_packages)->first();
            if($courseTaken) return redirect(route('learner.course.show', ['id' => $courseTaken->id]));
        endif;

        return view('frontend.shop.applied-discount', compact('course', 'coupon', 'discountData'));
    }

    /**
     * Check the discount for the course
     * @param $course_id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkDiscount($course_id, Request $request)
    {

        if (Auth::guest()) {
            if ($request->coupon) {
                $discountCoupon = CourseDiscount::where('course_id', $course_id)->where('coupon',
                    $request->coupon)->first();

                $applyDiscount = $discountCoupon->discount;
                $formattedDiscount = number_format($applyDiscount, 2,',','.');
                return response()->json(['discount' => $applyDiscount, 'discount_text' => 'Kr '.$formattedDiscount]);
            }
        }

        $hasPaidCourse = false;
        foreach( Auth::user()->coursesTaken as $courseTaken ) :
            if( $courseTaken->package->course->type != "Free" && $courseTaken->is_active ) :
                $hasPaidCourse = true;
                break;
            endif;
        endforeach;

        $package = Package::findOrFail($request->package_id);
        $price = 0;
        if( $hasPaidCourse && $package->course->type == 'Group' && $package->has_student_discount) {
            $price = ( (int)1500 );
        }

        if( $hasPaidCourse && $package->course->type == 'Single' && $package->has_student_discount) {
            $price = ( (int)500 );
        }

        if ($request->coupon) {
            $discountCoupon = CourseDiscount::where('course_id', $course_id)->where('coupon', $request->coupon)->first();

            if ($discountCoupon) {
                $convertDiscount = ( (int)$discountCoupon->discount);
                $applyDiscount = $discountCoupon->discount;

                if ($price > $convertDiscount) {
                    $applyDiscount = $price;
                }

                $formattedDiscount = number_format($applyDiscount, 2,',','.');
                return response()->json(['discount' => $applyDiscount, 'discount_text' => 'Kr '.$formattedDiscount]);
            }

        }

        return response()->json('', 404);
    }



    public function place_order($course_id, OrderCreateRequest $request)
    {
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

        // check if webinar-pakke
        if ($course_id == 17) {
            $course = Course::findOrFail($course_id);
            $course_packages = $course->packages->pluck('id')->toArray();
            $courseTaken = CoursesTaken::where('user_id', Auth::user()->id)->whereIn('package_id', $course_packages)->first();
            // check if the user already avails this course
            if($courseTaken) return redirect(route('learner.course.show', ['id' => $courseTaken->id]));
        }

        $hasPaidCourse = false;
        // check if course bought is not expired yet
        foreach( Auth::user()->coursesTakenNotOld as $courseTaken ) :
            if( $courseTaken->package->course->type != "Free" && $courseTaken->is_active ) :
                $hasPaidCourse = true;
                break;
            endif;
        endforeach;

        $paymentMode = PaymentMode::findOrFail($request->payment_mode_id);
        $paymentPlan = PaymentPlan::findOrFail($request->payment_plan_id);
        $package = Package::findOrFail($request->package_id);
        $add_to_automation = 0;

        $monthNumbers = [3 => 'months_3_enable', 6 => 'months_6_enable', 12 => 'months_12_enable'];
        // check if monthly payment is selected
        if (array_key_exists($paymentPlan->division, $monthNumbers)) {
            foreach($monthNumbers as $month => $field) {
                // check if the payment plan selected is allowed
                if ($month == $paymentPlan->division && $package->$field == 0) {
                    return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Invalid payment plan')]);
                }
            }
        }

        $payment_plan = ( $paymentMode->mode == "Paypal" ) ?  "Hele beløpet" : $paymentPlan->plan;

        // additional checking if the user selects correct payment mode for the selected plan
        // not faktura and payment plan is not full payment or split invoice
        if ($paymentMode->id !== 3 && ($paymentPlan->id != 8 || isset($request->split_invoice))) {
            return redirect()->back()->with(['errors' =>
                AdminHelpers::createMessageBag('Invalid payment mode for the selected plan')]);
        } else {
            // payment is faktura and wants to split invoice
            if ($paymentPlan->id == 8 && (isset($request->split_invoice) && $request->split_invoice)) {
                return redirect()->back()->with(['errors' =>
                    AdminHelpers::createMessageBag('Invalid payment mode for the selected plan')]);
            }
        }


        /* check if there's an issue date set ir not then use today*/
        $dueDate = $package->issue_date ?: date("Y-m-d");
        $dueDate = Carbon::parse($dueDate);
        $payment_plan = trim($payment_plan);

        // this is use to check if the current date is within a sale date
        // for the 3 plans/payments
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

        if( $payment_plan == "Hele beløpet" ) :
            $price = $isBetweenFull && $package->full_payment_sale_price
                ? (int)$package->full_payment_sale_price*100
                : (int)$package->full_payment_price*100;
            $product_ID = $package->full_price_product;
            $dueDate->addDays($package->full_price_due_date);
        elseif( $payment_plan == "3 måneder" ) :
            $price = $isBetweenMonths3 && $package->months_3_sale_price
                ? (int)$package->months_3_sale_price*100
                : (int)$package->months_3_price*100;
            $product_ID = $package->months_3_product;
            $dueDate->addDays($package->months_3_due_date);
        elseif( $payment_plan == "6 måneder" ) :
            $price = $isBetweenMonths6 && $package->months_6_sale_price
                ? (int)$package->months_6_sale_price*100
                : (int)$package->months_6_price*100;
            $product_ID = $package->months_6_product;
            $dueDate->addDays($package->months_6_due_date);
        elseif( $payment_plan == "12 måneder" ) :
            $price = $isBetweenMonths12 && $package->months_12_sale_price
                ? (int)$package->months_12_sale_price*100
                : (int)$package->months_12_price*100;
            $product_ID = $package->months_12_product;
            $dueDate->addDays($package->months_12_due_date);
        endif;
        $dueDate = date_format(date_create($dueDate), 'Y-m-d');

        $payment_mode = $paymentMode->mode;
        if( $payment_mode == 'Faktura' ) :
            $payment_mode = 'Bankoverføring';
        endif;

        $comment = '(Kurs: ' . $package->course->title . ' ['.$package->variation.'], ';
        $comment .= 'Betalingsmodus: ' . $payment_mode . ', ';
        $comment .= 'Betalingsplan: ' . $payment_plan . ')';

        $discount = 0;

        if ($request->coupon) {
            $discountCoupon = CourseDiscount::where('coupon', $request->coupon)->where('course_id', $course_id)->first();

            if ($discountCoupon->valid_to) {
                $valid_from = Carbon::parse($discountCoupon->valid_from)->format('Y-m-d');
                $valid_to   = Carbon::parse($discountCoupon->valid_to)->format('Y-m-d');
                $today      = Carbon::today()->format('Y-m-d');

                if ( ($today >= $valid_from) && ($today <= $valid_to)) {
                    //echo "valid date <br/>";
                } else {
                    return redirect()->back()->withInput()->with([
                        'errors' => AdminHelpers::createMessageBag('Rabattkupongen er ugyldig eller utløpt.')
                    ]);
                }
            }

            if ($discountCoupon) {
                $discount = ( (int) $discountCoupon->discount);
                $price = $price - ( (int)$discount*100 );
            }

        }

        if( $hasPaidCourse && $package->course->type == 'Group' && $package->has_student_discount) {
            /* original code
             * $comment .= ' - Discount: Kr 1.500,00';
            $price = $price - ( (int)1500*100 );*/

            $groupDiscount = 1000;

            if ($groupDiscount > $discount) {
                $discount = $groupDiscount;
            }

            $comment .= ' - Discount: Kr '.number_format($discount, 2,',','.');
            $price = $price - ( (int)$discount*100 );
        }

        if( $hasPaidCourse && $package->course->type == 'Single' && $package->has_student_discount) {
            /* original code
             * $comment .= ' - Discount: Kr 500,00';
            $price = $price - ( (int)500*100 );*/

            $singleDiscount = 500;

            if ($singleDiscount > $discount) {
                $discount = $singleDiscount;
            }

            $comment .= ' - Discount: Kr '.number_format($discount, 2,',','.');
            $price = $price - ( (int)$discount*100 );
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
            $division   = $paymentPlan->division * 100; // multiply the split count to get the correct value
            $price      = round($price/$division, 2); // round the value to the nearest tenths
            $price      = (int)$price*100;
            for ($i=1; $i <= $paymentPlan->division; $i++ ) { // loop based on the split count
                /*Carbon::today() - this is the old instead of Carbon parse*/
                $dueDate = $package->issue_date ?: date("Y-m-d");
                $dueDate =  Carbon::parse($dueDate)->addMonth($i)->format('Y-m-d'); // due date on every month on the same day
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
                    'comment' => $comment
                ];

                $invoice = new FikenInvoice();
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
                /*'issueDate' => $package->issue_date*/
            ];

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

        
        $courseTaken = CoursesTaken::firstOrNew(['user_id' => Auth::user()->id, 'package_id' => $package->id]);
        $courseTaken->is_active = 0;
        $courseTaken->save();

    

        // Check for shop manuscripts
        if( $package->shop_manuscripts->count() > 0 ) :
            foreach( $package->shop_manuscripts as $shop_manuscript ) :
            //$shopManuscriptTaken = ShopManuscriptsTaken::firstOrNew(['user_id' => Auth::user()->id, 'shop_manuscript_id' => $shop_manuscript->shop_manuscript_id]);
            $shopManuscriptTaken = new ShopManuscriptsTaken();
            $shopManuscriptTaken->user_id = Auth::user()->id;
            $shopManuscriptTaken->shop_manuscript_id = $shop_manuscript->shop_manuscript_id;
            $shopManuscriptTaken->is_active = false;
            $shopManuscriptTaken->package_shop_manuscripts_id = $package->shop_manuscripts[0]->id;
            $shopManuscriptTaken->save();
            endforeach;
        endif;


        if ($package->included_courses->count() > 0) {
            foreach ($package->included_courses as $included_course) {
                if ($included_course->included_package_id == 29) { // check if webinar-pakke is included
                    $add_to_automation++;
                }
            }
        }

        if ($package->course->id == 17) { //check if webinar-pakke
            $add_to_automation++;
        }

        if ($add_to_automation > 0) {
            $user_email = Auth::user()->email;
            $automation_id = 73;
            $user_name = Auth::user()->first_name;

            AdminHelpers::addToAutomation($user_email,$automation_id,$user_name);
        }

        // check if the course has activecampaign list then add the user
        if ($package->course->auto_list_id > 0) {
            $list_id = $package->course->auto_list_id;
            $listData = [
                'email' => Auth::user()->email,
                'name' => Auth::user()->first_name,
                'last_name' => Auth::user()->last_name
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
        $from       = 'post@forfatterskolen.no';
        $headers1 = "From: Forfatterskolen<".$from.">\r\n";
        $headers1 .= "MIME-Version: 1.0\r\n";
        $headers1 .= "Content-Type: text/html; charset=UTF-8\r\n";
        //mail('support@forfatterskolen.no', 'New Course Order', Auth::user()->first_name . ' has ordered the course ' . $package->course->title, $headers1);
        AdminHelpers::send_email('New Course Order',
            'post@forfatterskolen.no', 'support@forfatterskolen.no', Auth::user()->first_name . ' has ordered the course ' . $package->course->title);


        // Send course email
        $actionText = 'Mine Kurs';
        $actionUrl = route('learner.course');//'http://www.forfatterskolen.no/account/course';
        $headers = "From: Forfatterskolen<post@forfatterskolen.no>\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $user = Auth::user();

        $password = $user->need_pass_update ? 'Z5C5E5M2jv' : 'Skjult (kan endres inne i portalen eller via glemt passord)';

        $search_string = [
            '[username]', '[password]'
        ];
        $replace_string = [
            $courseTaken->user->email, $password
        ];
        $email_content = str_replace($search_string, $replace_string, $package->course->email);

        $user_email = $user->email;
        //mail($user->email, $package->course->title, view('emails.course_order', compact('actionText', 'actionUrl', 'user', 'email_content')), $headers);
        AdminHelpers::send_email($package->course->title,
            'post@forfatterskolen.no', $user_email,
            view('emails.course_order', compact('actionText', 'actionUrl', 'user', 'email_content')));

        if( $paymentMode->mode == "Paypal" ) :
            $paypal = new PayPal;

            $response = $paypal->purchase([
                'amount' => ($price/100),
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
        endif;

        // check if vipps payment mode and the current user id is 4
        if( $paymentMode->mode == "Vipps") :
            $orderId = $invoice->invoice_number;
            $transactionText = $package->course->title;
            $vippsData = [
                'amount' => $price,
                'orderId' => $orderId,
                'transactionText' => $transactionText
            ];
            return $this->vippsInitiatePayment($vippsData);
        endif;

        return redirect(route('front.shop.thankyou'));

    }

    public function place_order_test($course_id, OrderCreateRequest $request)
    {
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

        // check if webinar-pakke
        if ($course_id == 17) {
            $course = Course::findOrFail($course_id);
            $course_packages = $course->packages->pluck('id')->toArray();
            $courseTaken = CoursesTaken::where('user_id', Auth::user()->id)->whereIn('package_id', $course_packages)->first();
            // check if the user already avails this course
            if($courseTaken) return redirect(route('learner.course.show', ['id' => $courseTaken->id]));
        }

        $hasPaidCourse = false;
        // check if course bought is not expired yet
        foreach( Auth::user()->coursesTakenNotOld as $courseTaken ) :
            if( $courseTaken->package->course->type != "Free" && $courseTaken->is_active ) :
                $hasPaidCourse = true;
                break;
            endif;
        endforeach;

        $paymentMode = PaymentMode::findOrFail($request->payment_mode_id);
        $paymentPlan = PaymentPlan::findOrFail($request->payment_plan_id);
        $package = Package::findOrFail($request->package_id);
        $add_to_automation = 0;

        $monthNumbers = [3 => 'months_3_enable', 6 => 'months_6_enable', 12 => 'months_12_enable'];
        // check if monthly payment is selected
        if (array_key_exists($paymentPlan->division, $monthNumbers)) {
            foreach($monthNumbers as $month => $field) {
                // check if the payment plan selected is allowed
                if ($month == $paymentPlan->division && $package->$field == 0) {
                    return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Invalid payment plan')]);
                }
            }
        }

        $payment_plan = ( $paymentMode->mode == "Paypal" ) ?  "Hele beløpet" : $paymentPlan->plan;


        /* check if there's an issue date set ir not then use today*/
        $dueDate = $package->issue_date ?: date("Y-m-d");
        $dueDate = Carbon::parse($dueDate);
        $payment_plan = trim($payment_plan);

        // this is use to check if the current date is within a sale date
        // for the 3 plans/payments
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

        if( $payment_plan == "Hele beløpet" ) :
            $price = $isBetweenFull && $package->full_payment_sale_price
                ? (int)$package->full_payment_sale_price*100
                : (int)$package->full_payment_price*100;
            $product_ID = $package->full_price_product;
            $dueDate->addDays($package->full_price_due_date);
        elseif( $payment_plan == "3 måneder" ) :
            $price = $isBetweenMonths3 && $package->months_3_sale_price
                ? (int)$package->months_3_sale_price*100
                : (int)$package->months_3_price*100;
            $product_ID = $package->months_3_product;
            $dueDate->addDays($package->months_3_due_date);
        elseif( $payment_plan == "6 måneder" ) :
            $price = $isBetweenMonths6 && $package->months_6_sale_price
                ? (int)$package->months_6_sale_price*100
                : (int)$package->months_6_price*100;
            $product_ID = $package->months_6_product;
            $dueDate->addDays($package->months_6_due_date);
        elseif( $payment_plan == "12 måneder" ) :
            $price = $isBetweenMonths12 && $package->months_12_sale_price
                ? (int)$package->months_12_sale_price*100
                : (int)$package->months_12_price*100;
            $product_ID = $package->months_12_product;
            $dueDate->addDays($package->months_12_due_date);
        endif;
        $dueDate = date_format(date_create($dueDate), 'Y-m-d');

        $payment_mode = $paymentMode->mode;
        if( $payment_mode == 'Faktura' ) :
            $payment_mode = 'Bankoverføring';
        endif;

        $comment = '(Kurs: ' . $package->course->title . ' ['.$package->variation.'], ';
        $comment .= 'Betalingsmodus: ' . $payment_mode . ', ';
        $comment .= 'Betalingsplan: ' . $payment_plan . ')';

        $discount = 0;

        if ($request->coupon) {
            $discountCoupon = CourseDiscount::where('coupon', $request->coupon)->where('course_id', $course_id)->first();

            if ($discountCoupon->valid_to) {
                $valid_from = Carbon::parse($discountCoupon->valid_from)->format('Y-m-d');
                $valid_to   = Carbon::parse($discountCoupon->valid_to)->format('Y-m-d');
                $today      = Carbon::today()->format('Y-m-d');

                if ( ($today >= $valid_from) && ($today <= $valid_to)) {
                    //echo "valid date <br/>";
                } else {
                    return redirect()->back()->withInput()->with([
                        'errors' => AdminHelpers::createMessageBag('Rabattkupongen er ugyldig eller utløpt.')
                    ]);
                }
            }

            if ($discountCoupon) {
                $discount = ( (int) $discountCoupon->discount);
                $price = $price - ( (int)$discount*100 );
            }

        }

        if( $hasPaidCourse && $package->course->type == 'Group' && $package->has_student_discount) {
            /* original code
             * $comment .= ' - Discount: Kr 1.500,00';
            $price = $price - ( (int)1500*100 );*/

            $groupDiscount = 1000;

            if ($groupDiscount > $discount) {
                $discount = $groupDiscount;
            }

            $comment .= ' - Discount: Kr '.number_format($discount, 2,',','.');
            $price = $price - ( (int)$discount*100 );
        }

        if( $hasPaidCourse && $package->course->type == 'Single' && $package->has_student_discount) {
            /* original code
             * $comment .= ' - Discount: Kr 500,00';
            $price = $price - ( (int)500*100 );*/

            $singleDiscount = 500;

            if ($singleDiscount > $discount) {
                $discount = $singleDiscount;
            }

            $comment .= ' - Discount: Kr '.number_format($discount, 2,',','.');
            $price = $price - ( (int)$discount*100 );
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
            $division   = $paymentPlan->division * 100; // multiply the split count to get the correct value
            $price      = round($price/$division, 2); // round the value to the nearest tenths
            $price      = (int)$price*100;
            for ($i=1; $i <= $paymentPlan->division; $i++ ) { // loop based on the split count
                /*Carbon::today() - this is the old instead of Carbon parse*/
                $dueDate = $package->issue_date ?: date("Y-m-d");
                $dueDate =  Carbon::parse($dueDate)->addMonth($i)->format('Y-m-d'); // due date on every month on the same day
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
                    'comment' => $comment
                ];

                $invoice = new FikenInvoice();
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
                /*'issueDate' => $package->issue_date*/
            ];

            $invoice = new FikenInvoice();
            $invoice->create_invoice($invoice_fields);
        }

        if( $request->update_address ) :
            $address = Address::firstOrNew(['user_id' => Auth::user()->id]);
            $address->street = $request->street;
            $address->city = $request->city;
            $address->zip = $request->zip;
            $address->phone = $request->phone;
            //$address->save();
        endif;


        $courseTaken = CoursesTaken::firstOrNew(['user_id' => Auth::user()->id, 'package_id' => $package->id]);
        $courseTaken->is_active = 0;
        //$courseTaken->save();



        // Check for shop manuscripts
        if( $package->shop_manuscripts->count() > 0 ) :
            foreach( $package->shop_manuscripts as $shop_manuscript ) :
                //$shopManuscriptTaken = ShopManuscriptsTaken::firstOrNew(['user_id' => Auth::user()->id, 'shop_manuscript_id' => $shop_manuscript->shop_manuscript_id]);
                $shopManuscriptTaken = new ShopManuscriptsTaken();
                $shopManuscriptTaken->user_id = Auth::user()->id;
                $shopManuscriptTaken->shop_manuscript_id = $shop_manuscript->shop_manuscript_id;
                $shopManuscriptTaken->is_active = false;
                $shopManuscriptTaken->package_shop_manuscripts_id = $package->shop_manuscripts[0]->id;
                $shopManuscriptTaken->save();
            endforeach;
        endif;


        if ($package->included_courses->count() > 0) {
            foreach ($package->included_courses as $included_course) {
                if ($included_course->included_package_id == 29) { // check if webinar-pakke is included
                    $add_to_automation++;
                }
            }
        }

        if ($package->course->id == 17) { //check if webinar-pakke
            $add_to_automation++;
        }

        if ($add_to_automation > 0) {
            $user_email = Auth::user()->email;
            $automation_id = 73;
            $user_name = Auth::user()->first_name;

            AdminHelpers::addToAutomation($user_email,$automation_id,$user_name);
        }

        // check if the course has activecampaign list then add the user
        if ($package->course->auto_list_id > 0) {
            $list_id = $package->course->auto_list_id;
            $listData = [
                'email' => Auth::user()->email,
                'name' => Auth::user()->first_name,
                'last_name' => Auth::user()->last_name
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
        $from       = 'post@forfatterskolen.no';
        $headers1 = "From: Forfatterskolen<".$from.">\r\n";
        $headers1 .= "MIME-Version: 1.0\r\n";
        $headers1 .= "Content-Type: text/html; charset=UTF-8\r\n";
        //mail('support@forfatterskolen.no', 'New Course Order', Auth::user()->first_name . ' has ordered the course ' . $package->course->title, $headers1);
        /*AdminHelpers::send_email('New Course Order',
            'post@forfatterskolen.no', 'support@forfatterskolen.no', Auth::user()->first_name . ' has ordered the course ' . $package->course->title);*/


        // Send course email
        $actionText = 'Mine Kurs';
        $actionUrl = 'http://www.forfatterskolen.no/account/course';
        $headers = "From: Forfatterskolen<post@forfatterskolen.no>\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $user = Auth::user();
        $email_content = $package->course->email;
        //mail($user->email, $package->course->title, view('emails.course_order', compact('actionText', 'actionUrl', 'user', 'email_content')), $headers);
        /*AdminHelpers::send_email($package->course->title,
            'post@forfatterskolen.no', $user->email,
            view('emails.course_order', compact('actionText', 'actionUrl', 'user', 'email_content')));*/

        if( $paymentMode->mode == "Paypal" ) :
            echo '<form name="_xclick" id="paypal_form" style="display:none" action="https://www.paypal.com/cgi-bin/webscr" method="post">
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
            return;
        endif;


        //return redirect(route('front.shop.thankyou'));

    }

    public function thankyou()
    {
        return view('frontend.shop.thankyou');
    }

    /**
     * Claim course reward
     * @param $course_id
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function claimReward($course_id, Request $request)
    {
        $course = Course::find($course_id);
        if ($course->rewardCoupons()->count()) {
            if ($request->isMethod('post')) {
                $reward = $course->rewardCoupons()->where('coupon', $request->coupon)->first();

                if (!$reward) {
                    return redirect()->back()->withInput()->with(['errors' => AdminHelpers::createMessageBag('Invalid coupon.')]);
                }

                // check if the coupon is already been used
                if ($reward->is_used) {
                    return redirect()->back()->withInput()->with(['errors' => AdminHelpers::createMessageBag('Coupon is already used.')]);
                }

                $reward->is_used = 1;
                $reward->save();

                if( $request->update_address ) :
                    $address = Address::firstOrNew(['user_id' => Auth::user()->id]);
                    $address->street = $request->street;
                    $address->city = $request->city;
                    $address->zip = $request->zip;
                    $address->phone = $request->phone;
                    $address->save();
                endif;

                $course_packages = $course->packages->pluck('id')->toArray();
                $courseTaken = CoursesTaken::where('user_id', Auth::user()->id)->whereIn('package_id', $course_packages)->first();

                // check if the user already avails this course
                if($courseTaken) {
                    $courseTaken->is_active = 1;
                    // add one month to the end date
                    $courseTaken->end_date = Carbon::parse($courseTaken->end_date)->addMonth(1);//Carbon::now()->addMonth(1);
                } else {
                    $package = Package::findOrFail($request->package_id);

                    if (!$package) {
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
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPaymentPlanOptions($id)
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

    public function paypalIPN (Request $request){
        $ipn = new PaypalIPN();

        //$ipn->useSandbox();

        $verified = $ipn->verifyIPN();

        if ($verified) :
            // Create new transaction
            $invoice = Invoice::findOrFail($request->custom);
            $transaction = new Transaction();
            $transaction->invoice_id = $invoice->id;
            $transaction->mode = 'Paypal';
            $transaction->mode_transaction = $request->txn_id;
            $transaction->amount = $request->mc_gross;
            $transaction->save();

            $fiken_invoice = FrontendHelpers::FikenConnect($invoice->fiken_url);
            $balance = (double)$fiken_invoice->gross/100;

            if($invoice->payment_plan->division == 1 && ($balance - $invoice->transactions->sum('amount') ) <= 0 ) :
                $courseTaken = CoursesTaken::where('user_id', $invoice->user_id)->where('package_id', $invoice->package_id)->first();
                if(! $courseTaken ) :
                    $courseTaken = new CoursesTaken;
                    $courseTaken->user_id = $invoice->user_id;
                    $courseTaken->package_id = $invoice->package_id;
                endif;
                $courseTaken->is_active = 1;
                $courseTaken->save();
            endif;
        endif;

        return header("HTTP/1.1 200 OK");
    }
}
