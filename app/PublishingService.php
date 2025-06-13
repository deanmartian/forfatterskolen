<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Str;

class PublishingService extends Model
{
    protected $table = 'publishing_services';

    protected $fillable = [
        'product_service',
        'description',
        'price',
        'per_word_hour',
        'per_unit',
        'base_char_word',
        'slug',
        'service_type',
        'is_active',
    ];

    protected $appends = ['short_description'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function getShortDescriptionAttribute()
    {
        $text = $this->attributes['description'];
        $maxCharacters = 220;

        if (Str::length($text) > $maxCharacters) {
            $truncatedText = Str::limit($text, $maxCharacters, '...');
        } else {
            $truncatedText = $text;
        }

        return $truncatedText;
    }
}
