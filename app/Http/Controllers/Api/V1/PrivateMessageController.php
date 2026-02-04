<?php

namespace App\Http\Controllers\Api\V1;

use App\PrivateMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PrivateMessageController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $user = $this->apiUser($request);

        if (! $user) {
            return $this->errorResponse('Missing or invalid token.', 'unauthorized', 401);
        }

        $perPage = (int) $request->query('per_page', 10);
        $perPage = $perPage > 0 ? min($perPage, 50) : 10;

        if ($user->isDisabled) {
            return response()->json([
                'data' => [],
                'meta' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => $perPage,
                    'total' => 0,
                ],
            ]);
        }

        $messages = $user->messages()
            ->with('sender')
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'data' => $messages->getCollection()
                ->map(fn (PrivateMessage $message) => $this->formatMessage($message))
                ->values(),
            'meta' => [
                'current_page' => $messages->currentPage(),
                'last_page' => $messages->lastPage(),
                'per_page' => $messages->perPage(),
                'total' => $messages->total(),
            ],
        ]);
    }

    private function formatMessage(PrivateMessage $message): array
    {
        return [
            'id' => $message->id,
            'message' => $message->message,
            'from_user' => $message->sender ? [
                'id' => $message->sender->id,
                'name' => $message->sender->name,
            ] : null,
            'created_at' => $message->getRawOriginal('created_at'),
        ];
    }
}
