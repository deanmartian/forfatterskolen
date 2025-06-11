<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StorageInventory extends Model
{
    protected $fillable = [
        'project_book_id',
        'total',
        'delivered',
        'physical_items',
        'returns',
        'balance',
        'order',
        'reservations',
    ];
}
