<?php

namespace App;

use App\Http\FrontendHelpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserBookSale extends Model
{
    protected $fillable = [
        'user_id',
        'user_book_for_sale_id',
        'sale_type',
        'quantity',
        'amount',
        'date',
    ];

    protected $appends = [
        'amount_formatted',
        'total_amount',
        'total_amount_formatted',
        'sale_type_text',
    ];

    protected $saleTypes = [
        'physical' => 'Physical',
        'ebook' => 'Ebook',
        'sound_book' => 'Sound Book',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(\App\UserBookForSale::class, 'user_book_for_sale_id', 'id');
    }

    public function saleTypes()
    {
        return $this->saleTypes;
    }

    public function getAmountFormattedAttribute()
    {
        return isset($this->attributes['amount']) ? FrontendHelpers::currencyFormat($this->attributes['amount']) : null;
    }

    public function getTotalAmountAttribute()
    {
        return isset($this->attributes['amount']) && $this->attributes['amount']
            ? $this->attributes['amount']
            : $this->book->price * $this->attributes['quantity'];
    }

    public function getTotalAmountFormattedAttribute()
    {
        return FrontendHelpers::currencyFormat($this->getAttributeValue('total_amount'));
    }

    public function getSaleTypeTextAttribute()
    {
        return $this->saleTypes[$this->attributes['sale_type']];
    }
}
