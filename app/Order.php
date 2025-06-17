<?php

namespace App;

use App\Http\FrontendHelpers;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    const COURSE_TYPE = 1;

    const MANUSCRIPT_TYPE = 2;

    const WORKSHOP_TYPE = 3;

    const CORRECTION_TYPE = 4;

    const COPY_EDITING_TYPE = 5;

    const COURSE_UPGRADE_TYPE = 6;

    const MANUSCRIPT_UPGRADE_TYPE = 7;

    const ASSIGNMENT_UPGRADE_TYPE = 8;

    const COACHING_TIME_TYPE = 9;

    const EDITING_SERVICES = 10;

    protected $fillable = ['user_id', 'item_id', 'type', 'package_id', 'plan_id', 'payment_mode_id', 'price', 'discount',
        'svea_order_id', 'svea_invoice_id', 'svea_payment_type', 'svea_payment_type_description', 'svea_fullname',
        'svea_street', 'svea_postal_code', 'svea_city', 'svea_country_code', 'gift_card', 'svea_delivery_id', 'is_processed',
        'is_credited_amount', 'is_pay_later', 'additional'];

    protected $appends = ['item', 'packageVariation', 'created_at_formatted', 'price_formatted', 'discount_formatted',
        'monthly_price_formatted', 'total_formatted', 'total_price'];

    protected $with = ['paymentPlan', 'paymentMode', 'company'];

    public function paymentPlan(): BelongsTo
    {
        return $this->belongsTo(\App\PaymentPlan::class, 'plan_id', 'id');
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(\App\Package::class);
    }

    public function shopManuscriptOrder(): HasOne
    {
        return $this->hasOne(\App\OrderShopManuscript::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User::class);
    }

    public function upgrade(): HasOne
    {
        return $this->hasOne(\App\OrderUpgrade::class);
    }

    public function coachingTime(): HasOne
    {
        return $this->hasOne(\App\OrderCoachingTime::class);
    }

    public function paymentMode(): HasOne
    {
        return $this->hasOne(\App\PaymentMode::class, 'id', 'payment_mode_id');
    }

    #[Scope]
    protected function svea($query)
    {
        return $query->whereNotNull('svea_order_id');
    }

    #[Scope]
    protected function payLater($query)
    {
        return $query->where('is_pay_later', 1);
    }

    #[Scope]
    protected function isProcessed($query)
    {
        return $query->where('is_processed', 1);
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

        if ($this->attributes['type'] === static::EDITING_SERVICES) {
            return 'Editing Service';
        }

        return Course::find($this->attributes['item_id'])->title;
    }

    public function getPackageVariationAttribute()
    {
        $package = $this->item;
        if (in_array($this->attributes['type'], [1, 6])) {
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
        $paymentPlan = PaymentPlan::find($this->attributes['plan_id']);
        $totalPrice = $this->attributes['price'] - $this->attributes['discount'];
        $price = $paymentPlan ? $totalPrice / $paymentPlan->division : $totalPrice;

        return FrontendHelpers::currencyFormat($price);
    }

    public function getTotalFormattedAttribute()
    {
        $total = $this->attributes['price'] - $this->attributes['discount'] + $this->attributes['additional'];
        if ($this->coachingTime) {
            $total = $total + $this->coachingTime->additional_price;
        }

        return FrontendHelpers::currencyFormat($total);
    }

    public function getTotalPriceAttribute()
    {
        $total = $this->attributes['price'] - $this->attributes['discount'];
        if ($this->coachingTime) {
            $total = $total + $this->coachingTime->additional_price;
        }

        return $total;
    }

    public function company(): HasOne
    {
        return $this->hasOne(\App\OrderCompany::class);
    }
}
