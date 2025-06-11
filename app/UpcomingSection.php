<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UpcomingSection extends Model
{
    protected $fillable = ['name', 'title', 'description', 'date', 'link', 'link_label'];

    protected $appends = ['date_field'];

    public function getDateFieldAttribute()
    {
        if ($this->attributes['date']) {
            return strftime('%Y-%m-%dT%H:%M:%S', strtotime($this->attributes['date']));
        }

        return null;
    }
}
