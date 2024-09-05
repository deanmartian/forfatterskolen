<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectBookFormatting extends Model
{

    protected $table = 'project_book_formatting';
    protected $fillable = ['project_id', 'file', 'designer_id'];
    protected $appends = ['file_link', 'feedback_file_link'];

    public function designer()
    {
        return $this->belongsTo('App\User', 'designer_id', 'id');
    }

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

    public function getFeedbackFileLinkAttribute()
    {
        $fileLink = '';
        $filename = $this->attributes['feedback'];

        if ($filename) {
            $extension = explode('.', basename($filename));
            if (strpos($filename, 'storage')) {
                if( end($extension) == 'pdf' || end($extension) == 'odt' ) {
                    $fileLink = '<a href="/js/ViewerJS/#../..'.$filename.'">'.basename($filename).'</a>';
                } elseif( end($extension) == 'docx' || end($extension) == 'doc' ) {
                    $fileLink = '<a href="https://view.officeapps.live.com/op/embed.aspx?src='.url('').$filename.'">'
                        .basename($filename).'</a>';
                }
            } else {
                $fileLink = '<a href="'.route('dropbox.download_file', trim($filename)).'">
                <i class="fa fa-download"></i></a> ';
                $fileLink .= '<a href="'.route('dropbox.shared_link', $filename).'" target="_blank">' .basename($filename).'</a>';
            }
        }

        return $fileLink;
    }
}
