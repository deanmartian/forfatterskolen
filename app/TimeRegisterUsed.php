<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeRegisterUsed extends Model
{
    protected $table = 'time_register_used_times';

    protected $fillable = ['time_register_id', 'date', 'time', 'description'];

    public function timeRegister(): BelongsTo
    {
        return $this->belongsTo(\App\TimeRegister::class);
    }
}
