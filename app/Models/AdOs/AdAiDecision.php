<?php

namespace App\Models\AdOs;

use Illuminate\Database\Eloquent\Model;

class AdAiDecision extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'confidence' => 'decimal:2',
        'requires_approval' => 'boolean',
        'proposed_action' => 'array',
        'context_data' => 'array',
        'execution_result' => 'array',
        'executed_at' => 'datetime',
    ];

    public function campaign()
    {
        return $this->belongsTo(AdCampaign::class, 'campaign_id');
    }

    public function rule()
    {
        return $this->belongsTo(AdRule::class, 'rule_id');
    }

    public function approvalRequest()
    {
        return $this->hasOne(AdApprovalRequest::class, 'decision_id');
    }

    public function actionLog()
    {
        return $this->hasOne(AdActionLog::class, 'decision_id');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function canAutoExecute(): bool
    {
        return !$this->requires_approval && $this->risk_level === 'low';
    }
}
