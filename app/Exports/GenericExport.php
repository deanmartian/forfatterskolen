<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GenericExport implements FromArray, ShouldAutoSize, WithHeadings
{
    /**
     * @var array list of data
     * @var array list of headers
     */
    protected $records;

    protected $headers;

    /**
     * GenericExport constructor.
     */
    public function __construct($records, $headers)
    {
        $this->records = $records;
        $this->headers = $headers;
    }

    /**
     * data to be export
     */
    public function array(): array
    {
        return $this->records;
    }

    public function headings(): array
    {
        return $this->headers;
    }
}
