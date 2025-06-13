<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebinarEmailOut extends Model
{
    protected $table = 'webinar_email_out';

    protected $fillable = ['webinar_id', 'course_id', 'subject', 'send_date', 'message'];

    public function course(): BelongsTo
    {
        return $this->belongsTo(\App\Course::class);
    }

    public function webinar(): BelongsTo
    {
        return $this->belongsTo(\App\Webinar::class);
    }
}
