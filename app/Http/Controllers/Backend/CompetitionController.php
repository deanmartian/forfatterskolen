<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddCompetitionRequest;
use App\Repositories\Services\CompetitionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class CompetitionController extends Controller implements HasMiddleware
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

        $this->competitionService = $competitionService;
    }

    public static function middleware(): array
    {
        return [
            'checkPageAccess:10',
        ];
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
     */
    public function store(AddCompetitionRequest $request): RedirectResponse
    {
        $this->competitionService->store($request);

        return redirect()->route('admin.competition.index');
    }

    /**
     * Update a competition
     */
    public function update($id, AddCompetitionRequest $request): RedirectResponse
    {
        $this->competitionService->update($id, $request);

        return redirect()->route('admin.competition.index');
    }

    /**
     * Delete a competition
     */
    public function destroy($id): RedirectResponse
    {
        $this->competitionService->destroy($id);

        return redirect()->route('admin.competition.index');
    }
}
