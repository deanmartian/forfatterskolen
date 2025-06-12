<?php

namespace App\Http\Controllers\Backend;

use App\Course;
use App\CourseDiscount;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseDiscountController extends Controller
{
    /**
     * Display all of the course discounts
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index($course_id): View
    {
        $course = Course::find($course_id);

        if (! $course) {
            abort(404);
        }

        $typeList = (new CourseDiscount)->typeList();

        $discounts = $course->discounts()->paginate(15);

        return view('backend.course-discount.index', compact('course', 'discounts', 'typeList'));
    }

    /**
     * Create new course discount
     */
    public function store($course_id, Request $request): RedirectResponse
    {

        if ($request->valid_from && ! $request->valid_to) {
            return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Please add a valid to value.'),
                'alert_type' => 'danger']);
        }

        CourseDiscount::create([
            'course_id' => $course_id,
            'coupon' => $request->coupon,
            'discount' => $request->discount,
            'valid_from' => $request->valid_from,
            'valid_to' => $request->valid_to,
            'type' => $request->type,
        ]);

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Discount added successfully.'),
            'alert_type' => 'success']);
    }

    public function update($course_id, $discount_id, Request $request): RedirectResponse
    {
        $discount = CourseDiscount::find($discount_id);

        if (! $discount) {
            abort(404);
        }

        $discount->coupon = $request->coupon;
        $discount->discount = $request->discount;
        $discount->valid_from = $request->valid_from;
        $discount->valid_to = $request->valid_to;
        $discount->type = $request->type;

        if ($discount->save()) {
            return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Discount updated successfully.'),
                'alert_type' => 'success']);
        }

        return redirect()->back();
    }

    /**
     * Delete the course discount
     */
    public function destroy($course_id, $discount_id): RedirectResponse
    {
        $discount = CourseDiscount::findOrFail($discount_id);
        $discount->forceDelete();

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Discount deleted successfully.'),
            'alert_type' => 'success']);
    }
}
