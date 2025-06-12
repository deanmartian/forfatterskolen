<?php

/**
 * Created by PhpStorm.
 * User: janiel
 * Date: 11/7/2019
 * Time: 9:42 AM
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonalTrainerApplicant extends Model
{
    protected $fillable = ['user_id', 'age', 'optional_words', 'reason_for_applying', 'need_in_course', 'expectations',
        'how_ready'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User::class);
    }
}
