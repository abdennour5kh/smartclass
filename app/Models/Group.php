<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        "section_id",
        "name",
    ];

    public function students () 
    {
        return $this->hasMany(Student::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function classes()
    {
        return $this->hasMany(Classe::class);
    }

    // Which modules does this group study?
    public function modules()
    {
        // a group belongs to many modules and cnx made through classes table
        return $this->belongsToMany(Module::class, "classes")
                                    ->withPivot("teacher_id")
                                    ->withTimestamps();
    }

    public function announcements()
    {
        return $this->hasManyThrough(
            Announcement::class,
            Classe::class,
            'group_id', // Foreign key on Classe
            'classe_id', // Foreign key on Announcement
            'id', // Local key on Group
            'id'  // Local key on Classe
        );
    }

}
