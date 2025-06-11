<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AssignmentEmailListExport implements FromArray, ShouldAutoSize
{
    protected $list;

    public function __construct($list)
    {
        $this->list = $list;
    }

    public function array(): array
    {
        return $this->list;
    }
}
