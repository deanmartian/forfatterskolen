<?php
namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\PackageCourse;
use App\Package;
use App\Http\AdminHelpers;

class PackageCourseController extends Controller
{
   
   
   public function store( Request $request )
   {
      $package = Package::findOrFail($request->package_id);
      $include_package = Package::findOrFail($request->include_package_id);
      PackageCourse::create([
         'package_id' => $package->id,
         'included_package_id' => $include_package->id,
      ]);
      return redirect()->back();
   }


   public function destroy($id)
   {
		$PackageCourse = PackageCourse::findOrFail($id);
		$PackageCourse->forceDelete();
		return redirect()->back();
   }

}
