<?php

namespace App\Http\Controllers\Api\V1;

use App\SelfPublishing;
use App\SelfPublishingFeedback;
use App\SelfPublishingOrder;
use App\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SelfPublishingController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $user = $this->apiUser($request);

        $records = SelfPublishing::whereHas('learner', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
            ->with('learner')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'data' => $records->map(function ($sp) {
                return $this->formatSelfPublishing($sp);
            })->values(),
        ]);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $user = $this->apiUser($request);

        $record = SelfPublishing::whereHas('learner', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
            ->with(['learner', 'feedbacks'])
            ->find($id);

        if (!$record) {
            return $this->errorResponse('Record not found.', 'not_found', 404);
        }

        $data = $this->formatSelfPublishing($record);
        $data['feedbacks'] = $record->feedbacks ? $record->feedbacks->map(function ($f) {
            return [
                'id' => $f->id,
                'file' => $f->file,
                'is_approved' => (bool) ($f->is_approved ?? false),
                'created_at' => $f->created_at?->toIso8601String(),
            ];
        })->values() : [];

        return response()->json(['data' => $data]);
    }

    public function orders(Request $request): JsonResponse
    {
        $user = $this->apiUser($request);

        $orders = SelfPublishingOrder::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'data' => $orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'status' => $order->status,
                    'total_amount' => $order->total_amount,
                    'created_at' => $order->created_at?->toIso8601String(),
                ];
            })->values(),
        ]);
    }

    public function downloadFeedback(Request $request, int $id)
    {
        $user = $this->apiUser($request);

        $feedback = SelfPublishingFeedback::find($id);

        if (!$feedback) {
            return $this->errorResponse('Feedback not found.', 'not_found', 404);
        }

        $selfPublishing = SelfPublishing::whereHas('learner', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->find($feedback->self_publishing_id);

        if (!$selfPublishing) {
            return $this->errorResponse('Not authorized.', 'forbidden', 403);
        }

        $path = $feedback->file;
        if (!$path || !file_exists($path)) {
            return $this->errorResponse('File not found.', 'file_not_found', 404);
        }

        return response()->download($path);
    }

    private function formatSelfPublishing(SelfPublishing $sp): array
    {
        return [
            'id' => $sp->id,
            'type' => $sp->type,
            'status' => $sp->status,
            'manuscript' => $sp->manuscript,
            'editor_id' => $sp->editor_id,
            'expected_finish' => $sp->expected_finish,
            'is_locked' => (bool) ($sp->is_locked ?? false),
            'created_at' => $sp->created_at?->toIso8601String(),
        ];
    }
}
