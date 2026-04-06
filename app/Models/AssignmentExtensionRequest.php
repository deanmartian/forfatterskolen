<?php

namespace App\Models;

use App\Assignment;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignmentExtensionRequest extends Model
{
    protected $fillable = [
        'assignment_id', 'user_id', 'original_deadline', 'requested_deadline',
        'reason', 'status', 'decided_by', 'decided_at', 'admin_note',
    ];

    protected $casts = [
        'decided_at' => 'datetime',
        'original_deadline' => 'date',
        'requested_deadline' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function decidedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
