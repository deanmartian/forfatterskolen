<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TimeRegister extends Model
{

    protected $fillable = ['user_id', 'project', 'date', 'time', 'time_used', 'description'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

}
