<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\SolutionCreateRequest;
use App\Repositories\Services\SolutionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SolutionController extends Controller
{
    /**
     * Storage of solution service
     *
     * @var SolutionService
     */
    protected $solutionService;

    /**
     * SolutionController constructor.
     */
    public function __construct(SolutionService $solutionService)
    {
        $this->solutionService = $solutionService;
    }

    /**
     * Display all solutions
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(): View
    {
        $solutions = $this->solutionService->getRecord();

        return view('backend.solution.index', compact('solutions'));
    }

    /**
     * Create new solution
     */
    public function store(SolutionCreateRequest $request): RedirectResponse
    {
        if ($this->solutionService->store($request)) {
            return redirect()->route('admin.solution.index');
        }

        return redirect()->back();
    }

    /**
     * Update a solution
     */
    public function update(int $id, SolutionCreateRequest $request): RedirectResponse
    {
        if ($this->solutionService->getRecord($id)) {
            $this->solutionService->update($id, $request);
        }

        return redirect()->route('admin.solution.index');
    }

    /**
     * Delete a solution
     */
    public function destroy($id): RedirectResponse
    {
        $this->solutionService->destroy($id);

        return redirect()->route('admin.solution.index');
    }
}
