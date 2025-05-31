<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 
        'fileable_id', 
        'fileable_type',
        'file_path'
    ];

    public function fileable()
    {
        return $this->morphTo();
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
