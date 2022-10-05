<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{

    protected $fillable = ['user_id', 'name', 'identifier', 'activity_id', 'start_date', 'end_date', 'description',
        'is_finished'];

}
