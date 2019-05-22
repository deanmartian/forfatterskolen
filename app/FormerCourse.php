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
    protected $fillable = ['user_id', 'package_id', 'is_active', 'started_at', 'start_date', 'end_date', 'access_lessons',
        'years', 'is_free', 'created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function package()
    {
        return $this->belongsTo('App\Package');
    }

}