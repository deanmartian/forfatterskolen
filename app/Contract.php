<?php

namespace App;

use App\Http\AdminHelpers;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{

    const SUPER_ADMIN_ONLY = 1;

    protected $fillable = [
        'code',
        'project_id',
        'title',
        'image',
        'details',
        'admin_name',
        'admin_signature',
        'admin_signed_date',
        'signature_label',
        'signature',
        'sent_file',
        'signed_file',
        'end_date',
        'signed_date',
        'is_file',
        'status'
    ];

    protected $appends = ['sent_file_link', 'signed_file_link', 'learner_download_link', 'signature_text'];

    protected static function boot()
    {
        parent::boot();

        // add value to code on create
        static::creating(function ($query) {
            $query->code = AdminHelpers::generateHash(10);
        });
    }

    public function scopeAdminOnly($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Accessor field
     * @return string
     */
    public function getSentFileLinkAttribute()
    {
        $fileLink = '';
        $filename = $this->attributes['sent_file'];

        $extension = explode('.', basename($filename));
        if( end($extension) == 'pdf' || end($extension) == 'odt' ) {
            $fileLink = '<a href="/js/ViewerJS/#../..'.$filename.'">'.basename($filename).'</a>';
        } elseif( end($extension) == 'docx' || end($extension) == 'doc' ) {
            $fileLink = '<a href="https://view.officeapps.live.com/op/embed.aspx?src='.url('').$filename.'">'
                .basename($filename).'</a>';
        }

        return $fileLink;
    }

    /**
     * Accessor field
     * @return string
     */
    public function getSignedFileLinkAttribute()
    {
        $fileLink = '';
        $filename = $this->attributes['signed_file'];

        $extension = explode('.', basename($filename));
        if( end($extension) == 'pdf' || end($extension) == 'odt' ) {
            $fileLink = '<a href="/js/ViewerJS/#../..'.$filename.'">'.basename($filename).'</a>';
        } elseif( end($extension) == 'docx' || end($extension) == 'doc' ) {
            $fileLink = '<a href="https://view.officeapps.live.com/op/embed.aspx?src='.url('').$filename.'">'
                .basename($filename).'</a>';
        }

        return $fileLink;
    }

    public function getLearnerDownloadLinkAttribute()
    {
        $link = route('front.contract.download', $this->attributes['code']);
        if ($this->attributes['is_file']) {
            $link = $this->attributes['signed_file'];
        }
        return $link;
    }

    public function getSignatureTextAttribute()
    {
        $label = '<label class="label label-warning">Unsigned</label>';
        if ($this->attributes['signature']) {
            $label = '<label class="label label-success">Signed</label>';
        }
        return $label;
    }

}