<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailConfirmation extends Model
{
    protected $fillable = [ 'user_id', 'email', 'token' ];

    public function user(){
        return  $this->belongsTo('App\User');
    }
}
