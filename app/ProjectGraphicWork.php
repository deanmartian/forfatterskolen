<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectGraphicWork extends Model
{
    protected $fillable = ['project_id', 'type', 'value', 'description', 'print_ready', 'format', 'isbn_id',
        'backside_text', 'backside_image', 'instruction', 'date', 'is_checked', 'upload_date'];

    protected $appends = ['image', 'file_link', 'interior', 'backside_type'];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if ($model->isDirty('value')) {
                $model->upload_date = today()->format('Y-m-d');
            }
        });

        /* static::deleting(function($graphicWork) { // before delete() method call this
            $file = public_path($graphicWork->value);
            if(\File::isFile($file)){
                \File::delete($file);
            }
        }); */
    }

    public function scopeCover($query)
    {
        $query->where('type', 'cover');
    }

    public function scopeBarcode($query)
    {
        $query->where('type', 'barcode');
    }

    public function scopeRewriteScripts($query)
    {
        $query->where('type', 'rewrite-script');
    }

    public function scopeTrialPage($query)
    {
        $query->where('type', 'trial-page');
    }

    public function scopeSampleBookPdf($query)
    {
        $query->where('type', 'sample-book-pdf');
    }

    public function scopePrintReady($query)
    {
        $query->where('type', 'print-ready');
    }

    public function scopeIndesigns($query)
    {
        $query->where('type', 'indesign');
    }

    public function isbn()
    {
        return $this->hasOne('\App\ProjectRegistration', 'id', 'isbn_id');
    }

    public function getImageAttribute()
    {
        $filename = $this->attributes['value'];
        $fileLink = null;
        if ($filename) {
            if (strpos($filename, 'project-')) {
                $fileLink = '<a href="'.url('/dropbox/shared-link/'.trim($filename)).'" target="_blank">'.basename($filename).'</a>';
            } else {
                $fileLink = '<a href="'.asset($filename).'">'.basename($filename).'</a>';
            }
        }

        return $fileLink;
    }

    public function getInteriorAttribute()
    {
        $filename = $this->attributes['description'];
        $fileLink = null;
        if ($filename) {
            if (strpos($filename, 'project-')) {
                $fileLink = '<a href="'.url('/dropbox/shared-link/'.trim($filename)).'" target="_blank">'.basename($filename).'</a>';
            } else {
                $fileLink = '<a href="'.asset($filename).'">'.basename($filename).'</a>';
            }
        }

        return $fileLink;
    }

    public function getFileLinkAttribute()
    {
        $fileLink = '';
        $filename = $this->attributes['value'];

        $extension = explode('.', basename($filename));
        if (strpos($filename, 'project-')) {
            $fileLink = '<a href="'.url('/dropbox/shared-link/'.trim($filename)).'" target="_blank">'.basename($filename).'</a>';
        } else {
            if (end($extension) == 'pdf' || end($extension) == 'odt') {
                $fileLink = '<a href="/js/ViewerJS/#../..'.$filename.'">'.basename($filename).'</a>';
            } elseif (end($extension) == 'docx' || end($extension) == 'doc') {
                $fileLink = '<a href="https://view.officeapps.live.com/op/embed.aspx?src='.url('').$filename.'">'
                    .basename($filename).'</a>';
            }
        }

        return $fileLink;
    }

    public function getBacksideTypeAttribute()
    {
        if (strpos($this->attributes['backside_text'], 'project-')) {
            return 'file';
        }

        return 'text';
    }
}
