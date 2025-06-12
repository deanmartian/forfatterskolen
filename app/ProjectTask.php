<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectTask extends Model
{
    protected $fillable = [
        'project_id',
        'assigned_to',
        'task',
        'status',
    ];

    public function editor(): BelongsTo
    {
        return $this->belongsTo(\App\User::class, 'assigned_to', 'id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(\App\Project::class);
    }
}
