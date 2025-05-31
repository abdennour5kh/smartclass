<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Justification extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'session_id',
        'status',
        'admin_desition',
        'teacher_desition',
        'message',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function files()
    {
        return $this->morphMany(StudentFile::class, 'fileable');
    }

    public function attendance()
    {
        return $this->hasOne(Attendance::class, 'session_id', 'session_id')
                    ->whereColumn('student_id', 'student_id');
    }
}
