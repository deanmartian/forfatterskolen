<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Webinar;
use App\WebinarEditor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WebinarEditorController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store($webinar_id, Request $request): RedirectResponse
    {
        $webinar = Webinar::findOrFail($webinar_id);

        $webinarEditor = new WebinarEditor;
        $webinarEditor->webinar_id = $webinar->id;
        $webinarEditor->presenter_url = $request->presenter_url;
        $webinarEditor->editor_id = $request->editor_id;
        $webinarEditor->name = $request->name;
        $webinarEditor->save();

        return redirect()->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(int $id, Request $request): RedirectResponse
    {
        $webinarEditor = WebinarEditor::findOrFail($id);
        $webinarEditor->presenter_url = $request->presenter_url;
        $webinarEditor->editor_id = $request->editor_id;
        $webinarEditor->name = $request->name;
        $webinarEditor->save();

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function deleteEditor(int $id): RedirectResponse
    {
        $webinarEditor = webinarEditor::findOrFail($id);
        $webinarEditor->forceDelete();

        return redirect()->back();
    }
}
