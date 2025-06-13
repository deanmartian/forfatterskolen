<?php

namespace App;

use FrontendHelpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SelfPublishingPortalRequest extends Model
{
    protected $fillable = ['user_id'];

    protected $appends = ['created_at_formatted'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User::class);
    }

    public function getCreatedAtFormattedAttribute()
    {
        return FrontendHelpers::formatDate($this->attributes['created_at']);
    }
}
