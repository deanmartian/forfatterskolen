<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssignmentDisabledLearner extends Model
{
    protected $fillable = ['assignment_id', 'user_id', 'personal_assignment_id'];
}
