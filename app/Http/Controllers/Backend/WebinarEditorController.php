<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\WebinarEditor;
use App\Webinar;

class WebinarEditorController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($webinar_id, Request $request)
    {
        $webinar = Webinar::findOrFail($webinar_id);

        $webinarEditor = new WebinarEditor();
        $webinarEditor->webinar_id = $webinar->id;
        $webinarEditor->presenter_url = $request->presenter_url;
        $webinarEditor->editor_id = $request->editor_id;
        $webinarEditor->name = $request->name;
        $webinarEditor->save();

        return redirect()->back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
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
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteEditor($id)
    {
        $webinarEditor = webinarEditor::findOrFail($id);
        $webinarEditor->forceDelete();
        return redirect()->back();
    }
}
