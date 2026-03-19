<?php

namespace App\Models\AdOs;

use Illuminate\Database\Eloquent\Model;

class AdSyncRun extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'details' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function account()
    {
        return $this->belongsTo(AdAccount::class, 'account_id');
    }

    public static function start(int $accountId, string $platform, string $type): self
    {
        return self::create([
            'account_id' => $accountId,
            'platform' => $platform,
            'type' => $type,
            'status' => 'running',
            'started_at' => now(),
        ]);
    }

    public function complete(int $recordsSynced = 0, array $details = []): void
    {
        $this->update([
            'status' => 'completed',
            'records_synced' => $recordsSynced,
            'details' => $details,
            'completed_at' => now(),
        ]);
    }

    public function fail(string $errorMessage, array $details = []): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
            'details' => $details,
            'completed_at' => now(),
        ]);
    }
}
