<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LearnerEmail extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'learner_emails';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'subject', 'email', 'attachment', 'from_name', 'from_email'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}