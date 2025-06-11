<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PublishingPrintCover extends Model
{
    protected $fillable = [
        'name',
        'type',
        'price',
    ];
}
