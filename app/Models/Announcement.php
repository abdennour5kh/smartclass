<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        "classe_id",
        "content",
    ];

    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }

    public function getGroupAttribute()
    {
        return $this->classe?->group;
    }


    public function getTeacherAttribute()
    {
        return $this->classe?->teacher;
    }


    public function getModuleAttribute()
    {
        return $this->classe?->module;
    }

}
