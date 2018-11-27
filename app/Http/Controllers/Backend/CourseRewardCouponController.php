<?php
namespace App\Http\Controllers\Backend;

use App\Course;
use App\CourseRewardCoupon;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CourseRewardCouponController extends Controller {

    /**
     * Create reward coupon
     * @param $course_id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store($course_id, Request $request)
    {
        $course = Course::find($course_id);
        if ($course) {
            $data = $request->all();
            $this->validate($request, ['coupon' => 'required|max:10|unique:course_reward_coupons,coupon']);
            $course->rewardCoupons()->create($data);
            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Reward Coupon created successfully.'),
                'alert_type' => 'success'
            ]);
        }

        return redirect()->route('admin.course.show', ['id' => $course->id, 'section' => 'reward-coupons']);
    }

    /**
     * update reward coupon
     * @param $course_id
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($course_id, $id, Request $request)
    {
        $course = Course::find($course_id);
        $reward = CourseRewardCoupon::find($id);
        if ($course && $reward) {
            $data = $request->all();
            $this->validate($request, ['coupon' => 'required|max:10|unique:course_reward_coupons,coupon,'.$reward->id]);
            $reward->update($data);
            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Reward Coupon updated successfully.'),
                'alert_type' => 'success'
            ]);
        }

        return redirect()->route('admin.course.show', ['id' => $course->id, 'section' => 'reward-coupons']);
    }

    /**
     * Delete reward coupon
     * @param $course_id
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($course_id, $id)
    {
        $course = Course::find($course_id);
        $reward = CourseRewardCoupon::find($id);
        if ($course && $reward) {
            $reward->delete();
            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Reward Coupon deleted successfully.'),
                'alert_type' => 'success'
            ]);
        }
        return redirect()->route('admin.course.show', ['id' => $course->id, 'section' => 'reward-coupons']);
    }

}