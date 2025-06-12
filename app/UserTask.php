<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class UserTask extends Model
{
    use Loggable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'assigned_to', 'task', 'status'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User::class);
    }
}
