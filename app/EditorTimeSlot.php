<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EditorTimeSlot extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['editor_id', 'date', 'start_time', 'duration'];
}
