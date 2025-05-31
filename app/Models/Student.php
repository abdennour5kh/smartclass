<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;


class Student extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "group_id",
        "registration_num",
        "email",
        "password",
        "first_name",
        "last_name",
        "phone_number",
        "address",
        "birth_date",
        "img_url",
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function sessions()
    {
        return $this->belongsToMany(Session::class, 'attendances')
                    ->withPivot('status', 'notes')
                    ->withTimestamps();
    }

    public function justifications()
    {
        return $this->hasMany(Justification::class);
    }

    public function documentRequests()
    {
        return $this->hasMany(DocumentRequest::class);
    }
    
    public function getClassesReport($moduleId = null)
    {
        $classesQuery = $this->group->classes()
            ->whereHas('completedSessions')
            ->with(['module', 'teacher', 'completedSessions']);

        if ($moduleId) {
            $classesQuery->where('module_id', $moduleId);
        }

        $classes = $classesQuery->get();
        //dd($classes);

        $classesReport = [];

        $validAttendanceStatuses = ['present', 'justified', 'late', 'excused'];

        foreach ($classes as $class) {
            $completedSessions = $class->completedSessions;

            // Collect valid attendance pivots for this class
            $pivots = $completedSessions->flatMap(function ($session) use ($validAttendanceStatuses) {
                $pivot = $session->students->where('id', $this->id)->first()?->pivot;

                return ($pivot && in_array($pivot->status, $validAttendanceStatuses)) ? [$pivot] : [];
            });

            $attendanceRate = $completedSessions->count() > 0
                ? round(($pivots->count() / $completedSessions->count()) * 100)
                : 0;

            $classesReport[] = [
                'id' => $class->id,
                'attendance' => $pivots,
                'module' => $class->module,
                'type' => $class->class_type,
                'teacher' => $class->teacher->user->full_name,
                'grade' => $class->teacher->grade,
                'attendanceRate' => $attendanceRate,
            ];
        }

        return $classesReport;
    }




}
