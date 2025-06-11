<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkshopTakenCount extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'workshop_taken_count';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'workshop_count'];
}
