<?php

namespace App\Http\Controllers\Backend;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddWritingGroupRequest;
use App\Repositories\Services\WritingGroupService;

class WritingGroupController extends Controller
{
    /**
     * Variable storage of the service
     *
     * @var WritingGroupService
     */
    protected $writingGroupService;

    /**
     * WritingGroupController constructor.
     */
    public function __construct(WritingGroupService $writingGroupService)
    {
        // middleware to check if admin have access to the faq page
        $this->middleware('checkPageAccess:10');
        $this->writingGroupService = $writingGroupService;
    }

    /**
     * Display all Writing group
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(): View
    {
        $writingGroups = $this->writingGroupService->getRecord();

        return view('backend.writing-group.index', compact('writingGroups'));
    }

    /**
     * Create page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(): View
    {
        $writingGroup = $this->writingGroupService->fields();
        $learners = AdminHelpers::getLearnerList();

        return view('backend.writing-group.create', compact('writingGroup', 'learners'));
    }

    /**
     * Insert writing group
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(AddWritingGroupRequest $request): RedirectResponse
    {
        $this->writingGroupService->store($request);

        return redirect()->route('admin.writing-group.index');
    }

    /**
     * @param  int  $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(int $id): View
    {
        $writingGroup = $this->writingGroupService->getRecord($id);
        $learners = AdminHelpers::getLearnerList();

        return view('backend.writing-group.edit', compact('writingGroup', 'learners'));
    }

    /**
     * Update writing group
     *
     * @param  $id  int
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id, AddWritingGroupRequest $request): RedirectResponse
    {
        $this->writingGroupService->update($id, $request);

        return redirect()->route('admin.writing-group.edit', $id);
    }

    /**
     * Delete writing group
     *
     * @param  $id  int
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id): RedirectResponse
    {
        $this->writingGroupService->destroy($id);

        return redirect()->route('admin.writing-group.index');
    }
}
