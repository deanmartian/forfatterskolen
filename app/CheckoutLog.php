<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CheckoutLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'parent', 'parent_id', 'is_ordered'];

    protected $appends = ['item_link', 'is_ordered_text', 'order_date'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(\App\Course::class, 'parent_id', 'id');
    }

    public function getItemLinkAttribute()
    {
        return $this->courseLink();
    }

    public function courseLink()
    {
        return $this->attributes['parent'] === 'course' ? "<a href='/course/".$this->course->id."'>"
            .$this->course->title.'</a>' : '';
    }

    public function getIsOrderedTextAttribute()
    {
        return $this->attributes['is_ordered'] ? 'Yes' : 'No';
    }

    public function getOrderDateAttribute()
    {
        return Carbon::parse($this->attributes['created_at'])->format('Y-m-d H:i A');
    }
}
