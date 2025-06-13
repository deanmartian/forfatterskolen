<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseRewardCoupon extends Model
{
    protected $fillable = ['course_id', 'coupon', 'is_used'];

    public function course(): BelongsTo
    {
        return $this->belongsTo(\App\Course::class);
    }
}
