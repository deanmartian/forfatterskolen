<?php

namespace App\Repositories\Services;

use App\Http\Requests\SurveyQuestionRequest;
use App\Survey;
use App\SurveyQuestion;

class SurveyQuestionService
{
    /**
     * Store the solution model
     *
     * @var SurveyQuestion
     */
    protected $surveyQuestion;

    /**
     * SurveyService constructor.
     *
     * @param  SurveyQuestion  $survey
     */
    public function __construct(SurveyQuestion $surveyQuestion)
    {
        $this->surveyQuestion = $surveyQuestion;
    }

    /**
     * @param  null  $id
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function getRecord($id = null, int $page = 15)
    {
        if ($id) {
            return $this->surveyQuestion->find($id);
        }

        return $this->surveyQuestion->paginate($page);
    }

    public function findSurvey($survey_id)
    {
        $survey = Survey::find($survey_id);
        if (! $survey) {
            return false;
        }

        return $survey;
    }

    /**
     * Create new survey
     *
     * @param  $request  SurveyQuestionRequest
     * @param  $survey_id  Survey id
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public function store($survey_id, $request)
    {
        $requestData = $request->all();
        if (isset($requestData['option_name'])) {
            $requestData['option_name'] = json_encode($requestData['option_name']);
        }

        $survey = Survey::find($survey_id);

        return $survey->questions()->create($requestData);
    }

    /**
     * For displaying edit page
     *
     * @param  $survey_id  Survey
     */
    public function edit($survey_id, $id): bool
    {
        if ($this->findSurvey($survey_id) && $this->getRecord($id)) {
            return true;
        }

        return false;
    }

    /**
     * @param  $id  SurveyQuestion int
     * @param  $request  SurveyQuestionRequest
     */
    public function update($id, $request): bool
    {
        $requestData = $request->except('_token', '_method');
        $requestData['option_name'] = isset($requestData['option_name']) ?
            json_encode($requestData['option_name']) : null;

        $surveyQuestion = $this->getRecord($id);

        return $surveyQuestion->update($requestData);
    }

    /**
     * Delete a survey question
     */
    public function destroy($id): bool
    {
        $surveyQuestion = $this->getRecord($id);
        if ($surveyQuestion) {
            $surveyQuestion->forceDelete();
        }

        return false;
    }
}
