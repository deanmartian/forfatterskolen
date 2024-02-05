<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectTask extends Model
{
    
    protected $fillable = [
        'project_id',
        'assigned_to',
        'task',
        'status'
    ];

    public function editor()
    {
        return $this->belongsTo('App\User', 'assigned_to', 'id');
    }

    public function project()
    {
        return $this->belongsTo('App\Project');
    }

}
