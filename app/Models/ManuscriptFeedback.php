<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ManuscriptFeedback extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'manuscript_feedback';

    protected $fillable = [
        'excerpt_id',
        'user_id',
        'content',
    ];

    public function excerpt(): BelongsTo
    {
        return $this->belongsTo(ManuscriptExcerpt::class, 'excerpt_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
