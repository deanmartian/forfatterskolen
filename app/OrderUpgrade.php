<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderUpgrade extends Model
{
    protected $fillable = ['order_id', 'parent', 'parent_id'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(\App\Order::class);
    }
}
