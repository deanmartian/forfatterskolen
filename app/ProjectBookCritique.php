<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ProjectBookCritique extends Model
{
    protected $fillable = ['project_id', 'book_content', 'description', 'is_file', 'feedback'];

    protected $appends = [
        'file_link',
        'filename',
        'date_uploaded',
        'feedback_file',
    ];

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
            if (end($extension) == 'pdf' || end($extension) == 'odt') {
                $fileLink = '<a href="/js/ViewerJS/#../..'.$filename.'">'.basename($filename).'</a>';
            } elseif (end($extension) == 'docx' || end($extension) == 'doc') {
                $fileLink = '<a href="https://view.officeapps.live.com/op/embed.aspx?src='.url('').$filename.'">'
                    .basename($filename).'</a>';
            }
        }

        return $fileLink;
    }

    public function getDateUploadedAttribute()
    {
        return Carbon::parse($this->attributes['created_at'])->format('d.m.Y');
    }

    public function getFeedbackFileAttribute()
    {
        $fileLink = '';
        $filename = $this->attributes['feedback'];

        $extension = explode('.', basename($filename));
        if (end($extension) == 'pdf' || end($extension) == 'odt') {
            $fileLink = '<a href="/js/ViewerJS/#../..'.$filename.'">'.basename($filename).'</a>';
        } elseif (end($extension) == 'docx' || end($extension) == 'doc') {
            $fileLink = '<a href="https://view.officeapps.live.com/op/embed.aspx?src='.url('').$filename.'">'
                .basename($filename).'</a>';
        }

        return $fileLink;
    }
}
