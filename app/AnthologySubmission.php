<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AnthologySubmission extends Model
{
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'connection',
        'course_name',
        'title',
        'genre',
        'description',
        'file_path',
        'file_name',
        'word_count',
        'consent_terms',
        'consent_marketing',
        'status',
        'editor_feedback',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'consent_terms' => 'boolean',
        'consent_marketing' => 'boolean',
        'reviewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function getConnectionLabelAttribute()
    {
        return match ($this->connection) {
            'elev' => 'Elev',
            'tidligere_elev' => 'Tidligere elev',
            'ny' => 'Ny skribent',
            default => $this->connection,
        };
    }

    public function getGenreLabelAttribute()
    {
        return match ($this->genre) {
            'novelle' => 'Novelle',
            'krim' => 'Krim & spenning',
            'barnefortelling' => 'Barnefortelling',
            'dikt' => 'Dikt & lyrikk',
            'feelgood' => 'Feelgood',
            'sakprosa' => 'Sakprosa / essay',
            default => $this->genre,
        };
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'received' => 'Mottatt',
            'under_review' => 'Under vurdering',
            'selected' => 'Valgt ut',
            'not_selected' => 'Ikke valgt',
            'feedback_sent' => 'Tilbakemelding sendt',
            default => $this->status,
        };
    }
}
