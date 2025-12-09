<?php

namespace App;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Model;

class ProjectEbook extends Model
{
    protected $fillable = ['project_id', 'type', 'value'];

    protected $appends = ['file_link'];

    protected function scopeEpub($query)
    {
        $query->where('type', 'epub');
    }

    protected function scopeMobi($query)
    {
        $query->where('type', 'mobi');
    }

    protected function scopeCover($query)
    {
        $query->where('type', 'cover');
    }

    public function getFileLinkAttribute()
    {
        $filename = $this->attributes['value'];
        $fileLink = null;
        if ($filename) {
            $fileLink = '<a href="'.'/dropbox/shared-link/'.$filename.'" target="_blank">'.basename($filename).'</a>';
        }

        return $fileLink;
    }
}
