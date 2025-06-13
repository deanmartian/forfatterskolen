<?php

namespace App\Repositories\Services;

use App\Solution;
use App\SolutionArticle;
use Illuminate\Database\Eloquent\Model;

class SolutionArticleService
{
    /**
     * Store the solution article model
     *
     * @var Solution
     */
    protected $solutionArticle;

    /**
     * Fields list
     *
     * @var array
     */
    protected $fields = [
        'id' => '',
        'title' => '',
        'details' => '',
    ];

    /**
     * SolutionArticleService constructor.
     */
    public function __construct(SolutionArticle $solutionArticle)
    {
        $this->solutionArticle = $solutionArticle;
    }

    /**
     * Table fields
     */
    public function fields(): array
    {
        return $this->fields;
    }

    /**
     * @param  null  $id
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function getRecord($id = null, int $page = 15)
    {
        if ($id) {
            return $this->solutionArticle->find($id);
        }

        return $this->solutionArticle->paginate($page);
    }

    /**
     * Create new article
     */
    public function store($solution_id, array $data): Model
    {
        $data['solution_id'] = $solution_id;

        return $this->solutionArticle->create($data);
    }

    /**
     * Update article
     */
    public function update($id, array $data): bool
    {
        $solutionArticle = $this->getRecord($id);
        if ($solutionArticle) {
            return $solutionArticle->update($data);
        }

        return false;
    }

    /**
     * Delete the article
     */
    public function destroy(SolutionArticle $id): bool
    {
        $solutionArticle = $this->getRecord($id);
        if ($solutionArticle) {
            $solutionArticle->forceDelete();

            return true;
        }

        return false;
    }
}
