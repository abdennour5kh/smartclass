<?php

namespace App\Imports;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class TeachersImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $admin = Auth::user()->admin;

        // email it later
        $password = Str::random(10);

        $user = User::create([
            'role' => 'teacher',
        ]);

        //dd($row);

        $teacher = Teacher::create([
            'user_id' => $user->id,
            'department_id' => $admin->department_id,
            'first_name' => $row['first_name'],
            'last_name' => $row['last_name'],
            'email' => $row['email'],
            'phone_number' => strval($row['phone_number']),
            'password' => Hash::make($password),
            'grade' => $row['grade'],
            'address' => $row['address'],
        ]);

        return $teacher;
    }

    public function rules(): array
    {
        return [
            'first_name'    => 'required|string|max:50',
            'last_name'     => 'required|string|max:50',
            'email'         => 'required|email|unique:teachers,email',
            'phone_number'  => 'required|max:20',
            'grade'         => 'required|in:Maître Assistant B, Maître Assistant A, Maître de Conférences A, Maître de Conférences B, Professeur',
            'address'       => 'nullable|string|max:255',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'first_name.required' => 'The first name is required.',
            'last_name.required' => 'The last name is required.',
            'email.required' => 'The email is required.',
            'email.email' => 'The email format is invalid.',
            'email.unique' => 'This email already exists.',
        ];
    }

}
