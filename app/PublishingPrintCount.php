<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PublishingPrintCount extends Model
{
    protected $fillable = [
        'name',
        'value',
        'price',
    ];
}
