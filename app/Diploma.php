<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Diploma extends Model
{
    protected $fillable = ['user_id', 'course_id', 'diploma'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(\App\Course::class);
    }

    public function getCreatedAtAttribute($value)
    {
        return date_format(date_create($value), 'M d, Y h:i a');
    }
}
