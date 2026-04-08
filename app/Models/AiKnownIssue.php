<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiKnownIssue extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'workaround',
        'status',
        'severity',
        'category',
        'discovered_at',
        'resolved_at',
        'created_by',
    ];

    protected $casts = [
        'discovered_at' => 'date',
        'resolved_at' => 'date',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
