<?php

namespace App;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssignmentGroup extends Model
{
    use Loggable;

    protected $table = 'assignment_groups';

    protected $fillable = ['assignment_id', 'title', 'submission_date', 'allow_feedback_download'];

    protected $appends = [
        'submission_date_time_text',
    ];

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(\App\Assignment::class);
    }

    public function learners(): HasMany
    {
        return $this->hasMany(\App\AssignmentGroupLearner::class)->orderBy('created_at', 'desc');
    }

    public function getSubmissionDateAttribute($value)
    {
        return $value ? date_format(date_create($value), 'M d, Y h:i A') : null;
    }

    public function getSubmissionDateTimeTextAttribute()
    {
        $submission_date = $this->attributes['submission_date'];

        return ucwords(strtr(trans('site.learner.submission-date-value'), [
            '_date_' => \Carbon\Carbon::parse($submission_date)->format('d.m.Y'),
            '_time_' => \Carbon\Carbon::parse($submission_date)->format('H:i'),
        ]));
    }
}
