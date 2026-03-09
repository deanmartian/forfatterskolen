<?php

namespace App;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class ProjectActivity extends Model
{
    use Loggable;
    
    protected $table = 'project_activity';

    /**
     * invoicing = 0 - never, 1 - sometimes, 2 - always
     * project_id - child id
     *
     * @var array
     */
    protected $fillable = ['project_id', 'activity', 'description', 'invoicing', 'hourly_rate'];
}
