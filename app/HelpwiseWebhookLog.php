<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HelpwiseWebhookLog extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'payload' => 'array',
    ];
}
