<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectMarketing extends Model
{

    protected $table = 'project_marketing';
    protected $fillable = ['project_id', 'type', 'value', 'details', 'date', 'is_finished'];
    protected $appends = ['is_finished_text', 'file_link'];

    public function scopeEmailBookstores( $query )
    {
        $query->where('type', 'email-bookstore');
    }

    public function scopeEmailLibraries( $query )
    {
        $query->where('type', 'email-library');
    }

    public function scopeEmailPress( $query )
    {
        $query->where('type', 'email-press');
    }

    public function scopeReviewCopiesSent( $query )
    {
        $query->where('type', 'review-copies-sent');
    }

    public function scopeSetupOnlineStore( $query )
    {
        $query->where('type', 'setup-online-store');
    }

    public function scopeSetupFacebook( $query )
    {
        $query->where('type', 'setup-facebook');
    }

    public function scopeAdvertisementFacebook( $query )
    {
        $query->where('type', 'advertisement-facebook');
    }

    public function scopeManuscriptSentToPrint( $query )
    {
        $query->where('type', 'manuscripts-sent-to-print');
    }

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

    public function scopeUpdateTheBookBase( $query )
    {
        $query->where('type', 'update-the-book-base');
    }

    public function scopeEbookOrdered( $query )
    {
        $query->where('type', 'ebook-ordered');
    }

    public function scopeEbookReceived( $query )
    {
        $query->where('type', 'ebook-received');
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
