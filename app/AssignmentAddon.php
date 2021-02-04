<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssignmentAddon extends Model
{
    
    protected $table = 'assignment_addons';
    protected $fillable = ['user_id', 'assignment_id'];

    public function assignment()
    {
        return $this->belongsTo('App\Assignment');
    }
}
