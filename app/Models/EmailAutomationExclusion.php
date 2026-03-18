<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailAutomationExclusion extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'contact_id', 'user_id', 'reason', 'course_id', 'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
