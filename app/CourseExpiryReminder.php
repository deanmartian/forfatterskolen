<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseExpiryReminder extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'course_expiration_reminder';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['course_id', 'subject_28_days', 'message_28_days', 'subject_1_week', 'message_1_week',
        'subject_1_day', 'message_1_day'];

    public function course(): BelongsTo
    {
        return $this->belongsTo(\App\Course::class);
    }
}
