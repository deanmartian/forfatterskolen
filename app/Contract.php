<?php

namespace App;

use App\Http\AdminHelpers;
use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contract extends Model
{
    use Loggable;

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
        'send_date',
        'is_file',
        'status',
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

    #[Scope]
    protected function adminOnly($query)
    {
        return $query->where('status', 1);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(\App\Project::class);
    }

    /**
     * Accessor field
     */
    public function getSentFileLinkAttribute(): string
    {
        $fileLink = '';
        $filename = isset($this->attributes['sent_file']) ? $this->attributes['sent_file'] : null;

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
    public function getSignedFileLinkAttribute(): string
    {
        $fileLink = '';
        if (isset($this->attributes['signed_file'])) {
            $filename = $this->attributes['signed_file'];

            $extension = explode('.', basename($filename));
            if (end($extension) == 'pdf' || end($extension) == 'odt') {
                $fileLink = '<a href="/js/ViewerJS/#../..'.$filename.'">'.basename($filename).'</a>';
            } elseif (end($extension) == 'docx' || end($extension) == 'doc') {
                $fileLink = '<a href="https://view.officeapps.live.com/op/embed.aspx?src='.url('').$filename.'">'
                    .basename($filename).'</a>';
            }
        }

        return $fileLink;
    }

    public function getLearnerDownloadLinkAttribute()
    {
        $link = route('front.contract.download', $this->attributes['code']);
        if ($this->attributes['is_file'] && isset($this->attributes['signed_file'])) {
            $link = $this->attributes['signed_file'];
        }

        return $link;
    }

    public function getSignatureTextAttribute()
    {
        $label = '<label class="label label-warning">Unsigned</label>';
        if (isset($this->attributes['signature']) && $this->attributes['signature']) {
            $label = '<label class="label label-success">Signed</label>';
        }

        return $label;
    }
}
