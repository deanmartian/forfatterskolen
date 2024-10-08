<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StorageDistributionCost extends Model
{
    
    protected $fillable = [
        'project_book_id',
        'nr',
        'service',
        'number',
        'amount',
    ];

}
