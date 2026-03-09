<?php

namespace App;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class ProjectRegistrationDistribution extends Model
{
    use Loggable;
    
    protected $table = 'project_registration_paid_distribution_cost';

    protected $fillable = ['project_registration_id', 'years'];

    protected function casts(): array
    {
        return [
            'years' => 'array',
        ];
    }
}
