<?php

namespace App;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Model;

class ProjectAudio extends Model
{
    protected $table = 'project_audios';

    protected $fillable = ['project_id', 'type', 'value'];

    protected $appends = ['file_link'];

    #[Scope]
    protected function files($query)
    {
        $query->where('type', 'files');
    }

    #[Scope]
    protected function cover($query)
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
