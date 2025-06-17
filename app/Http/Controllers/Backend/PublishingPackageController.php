<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\PublishingService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublishingPackageController extends Controller
{
    public function services(): View
    {
        return view('backend.publishing-package.services');
    }

    public function getAllServices()
    {
        $publishingService = PublishingService::all();

        return $publishingService;
    }

    public function saveService(Request $request)
    {
        $request->validate([
            'product_service' => 'required',
            'price' => 'required',
            'per_word_hour' => 'required',
            'per_unit' => 'required',
        ]);

        $publishingService = $request->id ? PublishingService::find($request->id) : new PublishingService;

        $publishingService->fill($request->all());
        $publishingService->slug = str_slug($request->product_service);
        $publishingService->is_active = isset($request->is_active) && $request->is_active;

        $publishingService->save();

        return $publishingService;
    }

    public function updateServiceField($service_id, Request $request)
    {
        $service = PublishingService::find($service_id);
        if (! $service) {
            return redirect()->route('admin.service.index');
        }

        $field = $request->field;
        $service->$field = $request->value;
        $service->save();

        return $service;
    }
}
