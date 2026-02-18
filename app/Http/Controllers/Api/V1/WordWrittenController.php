<?php

namespace App\Http\Controllers\Api\V1;

use App\WordWritten;
use App\WordWrittenGoal;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WordWrittenController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $user = $this->apiUser($request);

        $words = WordWritten::where('user_id', $user->id)
            ->orderByDesc('date')
            ->get();

        $totalWords = $words->sum('words');

        return response()->json([
            'total_words' => $totalWords,
            'data' => $words->map(function ($w) {
                return [
                    'id' => $w->id,
                    'date' => $w->date,
                    'words' => $w->words,
                ];
            })->values(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $this->apiUser($request);

        $data = $request->validate([
            'date' => ['required', 'date'],
            'words' => ['required', 'integer', 'min:0'],
        ]);

        $existing = WordWritten::where('user_id', $user->id)
            ->where('date', $data['date'])
            ->first();

        if ($existing) {
            $existing->update(['words' => $data['words']]);
            $record = $existing;
        } else {
            $record = WordWritten::create([
                'user_id' => $user->id,
                'date' => $data['date'],
                'words' => $data['words'],
            ]);
        }

        return response()->json([
            'message' => 'Word count saved.',
            'data' => [
                'id' => $record->id,
                'date' => $record->date,
                'words' => $record->words,
            ],
        ], $existing ? 200 : 201);
    }

    public function goals(Request $request): JsonResponse
    {
        $user = $this->apiUser($request);

        $goals = WordWrittenGoal::where('user_id', $user->id)
            ->orderByDesc('from_date')
            ->get();

        return response()->json([
            'data' => $goals->map(function ($goal) use ($user) {
                return $this->formatGoal($goal, $user->id);
            })->values(),
        ]);
    }

    public function storeGoal(Request $request): JsonResponse
    {
        $user = $this->apiUser($request);

        $data = $request->validate([
            'from_date' => ['required', 'date'],
            'to_date' => ['required', 'date', 'after_or_equal:from_date'],
            'total_words' => ['required', 'integer', 'min:1'],
        ]);

        $goal = WordWrittenGoal::create([
            'user_id' => $user->id,
            'from_date' => $data['from_date'],
            'to_date' => $data['to_date'],
            'total_words' => $data['total_words'],
        ]);

        return response()->json([
            'message' => 'Goal created.',
            'data' => $this->formatGoal($goal, $user->id),
        ], 201);
    }

    public function updateGoal(Request $request, int $id): JsonResponse
    {
        $user = $this->apiUser($request);

        $goal = WordWrittenGoal::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$goal) {
            return $this->errorResponse('Goal not found.', 'not_found', 404);
        }

        $data = $request->validate([
            'from_date' => ['sometimes', 'date'],
            'to_date' => ['sometimes', 'date'],
            'total_words' => ['sometimes', 'integer', 'min:1'],
        ]);

        $goal->update($data);

        return response()->json([
            'message' => 'Goal updated.',
            'data' => $this->formatGoal($goal->fresh(), $user->id),
        ]);
    }

    public function deleteGoal(Request $request, int $id): JsonResponse
    {
        $user = $this->apiUser($request);

        $goal = WordWrittenGoal::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$goal) {
            return $this->errorResponse('Goal not found.', 'not_found', 404);
        }

        $goal->delete();

        return response()->json(['message' => 'Goal deleted.']);
    }

    public function goalStatistic(Request $request, int $id): JsonResponse
    {
        $user = $this->apiUser($request);

        $goal = WordWrittenGoal::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$goal) {
            return $this->errorResponse('Goal not found.', 'not_found', 404);
        }

        $wordsInPeriod = WordWritten::where('user_id', $user->id)
            ->whereBetween('date', [$goal->from_date, $goal->to_date])
            ->get();

        $totalWritten = $wordsInPeriod->sum('words');
        $dailyBreakdown = $wordsInPeriod->map(function ($w) {
            return [
                'date' => $w->date,
                'words' => $w->words,
            ];
        })->values();

        return response()->json([
            'goal' => $this->formatGoal($goal, $user->id),
            'total_written' => $totalWritten,
            'remaining' => max(0, $goal->total_words - $totalWritten),
            'progress_percentage' => $goal->total_words > 0
                ? round(($totalWritten / $goal->total_words) * 100, 1)
                : 0,
            'daily_breakdown' => $dailyBreakdown,
        ]);
    }

    private function formatGoal(WordWrittenGoal $goal, int $userId): array
    {
        $wordsWritten = WordWritten::where('user_id', $userId)
            ->whereBetween('date', [$goal->from_date, $goal->to_date])
            ->sum('words');

        return [
            'id' => $goal->id,
            'from_date' => $goal->from_date,
            'to_date' => $goal->to_date,
            'total_words' => $goal->total_words,
            'words_written' => $wordsWritten,
            'progress_percentage' => $goal->total_words > 0
                ? round(($wordsWritten / $goal->total_words) * 100, 1)
                : 0,
        ];
    }
}
