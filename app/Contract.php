<?php

namespace App;

use App\Http\AdminHelpers;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{

    const SUPER_ADMIN_ONLY = 1;

    protected $fillable = [
        'code',
        'title',
        'image',
        'details',
        'admin_name',
        'admin_signature',
        'admin_signed_date',
        'signature_label',
        'signature',
        'end_date',
        'signed_date',
        'status'
    ];

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

}