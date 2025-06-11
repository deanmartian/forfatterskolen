<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoragePayout extends Model
{
    protected $fillable = [
        'project_registration_id',
        'year',
        'quarter',
        'is_paid',
        'paid_at',
    ];
}
