<?php

namespace App\Http\Controllers\Frontend;

use App\Course;
use App\CoursesTaken;
use App\Editor;
use App\Http\Controllers\Controller;
use App\Http\FrontendHelpers;
use App\Http\Requests\OrderCreateRequest;
use App\Order;
use App\Package;
use App\Services\CourseService;
use App\Services\GiftService;
use App\ShopManuscript;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class GiftController extends Controller
{

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function course()
    {
        $courses = Course::where('status', 1)
            //->orderBy('created_at', 'desc')
            // display the 0 last
            ->select(['*', \DB::raw('IF(display_order > 0, display_order, 1000000) display_order')])
            ->orderBy('display_order', 'asc')
            ->whereHas('packages')
            /*->whereHas('packages', function($query){
                return count($query) > 0;
            })*/
            ->get()
            ->filter(function($item) {
                return $item->is_active || $item->is_free;
            }); // the original don't have this filter
        $showRoute = 'front.gift.course.show';
        return view('frontend.course.index', compact('courses', 'showRoute'));
    }

    /**
     * @param $course_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|mixed
     */
    public function courseShow($course_id)
    {
        $course = Course::findOrFail($course_id);

        if (!$course->is_free): // added this condition to display page if it's free
            if( !FrontendHelpers::isCourseActive($course) || count($course->packages) == 0 ) : // Display 404 if Course has no Packages
                return abort(404);
            endif;
        endif;
        $checkoutRoute = 'front.gift.course.checkout';
        return view('frontend.course.show', compact('course', 'checkoutRoute'));
    }

    /**
     * @param $course_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View|mixed
     */
    public function courseCheckout( $course_id )
    {
        $course = Course::findOrFail($course_id);

        if (!$course->is_free): // added this condition to check if the course is for sale
            if( !FrontendHelpers::isCourseActive($course) || count($course->packages) == 0 ) : // Display 404 if Course has no Packages
                return abort(404);
            endif;
        endif;

        $hasPaidCourse = false;
        if( !Auth::guest() ) :
            foreach( \Auth::user()->coursesTakenNotOld as $courseTaken ) {
                if( $courseTaken->package->course->type != "Free" && $courseTaken->is_active ) :
                    if ($courseTaken->package->course->is_free != 1) {
                        $hasPaidCourse = true;
                    }
                    break;
                endif;
            }
        endif;

        if ($course->hide_price) {
            return redirect()->route('front.course.show', $course->id);
        }

        $packages = $course->packages()->isShow()->get();
        $package_id = \Request::has('package') ? \Request::get('package') :
            (isset($packages[1]) ? $packages[1]['id'] : $packages[0]['id']);
        $coupon = \request()->has('c') ? \request()->get('c') : '';
        $startIndex = \request()->has('si') ? \request()->get('si') : 0;

        $user = \Auth::user();

        if ($user) {
            $user['address'] = $user->address;
            $user->checkoutLogs()->firstOrCreate([
                'parent' => 'course',
                'parent_id' => $course->id
            ]);
        }

        $giftCards = FrontendHelpers::gitCards();
        $giftCard = Session::get('gift-card');

        return view('frontend.gift.course-checkout', compact('course', 'packages', 'package_id', 'coupon',
            'hasPaidCourse', 'user', 'startIndex', 'giftCard', 'giftCards'));
    }

    /**
     * @param $course_id
     * @param Request $request
     * @param CourseService $courseService
     * @param GiftService $giftService
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateCheckoutForm( $course_id, Request $request, CourseService $courseService, GiftService $giftService )
    {

        $validation = [
            'email'         => 'required|email',
            'first_name'    => 'required',
            'last_name'     => 'required',
            'street'        => 'required',
            'zip'           => 'required',
            'city'          => 'required',
            'phone'         => 'required',
        ];

        if (!\Auth::check()) {
            $validation['password'] = 'required|min:3';
        }

        $validator = \Validator::make($request->all(), $validation);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (!\Auth::check()) {
            $addressData = [
                'street'    => $request->street,
                'zip'       => $request->zip,
                'city'      => $request->city,
                'phone'     => $request->phone
            ];
            $courseService->evaluateUser($request->email, $request->password, $request->first_name, $request->last_name, $addressData);
        }

        $itemType = 'course';
        if ($request->has('item_type') && $request->item_type === 2) {
            $itemType = 'shop-manuscript';
        }

        return response()->json($giftService->processCheckout($request, $itemType));
    }

    /**
     * @param $item_id
     * @param Request $request
     * @param GiftService $giftService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function thankyou( $item_id, Request $request, GiftService $giftService )
    {

        // check if from svea payment
        if ($request->has('svea_ord')) {

            $order_id = $request->get('svea_ord');
            $order = Order::find($order_id);
            $parent = '';
            $parent_id = '';

            if ($order->type === 1) {
                $course = Course::find($item_id);
                $parent = 'course-package';
                $parent_id = $order->package_id;
                if (!$course) {
                    abort(404);
                }
            }

            if ($order->type === 2) {
                $shopManuscript = ShopManuscript::find($item_id);
                $parent = 'shop-manuscript';
                $parent_id = $order->item_id;
                if (!$shopManuscript) {
                    abort(404);
                }
            }

            // add course to user
            if (!$order->is_processed) {
                $giftPurchase = $giftService->addGiftPurchase($order->user_id, $parent, $parent_id);
                $giftService->notifyGiftBuyer($giftPurchase);
                $giftService->notifyAdmin($giftPurchase);
            }

            $order->is_processed = 1;
            $order->save();
        }
        return view('frontend.gift.thankyou', ['page' => 'gift-course']);
    }

    public function shopManuscript()
    {
        $shopManuscripts = ShopManuscript::orderBy('full_payment_price', 'asc')->get();
        $editors = Editor::orderBy('id', 'ASC')->get();
        $checkoutRoute = 'front.gift.shop-manuscript.checkout';
        return view('frontend.shop-manuscript.index', compact('shopManuscripts', 'editors', 'checkoutRoute'));
    }

    public function shopManuscriptCheckout( $manuscript_id )
    {
        $shopManuscript = ShopManuscript::findOrFail($manuscript_id);

        $user = \Auth::user();
        if ($user) {
            $user['address'] = $user->address;
        }

        return view('frontend.gift.shop-manuscript-checkout', compact('shopManuscript', 'user'));
    }

}