<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectBookFormatting extends Model
{

    protected $table = 'project_book_formatting';
    protected $fillable = ['project_id', 'file'];
    protected $appends = ['file_link'];

    public function getFileLinkAttribute()
    {
        $fileLink = '';
        $filename = $this->attributes['file'];

        $extension = explode('.', basename($filename));
        if (strpos($filename, 'storage')) {
            if( end($extension) == 'pdf' || end($extension) == 'odt' ) {
                $fileLink = '<a href="/js/ViewerJS/#../..'.$filename.'">'.basename($filename).'</a>';
            } elseif( end($extension) == 'docx' || end($extension) == 'doc' ) {
                $fileLink = '<a href="https://view.officeapps.live.com/op/embed.aspx?src='.url('').$filename.'">'
                    .basename($filename).'</a>';
            }
        } else {
            $fileLink = '<a href="'.route('dropbox.shared_link', $filename).'" target="_blank">' .basename($filename).'</a>';
        }

        return $fileLink;
    }
}
