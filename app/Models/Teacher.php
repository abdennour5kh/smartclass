<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;


class Teacher extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "department_id",
        "first_name",
        "last_name",
        "password",
        "email",
        "phone_number",
        "grade",
        "img_url",
        "address",
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function classes()
    {
        return $this->hasMany(Classe::class);
    }

    public function modules()
    {
        return $this->belongsToMany(Module::class, "classes")
                                    ->withPivot("group_id")
                                    ->withTimestamps();
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function announcements()
    {
        return $this->hasManyThrough(
            Announcement::class,
            Classe::class,
            'teacher_id', // Foreign key on classes
            'classe_id', // Foreign key on announcements
            'id', // Local key on teacher
            'id' // Local key on classes
        );
    }
    


}
