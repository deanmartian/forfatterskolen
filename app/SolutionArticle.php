<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class SolutionArticle extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'solution_articles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['solution_id', 'title', 'details'];

    public function solution(): BelongsTo
    {
        return $this->belongsTo(\App\Solution::class);
    }
}
