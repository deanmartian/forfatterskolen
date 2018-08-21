<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\SurveyRequest;
use App\Repositories\Services\SurveyService;
use App\SurveyAnswer;
use App\SurveyQuestion;

class SurveyController extends Controller {

    /**
     * Storage for survey service
     * @var SurveyService
     */
    protected $surveyService;

    /**
     * SurveyController constructor.
     * @param SurveyService $surveyService
     */
    public function __construct(SurveyService $surveyService)
    {
        $this->surveyService = $surveyService;
    }

    /**
     * Display all of the surveys
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $surveys = $this->surveyService->getRecord();
        return view('backend.survey.index', compact('surveys'));
    }

    /**
     * Create new survey
     * @param SurveyRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(SurveyRequest $request)
    {
        if ($this->surveyService->store($request)) {
            return redirect()->route('admin.survey.index');
        }

        return redirect()->back();
    }

    /**
     * Display single survey
     * @param $id SurveyService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function show($id)
    {
        $survey = $this->surveyService->getRecord($id);
        if (!$survey) {
            return redirect()->route('admin.survey.index');
        }

        return view('backend.survey.show', compact('survey'));

    }

    /**
     * Update survey
     * @param $id
     * @param SurveyRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id, SurveyRequest $request)
    {
        if ($this->surveyService->getRecord($id)) {
            $this->surveyService->update($id, $request);
        }

        return redirect()->back();
    }

    /**
     * Delete a survey
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        if ($this->surveyService->getRecord($id)) {
            $this->surveyService->destroy($id);
            return redirect()->route('admin.survey.index');
        }
        return redirect()->back();
    }


    public function downloadAnswers($id)
    {
        $survey = $this->surveyService->getRecord($id);
        if(!$survey) {
            abort(404);
        }

        $excel          = \App::make('excel');
        $downloadList     = [];

        $header = [];

        $questionList = [];
        $answerList = [];
        foreach($survey->questions as $k=>$question) {
            $questionList[][$question->id] = $question->title;
            $header[] = $question->title;
            foreach($question->answers as $answer) {
                $answerList[$question->id][] = $answer->answer;
            }
        }
        $downloadList[] = $header;
        $row = array();
        $key = 0;

        header('Pragma: private');
        header('Cache-control: private, must-revalidate');
        header('Content-type: text/csv');
        $csvFileName = preg_replace("/[^A-Za-z0-9_-]/", '', str_replace(' ', '_', $survey->title));
        header('Content-Disposition: attachment; filename=' . $csvFileName . '.csv');


        $fp = fopen('php://output', 'w');
        $headers = array();

        foreach ($survey->questions as $question)
            $headers[] = iconv('UTF-8', 'WINDOWS-1252', $question->title);
        fputcsv($fp, $headers);

        foreach ($survey->getResponse() as $response)
        {
            $row = array();
            foreach ($survey->questions as $question)
            {
                $field = 'question_' . $question->id;
                $formatField = implode("\n",explode(',', $response->$field));
                $row[] = $formatField;
                //$row[] = explode(', ',$response->$field);
            }
            fputcsv($fp, $row);
        }


        fclose($fp);
        exit;
    }
}