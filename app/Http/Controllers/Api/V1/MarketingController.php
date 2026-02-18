<?php

namespace App\Http\Controllers\Api\V1;

use App\MarketingPlan;
use App\MarketingPlanQuestion;
use App\MarketingPlanQuestionAnswer;
use App\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MarketingController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $user = $this->apiUser($request);

        $projectIds = Project::where('user_id', $user->id)->pluck('id');

        $plans = MarketingPlan::whereIn('project_id', $projectIds)
            ->with('project')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'data' => $plans->map(function ($plan) {
                return $this->formatPlan($plan);
            })->values(),
        ]);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $user = $this->apiUser($request);

        $plan = MarketingPlan::with(['project', 'questions.answers' => function ($q) use ($user) {
            $q->where('user_id', $user->id);
        }])->find($id);

        if (!$plan || !$plan->project || $plan->project->user_id !== $user->id) {
            return $this->errorResponse('Marketing plan not found.', 'not_found', 404);
        }

        return response()->json([
            'data' => $this->formatPlanDetailed($plan),
        ]);
    }

    public function saveAnswer(Request $request, int $projectId): JsonResponse
    {
        $user = $this->apiUser($request);

        $project = Project::where('id', $projectId)
            ->where('user_id', $user->id)
            ->first();

        if (!$project) {
            return $this->errorResponse('Project not found.', 'not_found', 404);
        }

        $data = $request->validate([
            'question_id' => ['required', 'exists:marketing_plan_questions,id'],
            'answer' => ['required', 'string'],
        ]);

        MarketingPlanQuestionAnswer::updateOrCreate(
            [
                'user_id' => $user->id,
                'marketing_plan_question_id' => $data['question_id'],
            ],
            [
                'answer' => $data['answer'],
            ]
        );

        return response()->json(['message' => 'Answer saved.']);
    }

    private function formatPlan(MarketingPlan $plan): array
    {
        return [
            'id' => $plan->id,
            'title' => $plan->title ?? null,
            'project' => $plan->project ? [
                'id' => $plan->project->id,
                'name' => $plan->project->name,
            ] : null,
            'created_at' => $plan->created_at?->toIso8601String(),
        ];
    }

    private function formatPlanDetailed(MarketingPlan $plan): array
    {
        $base = $this->formatPlan($plan);
        $base['questions'] = $plan->questions->map(function ($q) {
            $answer = $q->answers->first();
            return [
                'id' => $q->id,
                'question' => $q->question,
                'type' => $q->type ?? null,
                'my_answer' => $answer ? $answer->answer : null,
            ];
        })->values();

        return $base;
    }
}
