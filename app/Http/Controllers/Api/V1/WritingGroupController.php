<?php

namespace App\Http\Controllers\Api\V1;

use App\WritingGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WritingGroupController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $user = $this->apiUser($request);

        $groups = WritingGroup::where('contact_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'data' => $groups->map(function ($group) {
                return $this->formatGroup($group);
            })->values(),
        ]);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $user = $this->apiUser($request);

        $group = WritingGroup::where('id', $id)
            ->where('contact_id', $user->id)
            ->first();

        if (!$group) {
            return $this->errorResponse('Writing group not found.', 'not_found', 404);
        }

        return response()->json([
            'data' => $this->formatGroup($group),
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $user = $this->apiUser($request);

        $group = WritingGroup::where('id', $id)
            ->where('contact_id', $user->id)
            ->first();

        if (!$group) {
            return $this->errorResponse('Writing group not found.', 'not_found', 404);
        }

        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'next_meeting' => ['sometimes', 'nullable', 'date'],
        ]);

        $group->update($data);

        return response()->json([
            'message' => 'Writing group updated.',
            'data' => $this->formatGroup($group->fresh()),
        ]);
    }

    private function formatGroup(WritingGroup $group): array
    {
        return [
            'id' => $group->id,
            'name' => $group->name,
            'description' => $group->description,
            'group_photo' => $group->group_photo,
            'next_meeting' => $group->next_meeting,
            'created_at' => $group->created_at?->toIso8601String(),
        ];
    }
}
