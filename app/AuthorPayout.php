<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AuthorPayout extends Model
{
    protected $fillable = [
        'user_id',
        'year',
        'quarter',
        'amount_total',
        'paid_at',
        'paid_by_user_id',
        'note',
        'statement_path',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'amount_total' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(AuthorPayoutItem::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by_user_id');
    }
}
