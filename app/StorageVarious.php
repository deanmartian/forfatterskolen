<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StorageVarious extends Model
{
    protected $table = 'storage_various';

    protected $fillable = [
        'project_book_id', // id from project_registrations table
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
