<?php

namespace App\Http\Controllers\Backend;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddCompetitionRequest;
use App\Repositories\Services\CompetitionService;

class CompetitionController extends Controller
{
    /**
     * Service where methods is stored for this controller
     *
     * @var CompetitionService
     */
    protected $competitionService;

    /**
     * CompetitionController constructor.
     */
    public function __construct(CompetitionService $competitionService)
    {
        // middleware to check if admin have access to the faq page
        $this->middleware('checkPageAccess:10');
        $this->competitionService = $competitionService;
    }

    /**
     * Display all competitions
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(): View
    {
        $competitions = $this->competitionService->getRecord();

        return view('backend.competition.index', compact('competitions'));
    }

    /**
     * Create new competition
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(AddCompetitionRequest $request): RedirectResponse
    {
        $this->competitionService->store($request);

        return redirect()->route('admin.competition.index');
    }

    /**
     * Update a competition
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id, AddCompetitionRequest $request): RedirectResponse
    {
        $this->competitionService->update($id, $request);

        return redirect()->route('admin.competition.index');
    }

    /**
     * Delete a competition
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id): RedirectResponse
    {
        $this->competitionService->destroy($id);

        return redirect()->route('admin.competition.index');
    }
}
