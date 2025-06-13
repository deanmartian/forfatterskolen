<?php

namespace App;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseDiscount extends Model
{
    use Loggable;

    protected $fillable = ['course_id', 'coupon', 'discount', 'valid_from', 'valid_to', 'type'];

    protected $types = [
        0 => 'Additional',
        1 => 'Total',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(\App\Course::class);
    }

    public function typeList()
    {
        return $this->types;
    }
}
