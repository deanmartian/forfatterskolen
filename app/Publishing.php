<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Publishing extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'publishing';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['publishing', 'home_link', 'mail_address', 'visiting_address', 'phone', 'genre',
        'send_manuscript_link', 'email'];
}
