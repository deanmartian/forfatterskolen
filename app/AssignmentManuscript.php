<?php

namespace App;

use App\Http\AdminHelpers;
use App\Traits\Loggable;
use FrontendHelpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssignmentManuscript extends Model
{
    use Loggable;

    protected $table = 'assignment_manuscripts';

    protected $fillable = ['assignment_id', 'user_id', 'filename', 'words', 'grade', 'type', 'manu_type', 'editor_id',
        'join_group', 'letter_to_editor', 'expected_finish', 'editor_expected_finish', 'uploaded_at'];

    protected $appends = [
        'file_link',
        'file_link_with_download',
        'assignment_type',
        'where_in_script',
        'file_extension',
        'file_link_url',
        'uploaded_date',
    ];

    const APPROVED_STATUS = 1; // approved feedback status

    const FINISHED_STATUS = 2; // finished status

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(\App\Assignment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User::class);
    }

    public function feedbacks() // cannot use this.
    {
        return $this->hasMany(\App\AssignmentFeedback::class);
    }

    public function noGroupFeedbacks(): HasMany
    {
        return $this->hasMany(\App\AssignmentFeedbackNoGroup::class);
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(\App\User::class, 'editor_id', 'id');
    }

    /**
     * Accessor field
     */
    public function getFileLinkAttribute(): string
    {
        $fileLink = '';
        $filename = $this->attributes['filename'];

        $extension = explode('.', basename($filename));
        if (end($extension) == 'pdf' || end($extension) == 'odt') {
            $fileLink = '<a href="/js/ViewerJS/#../..'.$filename.'">'.basename($filename).'</a>';
        } elseif (end($extension) == 'docx' || end($extension) == 'doc') {
            $fileLink = '<a href="https://view.officeapps.live.com/op/embed.aspx?src='.url('').$filename.'">'
                .basename($filename).'</a>';
        }

        return $fileLink;
    }

    /**
     * Accessor field
     */
    public function getFileLinkWithDownloadAttribute(): string
    {
        $fileLink = '';
        $files = explode(',', $this->attributes['filename']);

        foreach ($files as $file) {
            $extension = explode('.', basename($file));

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

        return trim($fileLink, ', ');
    }

    public function getFileExtensionAttribute()
    {
        $file = explode('.', basename($this->attributes['filename']));

        return end($file);
    }

    public function getFileLinkUrlAttribute()
    {
        $fileLink = '';
        $file = $this->attributes['filename'];
        $extension = explode('.', basename($file));

        if (end($extension) == 'pdf' || end($extension) == 'odt') {
            $fileLink = '/js/ViewerJS/#../..'.trim($file);
        } else {
            $fileLink = 'https://view.officeapps.live.com/op/embed.aspx?src='.url('').trim($file);
        }

        return $fileLink;
    }

    public function getExpectedFinishAttribute($value)
    {
        return $value ? date_format(date_create($value), 'd.m.Y') : null;
    }

    public function getEditorExpectedFinishAttribute($value)
    {
        return $value ? date_format(date_create($value), 'd.m.Y') : null;
    }

    public function getAssignmentTypeAttribute()
    {
        return isset($this->attributes['type']) && $this->attributes['type']
        ? AdminHelpers::assignmentType($this->attributes['type']) : 'None';
    }

    public function getWhereInScriptAttribute()
    {
        return isset($this->attributes['manu_type']) && $this->attributes['manu_type']
        ? AdminHelpers::manuscriptType($this->attributes['manu_type']) : 'None';
    }

    public function getUploadedDateAttribute()
    {
        return $this->attributes['uploaded_at'] ? FrontendHelpers::formatDate($this->attributes['uploaded_at']) : null;
    }
}
