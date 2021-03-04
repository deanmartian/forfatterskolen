<?php

namespace App;

use App\Http\FrontendHelpers;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CopyEditingManuscript extends Model {
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
    protected $fillable = ['user_id', 'file', 'payment_price', 'editor_id'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function editor()
    {
        return $this->belongsTo('App\User', 'editor_id', 'id');
    }

    public function feedback()
    {
        return $this->hasOne('App\OtherServiceFeedback', 'service_id', 'id')
            ->where('service_type','=',1);
    }

    public function getExpectedFinishFormattedAttribute()
    {
        return $this->attributes['expected_finish'] ? FrontendHelpers::formatDate($this->attributes['expected_finish']) : '';
    }
}