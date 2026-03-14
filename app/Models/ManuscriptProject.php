<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ManuscriptProject extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'title',
        'genre',
        'description',
        'word_count',
        'status',
    ];

    protected $casts = [
        'word_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function excerpts(): HasMany
    {
        return $this->hasMany(ManuscriptExcerpt::class, 'project_id');
    }

    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'manuscript_followers', 'project_id', 'user_id')
            ->withTimestamps();
    }
}
