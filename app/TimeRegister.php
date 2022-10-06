<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TimeRegister extends Model
{

    protected $fillable = ['user_id', 'project_id', 'date', 'time', 'time_used', 'description'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function project()
    {
        return $this->hasOne('App\Project', 'id', 'project_id');
    }

}
