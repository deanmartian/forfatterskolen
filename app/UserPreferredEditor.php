<?php

namespace App;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User::class);
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(\App\User::class, 'editor_id', 'id');
    }
}
