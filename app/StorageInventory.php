<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StorageInventory extends Model
{
    protected $fillable = [
        'user_book_for_sale_id',
        'total',
        'delivered',
        'physical_items',
        'returns',
        'balance',
        'order',
        'reservations'
    ];
}
