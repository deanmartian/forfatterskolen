<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $table = 'packages';
    /**
     * issue_date is for the faktura issue date
     * @var array
     */
    protected $fillable = ['course_id', 'variation', 'full_months_price', 'months_3_price', 'months_6_price', 'months_12_price',
        'full_price_product', 'months_3_product', 'months_6_product', 'months_12_product', 'full_price_due_date',
        'months_3_due_date', 'months_6_due_date', 'months_12_due_date', 'months_3_enable', 'months_6_enable', 'months_12_enable',
        'manuscripts_count', 'due_date', 'has_student_discount', 'is_reward','issue_date', 'validity_period'];

    public function course()
    {
        return $this->belongsTo('App\Course')->orderBy('created_at', 'desc');
    }

    public function shop_manuscripts()
    {
    	return $this->hasMany('App\PackageShopManuscript')->orderBy('created_at', 'desc');
    }


    public function workshops()
    {
        return $this->hasMany('App\PackageWorkshop')->orderBy('created_at', 'desc');
    }


    public function included_courses()
    {
        return $this->hasMany('App\PackageCourse', 'package_id')->orderBy('created_at', 'desc');
    }
}
