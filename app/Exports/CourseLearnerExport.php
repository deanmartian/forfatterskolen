<?php

namespace App\Exports;

use App\Course;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class CourseLearnerExport implements FromArray, ShouldAutoSize, WithColumnFormatting, WithColumnWidths, WithHeadings
{
    protected $course_id;

    protected $type;

    public function __construct($course_id, $type = 'email')
    {
        $this->course_id = $course_id;
        $this->type = $type;
    }

    public function headings(): array
    {
        $type = $this->type;

        if ($type === 'address') {
            $headings = ['id', 'learner', 'street', 'postnumber', 'city']; // first row in excel
        } else {
            $headings = ['id', 'learner', $type]; // first row in excel
        }

        return $headings;
    }

    public function array(): array
    {
        $course = Course::find($this->course_id);
        $learners = $course->learners->get();
        $type = $this->type;

        $learnerList = [];

        // loop all the learners
        foreach ($learners as $learner) {
            $value = $type === 'email' ? $learner->user->email : $learner->user->fullAddress;

            if ($type === 'email') {
                $learnerList[] = [$learner->user->id, $learner->user->full_name, $value];
            } else {
                $learnerAddress = $learner->user->address;
                $street = $learnerAddress ? $learnerAddress->street : '';
                $zip = $learnerAddress ? $learnerAddress->zip : '';
                $city = $learnerAddress ? $learnerAddress->city : '';
                $learnerList[] = [$learner->user->id, $learner->user->full_name, $street, $zip, $city];
            }
        }

        return $learnerList;
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,
        ];
    }
}
