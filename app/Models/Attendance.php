<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 
        'session_id', 
        'status', 
        'notes',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function justification()
    {
        return $this->hasOne(Justification::class, 'session_id', 'session_id')
                    ->whereColumn('student_id', 'student_id');
    }
}
