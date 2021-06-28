<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CheckoutLog extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'parent', 'parent_id', 'is_ordered'];
    protected $appends = ['item_link', 'is_ordered_text', 'order_date'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function course()
    {
        return $this->belongsTo('App\Course', 'parent_id', 'id');
    }

    public function getItemLinkAttribute()
    {
        return $this->courseLink();
    }

    public function courseLink()
    {
        return $this->attributes['parent'] === 'course' ? "<a href='/course/" . $this->course->id. "'>"
            . $this->course->title . "</a>" : '';
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
