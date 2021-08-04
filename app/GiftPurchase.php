<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GiftPurchase extends Model
{

    protected $table = 'gift_purchases';
    protected $fillable = ['user_id', 'parent', 'parent_id', 'redeem_code', 'is_redeemed'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

}
