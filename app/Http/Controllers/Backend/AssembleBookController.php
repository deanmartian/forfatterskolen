<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\PublishingMarketingHelp;
use App\PublishingPrintColor;
use App\PublishingPrintCount;
use App\PublishingPrintCover;
use Illuminate\Http\Request;

class AssembleBookController extends Controller
{
    /**
     * get all options
     *
     * @return json
     */
    public function getOptions()
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
     *
     * @return json
     */
    public function saveCoverOrColor(Request $request)
    {
        $this->validate($request, [
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
        $this->validate($request, [
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
