<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class CompetitionApplicant extends Model
{

    protected $fillable = ['user_id', 'manuscript'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}