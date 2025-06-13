<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebinarRegistrant extends Model
{
    protected $table = 'webinar_registrants';

    protected $fillable = ['webinar_id', 'user_id', 'join_url'];

    protected $with = ['user'];

    public function webinar(): BelongsTo
    {
        return $this->belongsTo(\App\Webinar::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User::class);
    }
}
