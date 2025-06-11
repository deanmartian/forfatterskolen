<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\SolutionCreateRequest;
use App\Repositories\Services\SolutionService;

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
    public function index()
    {
        $solutions = $this->solutionService->getRecord();

        return view('backend.solution.index', compact('solutions'));
    }

    /**
     * Create new solution
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(SolutionCreateRequest $request)
    {
        if ($this->solutionService->store($request)) {
            return redirect()->route('admin.solution.index');
        }

        return redirect()->back();
    }

    /**
     * Update a solution
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id, SolutionCreateRequest $request)
    {
        if ($this->solutionService->getRecord($id)) {
            $this->solutionService->update($id, $request);
        }

        return redirect()->route('admin.solution.index');
    }

    /**
     * Delete a solution
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $this->solutionService->destroy($id);

        return redirect()->route('admin.solution.index');
    }
}
