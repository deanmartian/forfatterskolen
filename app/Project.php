<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Project extends Model
{
    protected $fillable = ['user_id', 'name', 'identifier', 'activity_id', 'start_date', 'end_date', 'description',
        'notes', 'is_finished', 'editor_id', 'editor_total_hours', 'is_standard'];

    protected $appends = ['short_notes', 'notes_formatted', 'book_name'];

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function userBookForSale()
    {
        return $this->hasOne(\App\UserBookForSale::class);
    }

    public function books()
    {
        return $this->hasMany(\App\ProjectBook::class);
    }

    public function book()
    {
        return $this->hasOne(\App\ProjectBook::class);
    }

    public function selfPublishingList()
    {
        return $this->hasMany(\App\SelfPublishing::class);
    }

    public function copyEditings()
    {
        return $this->hasMany(\App\CopyEditingManuscript::class)->orderBy('created_at', 'desc');
    }

    public function corrections()
    {
        return $this->hasMany(\App\CorrectionManuscript::class)->orderBy('created_at', 'desc');
    }

    public function registrations()
    {
        return $this->hasMany(\App\ProjectRegistration::class);
    }

    public function print()
    {
        return $this->hasOne(\App\ProjectPrint::class);
    }

    public function getShortNotesAttribute()
    {
        return Str::words($this->attributes['notes'], 250,
            ' ...<a href="'.route('admin.project.notes', $this->attributes['id']).'" class="see-more"">'.'See more'.'</a>');
    }

    public function getNotesFormattedAttribute()
    {
        return nl2br($this->attributes['notes']);
    }

    public function getBookNameAttribute()
    {
        return $this->book ? $this->book->book_name : '';
    }
}
