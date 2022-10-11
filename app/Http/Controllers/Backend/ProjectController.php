<?php

namespace App\Http\Controllers\Backend;


use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Project;
use App\ProjectActivity;
use App\ProjectBook;
use App\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{

    public function index()
    {
        $learners =  User::where('role', 2)->where('is_self_publishing_learner', 1)->get();
        $activities = ProjectActivity::all();
        $projects = Project::all();
        return view('backend.project.index', compact('learners', 'activities', 'projects'));
    }

    public function show($id)
    {
        $project = Project::find($id)->load(['books', 'user', 'selfPublishingList']);
        $editors = AdminHelpers::editorList();
        $learners = User::where('role', 2)->where('is_self_publishing_learner', 1)->get();
        return view('backend.project.show', compact('project', 'editors', 'learners'));
    }

    public function saveProject( Request $request )
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        if (!$request->id) {
            $this->validate($request, [
                'number' => 'required|unique:projects,identifier'
            ]);
        }

        $model = $request->id ? Project::find($request->id) : new Project();
        $model->user_id = $request->user_id;
        $model->name = $request->name;
        $model->identifier = $request->number;
        $model->activity_id = $request->activity_id;
        $model->start_date = $request->start_date;
        $model->end_date = $request->end_date;
        $model->description = $request->description;
        $model->is_finished = $request->is_finished;
        $model->save();

        if ($request->user_id) {
            $model->books()->update([
                'user_id' => $request->user_id
            ]);
        }

        return $model;
    }

    public function deleteProject( $project_id )
    {
        $project = Project::find($project_id);

        $activity = ProjectActivity::where('project_id', $project_id)->update([
            'project_id' => NULL
        ]);

        $project->delete();
        return response()->json();
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

    public function saveNote( $project_id, Request $request )
    {
        $project = Project::find($project_id);
        $project->notes = $request->notes;
        $project->save();

        return response()->json($project);
    }

    public function saveBook( $project_id, Request $request )
    {
        $this->validate($request, [
            'book_name' => 'required'
        ]);

        $model = $request->id ? ProjectBook::find($request->id) : new ProjectBook();
        $model->project_id = $project_id;
        $model->user_id = $request->user_id;
        $model->book_name = $request->book_name;
        $model->isbn_hardcover_book = $request->isbn_hardcover_book;
        $model->isbn_ebook = $request->isbn_ebook;
        $model->save();

        if ($request->user_id) {
            Project::find($project_id)->update(['user_id' => $request->user_id]);
        }

        $project = Project::find($project_id)->load(['books', 'user', 'selfPublishingList']);

        return response()->json([
            'book' => $model,
            'project' => $project
        ]);
    }

    public function deleteBook( $id )
    {
        ProjectBook::find($id)->delete();
        return response()->json();
    }
}