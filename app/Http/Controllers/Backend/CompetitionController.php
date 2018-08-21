<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddCompetitionRequest;
use App\Repositories\Services\CompetitionService;

class CompetitionController extends Controller {

    /**
     * Service where methods is stored for this controller
     * @var CompetitionService
     */
    protected $competitionService;

    /**
     * CompetitionController constructor.
     * @param CompetitionService $competitionService
     */
    public function __construct(CompetitionService $competitionService)
    {
        // middleware to check if admin have access to the faq page
        $this->middleware('checkPageAccess:10');
        $this->competitionService = $competitionService;
    }

    /**
     * Display all competitions
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $competitions = $this->competitionService->getRecord();
        return view('backend.competition.index', compact('competitions'));
    }

    /**
     * Create new competition
     * @param AddCompetitionRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(AddCompetitionRequest $request)
    {
        $this->competitionService->store($request);
        return redirect()->route('admin.competition.index');
    }

    /**
     * Update a competition
     * @param $id
     * @param AddCompetitionRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id, AddCompetitionRequest $request)
    {
        $this->competitionService->update($id, $request);
        return redirect()->route('admin.competition.index');
    }

    /**
     * Delete a competition
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $this->competitionService->destroy($id);
        return redirect()->route('admin.competition.index');
    }

}