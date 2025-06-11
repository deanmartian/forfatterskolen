<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OtherServiceFeedback extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'other_service_feedbacks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['service_id', 'service_type', 'manuscript', 'hours_worked', 'notes_to_head_editor'];
}
