<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AuthorPayoutItem extends Model
{
    protected $fillable = [
        'author_payout_id',
        'project_registration_id',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function payout()
    {
        return $this->belongsTo(AuthorPayout::class, 'author_payout_id');
    }
}
