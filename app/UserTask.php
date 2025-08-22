<?php

namespace App;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserTask extends Model
{
    use Loggable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'assigned_to', 'task', 'status', 'available_date'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User::class);
    }
}
