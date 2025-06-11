<?php

namespace App;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class UserPreferredEditor extends Model
{
    use Loggable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_preferred_editor';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'editor_id'];

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function editor()
    {
        return $this->belongsTo(\App\User::class, 'editor_id', 'id');
    }
}
