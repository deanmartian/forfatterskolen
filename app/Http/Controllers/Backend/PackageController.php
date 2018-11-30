<?php
namespace App\Http\Controllers\Backend;

use App\Http\AdminHelpers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddPackageRequest;
use App\Course;
use App\Package;

class PackageController extends Controller
{
   
    public function store($course_id, AddPackageRequest $request)
    {
        $course = Course::findOrFail($course_id);
        $exists = Package::where('variation', $request->variation)->where('course_id', $course_id)->first();

        if($exists) :
            return redirect()->back()->withErrors('Package already exists');
        else :
            $package = new Package();
            $package->course_id = $course_id;
            $package->variation = $request->variation;
            $package->description = $request->description;
            $package->manuscripts_count = $request->manuscripts_count;

            $package->full_payment_price = $request->full_payment_price;
            $package->months_3_price = $request->months_3_price;
            $package->months_6_price = $request->months_6_price;
            $package->months_12_price = $request->months_12_price;

            $package->full_price_product = $request->full_price_product;
            $package->months_3_product = $request->months_3_product;
            $package->months_6_product = $request->months_6_product;
            $package->months_12_product = $request->months_12_product;

            $package->full_price_due_date = $request->full_price_due_date;
            $package->months_3_due_date = $request->months_3_due_date;
            $package->months_6_due_date = $request->months_6_due_date;
            $package->months_12_due_date = $request->months_12_due_date;
            $package->workshops = $request->workshops;

            $package->full_payment_sale_price       = $request->full_payment_sale_price;
            $package->full_payment_sale_price_from  = $request->full_payment_sale_price_from;
            $package->full_payment_sale_price_to    = $request->full_payment_sale_price_to;

            $package->months_3_sale_price       = $request->months_3_sale_price;
            $package->months_3_sale_price_from  = $request->months_3_sale_price_from;
            $package->months_3_sale_price_to    = $request->months_3_sale_price_to;

            $package->months_6_sale_price       = $request->months_6_sale_price;
            $package->months_6_sale_price_from  = $request->months_6_sale_price_from;
            $package->months_6_sale_price_to    = $request->months_6_sale_price_to;

            $package->months_12_sale_price       = $request->months_12_sale_price;
            $package->months_12_sale_price_from  = $request->months_12_sale_price_from;
            $package->months_12_sale_price_to    = $request->months_12_sale_price_to;

            $package->full_payment_upgrade_price    = $request->full_payment_upgrade_price ?:0;
            $package->months_3_upgrade_price        = $request->months_3_upgrade_price ?:0;
            $package->months_6_upgrade_price        = $request->months_6_upgrade_price ?:0;
            $package->months_12_upgrade_price       = $request->months_12_upgrade_price ?:0;

            $package->full_payment_standard_upgrade_price    = $request->full_payment_standard_upgrade_price ?:0;
            $package->months_3_standard_upgrade_price        = $request->months_3_standard_upgrade_price ?:0;
            $package->months_6_standard_upgrade_price        = $request->months_6_standard_upgrade_price ?:0;
            $package->months_12_standard_upgrade_price       = $request->months_12_standard_upgrade_price ?:0;

            $hasStudentDiscount = isset($request->has_student_discount) ? 1 : 0 ;
            $months_3_enable    = isset($request->months_3_enable) ? 1 : 0 ;
            $months_6_enable    = isset($request->months_6_enable) ? 1 : 0 ;
            $months_12_enable   = isset($request->months_12_enable) ? 1 : 0 ;

            $package->disable_upgrade_price_date = $request->disable_upgrade_price_date;
            $package->disable_upgrade_price = isset($request->disable_upgrade_price) ? 1 : NULL ;
            $package->course_type = $request->course_type;

            $package->has_student_discount  = $hasStudentDiscount;
            $package->months_3_enable       = $months_3_enable;
            $package->months_6_enable       = $months_6_enable;
            $package->months_12_enable      = $months_12_enable;

            $package->issue_date = $request->issue_date;
            $package->is_reward = isset($request->is_reward) ? $request->is_reward : 0;
            $package->validity_period = $request->validity_period;

            $package->save();
            return redirect()->back();
        endif;
    }




