<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SelfPublishingLearner extends Model
{

    protected $fillable = ['user_id', 'self_publishing_id'];

    public function selfPublishing()
    {
        return $this->belongsTo('App\SelfPublishing');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

}
