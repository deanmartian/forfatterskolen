<?php

namespace App\Http\Controllers\Backend;


use App\Contract;
use App\ContractTemplate;
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
use App\ProjectGraphicWork;
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
        $saveGraphicRoute = 'admin.project.save-graphic-work';
        $deleteGraphicRoute = 'admin.project.delete-graphic-work';
        if (AdminHelpers::isGiutbokPage()) {
            $layout = 'giutbok.layout';
            $saveGraphicRoute = 'g-admin.project.save-graphic-work';
            $deleteGraphicRoute = 'g-admin.project.delete-graphic-work';
        }
        $covers = ProjectGraphicWork::cover()->where('project_id', $project_id)->get();
        $barCodes = ProjectGraphicWork::barcode()->where('project_id', $project_id)->get();
        $rewriteScripts = ProjectGraphicWork::rewriteScripts()->where('project_id', $project_id)->get();
        $trialPages = ProjectGraphicWork::trialPage()->where('project_id', $project_id)->get();
        $sampleBookPDFs = ProjectGraphicWork::sampleBookPdf()->where('project_id', $project_id)->get();

        return view('backend.project.graphic-work', compact('project', 'layout', 'saveGraphicRoute',
            'deleteGraphicRoute', 'covers', 'barCodes', 'rewriteScripts', 'trialPages', 'sampleBookPDFs'));
    }

    public function saveGraphicWork( $project_id, Request $request, ProjectService $projectService )
    {
        $request->merge(['project_id' => $project_id]);

        // create graphic work folder first
        AdminHelpers::createDirectory('storage/project-graphic-work');

        switch ($request->type) {
            case 'cover':
                $this->validate($request, ['cover' => 'required|mimes:jpeg,jpg,png,gif']);
                break;

            case 'barcode':
                $this->validate($request, ['barcode' => 'required|mimes:jpeg,jpg,png,gif']);
                break;

            case 'rewrite-script':
                $this->validate($request, ['rewrite_script' => 'required|mimes:pdf']);
                break;

            case 'trial-page':
                $this->validate($request, ['trial_page' => 'required|mimes:jpeg,jpg,png,gif']);
                break;

            case 'sample-book-pdf':
                $this->validate($request, ['sample_book_pdf' => 'required|mimes:pdf']);
                break;
        }

        $projectService->saveGraphicWorks($request);

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag(ucfirst(str_replace('-',' ', $request->type)) . ' saved successfully.'),
                'alert_type' => 'success']);
    }

    public function deleteGraphicWork( $project_id, $graphic_work_id )
    {
        $graphicWork = ProjectGraphicWork::find($graphic_work_id);
        $type = $graphicWork->type;
        $graphicWork->delete();

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag(ucfirst(str_replace('-',' ', $type)) . ' delete successfully.'),
                'alert_type' => 'success']);
    }

    public function registration( $project_id )
    {
        $layout = 'backend.layout';
        if (AdminHelpers::isGiutbokPage()) {
            $layout = 'giutbok.layout';
        }
        $project = Project::find($project_id);
        return view('backend.project.registration', compact('project', 'layout'));
    }

    public function marketing( $project_id )
    {
        $layout = 'backend.layout';
        if (AdminHelpers::isGiutbokPage()) {
            $layout = 'giutbok.layout';
        }
        $project = Project::find($project_id);
        return view('backend.project.marketing', compact('project', 'layout'));
    }

    /**
     * @param $project_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function contract( $project_id )
    {
        $layout = 'backend.layout';
        $uploadContractRoute = 'admin.project.contract-upload';
        $createContractRoute = 'admin.project.contract-create';
        $signedUploadRoute = 'admin.project.contract-signed-upload';
        $contractShowRoute = 'admin.project.contract-show';
        $contractEditRoute = 'admin.project.contract-edit';
        if (AdminHelpers::isGiutbokPage()) {
            $layout = 'giutbok.layout';
            $uploadContractRoute = 'g-admin.project.contract-upload';
            $createContractRoute = 'g-admin.project.contract-create';
            $signedUploadRoute = 'g-admin.project.contract-signed-upload';
            $contractShowRoute = 'g-admin.project.contract-show';
            $contractEditRoute = 'g-admin.project.contract-edit';
        }

        $project = Project::find($project_id);
        $contracts = Contract::whereNotNull('project_id')->paginate(10);

        return view('backend.project.contract.index', compact('project', 'layout', 'contracts',
            'uploadContractRoute', 'createContractRoute', 'signedUploadRoute', 'contractShowRoute', 'contractEditRoute'));
    }

    /**
     * @param $project_id
     * @param Request $request
     * @param ProjectService $projectService
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function uploadContract( $project_id, Request $request, ProjectService $projectService )
    {
        $request->merge([
            'project_id' => $project_id,
            'title' => 'Contract'
        ]);
        $projectService->uploadContract($request);

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag('Contract uploaded successfully.'),
                'alert_type' => 'success']);

    }

    public function uploadSignedContract( $project_id, $contract_id, Request $request, ProjectService $projectService )
    {
        $request->merge([
            'id' => $contract_id
        ]);
        $projectService->uploadContract($request);

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag('Signed contract uploaded successfully.'),
                'alert_type' => 'success']);
    }

    public function createContract( $project_id )
    {
        $route = route('admin.project.contract-store', $project_id);
        $action = 'create';
        $contract = [
            'title' => '',
            'details' => '',
            'signature' => '',
            'signature_label' => 'Signature',
            'end_date' => null,
            'is_file' => ''
        ];
        $title = 'Create Contract';
        $templates = ContractTemplate::where('show_in_project', 1)->get();
        $backRoute = route('admin.project.contract', $project_id);
        $layout = 'backend.layout';
        if (AdminHelpers::isGiutbokPage()) {
            $backRoute = route('g-admin.project.contract', $project_id);
            $layout = 'giutbok.layout';
            $route = route('g-admin.project.contract-store', $project_id);
        }
        return view('backend.contract.form', compact('route', 'action', 'contract', 'title', 'templates', 'backRoute', 'layout'));
    }

    public function storeContract( $project_id, Request $request, ProjectService $projectService )
    {
        $contract = $projectService->saveContract( $request->merge(['project_id' => $project_id]) );

        $route = 'admin.project.contract-edit';
        if (AdminHelpers::isGiutbokPage()) {
            $route = 'g-admin.project.contract-edit';
        }

        return redirect(route($route, [$project_id, $contract->id]))
            ->with(['errors' => AdminHelpers::createMessageBag('Contract saved successfully.'),
                'alert_type' => 'success']);
    }

    /**
     * @param $project_id
     * @param $contract_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function editContract( $project_id, $contract_id )
    {
        $contract = Contract::findOrFail($contract_id)->toArray();

        if ($contract['signature']) {
            return redirect()->route('admin.project.contract-show', $contract['id']);
        }

        $action = 'edit';
        $title = 'Edit ' . $contract['title'];
        $backRoute = route('admin.project.contract', $project_id);
        $route = route('admin.project.contract-update', [$project_id, $contract['id']]);
        $layout = 'backend.layout';
        if (AdminHelpers::isGiutbokPage()) {
            $backRoute = route('g-admin.project.contract', $project_id);
            $layout = 'giutbok.layout';
            $route = route('g-admin.project.contract-update', [$project_id, $contract['id']]);
        }
        return view('backend.contract.form', compact('route', 'action', 'contract', 'title', 'backRoute',
            'layout'));
    }

    /**
     * @param $project_id
     * @param $contract_id
     * @param Request $request
     * @param ProjectService $projectService
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function updateContract( $project_id, $contract_id, Request $request, ProjectService $projectService )
    {
        $projectService->saveContract( $request, $contract_id);
        $route = 'admin.project.contract-edit';
        if (AdminHelpers::isGiutbokPage()) {
            $route = 'g-admin.project.contract-edit';
        }

        return redirect(route($route, [$project_id, $contract_id]))
            ->with(['errors' => AdminHelpers::createMessageBag('Contract saved successfully.'),
                'alert_type' => 'success']);
    }

    /**
     * @param $project_id
     * @param $contract_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showContract( $project_id, $contract_id )
    {
        $contract = Contract::findOrFail($contract_id);
        $backRoute = route('admin.project.contract', $project_id);

        $layout = 'backend.layout';
        if (AdminHelpers::isGiutbokPage()) {
            $backRoute = route('g-admin.project.contract', $project_id);
            $layout = 'giutbok.layout';
        }

        return view('backend.contract.show', compact('contract', 'backRoute', 'layout'));
    }
}