<?php

namespace App;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SelfPublishingOrder extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'project_id',
        'parent',
        'parent_id',
        'title',
        'description',
        'file',
        'price',
        'word_count',
        'status',
    ];

    protected $appends = ['service_name'];

    #[Scope]
    protected function active($query)
    {
        $query->where('status', 'active');
    }

    #[Scope]
    protected function paid($query)
    {
        $query->where('status', 'paid');
    }

    #[Scope]
    protected function quote($query)
    {
        $query->where('status', 'quote');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(\App\PublishingService::class, 'parent_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User::class);
    }

    public function getServiceNameAttribute()
    {
        return $this->service->product_service;
    }
}
