<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceExport implements FromArray, WithHeadings, WithCustomStartCell, WithStyles
{
    protected array $sessionDates;
    protected array $rows;

    public function __construct(array $sessionDates, array $rows)
    {
        $this->sessionDates = $sessionDates;
        $this->rows = $rows;
    }

    public function array(): array
    {
        $result = [];

        foreach ($this->rows as $student) {
            $row = [
                $student['registration_num'],
                $student['last_name'],
                $student['first_name'],
            ];

            foreach ($student['attendances'] as [$status, $note]) {
                $row[] = $status;
                $row[] = $note;
            }

            $result[] = $row;
        }

        return $result;
    }

    public function headings(): array
    {
        
        $header1 = ['Reg. #', 'Last Name', 'First Name'];
        $header2 = ['', '', ''];

        foreach ($this->sessionDates as $date) {
            $header1[] = $date;
            $header1[] = ''; 
            $header2[] = 'Status';
            $header2[] = 'Notes';
        }

        return [$header1, $header2];
    }

    public function startCell(): string
    {
        return 'A1';
    }

    public function styles(Worksheet $sheet)
    {
        $col = 'D';
        foreach ($this->sessionDates as $i => $date) {
            $sheet->mergeCells("{$col}1:" . chr(ord($col) + 1) . "1");
            $col = chr(ord($col) + 2);
        }

        return [
            1 => ['font' => ['bold' => true]],
            2 => ['font' => ['bold' => true]],
        ];
    }
}
