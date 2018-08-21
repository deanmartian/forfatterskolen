<?php
namespace App\Http\Controllers\Backend;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\PackageWorkshop;
use App\Package;
use App\Workshop;
use App\WorkshopsTaken;
use App\Http\AdminHelpers;

class PackageWorkshopController extends Controller
{
   
   public function store($package_id, Request $request)
   {
   		$package = Package::findOrFail($package_id);
   		$workshop = Workshop::findOrFail($request->workshop_id);

   		if( !in_array($workshop->id, $package->workshops()->pluck('workshop_id')->toArray()) ) :
	   		$packageWorkshop = new PackageWorkshop();
	   		$packageWorkshop->package_id = $package->id;
	   		$packageWorkshop->workshop_id = $workshop->id;
	   		$packageWorkshop->save();
   		endif;
   		return redirect()->back();
   }


   public function delete($workshop_id)
   {
		$workshop = PackageWorkshop::findOrFail($workshop_id);
		$workshop->forceDelete();
		return redirect()->back();
   }


   public function approve($workshop_taken_id, Request $request)
   {
      $workshopTaken = WorkshopsTaken::findOrFail($workshop_taken_id);
      $workshopTaken->is_active = TRUE;

       $user = User::find($request->workshop_user_id);
       $workshop = Workshop::find($request->workshop_id);
       if ($user && $workshop && $workshop->email_title && $workshop->email_body) {
           $to = $user->email;
           $headers = "From: Forfatterskolen<post@forfatterskolen.no>\r\n";
           mail($to, $workshop->email_title, $workshop->email_body, $headers);
       }

       $workshopTaken->save();
      return redirect()->back();
   }


   public function disapprove($workshop_id)
   {
      $workshopTaken = WorkshopsTaken::findOrFail($workshop_id);
      $workshopTaken->forceDelete();
      return redirect()->back();
   }

}
