<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class PrivateGroupSharedBook extends Model
{
    protected $fillable = ['private_group_id', 'book_id', 'visibility'];

    public function book(): BelongsTo
    {
        return $this->belongsTo(\App\PilotReaderBook::class);
    }
}
