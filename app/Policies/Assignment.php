<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assignment extends Model
{
    protected $table = 'assignments';

    protected $fillable = ['course_id', 'title', 'description'];

    public function course(): BelongsTo
    {
        return $this->belongsTo(\App\Course::class);
    }

    public function learners(): HasMany
    {
        return $this->hasMany(\App\AssignmentLearner::class)->orderBy('created_at', 'desc');
    }
}
