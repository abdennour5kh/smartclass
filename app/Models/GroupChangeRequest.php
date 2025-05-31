<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupChangeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 
        'from_group_id', 
        'to_group_id', 
        'status', 
        'reason'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function fromGroup()
    {
        return $this->belongsTo(Group::class, 'from_group_id');
    }

    public function toGroup()
    {
        return $this->belongsTo(Group::class, 'to_group_id');
    }
}
