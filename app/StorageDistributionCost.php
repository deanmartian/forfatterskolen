<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StorageDistributionCost extends Model
{
    
    protected $fillable = [
        'user_book_for_sale_id',
        'nr',
        'service',
        'number',
        'amount',
    ];

}
