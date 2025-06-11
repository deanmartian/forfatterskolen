<?php

namespace App\Http\Controllers\Backend;

use App\Course;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddPackageRequest;
use App\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function store($course_id, AddPackageRequest $request)
    {
        $course = Course::findOrFail($course_id);
        $exists = Package::where('variation', $request->variation)->where('course_id', $course_id)->first();

        if ($exists) {
            return redirect()->back()->withErrors('Package already exists');
        } else {
            $package = new Package;
            $package->course_id = $course_id;
            $package->variation = $request->variation;
            $package->description = $request->description;
            $package->manuscripts_count = $request->manuscripts_count;

            $package->full_payment_price = $request->full_payment_price;
            $package->months_3_price = $request->months_3_price;
            $package->months_6_price = $request->months_6_price;

            $package->full_price_product = $request->full_price_product;
            $package->months_3_product = $request->months_3_product;
            $package->months_6_product = $request->months_6_product;

            $package->full_price_due_date = $request->full_price_due_date;
            $package->months_3_due_date = $request->months_3_due_date;
            $package->months_6_due_date = $request->months_6_due_date;
            $package->workshops = $request->workshops;

            $package->save();

            return redirect()->back();
        }
    }

    public function update($course_id, $id, AddPackageRequest $request)
    {
        $course = Course::findOrFail($course_id);
        $exists = Package::where('variation', $request->variation)->where('course_id', $course_id)->where('id', '!=', $request->variation_id)->first();
        if ($exists) {
            return redirect()->back()->withErrors('Package already exists');
        } else {
            $package = Package::findOrFail($request->variation_id);
            $package->variation = $request->variation;
            $package->description = $request->description;
            $package->manuscripts_count = $request->manuscripts_count;

            $package->full_payment_price = $request->full_payment_price;
            $package->months_3_price = $request->months_3_price;
            $package->months_6_price = $request->months_6_price;

            $package->full_price_product = $request->full_price_product;
            $package->months_3_product = $request->months_3_product;
            $package->months_6_product = $request->months_6_product;

            $package->full_price_due_date = $request->full_price_due_date;
            $package->months_3_due_date = $request->months_3_due_date;
            $package->months_6_due_date = $request->months_6_due_date;
            $package->workshops = $request->workshops;

            $package->save();
        }

        return redirect()->back();
    }

    public function destroy($course_id, $id, Request $request)
    {
        $course = Course::findOrFail($course_id);
        $package = Package::findOrFail($request->variation_id);
        $package->forceDelete();

        return redirect()->back();
    }
}
