<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentPlan extends Model
{
    protected $table = 'payment_plans';

    protected $fillable = ['plan', 'division'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User::class);
    }

    public function getPlanAttribute($value)
    {
        return trim($value);
    }
}
