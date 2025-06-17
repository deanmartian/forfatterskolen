<?php

namespace App\Http\Controllers\Backend;

use App\Course;
use App\CourseRewardCoupon;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CourseRewardCouponController extends Controller
{
    /**
     * Create reward coupon
     */
    public function store($course_id, Request $request): RedirectResponse
    {
        $course = Course::find($course_id);
        if ($course) {
            $data = $request->all();
            $request->validate(['coupon' => 'required|max:10|unique:course_reward_coupons,coupon']);
            $course->rewardCoupons()->create($data);

            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Reward Coupon created successfully.'),
                'alert_type' => 'success',
            ]);
        }

        return redirect()->route('admin.course.show', ['id' => $course->id, 'section' => 'reward-coupons']);
    }

    /**
     * update reward coupon
     */
    public function update($course_id, $id, Request $request): RedirectResponse
    {
        $course = Course::find($course_id);
        $reward = CourseRewardCoupon::find($id);
        if ($course && $reward) {
            $data = $request->all();
            $request->validate(['coupon' => 'required|max:10|unique:course_reward_coupons,coupon,'.$reward->id]);
            $reward->update($data);

            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Reward Coupon updated successfully.'),
                'alert_type' => 'success',
            ]);
        }

        return redirect()->route('admin.course.show', ['id' => $course->id, 'section' => 'reward-coupons']);
    }

    /**
     * Delete reward coupon
     */
    public function destroy($course_id, $id): RedirectResponse
    {
        $course = Course::find($course_id);
        $reward = CourseRewardCoupon::find($id);
        if ($course && $reward) {
            $reward->delete();

            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Reward Coupon deleted successfully.'),
                'alert_type' => 'success',
            ]);
        }

        return redirect()->route('admin.course.show', ['id' => $course->id, 'section' => 'reward-coupons']);
    }

    /**
     * Create multiple reward coupon
     */
    public function multipleStore($course_id, Request $request): RedirectResponse
    {
        $course = Course::find($course_id);

        if ($course) {
            $numCodesToGenerate = $request->coupon_count;
            $this->couponIterator($numCodesToGenerate, $course_id);

            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Reward Coupons created successfully.'),
                'alert_type' => 'success',
            ]);
        }

        return redirect()->route('admin.course.show', ['id' => $course->id, 'section' => 'reward-coupons']);
    }

    /**
     * Export the coupon codes to a text file
     */
    public function exportToText($course_id): RedirectResponse
    {
        $course = Course::find($course_id);
        if ($course) {
            $filename = 'coupon-codes';
            $handle = fopen($filename.'.txt', 'w');
            foreach ($course->rewardCoupons as $rewardCoupon) {
                fwrite($handle, $rewardCoupon['coupon']."\r\n");
            }
            fclose($handle);
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.basename($filename.'.txt'));
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: '.filesize($filename.'.txt'));
            readfile($filename.'.txt');
            exit;
        }

        return redirect()->back();
    }

    /**
     * Creating multiple coupon iterator
     *
     * @return null
     */
    public function couponIterator($count, $course_id)
    {
        for ($i = 0; $i < $count; $i++) {
            $code = $this->generateCouponCode();

            $checkReward = CourseRewardCoupon::where('coupon', '=', $code)
                ->where('course_id', '=', $course_id)
                ->get();

            // check if the coupon code already exists for that course then re-run the iterator
            if ($checkReward->count() > 0) {
                $newCount = $count - 1;

                return $this->couponIterator($newCount, $course_id);
            }

            CourseRewardCoupon::create([
                'course_id' => $course_id,
                'coupon' => $code,
            ]);
        }

        return null;
    }

    /**
     * Generate coupon code
     *
     * @return string
     */
    public function generateCouponCode()
    {
        $possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $res = '';
        for ($i = 0; $i < 6; $i++) {
            $res .= $possible[mt_rand(0, strlen($possible) - 1)];
        }

        return $res;
    }
}
