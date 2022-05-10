<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GenericExport implements FromArray, WithHeadings, ShouldAutoSize
{

    /**
     * @var $records array list of data
     * @var $headers array list of headers
     */
    protected $records;
    protected $headers;

    /**
     * GenericExport constructor.
     * @param $records
     * @param $headers
     */
    public function __construct($records, $headers)
    {
        $this->records = $records;
        $this->headers = $headers;
    }

    /**
     * data to be export
     * @return array
     */
    public function array(): array
    {
        return $this->records;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return $this->headers;
    }

}