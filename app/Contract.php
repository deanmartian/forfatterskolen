<?php

namespace App;

use App\Http\AdminHelpers;
use App\Traits\Loggable;
use Carbon\Carbon;
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
        'contract_type',
        'org_nr',
        'fodselsnummer',
        'mobile',
        'timepris',
        'start_date',
        'reminder_sent_60',
        'reminder_sent_30',
        'renewed_from_id',
        'receiver_name',
        'receiver_email',
        'receiver_address',
    ];

    protected $appends = ['sent_file_link', 'signed_file_link', 'learner_download_link', 'signature_text', 'computed_status'];

    protected $casts = [
        'end_date' => 'date',
        'start_date' => 'date',
        'send_date' => 'date',
        'signed_date' => 'date',
        'admin_signed_date' => 'date',
        'timepris' => 'decimal:2',
        'reminder_sent_60' => 'boolean',
        'reminder_sent_30' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($query) {
            $query->code = AdminHelpers::generateHash(10);
        });
    }

    #[Scope]
    protected function adminOnly($query)
    {
        return $query->where('status', 1);
    }

    #[Scope]
    protected function firma($query)
    {
        return $query->where('contract_type', 'firma');
    }

    #[Scope]
    protected function person($query)
    {
        return $query->where('contract_type', 'person');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(\App\Project::class);
    }

    public function renewedFrom(): BelongsTo
    {
        return $this->belongsTo(self::class, 'renewed_from_id');
    }

    /**
     * Computed status based on dates and signatures.
     */
    public function getComputedStatusAttribute(): string
    {
        if (! $this->send_date && ! $this->signature) {
            return 'draft';
        }
        if ($this->send_date && ! $this->signature) {
            return 'sent';
        }
        if ($this->signature && $this->end_date) {
            if ($this->end_date->isPast()) {
                return 'expired';
            }
            if ($this->end_date->diffInDays(now()) <= 60) {
                return 'expiring';
            }

            return 'active';
        }
        if ($this->signature) {
            return 'signed';
        }

        return 'draft';
    }

    /**
     * Get status badge color class.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->computed_status) {
            'draft' => 'default',
            'sent' => 'info',
            'signed' => 'success',
            'active' => 'success',
            'expiring' => 'warning',
            'expired' => 'danger',
            default => 'default',
        };
    }

    /**
     * Get Norwegian status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->computed_status) {
            'draft' => 'Utkast',
            'sent' => 'Sendt',
            'signed' => 'Signert',
            'active' => 'Aktiv',
            'expiring' => 'Utl&oslash;per snart',
            'expired' => 'Utl&oslash;pt',
            default => 'Ukjent',
        };
    }

    public function getContractTypeLabelAttribute(): string
    {
        return match ($this->contract_type) {
            'firma' => 'Firma',
            'person' => 'Person',
            default => '-',
        };
    }

    public function isExpiringSoon(int $days = 60): bool
    {
        return $this->end_date
            && $this->end_date->isFuture()
            && $this->end_date->diffInDays(now()) <= $days;
    }

    public function isExpired(): bool
    {
        return $this->end_date && $this->end_date->isPast();
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
