<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    
    protected $table = 'assignments';
    protected $fillable = ['course_id', 'title', 'description', 'submission_date', 'allowed_package', 'add_on_price',
        'max_words', 'for_editor', 'generated_filepath'];



    public function course()
    {
        return $this->belongsTo('App\Course');
    }


    public function manuscripts()
    {
        return $this->hasMany('App\AssignmentManuscript')->orderBy('grade', 'desc');
    }


    public function groups()
    {
        return $this->hasMany('App\AssignmentGroup')->orderBy('created_at', 'desc');
    }

    public function getSubmissionDateAttribute($value)
    {
        return $value ? date_format(date_create($value), 'M d, Y h:i A') : NULL;
    }
}
