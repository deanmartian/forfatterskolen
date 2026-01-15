<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoyaltyPayout extends Model
{
    protected $fillable = [
        'user_id',
        'year',
        'quarter',
        'is_paid',
        'paid_at',
    ];
}
