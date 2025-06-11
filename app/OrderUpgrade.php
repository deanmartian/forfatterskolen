<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderUpgrade extends Model
{
    protected $fillable = ['order_id', 'parent', 'parent_id'];

    public function order()
    {
        return $this->belongsTo('App\Order');
    }
}
