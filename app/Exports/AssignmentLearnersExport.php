<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AssignmentLearnersExport implements FromArray, WithHeadings, ShouldAutoSize
{

    protected $learners;

    public function __construct($learners)
    {
        $this->learners = $learners;
    }

    public function array(): array
    {
        return $this->learners;
    }

    public function headings(): array
    {
        $headings = ['Name', 'Email']; // first row in excel
        return $headings;
    }

}