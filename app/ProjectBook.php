<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProjectBook extends Model
{
    protected $fillable = ['project_id', 'user_id', 'book_name', 'isbn_hardcover_book', 'isbn_ebook'];

    public function detail(): HasOne
    {
        return $this->hasOne(\App\StorageDetail::class);
    }

    public function various(): HasOne
    {
        return $this->hasOne(\App\StorageVarious::class);
    }

    public function inventory(): HasOne
    {
        return $this->hasOne(\App\StorageInventory::class);
    }

    public function distributionCosts(): HasMany
    {
        return $this->hasMany(\App\StorageDistributionCost::class, 'project_book_id', 'id');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(\App\ProjectBookSale::class, 'project_book_id', 'id');
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
