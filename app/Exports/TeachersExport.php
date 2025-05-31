<?php

namespace App\Exports;

use App\Models\Teacher;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TeachersExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $teachers;

    public function __construct($teachers)
    {
        $this->teachers = $teachers;
    }

    public function collection()
    {
        return $this->teachers->map(function ($teacher) {
            return [
                $teacher->first_name,
                $teacher->last_name,
                $teacher->email,
                strval($teacher->phone_number),
                $teacher->department->department_name,
                $teacher->grade,
                $teacher->address,
            ];
        });
    }

    public function headings(): array
    {
        return ['First Name', 'Last Name', 'Email', 'Phone Number', 'Department', 'Grade', 'Address'];
    }
}
