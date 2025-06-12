<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class SelfPublishingLearner extends Model
{
    use Loggable;

    protected $fillable = ['user_id', 'self_publishing_id'];

    public function selfPublishing(): BelongsTo
    {
        return $this->belongsTo(\App\SelfPublishing::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User::class);
    }
}
