<?php

namespace App;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Model;

class ProjectEbook extends Model
{
    protected $fillable = ['project_id', 'type', 'value'];

    protected $appends = ['file_link'];

    #[Scope]
    protected function epub($query)
    {
        $query->where('type', 'epub');
    }

    #[Scope]
    protected function mobi($query)
    {
        $query->where('type', 'mobi');
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
            $fileLink = '<a href="'.'/dropbox/shared-link/'.$filename.'" target="_blank">'.basename($filename).'</a>';
        }

        return $fileLink;
    }
}
