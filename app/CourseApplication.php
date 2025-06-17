<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseApplication extends Model
{
    protected $fillable = [
        'package_id',
        'user_id',
        'age',
        'file_path',
        'approved_date',
    ];

    protected $appends = [
        'file_link',
    ];

    protected function casts(): array
    {
        return [
            'approved_date' => 'timestamp',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(\App\Package::class);
    }

    public function getFileLinkAttribute()
    {
        $fileLink = '';
        $files = explode(',', $this->attributes['file_path']);

        foreach ($files as $file) {
            $extension = explode('.', basename($file));
            if (end($extension) == 'pdf' || end($extension) == 'odt') {
                $fileLink .= '<a href="/js/ViewerJS/#../..'.trim($file).'">'.basename($file).'</a>';
                $fileLink .= ', ';
            } else {
                $fileLink .= '<a href="https://view.officeapps.live.com/op/embed.aspx?src='.url('').trim($file).'">'.basename($file).'</a>';
                $fileLink .= ', ';
            }
        }

        return trim($fileLink, ', ');

        /* $extension = explode('.', basename($filename));
        if( end($extension) == 'pdf' || end($extension) == 'odt' ) {
            $fileLink = '<a href="/js/ViewerJS/#../..'.$filename.'">'.basename($filename).'</a>';
        } elseif( end($extension) == 'docx' || end($extension) == 'doc' ) {
            $fileLink = '<a href="https://view.officeapps.live.com/op/embed.aspx?src='.url('').$filename.'">'
                .basename($filename).'</a>';
        }

        return $fileLink; */
    }
}
