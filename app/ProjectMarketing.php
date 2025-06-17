<?php

namespace App;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Model;

class ProjectMarketing extends Model
{
    protected $table = 'project_marketing';

    protected $fillable = ['project_id', 'type', 'value', 'details', 'date', 'is_finished'];

    protected $appends = ['is_finished_text', 'file_link'];

    #[Scope]
    protected function emailBookstores($query)
    {
        $query->where('type', 'email-bookstore');
    }

    #[Scope]
    protected function emailLibraries($query)
    {
        $query->where('type', 'email-library');
    }

    #[Scope]
    protected function emailPress($query)
    {
        $query->where('type', 'email-press');
    }

    #[Scope]
    protected function reviewCopiesSent($query)
    {
        $query->where('type', 'review-copies-sent');
    }

    #[Scope]
    protected function setupOnlineStore($query)
    {
        $query->where('type', 'setup-online-store');
    }

    #[Scope]
    protected function setupFacebook($query)
    {
        $query->where('type', 'setup-facebook');
    }

    #[Scope]
    protected function advertisementFacebook($query)
    {
        $query->where('type', 'advertisement-facebook');
    }

    #[Scope]
    protected function manuscriptSentToPrint($query)
    {
        $query->where('type', 'manuscripts-sent-to-print');
    }

    #[Scope]
    protected function culturalCouncils($query)
    {
        $query->where('type', 'cultural-council');
    }

    #[Scope]
    protected function freeWords($query)
    {
        $query->where('type', 'application-free-word');
    }

    #[Scope]
    protected function printEbooks($query)
    {
        $query->where('type', 'print-ebook');
    }

    #[Scope]
    protected function sampleBookApproved($query)
    {
        $query->where('type', 'sample-book-approved');
    }

    #[Scope]
    protected function pdfPrintIsApproved($query)
    {
        $query->where('type', 'pdf-print-is-approved');
    }

    #[Scope]
    protected function numberOfAuthorBooks($query)
    {
        $query->where('type', 'number-of-author-books');
    }

    #[Scope]
    protected function updateTheBookBase($query)
    {
        $query->where('type', 'update-the-book-base');
    }

    #[Scope]
    protected function agreementOnTimeRegistration($query)
    {
        $query->where('type', 'agreement-on-time-registration');
    }

    #[Scope]
    protected function ebookOrdered($query)
    {
        $query->where('type', 'ebook-ordered');
    }

    #[Scope]
    protected function ebookReceived($query)
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
        if (end($extension) == 'pdf' || end($extension) == 'odt') {
            $fileLink = '<a href="/js/ViewerJS/#../..'.$filename.'">'.basename($filename).'</a>';
        } elseif (end($extension) == 'docx' || end($extension) == 'doc') {
            $fileLink = '<a href="https://view.officeapps.live.com/op/embed.aspx?src='.url('').$filename.'">'
                .basename($filename).'</a>';
        }

        return $fileLink;
    }
}
