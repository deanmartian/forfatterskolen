<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    
    protected $table = 'assignments';
    protected $fillable = ['course_id', 'title', 'description'];



    public function course()
    {
        return $this->belongsTo('App\Course');
    }


    public function learners()
    {
        return $this->hasMany('App\AssignmentLearner')->orderBy('created_at', 'desc');
    }
}
