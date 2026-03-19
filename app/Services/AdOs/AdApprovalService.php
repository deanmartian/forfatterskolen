<?php

namespace App\Services\AdOs;

use App\Models\AdOs\AdApprovalRequest;
use App\Models\AdOs\AdAiDecision;
use App\Models\AdOs\AdActionLog;

class AdApprovalService
{
    public function __construct(
        private readonly AdDecisionService $decisionService,
    ) {}

    public function getPendingApprovals()
    {
        return AdApprovalRequest::where('status', 'pending')
            ->with(['decision.campaign'])
            ->orderByDesc('created_at')
            ->get();
    }

    public function createApprovalRequest(int $decisionId): AdApprovalRequest
    {
        $decision = AdAiDecision::findOrFail($decisionId);

        return AdApprovalRequest::create([
            'decision_id' => $decision->id,
            'action_payload' => $decision->proposed_action,
            'ai_summary' => $decision->reasoning_summary,
            'status' => 'pending',
        ]);
    }

    public function approve(int $approvalId, ?string $notes = null): array
    {
        $approval = AdApprovalRequest::findOrFail($approvalId);

        if (!$approval->isPending()) {
            return ['success' => false, 'error' => 'Denne forespørselen er allerede behandlet'];
        }

        $approval->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'reviewer_notes' => $notes,
        ]);

        $approval->decision->update(['status' => 'approved']);

        // Execute the approved action
        $result = $this->decisionService->executeDecision($approval->decision_id);

        $approval->update(['execution_result' => $result]);

        AdActionLog::log('approval_granted', [
            'target_type' => 'approval_request',
            'target_id' => $approval->id,
            'triggered_by' => 'human',
            'user_id' => auth()->id(),
            'payload' => ['decision_id' => $approval->decision_id, 'notes' => $notes],
            'result' => $result,
        ]);

        return $result;
    }

    public function reject(int $approvalId, ?string $reason = null): bool
    {
        $approval = AdApprovalRequest::findOrFail($approvalId);

        $approval->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'reviewer_notes' => $reason,
        ]);

        $approval->decision->update(['status' => 'rejected']);

        AdActionLog::log('approval_rejected', [
            'target_type' => 'approval_request',
            'target_id' => $approval->id,
            'triggered_by' => 'human',
            'user_id' => auth()->id(),
            'payload' => ['decision_id' => $approval->decision_id, 'reason' => $reason],
        ]);

        return true;
    }

    public function getPendingCount(): int
    {
        return AdApprovalRequest::where('status', 'pending')->count();
    }
}
