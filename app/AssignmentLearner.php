<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssignmentLearner extends Model
{
    protected $table = 'assignment_learners';

    protected $fillable = ['assignment_id', 'user_id', 'filename'];

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(\App\Assignment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User::class);
    }

    public function feedbacks(): HasMany
    {
        return $this->hasMany(\App\AssignmentFeedback::class);
    }
}
