<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Package;
use App\PackageCourse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PackageCourseController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $package = Package::findOrFail($request->package_id);
        $include_package = Package::findOrFail($request->include_package_id);
        PackageCourse::create([
            'package_id' => $package->id,
            'included_package_id' => $include_package->id,
        ]);

        return redirect()->back();
    }

    public function destroy($id): RedirectResponse
    {
        $PackageCourse = PackageCourse::findOrFail($id);
        $PackageCourse->forceDelete();

        return redirect()->back();
    }
}
