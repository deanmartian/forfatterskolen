<?php

namespace App\Models\AdOs;

use Illuminate\Database\Eloquent\Model;

class AdStrategistConversation extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'campaign_context' => 'array',
        'ai_response' => 'array',
        'execution_results' => 'array',
        'executed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(\App\User::class, 'user_id');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isExecuted(): bool
    {
        return $this->status === 'executed';
    }

    public function getActionCountAttribute(): int
    {
        return count($this->ai_response['actions'] ?? []);
    }

    public function getSuccessCountAttribute(): int
    {
        if (!$this->execution_results) return 0;
        return collect($this->execution_results)->where('success', true)->count();
    }
}
