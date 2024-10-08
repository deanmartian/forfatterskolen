<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectBook extends Model
{

    protected $fillable = ['project_id', 'user_id', 'book_name', 'isbn_hardcover_book', 'isbn_ebook'];

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
        return $this->hasMany('\App\StorageDistributionCost', 'project_book_id', 'id');
    }

    public function sales()
    {
        return $this->hasMany('\App\ProjectBookSale', 'project_book_id', 'id');
    }

    public function totalDistributionCost() {
        return $this->distributionCosts()->sum('amount');
    }

    public function getPriceFormattedAttribute()
    {
        return FrontendHelpers::currencyFormat($this->attributes['price']);
    }
}
