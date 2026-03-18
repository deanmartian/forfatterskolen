<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebinarRegistration extends Model
{
    protected $table = 'webinar_registrations';

    protected $fillable = [
        'free_webinar_id', 'email', 'first_name', 'last_name', 'join_url',
        'confirmation_sent', 'reminder_day_before_sent', 'reminder_hour_before_sent',
    ];

    public function webinar(): BelongsTo
    {
        return $this->belongsTo(\App\FreeWebinar::class, 'free_webinar_id');
    }
}
