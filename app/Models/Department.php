<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "is_group_change_allowed",
    ];

    public function promotions ()
    {
        return $this->hasMany(Promotion::class);
    }

    public function teachers ()
    {
        return $this->hasMany(Teacher::class);
    }
}
