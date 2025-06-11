<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectRoadmapStep extends Model
{
    protected $fillable = ['project_id', 'step_number', 'expected_date', 'status'];

    protected $appends = ['status_text'];

    public const STEPS = [
        1 => 'Ferdig manuskript',
        2 => 'Språkvask', // 'Redaktør & korrektur',
        3 => 'Korrektur', // 'Bokdesign & layout',
        4 => 'Omslag', // 'ISBN & metadata',
        5 => 'Ombrekk', // 'Publisering',
        6 => 'Ebok', // 'Markedsføring',
        7 => 'Lydbok', // 'Oppfølging & salg',
        8 => 'Print',
        /* 8 => 'Publisering (sende til trykk)',
        9 => 'Utsending av bøker når de har kommet inn på lager',
        10 => 'Markedføring', */
    ];

    public function getStepTitleAttribute()
    {
        return self::STEPS[$this->step_number] ?? 'Unknown Step';
    }

    public function getStatusTextAttribute()
    {
        return ucwords(str_replace('_', ' ', $this->status));
    }
}
