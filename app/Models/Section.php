<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        "semester_id",
        "name",
    ];

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function groups()
    {
        return $this->hasMany(Group::class);
    }
}
