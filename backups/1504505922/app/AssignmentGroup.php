<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class AssignmentGroup extends Model
{
    
    protected $table = 'assignment_groups';
    protected $fillable = ['assignment_id', 'title'];



    public function assignment()
    {
        return $this->belongsTo('App\Assignment');
    }


    public function learners()
    {
        return $this->hasMany('App\AssignmentGroupLearner')->orderBy('created_at', 'desc');
    }

}
