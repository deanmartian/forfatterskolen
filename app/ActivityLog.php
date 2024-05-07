<?php

namespace App;

use FrontendHelpers;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    
    protected $fillable = [
        'user_id',
        'log_date',
        'table_name',
        'log_type',
        'data'
    ];

    public $dates = ['log_date'];
    protected $appends = ['dateHumanize', 'json_data', 'formatted_date'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getDateHumanizeAttribute()
    {
        return $this->log_date->diffForHumans();
    }

    public function getJsonDataAttribute()
    {
        return json_decode($this->data, true);
    }

    public function getFormattedDateAttribute()
    {
        return FrontendHelpers::formatToYMDtoPrettyDate($this->log_date);
    }
}
