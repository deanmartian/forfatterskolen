<?php

namespace App\Services\AdOs;

use App\Models\AdOs\AdActionLog;

class AdActionLogService
{
    public function getLogs(array $filters = [], int $perPage = 30)
    {
        $query = AdActionLog::with(['user', 'rule', 'decision'])
            ->orderByDesc('created_at');

        if (isset($filters['action_type'])) {
            $query->where('action_type', $filters['action_type']);
        }
        if (isset($filters['triggered_by'])) {
            $query->where('triggered_by', $filters['triggered_by']);
        }
        if (isset($filters['target_type'])) {
            $query->where('target_type', $filters['target_type']);
        }
        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }
        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        return $query->paginate($perPage);
    }

    public function getRecentActions(int $limit = 10)
    {
        return AdActionLog::with(['user'])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    public function getActionTypes(): array
    {
        return AdActionLog::select('action_type')
            ->distinct()
            ->pluck('action_type')
            ->toArray();
    }
}
