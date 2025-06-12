<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Blog extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'blog';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'description', 'user_id', 'image', 'author_name', 'author_image', 'status',
        'schedule'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User::class);
    }

    public function getCreatedAtAttribute($value)
    {
        return date_format(date_create($value), 'M d, Y');
    }

    public static function activeOnly()
    {
        return self::where('status', '=', 1)
            ->where(function ($query) {
                $query->whereDate('schedule', '<=', Carbon::today()->format('Y-m-d'))
                    ->orWhereNull('schedule');
            });
    }
}
