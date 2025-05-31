<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ErrorsExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $errors;

    public function __construct($errors)
    {
        $this->errors = $errors;
    }

    public function collection()
    {
        return collect($this->errors);
    }

    public function headings(): array
    {
        return [
            'Registration Number', 
            'Error Message'
        ];
    }
}
