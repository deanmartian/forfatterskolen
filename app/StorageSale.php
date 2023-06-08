<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StorageSale extends Model
{
    protected $fillable = [
        'user_book_for_sale_id',
        'type',
        'value',
        'date',
    ];
}
