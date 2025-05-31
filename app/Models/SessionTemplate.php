<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'classe_id',
        'weekday',
        'start_time',
        'end_time',
        'location',
        'notes',
        'status',
        'type',
    ];

    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }
}
