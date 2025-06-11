<?php

namespace App;

use App\Http\FrontendHelpers;
use Illuminate\Database\Eloquent\Model;

class UserBookForSale extends Model
{
    protected $table = 'user_books_for_sale';

    protected $fillable = [
        'user_id',
        'project_id',
        'isbn',
        'ebook_isbn',
        'title',
        'description',
        'price',
    ];

    protected $appends = ['price_formatted'];

    public function user()
    {
        return $this->belongsTo('\App\User');
    }

    public function project()
    {
        return $this->belongsTo('\App\Project');
    }

    public function sales()
    {
        return $this->hasMany('\App\UserBookSale', 'user_book_for_sale_id', 'id');
    }

    public function detail()
    {
        return $this->hasOne('\App\StorageDetail');
    }

    public function various()
    {
        return $this->hasOne('\App\StorageVarious');
    }

    public function inventory()
    {
        return $this->hasOne('\App\StorageInventory');
    }

    public function distributionCosts()
    {
        return $this->hasMany('\App\StorageDistributionCost', 'user_book_for_sale_id', 'id');
    }

    public function totalDistributionCost()
    {
        return $this->distributionCosts()->sum('amount');
    }

    public function getPriceFormattedAttribute()
    {
        return FrontendHelpers::currencyFormat($this->attributes['price']);
    }
}
