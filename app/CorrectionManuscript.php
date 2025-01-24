<?php

namespace App;

use App\Http\FrontendHelpers;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CorrectionManuscript extends Model {

    const NotStarted = 0;
    const Started = 1;
    const Finished = 2;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'correction_manuscripts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'project_id', 'file', 'payment_price', 'editor_id', 'status', 'expected_finish'];

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
            ->where('service_type','=',2);
    }

    public function project()
    {
        return $this->belongsTo('App\Project', 'project_id', 'id');
    }

    public function getExpectedFinishFormattedAttribute()
    {
        return $this->attributes['expected_finish'] ? FrontendHelpers::formatDate($this->attributes['expected_finish']) : '';
    }
}