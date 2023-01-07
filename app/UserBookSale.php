<?php

namespace App;

use App\Http\FrontendHelpers;
use Illuminate\Database\Eloquent\Model;

class UserBookSale extends Model
{

    protected $fillable = ['user_id', 'user_book_for_sale_id', 'quantity', 'amount', 'date'];
    protected $appends = ['amount_formatted'];

    public function user()
    {
        return $this->belongsTo('\App\User');
    }

    public function book()
    {
        return $this->belongsTo('\App\UserBookForSale', 'user_book_for_sale_id', 'id');
    }

    public function getAmountFormattedAttribute()
    {
        return isset($this->attributes['amount']) ? FrontendHelpers::currencyFormat($this->attributes['amount']) : null;
    }

}
