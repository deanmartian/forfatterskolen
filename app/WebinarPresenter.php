<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebinarPresenter extends Model
{
    protected $table = 'webinar_presenters';

    protected $fillable = ['webinar_id', 'first_name', 'last_name', 'email', 'image'];

    public function webinar(): BelongsTo
    {
        return $this->belongsTo(\App\Webinar::class);
    }
}
