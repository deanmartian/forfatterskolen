<?php

namespace App;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageShopManuscript extends Model
{
    use Loggable;

    protected $table = 'package_shop_manuscripts';

    protected $fillable = ['package_id', 'shop_manuscript_id'];

    public function package(): BelongsTo
    {
        return $this->belongsTo(\App\Package::class);
    }

    public function shop_manuscript(): BelongsTo
    {
        return $this->belongsTo(\App\ShopManuscript::class);
    }
}
