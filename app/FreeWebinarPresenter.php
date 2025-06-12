<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FreeWebinarPresenter extends Model
{
    protected $table = 'free_webinar_presenters';

    protected $fillable = ['free_webinar_id', 'first_name', 'last_name', 'email', 'image'];

    public function webinar(): BelongsTo
    {
        return $this->belongsTo(\App\FreeWebinar::class);
    }
}
