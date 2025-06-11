<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StorageDistributionCost extends Model
{
    protected $fillable = [
        'project_book_id',
        'nr',
        'service',
        'number',
        'amount',
        'date',
    ];

    protected $appends = [
        'learner_amount',
    ];

    public function getLearnerAmountAttribute()
    {
        return $this->attributes['amount'] * 1.2;
    }
}
