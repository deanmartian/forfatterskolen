<?php
namespace App\Repositories\Services;

use App\Http\Requests\SurveyRequest;
use App\Survey;

class SurveyService {

    /**
     * Store the solution model
     * @var Survey
     */
    protected $survey;

    /**
     * SurveyService constructor.
     * @param Survey $survey
     */
    public function __construct(Survey $survey)
    {
        $this->survey = $survey;
    }

    /**
     * @param null $id
     * @param int $page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function getRecord($id = NULL, $page = 15)
    {
        if ($id) {
            return $this->survey->find($id);
        }
        return $this->survey->paginate($page);
    }

    /**
     * Create new survey
     * @param $request SurveyRequest
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public function store($request)
    {
        $requestData = $request->toArray();
        return $this->survey->create($requestData);
    }

    /**
     * Update a survey
     * @param $id
     * @param SurveyRequest $request
     * @return bool
     */
    public function update($id, $request)
    {
        $survey = $this->getRecord($id);
        $requestData = $request->toArray();
        return $survey->update($requestData);
    }

    /**
     * Delete a survey
     * @param $id
     * @return bool
     */
    public function destroy($id)
    {
        $survey = $this->getRecord($id);
        if ($survey) {
            $survey->forceDelete();
        }
        return false;
    }

}