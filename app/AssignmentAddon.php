<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignmentAddon extends Model
{
    protected $table = 'assignment_addons';

    protected $fillable = ['user_id', 'assignment_id'];

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(\App\Assignment::class);
    }
}
