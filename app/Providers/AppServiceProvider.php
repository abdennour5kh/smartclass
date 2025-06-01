<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('admin.*', function ($view) {
            $view->with('admin', Auth::user()->admin);
        });

        View::composer('student.*', function ($view) {
            $view->with('student', Auth::user()->student);
        });

        View::composer('teacher.*', function ($view) {
            $view->with('teacher', Auth::user()->teacher);
        });

        View::composer(['student.partials.navbar', 'teacher.partials.navbar', 'admin.partials.navbar'], function ($view) {
            $user = Auth::user();
            if ($user && ($user->role === 'student' || $user->role === 'teacher')) {
                /** @var User $user */
                $conversations = $user->conversations()
                    ->with(['messages.sender'])
                    ->withPivot('last_read_at')
                    ->whereHas('messages', function ($q) use ($user) {
                        $q->whereColumn('messages.created_at', '>', 'conversation_user.last_read_at')
                          ->where('sender_id', '!=', $user->id);
                    })
                    ->latest('updated_at')
                    ->get();
    
                $view->with('conversations', $conversations);
            }
        });

        View::composer('student.*', function ($view) {
            if (Auth::check() && Auth::user()->role === 'student') {
                $student = Auth::user()->student;

                $classes = $student->group->classes()
                    ->with(['module', 'teacher'])
                    ->get();

                $teachers = $classes->groupBy('teacher_id')->map(function ($classesForTeacher) {
                    $teacher = $classesForTeacher->first()->teacher;

                    $teacher->classes_info = $classesForTeacher->map(function ($class) {
                        return [
                            'module' => $class->module->name,
                            'class_type' => $class->class_type,
                        ];
                    });

                    return $teacher;
                });

                $view->with('currentStudentTeachers', $teachers);
            }
        });
    }
}
