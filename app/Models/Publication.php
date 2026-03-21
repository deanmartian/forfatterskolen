<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Publication extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'word_count' => 'integer',
        'page_count' => 'integer',
        'chapter_count' => 'integer',
        'spine_width_mm' => 'float',
        'wizard_step' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(\App\Project::class);
    }

    public function isProcessing(): bool
    {
        return in_array($this->status, ['parsing', 'composing', 'generating']);
    }

    public function isReady(): bool
    {
        return $this->status === 'preview' || $this->status === 'approved';
    }

    public function hasError(): bool
    {
        return $this->status === 'error';
    }
}
