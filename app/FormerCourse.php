<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class FormerCourse extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'former_courses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'package_id', 'date_ended', 'course_created_at'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function package()
    {
        return $this->belongsTo('App\Package');
    }

}