<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Package;
use App\PackageShopManuscript;
use App\ShopManuscript;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PackageShopManuscriptController extends Controller
{
    public function store($package_id, Request $request): RedirectResponse
    {
        $package = Package::findOrFail($package_id);
        $shopManuscript = ShopManuscript::findOrFail($request->shop_manuscript_id);
        $packageShopManuscript = new PackageShopManuscript;
        $packageShopManuscript->package_id = $package->id;
        $packageShopManuscript->shop_manuscript_id = $shopManuscript->id;
        $packageShopManuscript->save();

        return redirect()->back();
    }

    public function delete($shop_manuscript_id): RedirectResponse
    {
        $shopManuscript = PackageShopManuscript::findOrFail($shop_manuscript_id);
        $shopManuscript->forceDelete();

        return redirect()->back();
    }
}
