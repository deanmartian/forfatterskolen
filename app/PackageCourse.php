<?php

namespace App;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageCourse extends Model
{
    use Loggable;

    protected $table = 'package_courses';

    protected $fillable = ['package_id', 'included_package_id'];

    protected $appends = ['included_package_course_title', 'included_package_variation'];

    public function package(): BelongsTo
    {
        return $this->belongsTo(\App\Package::class, 'package_id');
    }

    public function included_package(): BelongsTo
    {
        return $this->belongsTo(\App\Package::class, 'included_package_id');
    }

    public function getIncludedPackageCourseTitleAttribute()
    {
        return $this->included_package->course->title;
    }

    public function getIncludedPackageVariationAttribute()
    {
        return $this->included_package->variation;
    }
}
