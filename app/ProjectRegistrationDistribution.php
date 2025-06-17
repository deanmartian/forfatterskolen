<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectRegistrationDistribution extends Model
{
    protected $table = 'project_registration_paid_distribution_cost';

    protected $fillable = ['project_registration_id', 'years'];

    protected function casts(): array
    {
        return [
            'years' => 'array',
        ];
    }
}
