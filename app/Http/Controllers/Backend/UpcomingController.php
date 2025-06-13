<?php

namespace App\Http\Controllers\Backend;

use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\UpcomingSection;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UpcomingController extends Controller
{
    public function index(): View
    {
        $upcomingSections = UpcomingSection::all();

        return view('backend.upcoming.index', compact('upcomingSections'));
    }

    public function saveSection($id, Request $request): RedirectResponse
    {

        $section = UpcomingSection::find($id);

        $section->name = $request->name;
        $section->title = $request->title;
        $section->description = $request->description;
        $section->date = $request->date ? Carbon::parse($request->date)->format('Y-m-d H:i:s') : null;
        $section->link = $request->link;
        $section->link_label = $request->link_label;
        $section->save();

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Record saved successfully.'),
            'alert_type' => 'success']);
    }
}
