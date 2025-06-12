<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectWholeBook extends Model
{
    protected $fillable = ['project_id', 'book_content', 'description', 'dropbox_link', 'is_file', 'designer_id',
        'page_count', 'width', 'height', 'designer_description'];

    protected $appends = ['file_link', 'filename', 'date_uploaded'];

    public function designer(): BelongsTo
    {
        return $this->belongsTo(\App\User::class, 'designer_id', 'id');
    }

    public function getFilenameAttribute()
    {
        return basename($this->attributes['book_content']);
    }

    public function getFileLinkAttribute()
    {
        $fileLink = '';
        $filename = $this->attributes['book_content'];

        if ($this->attributes['is_file']) {
            if (strpos($filename, 'project-')) {
                $fileLink = '<a href="'.route('dropbox.shared_link', $filename).'" target="_blank">'.basename($filename).'</a>';
            } else {
                $extension = explode('.', basename($filename));
                if (end($extension) == 'pdf' || end($extension) == 'odt') {
                    $fileLink = '<a href="/js/ViewerJS/#../..'.$filename.'">'.basename($filename).'</a>';
                } elseif (end($extension) == 'docx' || end($extension) == 'doc') {
                    $fileLink = '<a href="https://view.officeapps.live.com/op/embed.aspx?src='.url('').$filename.'">'
                        .basename($filename).'</a>';
                }
            }
        }

        return $fileLink;
    }

    public function getDateUploadedAttribute()
    {
        return Carbon::parse($this->attributes['created_at'])->format('d.m.Y');
    }
}
