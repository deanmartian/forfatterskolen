<?php
namespace App\Repositories\Services;

use App\Solution;
use App\SolutionArticle;

class SolutionArticleService {

    /**
     * Store the solution article model
     * @var Solution
     */
    protected $solutionArticle;

    /**
     * Fields list
     * @var array
     */
    protected $fields = [
        'id'        => '',
        'title'     => '',
        'details'   => ''
    ];

    /**
     * SolutionArticleService constructor.
     * @param SolutionArticle $solutionArticle
     */
    public function __construct(SolutionArticle $solutionArticle)
    {
        $this->solutionArticle = $solutionArticle;
    }

    /**
     * Table fields
     * @return array
     */
    public function fields()
    {
        return $this->fields;
    }

    /**
     * @param null $id
     * @param int $page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function getRecord($id = NULL, $page = 15)
    {
        if ($id) {
            return $this->solutionArticle->find($id);
        }
        return $this->solutionArticle->paginate($page);
    }

    /**
     * Create new article
     * @param $solution_id
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function store($solution_id, array $data)
    {
        $data['solution_id'] = $solution_id;
        return $this->solutionArticle->create($data);
    }

    /**
     * Update article
     * @param $id
     * @param array $data
     * @return bool
     */
    public function update($id, array $data)
    {
        $solutionArticle = $this->getRecord($id);
        if ($solutionArticle) {
            return $solutionArticle->update($data);
        }
        return false;
    }

    /**
     * Delete the article
     * @param SolutionArticle $id
     * @return bool
     */
    public function destroy($id)
    {
        $solutionArticle = $this->getRecord($id);
        if ($solutionArticle) {
            $solutionArticle->forceDelete();
            return true;
        }
        return false;
    }

}