<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkshopsTaken extends Model
{
    protected $table = 'workshops_taken';

    protected $fillable = ['user_id', 'workshop_id', 'menu_id', 'notes', 'is_active'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User::class);
    }

    public function workshop(): BelongsTo
    {
        return $this->belongsTo(\App\Workshop::class);
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(\App\WorkshopMenu::class);
    }

    public function getCreatedAtAttribute($value)
    {
        return date_format(date_create($value), 'M d, Y h:i a');
    }
}
