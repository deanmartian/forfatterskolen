<?php

namespace App\Http\Controllers\Api\V1;

use App\Survey;
use App\SurveyAnswer;
use App\SurveyQuestion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SurveyController extends ApiController
{
    public function show(Request $request, int $id): JsonResponse
    {
        $user = $this->apiUser($request);

        $survey = Survey::with('questions')->find($id);

        if (!$survey) {
            return $this->errorResponse('Survey not found.', 'not_found', 404);
        }

        $existingAnswers = SurveyAnswer::where('user_id', $user->id)
            ->whereIn('survey_question_id', $survey->questions->pluck('id'))
            ->get()
            ->keyBy('survey_question_id');

        $hasTaken = $existingAnswers->isNotEmpty();

        return response()->json([
            'data' => [
                'id' => $survey->id,
                'title' => $survey->title,
                'description' => $survey->description,
                'start_date' => $survey->start_date,
                'end_date' => $survey->end_date,
                'has_taken' => $hasTaken,
                'questions' => $survey->questions->map(function ($q) use ($existingAnswers) {
                    return [
                        'id' => $q->id,
                        'title' => $q->title,
                        'question_type' => $q->question_type,
                        'option_name' => $q->option_name,
                        'my_answer' => $existingAnswers->has($q->id)
                            ? $existingAnswers[$q->id]->answer
                            : null,
                    ];
                })->values(),
            ],
        ]);
    }

    public function submit(Request $request, int $id): JsonResponse
    {
        $user = $this->apiUser($request);

        $survey = Survey::with('questions')->find($id);

        if (!$survey) {
            return $this->errorResponse('Survey not found.', 'not_found', 404);
        }

        $data = $request->validate([
            'answers' => ['required', 'array'],
            'answers.*.question_id' => ['required', 'exists:survey_question,id'],
            'answers.*.answer' => ['required', 'string'],
        ]);

        foreach ($data['answers'] as $answerData) {
            $question = SurveyQuestion::find($answerData['question_id']);
            if (!$question || $question->survey_id !== $survey->id) {
                continue;
            }

            SurveyAnswer::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'survey_question_id' => $answerData['question_id'],
                ],
                [
                    'answer' => $answerData['answer'],
                ]
            );
        }

        return response()->json(['message' => 'Survey submitted.']);
    }
}
