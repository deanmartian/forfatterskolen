<?php

namespace App\Models\AdOs;

use Illuminate\Database\Eloquent\Model;

class AdApprovalRequest extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'action_payload' => 'array',
        'execution_result' => 'array',
        'approved_at' => 'datetime',
    ];

    public function decision()
    {
        return $this->belongsTo(AdAiDecision::class, 'decision_id');
    }

    public function approver()
    {
        return $this->belongsTo(\App\User::class, 'approved_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
