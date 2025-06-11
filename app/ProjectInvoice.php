<?php

namespace App;

use App\Http\AdminHelpers;
use Illuminate\Database\Eloquent\Model;

class ProjectInvoice extends Model
{
    protected $fillable = ['project_id', 'invoice_file', 'notes'];

    public function getFilenameAttribute()
    {
        return AdminHelpers::extractFileName($this->attributes['invoice_file']);
    }
}
