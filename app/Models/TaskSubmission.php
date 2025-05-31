<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'student_id',
        'message',
        'status',
        'grade',
        'feedback',
    ];

    public function studentFiles()
    {
        return $this->morphMany(StudentFile::class, 'fileable');
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

}
