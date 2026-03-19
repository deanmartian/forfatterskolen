<?php

namespace App\Models\AdOs;

use Illuminate\Database\Eloquent\Model;

class AdActionLog extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'payload' => 'array',
        'result' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(\App\User::class, 'user_id');
    }

    public function rule()
    {
        return $this->belongsTo(AdRule::class, 'rule_id');
    }

    public function decision()
    {
        return $this->belongsTo(AdAiDecision::class, 'decision_id');
    }

    public static function log(string $actionType, array $data = []): self
    {
        return self::create(array_merge([
            'action_type' => $actionType,
            'triggered_by' => 'system',
            'status' => 'success',
        ], $data));
    }
}
