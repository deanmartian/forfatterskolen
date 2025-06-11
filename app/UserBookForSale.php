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
        return $this->belongsTo(\App\User::class);
    }

    public function project()
    {
        return $this->belongsTo(\App\Project::class);
    }

    public function sales()
    {
        return $this->hasMany(\App\UserBookSale::class, 'user_book_for_sale_id', 'id');
    }

    public function detail()
    {
        return $this->hasOne(\App\StorageDetail::class);
    }

    public function various()
    {
        return $this->hasOne(\App\StorageVarious::class);
    }

    public function inventory()
    {
        return $this->hasOne(\App\StorageInventory::class);
    }

    public function distributionCosts()
    {
        return $this->hasMany(\App\StorageDistributionCost::class, 'user_book_for_sale_id', 'id');
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
