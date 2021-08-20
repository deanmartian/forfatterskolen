<?php

namespace App;

use App\Http\FrontendHelpers;
use Illuminate\Database\Eloquent\Model;

class Order extends Model {

    const COURSE_TYPE = 1;
    const MANUSCRIPT_TYPE = 2;
    const WORKSHOP_TYPE = 3;
    const CORRECTION_TYPE = 4;
    const COPY_EDITING_TYPE = 5;

    protected $fillable = ['user_id', 'item_id', 'type', 'package_id', 'plan_id', 'payment_mode_id', 'price', 'discount',
        'svea_order_id', 'svea_invoice_id', 'gift_card', 'is_processed'];
    protected $appends = ['item', 'packageVariation', 'created_at_formatted', 'price_formatted', 'discount_formatted',
        'monthly_price_formatted', 'total_formatted'];
    protected $with = ['paymentPlan'];

    public function paymentPlan()
    {
        return $this->belongsTo('App\PaymentPlan', 'plan_id', 'id');
    }

    public function paymentMode()
    {
        return $this->belongsTo('App\PaymentMode');
    }

    public function package()
    {
        return $this->belongsTo('App\Package');
    }

    public function scopeSvea($query)
    {
        return $query->whereNotNull('svea_order_id');
    }

    public function getItemAttribute()
    {
        if ($this->attributes['type'] === 2) {
            return ShopManuscript::find($this->attributes['item_id'])->title;
        }

        return Course::find($this->attributes['item_id'])->title;
    }

    public function getPackageVariationAttribute()
    {
        $package = '';
        if ($this->attributes['type'] === 1) {
            return $this->package->variation;
        }

        return $package;
    }

    public function getCreatedAtFormattedAttribute()
    {
        return FrontendHelpers::formatDate($this->attributes['created_at']);
    }

    public function getPriceFormattedAttribute()
    {
        return FrontendHelpers::currencyFormat($this->attributes['price']);
    }

    public function getDiscountFormattedAttribute()
    {
        return FrontendHelpers::currencyFormat($this->attributes['discount']);
    }

    public function getMonthlyPriceFormattedAttribute()
    {
        $paymentPlan =  PaymentPlan::find($this->attributes['plan_id']);
        $totalPrice = $this->attributes['price'] - $this->attributes['discount'];
        $price = $totalPrice/$paymentPlan->division;
        return FrontendHelpers::currencyFormat($price);
    }

    public function getTotalFormattedAttribute()
    {
        $total = $this->attributes['price'] - $this->attributes['discount'];
        return FrontendHelpers::currencyFormat($total);
    }
}