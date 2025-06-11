<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'role', 'joined_reader_community'];
}
