<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserPreferredEditor extends Model
{
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
        return $this->belongsTo('App\User');
    }

    public function editor()
    {
        return $this->belongsTo('App\User', 'editor_id', 'id');
    }
}
