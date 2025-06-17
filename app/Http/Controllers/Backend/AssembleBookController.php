<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\PublishingMarketingHelp;
use App\PublishingPrintColor;
use App\PublishingPrintCount;
use App\PublishingPrintCover;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AssembleBookController extends Controller
{
    /**
     * get all options
     */
    public function getOptions(): JsonResponse
    {
        $printColors = PublishingPrintColor::all();
        $printCounts = PublishingPrintCount::all();
        $printCovers = PublishingPrintCover::all();
        $marketingHelps = PublishingMarketingHelp::all();

        return response()->json([
            'print_colors' => $printColors,
            'print_counts' => $printCounts,
            'print_covers' => $printCovers,
            'marketing_helps' => $marketingHelps,
        ]);
    }

    /**
     * save the cover or color
     */
    public function saveCoverOrColor(Request $request): json
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required',
        ]);

        $model = $request->formType === 'cover' ? PublishingPrintCover::find($request->id) : PublishingPrintColor::find($request->id);
        $model->name = $request->name;
        $model->price = $request->price;
        $model->save();

        return $model;
    }

    public function saveCountOrHelp(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'value' => 'required|numeric',
            'price' => 'required|numeric',
        ]);

        $model = $request->formType === 'count' ? PublishingPrintCount::find($request->id) : PublishingMarketingHelp::find($request->id);
        $model->name = $request->name;
        $model->value = $request->value;
        $model->price = $request->price;
        $model->save();

        return $model;
    }
}
