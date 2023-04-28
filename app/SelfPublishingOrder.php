<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SelfPublishingOrder extends Model
{
    
    protected $fillable = [
        'user_id',
        'project_id',
        'parent',
        'parent_id',
        'file',
        'price',
        'word_count',
        'status'
    ];

    protected $appends = ['service_name'];

    public function scopeActive($query)
    {
        $query->where('status', 'active');
    }

    public function scopePaid($query)
    {
        $query->where('status', 'paid');
    }

    public function service()
    {
        return $this->belongsTo('App\PublishingService', 'parent_id', 'id');
    }

    public function getServiceNameAttribute() 
    {
        return $this->service->product_service;
    }

}
