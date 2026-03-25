<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthorProfile extends Model
{
    protected $fillable = [
        'user_id', 'display_name', 'slug', 'bio', 'long_bio',
        'photo_path', 'website', 'social_links', 'is_visible',
    ];

    protected $casts = [
        'social_links' => 'array',
        'is_visible' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function books()
    {
        return $this->user->projects()
            ->with('books')
            ->get()
            ->pluck('books')
            ->flatten()
            ->where('shop_visible', true);
    }
}
