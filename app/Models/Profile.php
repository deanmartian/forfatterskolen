<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'name',
        'author_name',
        'avatar_url',
        'bio',
        'genres',
        'writing_interests',
        'current_project',
        'badge',
        'access_level',
        'is_suspended',
    ];

    protected $casts = [
        'genres' => 'array',
        'writing_interests' => 'array',
        'is_suspended' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
