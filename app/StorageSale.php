<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StorageSale extends Model
{
    protected $fillable = [
        'project_book_id',
        'type',
        'value',
        'date',
    ];
}
