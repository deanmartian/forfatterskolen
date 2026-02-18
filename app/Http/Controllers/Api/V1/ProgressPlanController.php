<?php

namespace App\Http\Controllers\Api\V1;

use App\Project;
use App\ProjectRoadmapStep;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProgressPlanController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $user = $this->apiUser($request);

        $project = Project::where('user_id', $user->id)
            ->where('is_standard', 1)
            ->first();

        if (!$project) {
            $project = Project::where('user_id', $user->id)
                ->orderByDesc('created_at')
                ->first();
        }

        if (!$project) {
            return response()->json([
                'data' => null,
                'message' => 'No project found.',
            ]);
        }

        $steps = ProjectRoadmapStep::where('project_id', $project->id)
            ->orderBy('step_order')
            ->get();

        return response()->json([
            'project' => [
                'id' => $project->id,
                'name' => $project->name,
            ],
            'data' => $steps->map(function ($step) {
                return $this->formatStep($step);
            })->values(),
        ]);
    }

    public function show(Request $request, int $step): JsonResponse
    {
        $user = $this->apiUser($request);

        $project = Project::where('user_id', $user->id)
            ->where('is_standard', 1)
            ->first();

        if (!$project) {
            $project = Project::where('user_id', $user->id)
                ->orderByDesc('created_at')
                ->first();
        }

        if (!$project) {
            return $this->errorResponse('No project found.', 'not_found', 404);
        }

        $roadmapStep = ProjectRoadmapStep::where('project_id', $project->id)
            ->where('step_order', $step)
            ->first();

        if (!$roadmapStep) {
            return $this->errorResponse('Step not found.', 'not_found', 404);
        }

        return response()->json([
            'project' => [
                'id' => $project->id,
                'name' => $project->name,
            ],
            'data' => $this->formatStep($roadmapStep),
        ]);
    }

    public function uploadManuscript(Request $request): JsonResponse
    {
        $user = $this->apiUser($request);

        $data = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'manuscript' => ['required', 'file', 'mimes:doc,docx,pdf'],
        ]);

        $project = Project::where('id', $data['project_id'])
            ->where('user_id', $user->id)
            ->first();

        if (!$project) {
            return $this->errorResponse('Project not found.', 'not_found', 404);
        }

        $file = $request->file('manuscript');
        $destinationPath = 'storage/progress-plan-manuscripts/';
        $fileName = time() . '_' . $file->getClientOriginalName();
        $file->move($destinationPath, $fileName);

        return response()->json([
            'message' => 'Manuscript uploaded.',
            'file' => $destinationPath . $fileName,
        ], 201);
    }

    private function formatStep(ProjectRoadmapStep $step): array
    {
        return [
            'id' => $step->id,
            'step_order' => $step->step_order,
            'title' => $step->title ?? null,
            'description' => $step->description ?? null,
            'status' => $step->status ?? null,
            'is_completed' => (bool) ($step->is_completed ?? false),
            'created_at' => $step->created_at?->toIso8601String(),
        ];
    }
}
