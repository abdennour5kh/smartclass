<?php

namespace App\Imports;

use App\Exports\ErrorsExport;
use App\Models\Group;
use App\Models\Promotion;
use App\Models\Section;
use App\Models\Semester;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;


class StudentsImport implements ToModel, WithHeadingRow, WithValidation, WithEvents
{
    protected $errors = [];

    public function model(array $row)
    {
        // container of our current row 
        
        $admin = Auth::user()->admin;
        $department_id = $admin->department_id;

        $promotion = Promotion::where('department_id', $department_id)
                      ->where('name', $row['promotion'])
                      ->first();

        if(!$promotion) {
            $this->errors[] = [
                'Registration Number' => $row['registration_number'] ?? '-',
                'Error Message' => "Promotion '{$row['promotion']}' not found."
            ];
            return null;
        }

        $semester = Semester::where('promotion_id', $promotion->id)
                            ->where('name', $row['semester'])
                            ->first();

        if(!$semester) {
            $this->errors[] = [
                'Registration Number' => $row['registration_number'] ?? '-',
                'Error Message' => "Semester '{$row['semester']}' not found."
            ];
            return null;
        }

        $section = Section::where('semester_id', $semester->id)
                        ->where('name', $row['section'])
                        ->first();

        if(!$section) {
            $this->errors[] = [
                'Registration Number' => $row['registration_number'] ?? '-',
                'Error Message' => "Section '{$row['section']}' not found."
            ];
            return null;
        }

        $group = Group::where('section_id', $section->id)
                    ->where('name', $row['group'])
                    ->first();

        if(!$group) {
            $this->errors[] = [
                'Registration Number' => $row['registration_number'] ?? '-',
                'Error Message' => "Group '{$row['group']}' not found."
            ];
            return null;
        }

        $user = User::create([
            'role' => 'student',
        ]);

        return new Student([
            'user_id' => $user->id,
            'group_id' => $group->id,
            'registration_num' => $row['registration_number'],
            'first_name' => $row['first_name'],
            'last_name' => $row['last_name'],
            'email' => $row['email'],
            'phone_number' => $row['phone_number'],
            'password' => $row['password'] ?? 'student123',
            'gender' => $row['gender'] ?? '-',
            'address' => $row['address'] ?? '-',
            'birth_date' => $row['birth_date'],
            'img_url' => null,
        ]);
    }

    public function rules(): array
    {
        return [
            'first_name'               => 'required|string|max:50',
            'last_name'                => 'required|string|max:50',
            'email'                    => 'required|email|unique:students,email',
            'registration_number'         => 'required|unique:students,registration_num',
            'phone_number'             => 'required|max:20',
            'password'                 => 'string|max:100',
            'gender'                   => 'nullable|string|max:7',
            'address'                  => 'nullable|string|max:255',
            'birth_date'               => 'required|date|before:today'
        ];
    }

    public function customValidationMessages()
    {
        return [
            // First Name
            'first_name.required' => 'The first name is required.',
            'first_name.string' => 'The first name must be a string.',
            'first_name.max' => 'The first name must not exceed 50 characters.',

            // Last Name
            'last_name.required' => 'The last name is required.',
            'last_name.string' => 'The last name must be a string.',
            'last_name.max' => 'The last name must not exceed 50 characters.',

            // Email
            'email.required' => 'The email is required.',
            'email.email' => 'The email format is invalid.',
            'email.unique' => 'This email already exists.',

            // Registration Number
            'registration_number.required' => 'The registration number is required.',
            'registration_number.string' => 'The registration number must be a string.',
            'registration_number.unique' => 'This registration number already exists.',

            // Phone Number
            'phone_number.required' => 'The phone number is required.',
            'phone_number.max' => 'The phone number must not exceed 20 characters.',

            // Password
            'password.required' => 'The password is required.',
            'password.string' => 'The password must be a string.',
            'password.max' => 'The password must not exceed 100 characters.',

            // Gender
            'gender.string' => 'The gender must be a string.',
            'gender.max' => 'The gender must not exceed 7 characters.',

            // Address
            'address.string' => 'The address must be a string.',
            'address.max' => 'The address must not exceed 255 characters.',

            // Birth Date
            'birth_date.required' => 'The birth date is required.',
            'birth_date.date' => 'The birth date must be a valid date.',
            'birth_date.before' => 'The birth date must be before today.',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterImport::class => function (AfterImport $event) {
                if (!empty($this->errors)) {
                    // so basicly we store the failed_imports file on the server
                    // then save the path to that file into a session variable
                    $fileName = time() . '_failed_imports.xlsx';
                    $filePath = 'public/failed_imports/' . $fileName;

                    Excel::store(new ErrorsExport($this->errors), $filePath);
                    
                    session(['failed_import_file' => $fileName]);
                }
            },
        ];
    }
}
