<?php

namespace App;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assignment extends Model
{
    use Loggable;

    protected $table = 'assignments';

    protected $fillable = ['course_id', 'title', 'description', 'submission_date', 'available_date', 'allowed_package', 'add_on_price',
        'max_words', 'allow_up_to', 'for_editor', 'editor_id', 'editor_manu_generate_count', 'generated_filepath',
        'show_join_group_question', 'send_letter_to_editor', 'check_max_words', 'assigned_editor', 'parent_id', 'parent',
        'editor_expected_finish', 'expected_finish'];

    protected $appends = ['submission_date_time_text'];

    // filter for course assignments
    #[Scope]
    protected function forCourseOnly($query)
    {
        return $query->whereNull('parent')->orWhere('parent', 'course');
    }

    // filter for learner assignments
    #[Scope]
    protected function forLearnerOnly($query)
    {
        return $query->where('parent', 'users');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(\App\Course::class);
    }

    public function manuscripts(): HasMany
    {
        return $this->hasMany(\App\AssignmentManuscript::class)->orderBy('grade', 'desc');
    }

    public function notFinishedManuscripts(): HasMany
    {
        return $this->hasMany(\App\AssignmentManuscript::class)
            ->where('status', 0)
            ->orderBy('grade', 'desc');
    }

    public function groups(): HasMany
    {
        return $this->hasMany(\App\AssignmentGroup::class)->orderBy('created_at', 'desc');
    }

    public function getSubmissionDateAttribute($value)
    {
        $submission_date = null;
        if ($value) {
            if (! is_numeric($value)) {
                $submission_date = date_format(date_create($value), 'M d, Y h:i A');
            } else {
                $submission_date = $value;
            }
        }

        return $submission_date;
    }

    public function getAvailableDateAttribute($value)
    {
        return $value ? date_format(date_create($value), 'M d, Y') : null;
    }

    public function learner(): BelongsTo
    {
        return $this->belongsTo(\App\User::class, 'parent_id', 'id');
    }

    public function getAllowedPackagesAttribute()
    {
        return json_decode($this->attributes['allowed_package']);
    }

    public function getEditorExpectedFinishAttribute($value)
    {
        return $value ? date_format(date_create($value), 'd.m.Y') : null;
    }

    public function getSubmissionDateTimeTextAttribute()
    {
        $value = $this->attributes['submission_date'];
        $submission_date = null;
        if ($value) {
            if (! is_numeric($value)) {
                $submission_date = ucwords(strtr(trans('site.learner.submission-date-value'), [
                    '_date_' => \Carbon\Carbon::parse($this->attributes['submission_date'])->format('d M Y'),
                    '_time_' => \Carbon\Carbon::parse($this->attributes['submission_date'])->format('H:i')]));
            }
        }

        return $submission_date;
    }

    public function assignmentManuscriptEditorCanTake(): HasMany
    {
        return $this->hasMany(\App\AssignmentManuscriptEditorCanTake::class, 'assignment_manuscript_id', 'id');
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(\App\User::class, 'editor_id', 'id');
    }

    public function linkedAssignment(): BelongsTo
    {
        return $this->belongsTo(\App\Assignment::class, 'parent_id', 'id');
    }

    public function disabledLearners(): HasMany
    {
        return $this->hasMany(\App\AssignmentDisabledLearner::class);
    }

    public function getLinkedPersonalAssignment($user_id)
    {
        $disabledLearner = $this->disabledLearners()->where('user_id', $user_id)->first();

        return $disabledLearner ? $this->find($disabledLearner->personal_assignment_id) : null;
    }
}
