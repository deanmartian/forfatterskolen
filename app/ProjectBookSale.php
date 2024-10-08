<?php

namespace App;

use FrontendHelpers;
use Illuminate\Database\Eloquent\Model;

class ProjectBookSale extends Model
{
    
    protected $fillable = [
        'project_book_id',
        'sale_type',
        'quantity',
        'amount',
        'date',
    ];

    protected $saleTypes = [
        'physical' => 'Physical',
        'ebook' => 'Ebook',
        'sound_book' => 'Sound Book',
    ];

    protected $appends = [
        'amount_formatted', 
        'total_amount',
        'total_amount_formatted',
        'sale_type_text'
    ];

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
