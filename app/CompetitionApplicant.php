<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetitionApplicant extends Model
{
    protected $fillable = ['user_id', 'manuscript'];

    public static function boot()
    {
        parent::boot();

        // if the row is deleted, delete also the document for that row
        CompetitionApplicant::deleted(function ($competition) {
            $file = public_path($competition->manuscript);
            if (\File::isFile($file)) {
                \File::delete($file);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User::class);
    }
}
