<?php

namespace App\Repositories\Services;

use App\Http\Requests\SurveyRequest;
use App\Survey;

class SurveyService
{
    /**
     * Store the solution model
     *
     * @var Survey
     */
    protected $survey;

    /**
     * SurveyService constructor.
     */
    public function __construct(Survey $survey)
    {
        $this->survey = $survey;
    }

    /**
     * @param  null  $id
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function getRecord($id = null, int $page = 15)
    {
        if ($id) {
            return $this->survey->find($id);
        }

        return $this->survey->paginate($page);
    }

    /**
     * Create new survey
     *
     * @param  $request  SurveyRequest
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public function store($request)
    {
        $requestData = $request->toArray();

        return $this->survey->create($requestData);
    }

    /**
     * Update a survey
     */
    public function update($id, $request): bool
    {
        $survey = $this->getRecord($id);
        $requestData = $request->toArray();

        return $survey->update($requestData);
    }

    /**
     * Delete a survey
     */
    public function destroy($id): bool
    {
        $survey = $this->getRecord($id);
        if ($survey) {
            $survey->forceDelete();
        }

        return false;
    }
}
