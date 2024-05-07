<?php

namespace App;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class SelfPublishing extends Model
{
    
    protected $table = 'self_publishing';
    protected $fillable = ['title', 'description', 'manuscript', 'word_count', 'editor_id', 'project_id', 'price',
        'editor_share', 'expected_finish'];
    protected $appends = ['file_link', 'file_link_with_download'];

    public function learners()
    {
        return $this->hasMany('App\SelfPublishingLearner');
    }

    public function editor()
    {
        return $this->belongsTo('App\User');
    }

    public function feedback()
    {
        return $this->hasOne('App\SelfPublishingFeedback');
    }

    public function project()
    {
        return $this->belongsTo('App\Project');
    }

    /**
     * Accessor field
     * @return string
     */
    public function getFileLinkAttribute()
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
     * @return string
     */
    public function getFileLinkWithDownloadAttribute()
    {
        $fileLink = '';
        $files = explode(',', $this->attributes['manuscript']);

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
}
