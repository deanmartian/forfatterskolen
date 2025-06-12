<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class UserAutoRegisterToCourseWebinar extends Model
{
    protected $table = 'user_auto_register_to_course_webinar';

    protected $fillable = ['user_id', 'course_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User::class);
    }
}
