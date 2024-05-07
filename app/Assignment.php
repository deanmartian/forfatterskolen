<?php

namespace App;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use Loggable;
    
    protected $table = 'assignments';
    protected $fillable = ['course_id', 'title', 'description', 'submission_date', 'available_date','allowed_package', 'add_on_price',
        'max_words', 'allow_up_to', 'for_editor', 'editor_id', 'editor_manu_generate_count', 'generated_filepath', 
        'show_join_group_question', 'send_letter_to_editor', 'check_max_words', 'assigned_editor', 'parent_id', 'parent', 
        'editor_expected_finish', 'expected_finish'];
    protected $appends = ['submission_date_time_text'];


    // filter for course assignments
    public function scopeForCourseOnly($query)
    {
        return $query->whereNull('parent')->orWhere('parent', 'course');
    }

    // filter for learner assignments
    public function scopeForLearnerOnly($query)
    {
        return $query->where('parent', 'users');
    }

    public function course()
    {
        return $this->belongsTo('App\Course');
    }


    public function manuscripts()
    {
        return $this->hasMany('App\AssignmentManuscript')->orderBy('grade', 'desc');
    }

    public function notFinishedManuscripts()
    {
        return $this->hasMany('App\AssignmentManuscript')
            ->where('status', 0)
            ->orderBy('grade', 'desc');
    }


    public function groups()
    {
        return $this->hasMany('App\AssignmentGroup')->orderBy('created_at', 'desc');
    }

    public function getSubmissionDateAttribute($value)
    {
        $submission_date = NULL;
        if ($value) {
            if (!is_numeric($value)) {
                $submission_date = date_format(date_create($value), 'M d, Y h:i A');
            } else {
                $submission_date = $value;
            }
        }
        return $submission_date;
    }

    public function getAvailableDateAttribute($value)
    {
        return $value ? date_format(date_create($value), 'M d, Y') : NULL;
    }

    public function learner()
    {
        return $this->belongsTo('App\User', 'parent_id', 'id');
    }

    public function getAllowedPackagesAttribute()
    {
        return json_decode($this->attributes['allowed_package']);
    }
    
    public function getEditorExpectedFinishAttribute($value) {
        return $value ? date_format(date_create($value), 'd.m.Y') : NULL;
    }

    public function getSubmissionDateTimeTextAttribute()
    {
        $value = $this->attributes['submission_date'];
        $submission_date = NULL;
        if ($value) {
            if (!is_numeric($value)) {
                $submission_date = ucwords(strtr(trans('site.learner.submission-date-value'), [
                    '_date_' => \Carbon\Carbon::parse($this->attributes['submission_date'])->format('d M Y'),
                    '_time_' => \Carbon\Carbon::parse($this->attributes['submission_date'])->format('H:i')]));
            }
        }
        return $submission_date;
    }

    public function assignmentManuscriptEditorCanTake(){
        return $this->hasMany('App\AssignmentManuscriptEditorCanTake', 'assignment_manuscript_id', 'id');
    }

    public function editor()
    {
        return $this->belongsTo('App\User', 'editor_id', 'id');
    }

    public function linkedAssignment()
    {
        return $this->belongsTo('App\Assignment', 'parent_id', 'id');
    }

    public function disabledLearners()
    {
        return $this->hasMany('App\AssignmentDisabledLearner');
    }

    public function getLinkedPersonalAssignment($user_id)
    {
        $disabledLearner = $this->disabledLearners()->where('user_id', $user_id)->first();
        return $disabledLearner ? $this->find($disabledLearner->personal_assignment_id) : null;
    }
}
