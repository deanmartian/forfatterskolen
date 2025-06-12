<?php

namespace App;

use App\Http\FrontendHelpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderCoachingTime extends Model
{
    protected $table = 'order_coaching_timer';

    protected $fillable = ['order_id', 'additional_price', 'file', 'suggested_date', 'help_with'];

    protected $appends = ['additional_price_formatted'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(\App\Order::class);
    }

    public function getAdditionalPriceFormattedAttribute()
    {
        return FrontendHelpers::currencyFormat($this->attributes['additional_price']);
    }
}
