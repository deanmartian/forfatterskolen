<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PowerOfficeInvoice extends Model
{
    protected $fillable = [
        'user_id',
        'order_id',
        'sales_order_no',
        'parent',
        'parent_id',
    ];

    public function user()
    {
        return $this->belongsTo('\App\User');
    }

    public function selfPublishing()
    {
        return $this->belongsTo('App\SelfPublishing', 'parent_id', 'id');
    }
}
