<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectGraphicWork extends Model
{

    protected $fillable = ['project_id', 'type', 'value'];
    protected $appends = ['image', 'file_link'];

    protected static function boot() {
        parent::boot();

        static::deleting(function($graphicWork) { // before delete() method call this
            $file = public_path($graphicWork->value);
            if(\File::isFile($file)){
                \File::delete($file);
            }
        });
    }

    public function scopeCover( $query )
    {
        $query->where('type', 'cover');
    }

    public function scopeBarcode( $query )
    {
        $query->where('type', 'barcode');
    }

    public function scopeRewriteScripts( $query )
    {
        $query->where('type', 'rewrite-script');
    }

    public function scopeTrialPage( $query )
    {
        $query->where('type', 'trial-page');
    }

    public function scopeSampleBookPdf( $query )
    {
        $query->where('type', 'sample-book-pdf');
    }

    public function getImageAttribute()
    {
        $filename = $this->attributes['value'];
        $fileLink = NULL;
        if ($filename) {
            $fileLink = '<a href="'.asset($filename).'">' .basename($filename).'</a>';
        }
        return $fileLink;
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
