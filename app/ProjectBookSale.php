<?php

namespace App;

use FrontendHelpers;
use Illuminate\Database\Eloquent\Model;

class ProjectBookSale extends Model
{
    protected $fillable = [
        'project_book_id',
        'project_registration_id',
        'invoice_number',
        'customer_name',
        'quantity',
        'full_price',
        'discount',
        'amount',
        'date',
    ];

    protected $saleTypes = [
        'physical' => 'Physical',
        'ebook' => 'Ebook',
        'sound_book' => 'Sound Book',
    ];

    protected $appends = [
        'price_formatted',
        'amount_formatted',
        'total_amount',
        'total_amount_formatted',
        'discount_formatted',
        // 'sale_type_text'
    ];

    public function saleTypes()
    {
        return $this->saleTypes;
    }

    public function getPriceFormattedAttribute()
    {
        return isset($this->attributes['full_price']) ? FrontendHelpers::currencyFormat($this->attributes['full_price']) : null;
    }

    public function getAmountFormattedAttribute()
    {
        return isset($this->attributes['amount']) ? FrontendHelpers::currencyFormat($this->attributes['amount']) : null;
    }

    public function getTotalAmountAttribute()
    {
        return isset($this->attributes['amount']) && $this->attributes['amount']
            ? $this->attributes['amount']
            : ($this->book ? $this->book->price * $this->attributes['quantity'] : 0);
    }

    public function getTotalAmountFormattedAttribute()
    {
        return FrontendHelpers::currencyFormat($this->getAttributeValue('total_amount'));
    }

    public function getDiscountFormattedAttribute()
    {
        return FrontendHelpers::currencyFormat($this->getAttributeValue('discount'));
    }

    /* public function getSaleTypeTextAttribute()
    {
        return $this->saleTypes[$this->attributes['sale_type']];
    } */

}
