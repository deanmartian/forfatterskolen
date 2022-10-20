<?php

namespace App\Http\Controllers\Backend;


use App\CopyEditingManuscript;
use App\CorrectionManuscript;
use App\Helpers\FileToText;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Http\FrontendHelpers;
use App\Http\Requests\ProjectActivityRequest;
use App\Http\Requests\ProjectBookRequest;
use App\Http\Requests\ProjectCopyEditingRequest;
use App\Http\Requests\ProjectRequest;
use App\Project;
use App\ProjectActivity;
use App\ProjectBook;
use App\Services\ProjectService;
use App\TimeRegister;
use App\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{

    public function index()
    {
        $learners =  User::where('role', 2)->where('is_self_publishing_learner', 1)->get();
        $activities = ProjectActivity::all();
        $projects = Project::all();
        $layout = str_contains(request()->getHttpHost(), 'giutbok') ? 'giutbok.layout' : 'backend.layout';
        return view('backend.project.index', compact('learners', 'activities', 'projects', 'layout'));
    }

    public function show($id)
    {
        $project = Project::find($id)->load(['books', 'user', 'selfPublishingList']);
        $editors = AdminHelpers::editorList();
        $learners = User::where('role', 2)->where('is_self_publishing_learner', 1)->get();
        $activities = ProjectActivity::all();
        $timeRegisters = TimeRegister::where('user_id', $project->user_id)->whereNull('project_id')->with('project')->get();
        $projectTimeRegisters = TimeRegister::where('project_id', $project->id)->with('project')->get();
        $projects = Project::all();

        $layout = 'backend.layout';
        $addOtherServiceRoute = 'admin.project.add-other-service';
        $selfPublishingStoreRoute = 'admin.self-publishing.store';
        $selfPublishingUpdateRoute = 'admin.self-publishing.update';
        $selfPublishingDeleteRoute = 'admin.self-publishing.destroy';
        $selfPublishingAddFeedbackRoute = 'admin.self-publishing.add-feedback';
        $selfPublishingDownloadFeedbackRoute = 'admin.self-publishing.download-feedback';
        $selfPublishingLearnersRoute = 'admin.self-publishing.learners';
        $assignEditorRoute = 'admin.other-service.assign-editor';
        $updateExpectedFinishRoute = 'admin.other-service.update-expected-finish';
        $updateStatusRoute = 'admin.other-service.update-status';
        $otherServiceDeleteRoute = 'admin.other-service.delete';

        if (str_contains(request()->getHttpHost(), 'giutbok')) {
            $layout = 'giutbok.layout';
            $addOtherServiceRoute = 'g-admin.project.add-other-service';
            $selfPublishingStoreRoute = 'g-admin.self-publishing.store';
            $selfPublishingUpdateRoute = 'g-admin.self-publishing.update';
            $selfPublishingDeleteRoute = 'g-admin.self-publishing.destroy';
            $selfPublishingAddFeedbackRoute = 'g-admin.self-publishing.add-feedback';
            $selfPublishingDownloadFeedbackRoute = 'g-admin.self-publishing.download-feedback';
            $selfPublishingLearnersRoute = 'g-admin.self-publishing.learners';
            $assignEditorRoute = 'g-admin.other-service.assign-editor';
            $updateExpectedFinishRoute = 'g-admin.other-service.update-expected-finish';
            $updateStatusRoute = 'g-admin.other-service.update-status';
            $otherServiceDeleteRoute = 'g-admin.other-service.delete';
        }

        return view('backend.project.show', compact('project', 'editors', 'learners', 'activities',
            'timeRegisters', 'projectTimeRegisters', 'projects', 'layout', 'addOtherServiceRoute', 'selfPublishingStoreRoute',
            'selfPublishingUpdateRoute', 'selfPublishingDeleteRoute', 'selfPublishingAddFeedbackRoute',
            'selfPublishingDownloadFeedbackRoute', 'selfPublishingLearnersRoute', 'assignEditorRoute',
            'updateExpectedFinishRoute', 'updateStatusRoute', 'otherServiceDeleteRoute'));
    }

    public function saveProject( ProjectRequest $request, ProjectService $projectService )
    {
        return $projectService->saveProject($request);
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

    public function saveActivity( ProjectActivityRequest $request, ProjectService $projectService )
    {
        return $projectService->saveActivity($request);
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

    public function saveBook( $project_id, ProjectBookRequest $request, ProjectService $projectService )
    {
        $request->merge(['project_id' => $project_id]);
        $response = $projectService->saveBook($request);

        return response()->json($response);
    }

    public function deleteBook( $id )
    {
        ProjectBook::find($id)->delete();
        return response()->json();
    }

    /**
     * Add to correction or copy editing
     * @param $project_id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addOtherService($project_id, ProjectCopyEditingRequest $request, ProjectService $projectService)
    {
        if ($project = Project::find($project_id)) {

            $manuType =  $projectService->saveOtherService($request->merge([
                'user_id' => $project->user_id,
                'project_id' => $project_id
            ]));

            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag($manuType.' Manuscript added successfully.'),
                'alert_type' => 'success',
                'not-former-courses' => true
            ]);

        }

        return redirect()->back();
    }

    public function graphicWork( $project_id )
    {
        $project = Project::find($project_id);
        $layout = 'backend.layout';
        if (str_contains(request()->getHttpHost(), 'giutbok')) {
            $layout = 'giutbok.layout';
        }
        return view('backend.project.graphic-work', compact('project', 'layout'));
    }

    public function registration( $project_id )
    {
        $layout = 'backend.layout';
        if (str_contains(request()->getHttpHost(), 'giutbok')) {
            $layout = 'giutbok.layout';
        }
        $project = Project::find($project_id);
        return view('backend.project.registration', compact('project', 'layout'));
    }

    public function marketing( $project_id )
    {
        $layout = 'backend.layout';
        if (str_contains(request()->getHttpHost(), 'giutbok')) {
            $layout = 'giutbok.layout';
        }
        $project = Project::find($project_id);
        return view('backend.project.marketing', compact('project', 'layout'));
    }
}