<?php

namespace App\Http\Controllers\Backend;

use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddWritingGroupRequest;
use App\Repositories\Services\WritingGroupService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class WritingGroupController extends Controller implements HasMiddleware
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

        $this->writingGroupService = $writingGroupService;
    }

    public static function middleware(): array
    {
        return [
            'checkPageAccess:10',
        ];
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
     */
    public function store(AddWritingGroupRequest $request): RedirectResponse
    {
        $this->writingGroupService->store($request);

        return redirect()->route('admin.writing-group.index');
    }

    /**
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
     */
    public function destroy($id): RedirectResponse
    {
        $this->writingGroupService->destroy($id);

        return redirect()->route('admin.writing-group.index');
    }
}
