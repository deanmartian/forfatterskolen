<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectEbook extends Model
{
    protected $fillable = ['project_id', 'type', 'value'];
    protected $appends = ['file_link'];

    public function scopeEpub( $query )
    {
        $query->where('type', 'epub');
    }

    public function scopeMobi( $query )
    {
        $query->where('type', 'mobi');
    }

    public function scopeCover( $query )
    {
        $query->where('type', 'cover');
    }

    public function getFileLinkAttribute()
    {
        $filename = $this->attributes['value'];
        $fileLink = NULL;
        if ($filename) {
            $fileLink = '<a href="'.route('dropbox.shared_link', $filename).'" target="_blank">' .basename($filename).'</a>';
        }
        return $fileLink;
    }
}
