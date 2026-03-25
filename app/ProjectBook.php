<?php

namespace App;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProjectBook extends Model
{
    use Loggable;

    protected $fillable = [
        'project_id', 'user_id', 'book_name', 'isbn_hardcover_book', 'isbn_ebook',
        'slug', 'short_description', 'long_description',
        'genre', 'categories', 'target_audience',
        'price_paperback', 'price_hardcover', 'price_ebook', 'price_audiobook',
        'shop_visible', 'shop_featured', 'shop_sort_order',
        'shop_cover_image', 'shop_gallery',
        'print_available', 'ebook_available', 'audiobook_available',
    ];

    protected $casts = [
        'categories' => 'array',
        'shop_gallery' => 'array',
        'shop_visible' => 'boolean',
        'shop_featured' => 'boolean',
        'print_available' => 'boolean',
        'ebook_available' => 'boolean',
        'audiobook_available' => 'boolean',
    ];

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

    public function project()
    {
        return $this->belongsTo(\App\Project::class);
    }

    public function scopeShopVisible($query)
    {
        return $query->where('shop_visible', true);
    }

    public function getAuthorNameAttribute(): ?string
    {
        return $this->project?->user?->full_name;
    }

    public function getAvailableFormatsAttribute(): array
    {
        $formats = [];
        if ($this->print_available) $formats[] = 'paperback';
        if ($this->price_hardcover) $formats[] = 'hardcover';
        if ($this->ebook_available) $formats[] = 'ebook';
        if ($this->audiobook_available) $formats[] = 'audiobook';
        return $formats;
    }

    public function getPriceFormattedAttribute()
    {
        return FrontendHelpers::currencyFormat($this->attributes['price'] ?? 0);
    }
}
