<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    use HasFactory;

    protected $fillable = [
        "promotion_id",
        "name",
    ];

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

    public function sections ()
    {
        return $this->hasMany(Section::class);
    }

    public function modules ()
    {
        return $this->hasMany(Module::class);
    }
}
