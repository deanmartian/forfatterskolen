<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StorageVarious extends Model
{
    protected $table = 'storage_various';
    protected $fillable = [
        'storage_book_id',
        'publisher',
        'minimum_stock',
        'weight',
        'height',
        'width',
        'thickness',
        'cost',
        'material_cost',
    ];
}
