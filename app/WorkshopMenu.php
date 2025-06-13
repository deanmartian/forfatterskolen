<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkshopMenu extends Model
{
    protected $table = 'workshop_menus';

    protected $fillable = ['workshop_id', 'title', 'description', 'image'];

    public function workshop(): BelongsTo
    {
        return $this->belongsTo(\App\Workshop::class);
    }
}
