<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkshopPresenter extends Model
{
    protected $table = 'workshop_presenters';

    protected $fillable = ['workshop_id', 'first_name', 'last_name', 'email', 'image'];

    public function workshop()
    {
        return $this->belongsTo('App\Workshop');
    }
}
