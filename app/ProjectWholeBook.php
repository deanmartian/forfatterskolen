<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectWholeBook extends Model
{

    protected $fillable = ['project_id', 'book_content', 'is_file'];
    protected $appends = ['file_link', 'filename'];

    public function getFilenameAttribute()
    {
        return basename($this->attributes['book_content']);
    }

    public function getFileLinkAttribute()
    {
        $fileLink = '';
        $filename = $this->attributes['book_content'];

        if ($this->attributes['is_file']) {
            $extension = explode('.', basename($filename));
            if( end($extension) == 'pdf' || end($extension) == 'odt' ) {
                $fileLink = '<a href="/js/ViewerJS/#../..'.$filename.'">'.basename($filename).'</a>';
            } elseif( end($extension) == 'docx' || end($extension) == 'doc' ) {
                $fileLink = '<a href="https://view.officeapps.live.com/op/embed.aspx?src='.url('').$filename.'">'
                    .basename($filename).'</a>';
            }
        }

        return $fileLink;
    }

}
