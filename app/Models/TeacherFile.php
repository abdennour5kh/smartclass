<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id', 
        'fileable_id', 
        'fileable_type',
        'file_path'
    ];

    public function fileable()
    {
        return $this->morphTo();
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }


}
