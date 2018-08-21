<?php

namespace App\Http\Controllers\Backend;

use App\Course;
use App\CourseDiscount;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CourseDiscountController extends Controller
{

    /**
     * Display all of the course discounts
     * @param $course_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index($course_id)
    {
        $course = Course::find($course_id);

        if (!$course) {
            abort(404);
        }

        $discounts = $course->discounts()->paginate(15);

        return view('backend.course-discount.index', compact('course', 'discounts'));
    }

    /**
     * Create new course discount
     * @param $course_id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store($course_id, Request $request)
    {
        CourseDiscount::create([
            'course_id' => $course_id,
            'coupon' => $request->coupon,
            'discount' => $request->discount
        ]);

        AdminHelpers::addFlashMessage('success', 'Discount added successfully.');
        return redirect()->back();
    }

    public function update($course_id, $discount_id, Request $request)
    {
        $discount = CourseDiscount::find($discount_id);

        if (!$discount) {
            abort(404);
        }

        $discount->coupon = $request->coupon;
        $discount->discount = $request->discount;

        if ($discount->save()) {
            AdminHelpers::addFlashMessage('success', 'Discount updated successfully.');
        }

        return redirect()->back();
    }

    /**
     * Delete the course discount
     * @param $course_id
     * @param $discount_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($course_id, $discount_id)
    {
        $discount = CourseDiscount::findOrFail($discount_id);
        $discount->forceDelete();
        AdminHelpers::addFlashMessage('success', 'Discount deleted successfully.');
        return redirect()->back();
    }

}