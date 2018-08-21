<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class PackageCourse extends Model
{
    protected $table = 'package_courses';
    protected $fillable = ['package_id', 'included_package_id'];



    public function package()
    {
        return $this->belongsTo('App\Package', 'package_id');
    }


    public function included_package()
    {
        return $this->belongsTo('App\Package', 'included_package_id');
    }
}
