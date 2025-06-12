<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SelfPublishingFeedback extends Model
{
    protected $fillable = ['self_publishing_id', 'feedback_user_id', 'manuscript', 'notes'];

    protected $appends = ['file_link'];

    public function selfPublishing(): BelongsTo
    {
        return $this->belongsTo(\App\SelfPublishing::class);
    }

    public function feedbackUser(): BelongsTo
    {
        return $this->belongsTo(\App\User::class, 'feedback_user_id', 'id');
    }

    /**
     * Accessor field
     */
    public function getFileLinkAttribute(): string
    {
        $fileLink = '';
        $files = explode(',', $this->attributes['manuscript']);

        foreach ($files as $file) {
            $extension = explode('.', basename($file));

            if (end($extension) == 'pdf' || end($extension) == 'odt') {
                $fileLink .= '<a href="/js/ViewerJS/#../..'.trim($file).'">'.basename($file).'</a>, ';
            } else {
                $fileLink .= '<a href="https://view.officeapps.live.com/op/embed.aspx?src='.url('').trim($file).'">'.basename($file).'</a>, ';
            }
        }

        return trim($fileLink, ', ');
    }
}
