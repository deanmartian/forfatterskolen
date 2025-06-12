<?php

namespace App;

use App\Http\FrontendHelpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(\App\Project::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(\App\UserBookSale::class, 'user_book_for_sale_id', 'id');
    }

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
