<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        "semester_id",
        "name",
        "img_url",
        "color",
    ];

    public function groupAssignments()
    {
        return $this->hasMany(Classe::class);
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'classes')
                    ->withPivot('teacher_id')
                    ->withTimestamps();
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

}
