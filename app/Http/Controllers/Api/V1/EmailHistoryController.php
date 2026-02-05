<?php

namespace App\Http\Controllers\Api\V1;

use App\EmailHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmailHistoryController extends ApiController
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

        $learnerAssignmentManuscripts = $user->assignmentManuscripts->pluck('id');
        $learnerShopManuscriptsTaken = $user->shopManuscriptsTaken->pluck('id');
        $learnerCoursesTaken = $user->coursesTaken->pluck('id');
        $registeredWebinarLists = $user->registeredWebinars->pluck('id');
        $learnerInvoices = $user->invoices->pluck('id');

        $emailHistories = EmailHistory::query()->where(function ($query) use ($learnerAssignmentManuscripts) {
            $query->where('parent', 'LIKE', 'assignment-manuscripts%');
            $query->whereIn('parent_id', $learnerAssignmentManuscripts);
        })
            ->orWhere(function ($query) use ($learnerShopManuscriptsTaken) {
                $query->where('parent', 'LIKE', 'shop-manuscripts-taken%');
                $query->whereIn('parent_id', $learnerShopManuscriptsTaken);
            })
            ->orWhere(function ($query) use ($learnerCoursesTaken) {
                $query->where('parent', 'LIKE', 'courses-taken%');
                $query->whereIn('parent_id', $learnerCoursesTaken);
            })
            ->orWhere(function ($query) use ($registeredWebinarLists) {
                $query->where('parent', '=', 'webinar-registrant');
                $query->whereIn('parent_id', $registeredWebinarLists);
            })
            ->orWhere(function ($query) use ($user) {
                $query->where('parent', '=', 'learner');
                $query->where('parent_id', $user->id);
            })
            ->orWhere(function ($query) use ($user) {
                $query->where('parent', '=', 'free-manuscripts');
                $query->where('recipient', $user->email);
            })
            ->orWhere(function ($query) use ($learnerInvoices) {
                $query->where('parent', '=', 'invoice');
                $query->whereIn('parent_id', $learnerInvoices);
            })
            ->orWhere(function ($query) use ($user) {
                $query->where('parent', 'LIKE', 'copy-editing%');
                $query->where('recipient', $user->email);
            })
            ->orWhere(function ($query) use ($user) {
                $query->where('parent', 'LIKE', 'correction%');
                $query->where('recipient', $user->email);
            })
            ->orWhere(function ($query) use ($user) {
                $query->where('parent', 'LIKE', 'gift-purchase');
                $query->where('recipient', $user->email);
            })
            ->orWhere(function ($query) use ($user) {
                $query->where('recipient', $user->email);
            })
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'data' => $emailHistories->getCollection()
                ->map(fn (EmailHistory $history) => $this->formatHistory($history))
                ->values(),
            'meta' => [
                'current_page' => $emailHistories->currentPage(),
                'last_page' => $emailHistories->lastPage(),
                'per_page' => $emailHistories->perPage(),
                'total' => $emailHistories->total(),
            ],
        ]);
    }

    private function formatHistory(EmailHistory $history): array
    {
        return [
            'id' => $history->id,
            'subject' => $history->subject,
            'from_email' => $history->from_email,
            'message' => $history->message,
            'parent' => $history->parent,
            'parent_id' => $history->parent_id,
            'recipient' => $history->recipient,
            'recipient_id' => $history->recipient_id,
            'recipient_email' => $history->recipient_email,
            'track_code' => $history->track_code,
            'date_open' => $history->getRawOriginal('date_open'),
            'created_at' => $history->getRawOriginal('created_at'),
        ];
    }
}
