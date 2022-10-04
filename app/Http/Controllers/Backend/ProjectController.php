<?php

namespace App\Http\Controllers\Backend;


use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\ProjectActivity;
use Illuminate\Http\Request;

class ProjectController extends Controller
{

    public function index()
    {
        $learners =  AdminHelpers::getLearnerList();
        $activities = ProjectActivity::all();
        return view('backend.project.index', compact('learners', 'activities'));
    }

    public function saveActivity( Request $request )
    {
        $this->validate($request, [
            'activity' => 'required'
        ]);

        $model = $request->id ? ProjectActivity::find($request->id) : new ProjectActivity();
        $model->activity = $request->activity;
        $model->project_id = $request->project_id ?: NULL;
        $model->description = $request->description;
        $model->invoicing = $request->invoicing;
        $model->hourly_rate = $request->hourly_rate;
        $model->save();

        return $model;
    }

    public function deleteActivity( $id )
    {
        ProjectActivity::find($id)->delete();
        return response()->json();
    }

}