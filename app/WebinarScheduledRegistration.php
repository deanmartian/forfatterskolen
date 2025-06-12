<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class WebinarScheduledRegistration extends Model
{
    use Loggable;

    protected $fillable = ['webinar_id', 'date'];

    public function webinar(): BelongsTo
    {
        return $this->belongsTo(\App\Webinar::class);
    }
}
