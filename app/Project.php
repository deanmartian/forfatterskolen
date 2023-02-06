<?php

namespace App;

use App\Http\AdminHelpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Project extends Model
{

    protected $fillable = ['user_id', 'name', 'identifier', 'activity_id', 'start_date', 'end_date', 'description',
        'notes', 'is_finished'];
    protected $appends = ['short_notes', 'notes_formatted'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function books()
    {
        return $this->hasMany('App\ProjectBook');
    }

    public function selfPublishingList()
    {
        return $this->hasMany('App\SelfPublishing');
    }

    public function copyEditings()
    {
        return $this->hasMany('App\CopyEditingManuscript')->orderBy('created_at', 'desc');
    }

    public function corrections()
    {
        return $this->hasMany('App\CorrectionManuscript')->orderBy('created_at', 'desc');
    }

    public function getShortNotesAttribute()
    {
        return Str::words($this->attributes['notes'], 250,
            ' ...<a href="'. route('admin.project.notes', $this->attributes['id']) .'" class="see-more"">'  . 'See more' . '</a>');
    }

    public function getNotesFormattedAttribute()
    {
        return nl2br($this->attributes['notes']);
    }
}
