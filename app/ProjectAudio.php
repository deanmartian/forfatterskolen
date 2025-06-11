<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectAudio extends Model
{
    protected $table = 'project_audios';

    protected $fillable = ['project_id', 'type', 'value'];

    protected $appends = ['file_link'];

    public function scopeFiles($query)
    {
        $query->where('type', 'files');
    }

    public function scopeCover($query)
    {
        $query->where('type', 'cover');
    }

    public function getFileLinkAttribute()
    {
        $filename = $this->attributes['value'];
        $fileLink = null;
        if ($filename) {
            $fileLink = '<a href="'.url('/dropbox/shared-link/'.trim($filename)).'" target="_blank">'.basename($filename).'</a>';
        }

        return $fileLink;
    }
}
