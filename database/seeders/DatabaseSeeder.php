<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Admin;
use App\Models\Attendance;
use App\Models\Department;
use App\Models\Group;
use App\Models\Classe;
use App\Models\Module;
use App\Models\Promotion;
use App\Models\Section;
use App\Models\Semester;
use App\Models\Session;
use App\Models\Student;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        foreach (range(1, 2) as $i) {

            $department = Department::factory()->create();

            $teachers = Teacher::factory(3)->create([
                'department_id' => $department->id
            ]);

            $promotion = Promotion::factory()->create([
                'department_id' => $department->id
            ]);

            $semester = Semester::factory()->create([
                'promotion_id' => $promotion->id
            ]);

            $modules = Module::factory(3)->create([
                'semester_id' => $semester->id
            ]);

            $section = Section::factory()->create([
                'semester_id' => $semester->id
            ]);

            $group = Group::factory()->create([
                'section_id' => $section->id
            ]);

            Student::factory(5)->create([
                'group_id' => $group->id
            ]);

            
            foreach ($modules as $module) {
                $teacher = $teachers->random();  

                
                while (Classe::where('group_id', $group->id)->where('teacher_id', $teacher->id)->exists()) {
                    $teacher = $teachers->random();  
                }

                
                Classe::create([
                    'group_id' => $group->id,
                    'module_id' => $module->id,
                    'teacher_id' => $teacher->id,
                    'class_type' => 'TD',
                ]);
            }
        }

        Admin::factory(1)->create([
            'department_id' => $department->id,
        ]);

        $groups = Group::all();
        $modules = Module::all();
        $teachers = Teacher::all();

        $weekDays = ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday'];
        $timeSlots = [
            ['08:00', '09:30'],
            ['09:45', '11:15'],
            ['11:30', '13:00'],
            ['14:00', '15:30'],
            ['15:45', '17:15'],
        ];

        $groups = Group::all();
        $modules = Module::all();
        $teachers = Teacher::all();

        foreach ($groups as $group) {
            foreach ($modules as $module) {
                foreach ($teachers as $teacher) {
                    $Classe = Classe::where('group_id', $group->id)
                        ->where('module_id', $module->id)
                        ->where('teacher_id', $teacher->id)
                        ->first();

                    if ($Classe) {
                        // Randomly select a weekday and get the next occurrence of it
                        $dayName = $weekDays[array_rand($weekDays)];
                        $sessionDate = Carbon::now()->next($dayName)->startOfDay(); // e.g., next Monday

                        // Pick a random time slot
                        $slot = $timeSlots[array_rand($timeSlots)];
                        [$startHour, $endHour] = $slot;

                        $startDateTime = $sessionDate->copy()->setTimeFrom(Carbon::parse($startHour));
                        $endDateTime = $sessionDate->copy()->setTimeFrom(Carbon::parse($endHour));

                        // Random status / Type
                        $status = ['scheduled', 'completed', 'canceled', 'rescheduled'][rand(0, 3)];
                        $type = ['TP', 'TD'][rand(0, 1)];

                        Session::create([
                            'classe_id' => $Classe->id,
                            'session_date' => $sessionDate->toDateString(),
                            'start_time' => $startDateTime,
                            'end_time' => $endDateTime,
                            'location' => 'Room ' . rand(101, 105),
                            'status' => $status,
                            'notes' => '',
                            'type' => $type,
                        ]);
                    }
                }
            }
        }

        $sessions = Session::with('classe.group.students')->get();
        $statuses = ['present', 'absent', 'late', 'excused'];

        foreach ($sessions as $session) {
            // Check if the session has a Classe and that Classe has a group
            if ($session->Classe && $session->Classe->group) {
            
                $students = $session->Classe->group->students;

                foreach ($students as $student) {
                    Attendance::create([
                        'student_id' => $student->id,
                        'session_id' => $session->id,
                        'status' => $statuses[array_rand($statuses)],
                        'notes' => fake()->boolean() ? fake()->sentence() : null,
                    ]);
                }
            }
        }

    }
}
