<?php

namespace App;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'testimonials';

    const ACTIVE = true;

    const INACTIVE = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'description', 'testimony', 'author_image', 'book_image', 'status'];

    public function getStatusTextAttribute()
    {
        return $this->attributes['status'] == self::ACTIVE ? 'Enabled' : 'Disabled';
    }

    #[Scope]
    protected function active($query)
    {
        return $query->where('status', self::ACTIVE);
    }
}
