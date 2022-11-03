<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectMarketing extends Model
{

    protected $table = 'project_marketing';
    protected $fillable = ['project_id', 'type', 'value', 'is_finished'];
    protected $appends = ['is_finished_text', 'file_link'];

    public function scopeCulturalCouncils( $query )
    {
        $query->where('type', 'cultural-council');
    }

    public function scopeFreeWords( $query )
    {
        $query->where('type', 'application-free-word');
    }

    public function scopePrintEbooks( $query )
    {
        $query->where('type', 'print-ebook');
    }

    public function scopeSampleBookApproved( $query )
    {
        $query->where('type', 'sample-book-approved');
    }

    public function scopePdfPrintIsApproved( $query )
    {
        $query->where('type', 'pdf-print-is-approved');
    }

    public function scopeNumberOfAuthorBooks( $query )
    {
        $query->where('type', 'number-of-author-books');
    }

    public function getIsFinishedTextAttribute()
    {
        return $this->attributes['is_finished'] ? 'Yes' : ' No';
    }

    public function getFileLinkAttribute()
    {
        $fileLink = '';
        $filename = $this->attributes['value'];

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
