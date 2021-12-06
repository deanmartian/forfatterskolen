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
    const COURSE_UPGRADE_TYPE = 6;
    const MANUSCRIPT_UPGRADE_TYPE = 7;
    const ASSIGNMENT_UPGRADE_TYPE = 8;
    const COACHING_TIME_TYPE = 9;

    protected $fillable = ['user_id', 'item_id', 'type', 'package_id', 'plan_id', 'payment_mode_id', 'price', 'discount',
        'svea_order_id', 'svea_invoice_id', 'svea_payment_type', 'svea_payment_type_description', 'svea_fullname',
        'svea_street', 'svea_postal_code', 'svea_city', 'svea_country_code', 'gift_card', 'is_processed'];
    protected $appends = ['item', 'packageVariation', 'created_at_formatted', 'price_formatted', 'discount_formatted',
        'monthly_price_formatted', 'total_formatted'];
    protected $with = ['paymentPlan', 'paymentMode'];

    public function paymentPlan()
    {
        return $this->belongsTo('App\PaymentPlan', 'plan_id', 'id');
    }

    public function package()
    {
        return $this->belongsTo('App\Package');
    }

    public function shopManuscriptOrder()
    {
        return $this->hasOne('App\OrderShopManuscript');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function upgrade()
    {
        return $this->hasOne('App\OrderUpgrade');
    }

    public function coachingTime()
    {
        return $this->hasOne('App\OrderCoachingTime');
    }

    public function paymentMode()
    {
        return $this->hasOne('App\PaymentMode', 'id', 'payment_mode_id');
    }

    public function scopeSvea($query)
    {
        return $query->whereNotNull('svea_order_id');
    }

    public function getItemAttribute()
    {
        if (in_array($this->attributes['type'], [2, 7])) {
            return ShopManuscript::find($this->attributes['item_id'])->title;
        }

        if ($this->attributes['type'] === static::ASSIGNMENT_UPGRADE_TYPE) {
            return Assignment::find($this->attributes['item_id'])->title;
        }

        if ($this->attributes['type'] === static::COACHING_TIME_TYPE) {
            $title = 'Coaching time';
            if ($this->attributes['item_id'] === 1) {
                $title .= ' (1 time)';
            } else {
                $title .= ' (0,5 time)';
            }
            return $title;
        }

        if ($this->attributes['type'] === static::WORKSHOP_TYPE) {
            return Workshop::find($this->attributes['item_id'])->title;
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
        $price = $paymentPlan ? $totalPrice/$paymentPlan->division : $totalPrice;
        return FrontendHelpers::currencyFormat($price);
    }

    public function getTotalFormattedAttribute()
    {
        $total = $this->attributes['price'] - $this->attributes['discount'];
        if ($this->coachingTime) {
            $total = $total + $this->coachingTime->additional_price;
        }
        return FrontendHelpers::currencyFormat($total);
    }
}