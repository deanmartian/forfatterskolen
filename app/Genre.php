<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Genre extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'genre';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    public $timestamps = false; // disable the update of created/updated since field does not exist

    public function editorGenrePreferences(): HasMany
    {
        return $this->hasMany('App\Models\EditorGenrePreferences', 'genre_id', 'id');
    }
}
