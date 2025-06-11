<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EditorGenrePreferences extends Model
{
    protected $fillable = ['editor_id', 'genre_id'];

    public function genre()
    {
        return $this->belongsTo(\App\Genre::class, 'genre_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class, 'editor_id', 'id');
    }
}
