<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SelfPublishing extends Model
{

    protected $table = 'self_publishing';
    protected $fillable = ['title', 'description', 'manuscript', 'word_count', 'price', 'editor_share'];
    protected $appends = ['file_link'];

    public function learners()
    {
        return $this->hasMany('App\SelfPublishingLearner');
    }

    /**
     * Accessor field
     * @return string
     */
    public function getFileLinkAttribute()
    {
        $fileLink = '';
        $filename = $this->attributes['manuscript'];

        $extension = explode('.', basename($filename));
        if( end($extension) == 'pdf' || end($extension) == 'odt' ) {
            $fileLink = '<a href="/js/ViewerJS/#../..'.$filename.'">'.basename($filename).'</a>';
        } elseif( end($extension) == 'docx' || end($extension) == 'doc' ) {
            $fileLink = '<a href="https://view.officeapps.live.com/op/embed.aspx?src='.url('').$filename.'">'
                .basename($filename).'</a>';
        }

        return $fileLink;
    }
}
