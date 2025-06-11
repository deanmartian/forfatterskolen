<?php

namespace App;

use FrontendHelpers;
use Illuminate\Database\Eloquent\Model;

class StoragePayoutLog extends Model
{
    protected $fillable = [
        'project_registration_id',
        'year',
        'quarter',
        'amount',
    ];

    protected $appends = [
        'date',
    ];

    public function getDateAttribute()
    {
        return FrontendHelpers::formatDate($this->attributes['created_at']);
    }
}
