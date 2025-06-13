<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\SolutionArticleCreateRequest;
use App\Repositories\Services\SolutionArticleService;
use App\Repositories\Services\SolutionService;
use App\Solution;
use App\SolutionArticle;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SolutionArticleController extends Controller
{
    /**
     * @var SolutionArticleService
     */
    protected $solutionArticleService;

    /**
     * @var SolutionService
     */
    protected $solutionService;

    /**
     * SolutionArticleController constructor.
     */
    public function __construct(SolutionArticleService $solutionArticleService, SolutionService $solutionService)
    {
        $this->solutionArticleService = $solutionArticleService;
        $this->solutionService = $solutionService;
    }

    /**
     * Display the articles based on the selected solution
     *
     * @param  SolutionService  $solutionService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index($solution_id)
    {
        $solution = $this->solutionService->getRecord($solution_id);
        if ($solution) {
            $articles = $solution->articles;

            return view('backend.solution.article.index', compact('solution', 'articles'));
        }

        return redirect()->route('admin.solution.index');
    }

    /**
     * Display the create article page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create($solution_id): View
    {
        $article = $this->solutionArticleService->fields();

        return view('backend.solution.article.create', compact('solution_id', 'article'));
    }

    /**
     * Create new article
     */
    public function store(Solution $solution_id, SolutionArticleCreateRequest $request): RedirectResponse
    {
        $solution = $this->solutionService->getRecord($solution_id);
        if ($solution) {
            $this->solutionArticleService->store($solution_id, $request->all());

            return redirect()->route('admin.solution-article.index', $solution_id);
        }

        return redirect()->route('admin.solution.index');
    }

    /**
     * Display edit article page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit(Solution $solution_id, SolutionArticle $id)
    {
        $solution = $this->solutionService->getRecord($solution_id);
        $article = $this->solutionArticleService->getRecord($id);
        if ($solution && $article) {
            $article = $article->toArray();

            return view('backend.solution.article.edit', compact('solution', 'article'));
        }

        return redirect()->route('admin.solution-article.index', $solution_id);
    }

    /**
     * Update the article
     */
    public function update(Solution $solution_id, SolutionArticle $id, SolutionArticleCreateRequest $request): RedirectResponse
    {
        $solution = $this->solutionService->getRecord($solution_id);
        $article = $this->solutionArticleService->getRecord($id);

        if ($solution && $article) {
            $this->solutionArticleService->update($id, $request->except('_token'));

            return redirect()->route('admin.solution-article.edit', ['solution_id' => $solution_id, 'id' => $id]);
        }

        return redirect()->route('admin.solution-article.index', $solution_id);
    }

    /**
     * Delete the solution article
     */
    public function destroy(Solution $solution_id, SolutionArticle $id): RedirectResponse
    {
        $solution = $this->solutionService->getRecord($solution_id);
        $article = $this->solutionArticleService->getRecord($id);

        if ($solution && $article) {
            $this->solutionArticleService->destroy($id);
        }

        return redirect()->route('admin.solution-article.index', $solution_id);
    }
}
