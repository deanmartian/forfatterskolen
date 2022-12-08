<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GiftPurchase extends Model
{

    protected $table = 'gift_purchases';
    // user_id is the buyer id
    protected $fillable = ['user_id', 'parent', 'parent_id', 'redeem_code', 'is_redeemed', 'expired_at'];

    public function buyer()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function coursePackage()
    {
        return $this->belongsTo('App\Package', 'parent_id', 'id');
    }

    public function shopManuscript()
    {
        return $this->belongsTo('App\ShopManuscript', 'parent_id', 'id');
    }

    public function getItemNameAttribute()
    {
        $itemName = '';
        if ($this->attributes['parent'] === 'course-package') {
            $itemName = $this->coursePackage->course->title . ' (' . $this->coursePackage->variation . ')';
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
