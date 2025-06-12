<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class WebinarEditor extends Model
{
    use Loggable;

    protected $fillable = ['editor_id', 'webinar_id', 'name', 'presenter_url'];

    public function editor(): BelongsTo
    {
        return $this->belongsTo(\App\User::class);
    }

    public function webinar(): BelongsTo
    {
        return $this->belongsTo(\App\Webinar::class);
    }
}
