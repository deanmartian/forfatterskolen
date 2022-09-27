<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderCompany extends Model
{

    protected $guarded = ['id'];


    public function order()
    {
        return $this->belongsTo('App\Order');
    }
}
