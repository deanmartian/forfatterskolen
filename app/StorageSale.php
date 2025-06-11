<?php

namespace App;

use AdminHelpers;
use Illuminate\Database\Eloquent\Model;

class StorageSale extends Model
{
    protected $fillable = [
        'project_book_id',
        'type',
        'value',
        'date',
    ];

    protected $appends = [
        'inventory_type',
    ];

    public function getInventoryTypeAttribute()
    {
        return AdminHelpers::inventorySalesType($this->attributes['type']);
    }

    public function getQuantityAttribute()
    {
        return $this->attributes['value'];
    }
}
