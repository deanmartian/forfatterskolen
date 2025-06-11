<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentPlan extends Model
{
    protected $table = 'payment_plans';

    protected $fillable = ['plan', 'division'];

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function getPlanAttribute($value)
    {
        return trim($value);
    }
}