    public function update($course_id, $id, AddPackageRequest $request)
    {
        $course = Course::findOrFail($course_id);
        $exists = Package::where('variation', $request->variation)->where('course_id', $course_id)->where('id', '!=', $request->variation_id)->first();
        if($exists) :
            return redirect()->back()->withErrors('Package already exists');
        else :
            $package = Package::findOrFail($request->variation_id);
            $package->variation = $request->variation;
            $package->description = $request->description;
            $package->manuscripts_count = $request->manuscripts_count;

            $package->full_payment_price = $request->full_payment_price;
            $package->months_3_price = $request->months_3_price;
            $package->months_6_price = $request->months_6_price;
            $package->months_12_price = $request->months_12_price;

            $package->full_price_product = $request->full_price_product;
            $package->months_3_product = $request->months_3_product;
            $package->months_6_product = $request->months_6_product;
            $package->months_12_product = $request->months_12_product;

            $package->full_price_due_date = $request->full_price_due_date;
            $package->months_3_due_date = $request->months_3_due_date;
            $package->months_6_due_date = $request->months_6_due_date;
            $package->months_12_due_date = $request->months_12_due_date;
            $package->workshops = $request->workshops;

            $package->full_payment_sale_price       = $request->full_payment_sale_price;
            $package->full_payment_sale_price_from  = $request->full_payment_sale_price_from;
            $package->full_payment_sale_price_to    = $request->full_payment_sale_price_to;

            $package->months_3_sale_price       = $request->months_3_sale_price;
            $package->months_3_sale_price_from  = $request->months_3_sale_price_from;
            $package->months_3_sale_price_to    = $request->months_3_sale_price_to;

            $package->months_6_sale_price       = $request->months_6_sale_price;
            $package->months_6_sale_price_from  = $request->months_6_sale_price_from;
            $package->months_6_sale_price_to    = $request->months_6_sale_price_to;

            $package->months_12_sale_price       = $request->months_12_sale_price;
            $package->months_12_sale_price_from  = $request->months_12_sale_price_from;
            $package->months_12_sale_price_to    = $request->months_12_sale_price_to;

            $package->full_payment_upgrade_price    = $request->full_payment_upgrade_price ?:0;
            $package->months_3_upgrade_price        = $request->months_3_upgrade_price ?:0;
            $package->months_6_upgrade_price        = $request->months_6_upgrade_price ?:0;
            $package->months_12_upgrade_price       = $request->months_12_upgrade_price ?:0;

            $package->full_payment_standard_upgrade_price    = $request->full_payment_standard_upgrade_price ?:0;
            $package->months_3_standard_upgrade_price        = $request->months_3_standard_upgrade_price ?:0;
            $package->months_6_standard_upgrade_price        = $request->months_6_standard_upgrade_price ?:0;
            $package->months_12_standard_upgrade_price       = $request->months_12_standard_upgrade_price ?:0;

            $hasStudentDiscount = isset($request->has_student_discount) ? 1 : 0 ;
            $months_3_enable    = isset($request->months_3_enable) ? 1 : 0 ;
            $months_6_enable    = isset($request->months_6_enable) ? 1 : 0 ;
            $months_12_enable   = isset($request->months_12_enable) ? 1 : 0 ;

            $package->disable_upgrade_price_date = $request->disable_upgrade_price_date;
            $package->disable_upgrade_price = isset($request->disable_upgrade_price) ? 1 : 0 ;
            $package->course_type = $request->course_type;

            $package->has_student_discount  = $hasStudentDiscount;
            $package->months_3_enable       = $months_3_enable;
            $package->months_6_enable       = $months_6_enable;
            $package->months_12_enable      = $months_12_enable;

            $package->issue_date = $request->issue_date;
            $package->is_reward = isset($request->is_reward) ? $request->is_reward : 0;
            $package->validity_period = $request->validity_period;

            $package->save();
        endif;
        return redirect()->back();
    }




    public function destroy($course_id, $id, Request $request)
    {
        $course = Course::findOrFail($course_id);
        $package = Package::findOrFail($request->variation_id);
        $package->forceDelete();
        return redirect()->back();
    }

    /**
     * Update the package if the admin wants to include/remove coaching session
     * @param $course_id
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function includeCoaching($course_id, $id, Request $request)
    {
        if (Course::find($course_id) && $package = Package::find($id)) {
            $package->has_coaching = $request->has_coaching;
            $package->save();
        }
        return redirect()->back();
    }




    
}
