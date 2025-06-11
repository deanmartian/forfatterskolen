<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WritingGroup extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'writing_groups';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'contact_id', 'description', 'group_photo', 'next_meeting'];
}
