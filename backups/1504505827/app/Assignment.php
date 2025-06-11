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

    public function manuscripts()
    {
        return $this->hasMany('App\AssignmentManuscript')->orderBy('created_at', 'desc');
    }

    public function groups()
    {
        return $this->hasMany('App\AssignmentGroup')->orderBy('created_at', 'desc');
    }
}
