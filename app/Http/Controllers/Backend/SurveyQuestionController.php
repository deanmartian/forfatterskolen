<?php

namespace App\Http\Controllers\Backend;

use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\SurveyQuestionRequest;
use App\Repositories\Services\SurveyQuestionService;
use App\Survey;
use App\SurveyQuestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SurveyQuestionController extends Controller
{
    /**
     * Storage for survey question service
     *
     * @var SurveyQuestion
     */
    protected $surveyQuestionService;

    /**
     * SurveyQuestionController constructor.
     *
     * @param  SurveyQuestion  $surveyQuestion
     */
    public function __construct(SurveyQuestionService $surveyQuestionService)
    {
        $this->surveyQuestionService = $surveyQuestionService;
    }

    /**
     * Create a question for the survey
     *
     * @param  $survey_id  Survey int
     */
    public function store($survey_id, SurveyQuestionRequest $request): RedirectResponse
    {
        if ($this->surveyQuestionService->store($survey_id, $request)) {
            return redirect()->route('admin.survey.show', $survey_id)
                ->with(['errors' => AdminHelpers::createMessageBag('Survey created successfully.'),
                    'alert_type' => 'success']);
        }

        return redirect()->back();
    }

    /**
     * Display the survey question edit page
     *
     * @param  $survey_id  Survey int
     * @param  $id  SurveyQuestion int
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($survey_id, $id): View
    {
        if (! $this->surveyQuestionService->edit($survey_id, $id)) {
            abort(404);
        }

        $surveyQuestion = $this->surveyQuestionService->getRecord($id);
        $survey = $this->surveyQuestionService->findSurvey($survey_id);

        return view('backend.survey.question.edit', compact('surveyQuestion', 'survey'));
    }

    /**
     * Update survey question
     *
     * @param  $survey_id  Survey int
     * @param  $id  SurveyQuestion int
     */
    public function update($survey_id, $id, SurveyQuestionRequest $request): RedirectResponse
    {
        if (! $this->surveyQuestionService->edit($survey_id, $id)) {
            abort(404);
        }

        $this->surveyQuestionService->update($id, $request);

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Survey updated successfully.'),
            'alert_type' => 'success']);
    }

    public function destroy($survey_id, $id): RedirectResponse
    {
        if (! $this->surveyQuestionService->edit($survey_id, $id)) {
            abort(404);
        }

        $this->surveyQuestionService->destroy($id);

        return redirect()->route('admin.survey.show', $survey_id)
            ->with(['errors' => AdminHelpers::createMessageBag('Survey question deleted successfully.'),
                'alert_type' => 'success']);
    }
}
