<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PowerOfficeInvoice extends Model
{
    protected $fillable = [
        'user_id',
        'order_id',
        'sales_order_no',
        'parent',
        'parent_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User::class);
    }

    public function selfPublishing(): BelongsTo
    {
        return $this->belongsTo(\App\SelfPublishing::class, 'parent_id', 'id');
    }
}
