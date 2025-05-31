<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'classe_id',
        'teacher_id',
        'title',
        'description',
        'deadline',
    ];

    public function teacherFiles()
    {
        return $this->morphMany(TeacherFile::class, 'fileable');
    }

    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }

    public function submissions()
    {
        return $this->hasMany(TaskSubmission::class);
    }

}
