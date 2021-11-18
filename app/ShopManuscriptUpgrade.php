<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShopManuscriptUpgrade extends Model
{
    protected $table = 'shop_manuscripts_upgrade';
    protected $fillable = ['shop_manuscript_id', 'upgrade_shop_manuscript_id', 'price'];
    protected $with = ['upgrade_manuscript'];
    protected $appends = ['price_formatted'];

    public function upgrade_manuscript()
    {
        return $this->belongsTo('App\ShopManuscript', 'upgrade_shop_manuscript_id');
    }

    public function getPriceFormattedAttribute()
    {
        return \App\Http\FrontendHelpers::currencyFormat($this->attributes['price']);
    }

}
