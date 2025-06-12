<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectBookFormatting extends Model
{
    protected $table = 'project_book_formatting';

    protected $fillable = ['project_id', 'file', 'corporate_page', 'designer_id', 'format', 'format_image', 'description'];

    protected $appends = ['file_link', 'feedback_file_link', 'corporate_page_link', 'format_image_link'];

    public function designer(): BelongsTo
    {
        return $this->belongsTo(\App\User::class, 'designer_id', 'id');
    }

    public function getFileLinkAttribute()
    {
        $fileLink = '';
        $files = explode(',', $this->attributes['file']);

        foreach ($files as $file) {
            $extension = explode('.', basename($file));

            if (strpos($file, 'project-')) {
                $fileLink .= '<a href="'.url('/dropbox/download/'.trim($file)).'">
                    <i class="fa fa-download" aria-hidden="true"></i></a>';
                $fileLink .= ' <a href="'.url('/dropbox/shared-link/'.trim($file)).'" target="_blank">'.basename($file).'</a>';
                $fileLink .= ', ';
            } else {
                if (end($extension) == 'pdf' || end($extension) == 'odt') {
                    $fileLink .= '<a href="/js/ViewerJS/#../..'.trim($file).'">'.basename($file).'</a>';

                    if ($file) {
                        $fileLink .= ' <a href="'.$file.'" download><i class="fa fa-download" aria-hidden="true"></i></a>';
                    }

                    $fileLink .= ', ';
                } else {
                    $fileLink .= '<a href="https://view.officeapps.live.com/op/embed.aspx?src='.url('').trim($file).'">'.basename($file).'</a>';

                    if ($file) {
                        $fileLink .= ' <a href="'.$file.'" download><i class="fa fa-download" aria-hidden="true"></i></a>';
                    }

                    $fileLink .= ', ';
                }
            }
        }

        return trim($fileLink, ', ');
    }

    public function getFeedbackFileLinkAttribute()
    {
        $fileLink = '';
        $filename = $this->attributes['feedback'];

        if ($filename) {
            $extension = explode('.', basename($filename));
            if (strpos($filename, 'storage')) {
                if (end($extension) == 'pdf' || end($extension) == 'odt') {
                    $fileLink = '<a href="/js/ViewerJS/#../..'.$filename.'">'.basename($filename).'</a>';
                } elseif (end($extension) == 'docx' || end($extension) == 'doc') {
                    $fileLink = '<a href="https://view.officeapps.live.com/op/embed.aspx?src='.url('').$filename.'">'
                        .basename($filename).'</a>';
                }
            } else {
                $fileLink = '<a href="'.url('/dropbox/download/'.trim($filename)).'">
                <i class="fa fa-download"></i></a> ';
                $fileLink .= '<a href="'.url('/dropbox/shared-link/'.trim($filename)).'" target="_blank">'.basename($filename).'</a>';
            }
        }

        return $fileLink;
    }

    public function getCorporatePageLinkAttribute()
    {
        $fileLink = '';
        $file = $this->attributes['corporate_page'];
        if ($file) {
            $fileLink .= '<a href="'.url('/dropbox/download/'.trim($file)).'">
                    <i class="fa fa-download" aria-hidden="true"></i></a>';
            $fileLink .= ' <a href="'.url('/dropbox/shared-link/'.trim($file)).'" target="_blank">'.basename($file).'</a>';
        }

        return $fileLink;
    }

    public function getFormatImageLinkAttribute()
    {
        $fileLink = '';
        $file = $this->attributes['format_image'];
        if ($file) {
            $fileLink .= '<a href="'.url('/dropbox/download/'.trim($file)).'">
                    <i class="fa fa-download" aria-hidden="true"></i></a>';
            $fileLink .= ' '.basename($file);
        }

        return $fileLink;
    }
}
