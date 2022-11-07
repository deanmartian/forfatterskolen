<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TimeRegister extends Model
{

    protected $fillable = ['user_id', 'project_id', 'date', 'time', 'time_used', 'description', 'invoice_file'];
    protected $appends = ['file_link'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function project()
    {
        return $this->hasOne('App\Project', 'id', 'project_id');
    }

    public function usedTimes()
    {
        return $this->hasMany('App\TimeRegisterUsed');
    }

    public function getFileLinkAttribute()
    {
        $fileLink = '';
        $filename = $this->attributes['invoice_file'];

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
