<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssignmentLearnerConfiguration extends Model
{
    
    protected $fillable = ['assignment_id', 'user_id', 'max_words'];

}
