<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentsExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $students;

    public function __construct($students)
    {
        $this->students = $students;
    }

    public function collection()
    {
        return $this->students->map(function ($student) {
            return [
                $student->registration_num,
                $student->email,
                $student->phone_number,
                $student->first_name,
                $student->last_name,
                $student->gender ?? '-',
                $student->address ?? '-',
                $student->birth_date,
                $student->group->name,
                $student->group->section->name,
                $student->group->section->semester->name,
                $student->group->section->semester->promotion->name,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Registration Number', 
            'Email', 
            'Phone Number', 
            'First Name', 
            'Last Name', 
            'Gender', 
            'Address', 
            'Birth Date',
            'Group',
            'Section',
            'Semester',
            'Promotion',
        ];
    }
}
