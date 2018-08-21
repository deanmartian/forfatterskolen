<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class AssignmentGroupLearner extends Model
{
    
    protected $table = 'assignment_group_learners';
    protected $fillable = ['assignment_group_id', 'user_id'];




    public function user()
    {
        return $this->belongsTo('App\User');
    }



    public function group()
    {
        return $this->belongsTo('App\AssignmentGroup', 'assignment_group_id');
    }

}
