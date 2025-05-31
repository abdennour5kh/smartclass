<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classe extends Model
{
    use HasFactory;

    protected $table = "classes";

    protected $fillable = [
        "group_id",
        "module_id",
        "teacher_id",
        "class_type"
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    public function sessionTemplate()
    {
        return $this->hasOne(SessionTemplate::class);
    }

    public function activeSessionTemplate()
    {
        return $this->hasOne(SessionTemplate::class)->where('status', 'active');
    }

    public function completedSessions()
    {
        return $this->hasMany(Session::class)->where('status', 'completed');
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class);
    }

}
