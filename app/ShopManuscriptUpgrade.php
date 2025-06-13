<?php

namespace App;

use App\Http\FrontendHelpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopManuscriptUpgrade extends Model
{
    protected $table = 'shop_manuscripts_upgrade';

    protected $fillable = ['shop_manuscript_id', 'upgrade_shop_manuscript_id', 'price'];

    protected $with = ['upgrade_manuscript'];

    protected $appends = [
        'price_formatted',
        'price_25_additional',
    ];

    public function upgrade_manuscript(): BelongsTo
    {
        return $this->belongsTo(\App\ShopManuscript::class, 'upgrade_shop_manuscript_id');
    }

    public function getPriceFormattedAttribute()
    {
        return \App\Http\FrontendHelpers::currencyFormat($this->attributes['price']);
    }

    public function getPrice25AdditionalAttribute()
    {
        $userHasPaidCourse = FrontendHelpers::userHasPaidCourse();
        if ($userHasPaidCourse) {
            return 0;
        }

        return $this->attributes['price'] * .25;
    }
}
