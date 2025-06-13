<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
