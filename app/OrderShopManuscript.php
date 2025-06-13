<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderShopManuscript extends Model
{
    protected $fillable = ['order_id', 'genre', 'file', 'words', 'description', 'synopsis', 'coaching_time_later',
        'send_to_email'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(\App\Order::class);
    }
}
