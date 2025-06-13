<?php

namespace App\Http\Controllers\Backend;

use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\SurveyRequest;
use App\Repositories\Services\SurveyService;
use App\Survey;
use App\SurveyAnswer;
use App\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SurveyController extends Controller
{
    /**
     * Storage for survey service
     *
     * @var SurveyService
     */
    protected $surveyService;

    /**
     * SurveyController constructor.
     */
    public function __construct(SurveyService $surveyService)
    {
        $this->surveyService = $surveyService;
    }

    /**
     * Display all of the surveys
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(): View
    {
        $surveys = $this->surveyService->getRecord();

        return view('backend.survey.index', compact('surveys'));
    }

    /**
     * Create new survey
     */
    public function store(SurveyRequest $request): RedirectResponse
    {
        if ($this->surveyService->store($request)) {
            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Survey created successfully.'),
                'alert_type' => 'success',
            ]);
        }

        return redirect()->back();
    }

    /**
     * Display single survey
     *
     * @param  $id  SurveyService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function show($id)
    {
        $survey = $this->surveyService->getRecord($id);
        if (! $survey) {
            return redirect()->route('admin.survey.index');
        }

        return view('backend.survey.show', compact('survey'));

    }

    /**
     * Update survey
     */
    public function update($id, SurveyRequest $request): RedirectResponse
    {
        if ($this->surveyService->getRecord($id)) {
            $this->surveyService->update($id, $request);
        }

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Survey updated successfully.'),
            'alert_type' => 'success',
        ]);
    }

    public function updateDate($id, Request $request): RedirectResponse
    {
        if ($this->surveyService->getRecord($id)) {
            $this->surveyService->update($id, $request);
        }

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Survey updated successfully.'),
            'alert_type' => 'success',
        ]);
    }

    /**
     * Delete a survey
     */
    public function destroy($id): RedirectResponse
    {
        if ($this->surveyService->getRecord($id)) {
            $this->surveyService->destroy($id);

            return redirect()->route('admin.survey.index');
        }

        return redirect()->back();
    }

    public function answers($id)
    {
        if ($survey = Survey::find($id)) {
            $answers = $survey->answers;
            $questions = $survey->questions()->with('answers')->get();

            return view('backend.survey.answers', compact('survey', 'questions', 'answers'));
        }

        return redirect()->route('admin.survey.index');
    }

    public function downloadAnswers($id)
    {
        $survey = $this->surveyService->getRecord($id);
        if (! $survey) {
            abort(404);
        }

        $excel = \App::make('excel');
        $questions = $survey->questions;
        $downloadList = [];
        $questionList = ['Learner ID'];
        $answerList = [];

        // add questions on the first row
        foreach ($questions as $qk => $question) {
            array_push($questionList, $question->title);
        }

        // get the answers grouped by user
        $surveyAnswers = SurveyAnswer::where('survey_id', $id)->groupBy('user_id')->get();
        foreach ($surveyAnswers as $answer) {
            $storeAnswerWithUser = [$answer->user->id];
            $searchByGroupedUser = SurveyAnswer::where(['survey_id' => $id, 'user_id' => $answer->user_id])
                ->get()->toArray();
            foreach ($searchByGroupedUser as $search) {
                $result = json_decode($search['answer']);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $result = implode(', ', (array) $result);
                } else {
                    $result = $search['answer'];
                }
                array_push($storeAnswerWithUser, $result);
            }

            /*
             * if user/learner is not required/displayed on first
             * $searchByGroupedUser = SurveyAnswer::where(['survey_id' => $id, 'user_id' => $answer->user_id])
                ->get()->map(function($data) {
                $result = json_decode($data['answer']);
                $user = User::find($data['user_id']);
                if (json_last_error() === JSON_ERROR_NONE) {
                    return implode(", ", (array)$result);
                }
                    return $data['answer'];

            })->toArray();
            $answerList[] = $searchByGroupedUser;*/
            $answerList[] = $storeAnswerWithUser;
        }
        $downloadList[] = $questionList;
        $downloadList = array_merge($downloadList, $answerList);

        $excel->create($survey->title.' Answers', function ($excel) use ($downloadList) {

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function ($sheet) use ($downloadList) {
                // prevent inserting an empty first row
                $sheet->fromArray($downloadList, null, 'A1', false, false);
            });
        })->download('xlsx');
    }
}
