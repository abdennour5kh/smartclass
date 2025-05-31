<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasFactory;

    protected $fillable = [
        'classe_id',
        'session_date',
        'start_time', 
        'end_time', 
        'location',
        'notes',
        'status',
        'type',
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'attendances')
                    ->withPivot('status', 'notes')
                    ->withTimestamps();
    }

    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }

    public function group()
    {
        return $this->classe->group;
    }

    public function module()
    {
        return $this->classe->module;
    }

    public function teacher()
    {
        return $this->classe->teacher;
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCanceled(): bool
    {
        return $this->status === 'canceled';
    }

    public function isRescheduled(): bool
    {
        return $this->status === 'rescheduled';
    }

    public function isScheduled(): bool
    {
        return $this->status === 'scheduled';
    }
}
