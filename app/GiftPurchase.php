<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GiftPurchase extends Model
{
    protected $table = 'gift_purchases';

    // user_id is the buyer id
    protected $fillable = ['user_id', 'parent', 'parent_id', 'redeem_code', 'is_redeemed', 'expired_at'];

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(\App\User::class, 'user_id', 'id');
    }

    public function coursePackage(): BelongsTo
    {
        return $this->belongsTo(\App\Package::class, 'parent_id', 'id');
    }

    public function shopManuscript(): BelongsTo
    {
        return $this->belongsTo(\App\ShopManuscript::class, 'parent_id', 'id');
    }

    public function getItemNameAttribute()
    {
        $itemName = '';
        if ($this->attributes['parent'] === 'course-package') {
            $itemName = $this->coursePackage->course->title.' ('.$this->coursePackage->variation.')';
        }

        return $itemName;
    }

    public function getItemLinkAttribute()
    {
        $itemLink = '';
        if ($this->attributes['parent'] === 'course-package') {
            $itemLink = route('front.course.show', $this->coursePackage->course_id);
        }

        return $itemLink;
    }
}
