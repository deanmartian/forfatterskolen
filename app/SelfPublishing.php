<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SelfPublishing extends Model
{
    protected $table = 'self_publishing';

    protected $fillable = ['title', 'description', 'manuscript', 'word_count', 'editor_id', 'project_id', 'price',
        'editor_share', 'expected_finish', 'status'];

    protected $appends = ['file_link', 'file_link_with_download', 'dropbox_file_link_with_download'];

    public function learners(): HasMany
    {
        return $this->hasMany(\App\SelfPublishingLearner::class);
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(\App\User::class);
    }

    public function feedback(): HasOne
    {
        return $this->hasOne(\App\SelfPublishingFeedback::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(\App\Project::class);
    }

    public function poInvoice(): HasOne
    {
        return $this->hasOne(\App\PowerOfficeInvoice::class, 'parent_id', 'id')->where('parent', 'self-publishing');
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

    /**
     * Accessor field
     */
    public function getFileLinkWithDownloadAttribute(): string
    {
        $fileLink = '';
        $files = explode(',', $this->attributes['manuscript']);

        foreach ($files as $file) {
            $extension = explode('.', basename($file));

            if (strpos($file, 'project-')) {
                $fileLink .= '<a href="'.route('dropbox.shared_link', trim($file)).'" target="_blank">'.basename($file).'</a>';
                $fileLink .= ' <a href="'.route('dropbox.download_file', trim($file)).'">
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

    public function getDropboxFileLinkWithDownloadAttribute()
    {
        $fileLink = '';
        $files = explode(',', $this->attributes['manuscript']);

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
