<?php

namespace App;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TimeRegister extends Model
{
    use Loggable;

    protected $fillable = ['user_id', 'project_id', 'date', 'time', 'time_used', 'description', 'invoice_file', 'notes'];

    protected $appends = ['file_link', 'notes_formatted'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User::class);
    }

    public function project(): HasOne
    {
        return $this->hasOne(\App\Project::class, 'id', 'project_id');
    }

    public function usedTimes(): HasMany
    {
        return $this->hasMany(\App\TimeRegisterUsed::class);
    }

    public function usedTimesDurationSum(): HasMany
    {
        return $this->hasMany(\App\TimeRegisterUsed::class)->selectRaw('SUM(time_used) as total_duration')
            ->groupBy('time_register_id');
    }

    public function getFileLinkAttribute()
    {
        $fileLink = '';
        if (array_key_exists('invoice_file', $this->attributes)) {
            $filename = $this->attributes['invoice_file'];
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

    public function getNotesFormattedAttribute()
    {
        return nl2br($this->attributes['notes']);
    }
}
