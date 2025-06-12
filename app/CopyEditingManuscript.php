<?php

namespace App;

use App\Http\FrontendHelpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CopyEditingManuscript extends Model
{
    const NotStarted = 0;

    const Started = 1;

    const Finished = 2;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'copy_editing_manuscripts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'project_id', 'file', 'payment_price', 'editor_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User::class);
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(\App\User::class, 'editor_id', 'id');
    }

    public function feedback(): HasOne
    {
        return $this->hasOne(\App\OtherServiceFeedback::class, 'service_id', 'id')
            ->where('service_type', '=', 1);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(\App\Project::class, 'project_id', 'id');
    }

    public function getExpectedFinishFormattedAttribute()
    {
        return $this->attributes['expected_finish'] ? FrontendHelpers::formatDate($this->attributes['expected_finish']) : '';
    }
}
