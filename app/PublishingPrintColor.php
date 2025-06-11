<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PublishingPrintColor extends Model
{
    protected $fillable = [
        'name',
        'type',
        'price',
    ];
}
