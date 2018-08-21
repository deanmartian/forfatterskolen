<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class AssignmentManuscript extends Model
{
    
    protected $table = 'assignment_manuscripts';
    protected $fillable = ['assignment_id', 'user_id', 'filename', 'words', 'grade', 'type', 'manu_type', 'editor_id'];



    public function assignment()
    {
        return $this->belongsTo('App\Assignment');
    }


    public function user()
    {
        return $this->belongsTo('App\User');
    }


    public function feedbacks()
    {
        return $this->hasMany('App\AssignmentFeedback');
    }

    public function noGroupFeedbacks()
    {
        return $this->hasMany('App\AssignmentFeedbackNoGroup');
    }

}
