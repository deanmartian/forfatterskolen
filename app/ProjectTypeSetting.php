<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectTypeSetting extends Model
{
    protected $fillable = ['project_id', 'file'];

    protected $appends = [
        'dropbox_file_link_with_download',
    ];

    public function getDropboxFileLinkWithDownloadAttribute()
    {
        $fileLink = '';
        $files = explode(',', $this->attributes['file']);

        foreach ($files as $file) {
            $extension = explode('.', basename($file));

            if (strpos($file, 'project-') || strpos($file, 'Forfatterskolen_app')) {
                $fileLink .= '<a href="'.url('/dropbox/shared-link/'.trim($file)).'" target="_blank">'.basename($file).'</a>';
                $fileLink .= ' <a href="'.url('/dropbox/download/'.trim($file)).'">
                    <i class="fa fa-download" aria-hidden="true"></i></a>';
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
}
