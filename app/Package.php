<?php

namespace App;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Package extends Model
{
    use Loggable;

    protected $table = 'packages';

    /**
     * issue_date is for the faktura issue date
     *
     * @var array
     */
    protected $fillable = ['course_id', 'variation', 'full_months_price', 'months_3_price', 'months_6_price', 'months_12_price',
        'full_price_product', 'months_3_product', 'months_6_product', 'months_12_product', 'full_price_due_date', 'description',
        'months_3_due_date', 'months_6_due_date', 'months_12_due_date', 'months_3_enable', 'months_6_enable', 'months_12_enable',
        'manuscripts_count', 'due_date', 'has_student_discount', 'is_reward', 'issue_date', 'validity_period', 'is_show',
        'is_upgradeable', 'is_pay_later_allowed', 'is_standard'];

    protected $appends = ['description_formatted', 'description_with_check', 'sale_discount', 'full_payment_is_sale',
        'months_3_is_sale', 'months_6_is_sale', 'months_12_is_sale', 'calculated_price'];

    protected $with = ['included_courses'];

    #[Scope]
    protected function isShow($query)
    {
        return $query->where('is_show', 1);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(\App\Course::class)->orderBy('created_at', 'desc');
    }

    public function shop_manuscripts(): HasMany
    {
        return $this->hasMany(\App\PackageShopManuscript::class)->orderBy('created_at', 'desc');
    }

    public function workshops(): HasMany
    {
        return $this->hasMany(\App\PackageWorkshop::class)->orderBy('created_at', 'desc');
    }

    public function included_courses(): HasMany
    {
        return $this->hasMany(\App\PackageCourse::class, 'package_id')->orderBy('created_at', 'desc');
    }

    public function certificate(): HasOne
    {
        return $this->hasOne(\App\CourseCertificate::class);
    }

    public function getDescriptionFormattedAttribute()
    {
        return nl2br($this->attributes['description']);
    }

    public function getDescriptionWithCheckAttribute()
    {
        //return str_replace('-', '<i class="checkmark"></i>', nl2br($this->attributes['description']));
        $description = nl2br($this->attributes['description']);

        // Replace hyphens only if they start a line or follow a <br> (like a bullet)
        $description = preg_replace('/(^|<br\s*\/?>)\s*-\s*/', '$1<i class="checkmark"></i> ', $description);

        return $description;
    }

    public function getSaleDiscountAttribute()
    {
        $today = \Carbon\Carbon::today()->format('Y-m-d');
        $fromFull = \Carbon\Carbon::parse($this->attributes['full_payment_sale_price_from'])->format('Y-m-d');
        $toFull = \Carbon\Carbon::parse($this->attributes['full_payment_sale_price_to'])->format('Y-m-d');
        $isBetweenFull = (($today >= $fromFull) && ($today <= $toFull)) ? 1 : 0;

        $fromFullOther = \Carbon\Carbon::parse($this->attributes['full_payment_other_sale_price_from'])->format('Y-m-d');
        $toFullOther = \Carbon\Carbon::parse($this->attributes['full_payment_other_sale_price_to'])->format('Y-m-d');
        $isBetweenFullOther = (($today >= $fromFullOther) && ($today <= $toFullOther)) ? 1 : 0;

        if ($isBetweenFull && $this->attributes['full_payment_sale_price']) {
            return $this->attributes['full_payment_price'] - $this->attributes['full_payment_sale_price'];
        }

        if ($isBetweenFullOther && $this->attributes['full_payment_other_sale_price']) {
            return $this->attributes['full_payment_price'] - $this->attributes['full_payment_other_sale_price'];
        }

        return 0;
    }

    public function getCalculatedPriceAttribute()
    {
        $today = \Carbon\Carbon::today()->format('Y-m-d');
        $fromFull = \Carbon\Carbon::parse($this->attributes['full_payment_sale_price_from'])->format('Y-m-d');
        $toFull = \Carbon\Carbon::parse($this->attributes['full_payment_sale_price_to'])->format('Y-m-d');
        $isBetweenFull = (($today >= $fromFull) && ($today <= $toFull)) ? 1 : 0;

        $fromFullOther = \Carbon\Carbon::parse($this->attributes['full_payment_other_sale_price_from'])->format('Y-m-d');
        $toFullOther = \Carbon\Carbon::parse($this->attributes['full_payment_other_sale_price_to'])->format('Y-m-d');
        $isBetweenFullOther = (($today >= $fromFullOther) && ($today <= $toFullOther)) ? 1 : 0;

        if ($isBetweenFull && $this->attributes['full_payment_sale_price']) {
            return $this->attributes['full_payment_sale_price'];
        }

        if ($isBetweenFullOther && $this->attributes['full_payment_other_sale_price']) {
            return $this->attributes['full_payment_other_sale_price'];
        }

        return $this->attributes['full_payment_price'];
    }

    public function getFullPaymentIsSaleAttribute()
    {
        $today = \Carbon\Carbon::today()->format('Y-m-d');
        $fromMonthsFull = \Carbon\Carbon::parse($this->attributes['full_payment_sale_price_from'])->format('Y-m-d');
        $toMonthsFull = \Carbon\Carbon::parse($this->attributes['full_payment_sale_price_to'])->format('Y-m-d');

        return (($today >= $fromMonthsFull) && ($today <= $toMonthsFull)) ? true : false;
    }

    public function getMonths3IsSaleAttribute()
    {
        $today = \Carbon\Carbon::today()->format('Y-m-d');
        $fromMonths3 = \Carbon\Carbon::parse($this->attributes['months_3_sale_price_from'])->format('Y-m-d');
        $toMonths3 = \Carbon\Carbon::parse($this->attributes['months_3_sale_price_to'])->format('Y-m-d');

        return (($today >= $fromMonths3) && ($today <= $toMonths3)) ? true : false;
    }

    public function getMonths6IsSaleAttribute()
    {
        $today = \Carbon\Carbon::today()->format('Y-m-d');
        $fromMonths6 = \Carbon\Carbon::parse($this->attributes['months_6_sale_price_from'])->format('Y-m-d');
        $toMonths6 = \Carbon\Carbon::parse($this->attributes['months_6_sale_price_to'])->format('Y-m-d');

        return (($today >= $fromMonths6) && ($today <= $toMonths6)) ? true : false;
    }

    public function getMonths12IsSaleAttribute()
    {
        $today = \Carbon\Carbon::today()->format('Y-m-d');
        $fromMonths12 = \Carbon\Carbon::parse($this->attributes['months_12_sale_price_from'])->format('Y-m-d');
        $toMonths12 = \Carbon\Carbon::parse($this->attributes['months_12_sale_price_to'])->format('Y-m-d');

        return (($today >= $fromMonths12) && ($today <= $toMonths12)) ? true : false;
    }
}
