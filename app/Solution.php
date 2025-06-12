<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Solution extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'solutions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'description', 'is_instruction', 'image'];

    public function articles(): HasMany
    {
        return $this->hasMany(\App\SolutionArticle::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($lesson) { // before delete() method call this
            $lesson->articles()->delete(); // delete lesson document
        });
    }
}
