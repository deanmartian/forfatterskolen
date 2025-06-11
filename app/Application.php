<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'address',
        'zip',
        'city',
        'file',
    ];

    protected $appends = ['file_link'];

    public function getFileLinkAttribute()
    {
        $fileLink = '';
        $filename = $this->attributes['file'];

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
