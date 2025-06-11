<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Package;
use App\PackageWorkshop;
use App\Workshop;
use App\WorkshopsTaken;
use Illuminate\Http\Request;

class PackageWorkshopController extends Controller
{
    public function store($package_id, Request $request)
    {
        $package = Package::findOrFail($package_id);
        $workshop = Workshop::findOrFail($request->workshop_id);

        if (! in_array($workshop->id, $package->workshops()->pluck('workshop_id')->toArray())) {
            $packageWorkshop = new PackageWorkshop;
            $packageWorkshop->package_id = $package->id;
            $packageWorkshop->workshop_id = $workshop->id;
            $packageWorkshop->save();
        }

        return redirect()->back();
    }

    public function delete($workshop_id)
    {
        $workshop = PackageWorkshop::findOrFail($workshop_id);
        $workshop->forceDelete();

        return redirect()->back();
    }

    public function approve($workshop_id)
    {
        $workshopTaken = WorkshopsTaken::findOrFail($workshop_id);
        $workshopTaken->is_active = true;
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
