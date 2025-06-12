<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class CourseRewardCoupon extends Model
{
    protected $fillable = ['course_id', 'coupon', 'is_used'];

    public function course(): BelongsTo
    {
        return $this->belongsTo(\App\Course::class);
    }
}
