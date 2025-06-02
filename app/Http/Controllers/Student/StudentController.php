<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Attendance;
use App\Models\Classe;
use App\Models\Conversation;
use App\Models\DocumentRequest;
use App\Models\Group;
use App\Models\GroupChangeRequest;
use App\Models\Justification;
use App\Models\Module;
use App\Models\Session;
use App\Models\Student;
use App\Models\StudentFile;
use App\Models\Task;
use App\Models\TaskSubmission;
use App\Models\Teacher;
use App\Notifications\JustificationSubmitted;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function dashboard(Request $request) {
        $student = Auth::user()->student;
        $studentUser = Auth::user();
        //dd($student);
        
        
        $announcements = $student->group->announcements()
        ->with("classe.teacher", "classe.module")
        ->latest()
        ->get();
        
        $classesReport = $student->getClassesReport();
        
        $group = $student->group;
        $tasks = Task::whereHas('classe', function ($q) use ($group) {
            $q->where('group_id', $group->id);
        })
        ->with(['classe.module', 'submissions' => function ($q) use ($student) {
            $q->where('student_id', $student->id);
        }])
        ->latest()
        ->get();

        //dd($tasks);

        return view('student.dashboard', compact([
            'announcements',
            'classesReport',
            'student',
            'tasks'
            //'conversations',
        ]));
    }

    public function classes() {
        $student = Auth::user()->student;
        $modules = $student->group->classes;
        $info = $student->getClassesReport();
        //dd($info);

        return view('student.classes', compact([
            'info',
            'student'
        ]));
    }

    public function class_details($id) {
        $student = Auth::user()->student;

        $classe = Classe::with(['sessions.students', 'teacher'])->findOrFail($id);

        if ($classe->group_id !== $student->group_id) {
            abort(403, 'Unauthorized access to this class.');
        }

        $attendance = [];

        foreach ($classe->sessions as $session) {
            $pivot = $session->students->where('id', $student->id)->first()?->pivot;

            if ($pivot) {
                $attendance[] = [
                    'date' => $session->session_date,
                    'start' => Carbon::parse($session->start_time)->format('H:i A'),
                    'end' => Carbon::parse($session->end_time)->format('H:i A'),
                    'room' => $session->location,
                    'type' => $session->type,
                    'status' => $pivot->status,
                    'note' => $pivot->notes ?? '-',
                    'teacher' => $classe->teacher->first_name . ' ' . $classe->teacher->last_name,
                ];
            }
        }

        $module = $classe->module; // assuming the Classe model has a `module()` relationship

        return view('student.class_details', compact('attendance', 'student', 'module'));
    }


    public function schedule(Request $request) {

        // rule : we are using the servers time zone
        // so server must be in algeria
        // if not update server timezone to algeia

        // first we get current auth user
        $user = Auth::user();
        // we are on student route, so we fetch current student
        $student = $user->student;
        $group = $student->group;
        
        // default timeSlots
        // hard coded , can be modified later
        $timeSlots = [
            '08:00 - 09:30',
            '09:45 - 11:15',
            '11:30 - 13:00',
            '14:00 - 15:30',
            '15:45 - 17:15',
        ];
        
        // fixed for life
        $weekDays = ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday'];
    
        // currently empty
        $schedule = [];
    
        // initialise an empty schedule table
        foreach ($weekDays as $day) {
            foreach ($timeSlots as $slot) {
                $schedule[$day][$slot] = null;
            }
        }

        $startOfWeek = Carbon::now()->startOfWeek(Carbon::SATURDAY)->startOfDay();
        $endOfWeek = Carbon::now()->endOfWeek(Carbon::THURSDAY)->endOfDay();
    
        // get sessions for my group
        $sessions = Session::with('classe.module', 'classe.teacher')
            ->whereHas('classe', fn($q) => $q->where('group_id', $group->id)) // sessions for this gp only
            ->whereBetween('session_date', [$startOfWeek, $endOfWeek]) // sessions for this week only
            ->get();

        //dd($sessions);
    
        // now we start populating the schedule
        foreach ($sessions as $session) {
            // use carbon for date formatting
            $day = Carbon::parse($session->session_date)->format('l');
            $startTime = Carbon::parse($session->start_time)->format('H:i');
            $endTime = Carbon::parse($session->end_time)->format('H:i');
            //dd($day, $startTime, $endTime, $slot);

            
            // combine start and end time into a slot string
            $slot = "{$startTime} - {$endTime}";
    
            if (in_array($day, $weekDays) && in_array($slot, $timeSlots)) {
                $schedule[$day][$slot] = [
                    'module' => $session->classe->module,
                    'teacher' => $session->classe->teacher->user->full_name,
                    'location' => $session->location,
                ];
            }
        }

        // dd($schedule);
    
        return view('student.schedule', compact([
            'timeSlots',
            'schedule',
            'weekDays',
            'student'
        ]));
    }

    public function justifications(Request $request) {
        $student = Auth::user()->student;

        $absences = Attendance::with([
            'session.classe.group',
            'session.classe.module',
            'justification',
        ])
        ->where('student_id', $student->id)
        ->where('status', 'absent')
        ->orderByDesc('created_at')
        ->get();
        
        //dd($absences);

        return view('student.justifications', compact([
            'absences',
        ]));
    }

    public function store_justification(Request $request) {

        $request->validate([
            'attendance_id' => 'required|exists:attendances,id',
            'message' => 'required|string|max:1000',
            'files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:10240',
        ]);
    
        $attendance = Attendance::findOrFail($request->attendance_id);

        // we make sure the student making request owns the att record
        if ($attendance->student_id !== Auth::user()->student->id) {
            abort(403);
        }        
    
        if ($attendance->justification) {
            return response()->json(['error' => 'Justification already submitted.'], 400);
        }
    
        $justification = Justification::create([
            'student_id' => $attendance->student_id,
            'session_id' => $attendance->session_id,
            'message' => $request->message,
            'status' => 'pending',
            'admin_decision' => '2',
            'teacher_decision' => '2',
        ]);
    
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('justifications', 'public');
    
                StudentFile::create([
                    'student_id' => $attendance->student_id,
                    'file_path' => $path,
                    'fileable_id' => $justification->id,
                    'fileable_type' => Justification::class,
                ]);
            }
        }

        $teacher = $justification->session->classe->teacher;
        $teacher->user->notify(new JustificationSubmitted($justification));

        // No option to get the admin, so we must go all the way up to the department_id
        // We assume in the future a department can have 1 or N admin
        $department_id = $justification->session->classe->module->semester->promotion->department_id;
        $admins = Admin::where('department_id', $department_id)->get();
        foreach($admins as $admin) {
            $admin->user->notify(new JustificationSubmitted($justification));
        }

        return response()->json(['success' => true]);
    }

    public function view_justification(Request $request) {
        $attendance = Attendance::with([
            'justification.files'
        ])->findOrFail($request->absence_id);

        if ($attendance->student_id !== auth()->user()->student->id) {
            abort(403);
        }        
    
        $justification = $attendance->justification;
    
        if (!$justification) {
            return response()->json(['error' => 'Justification not found.'], 404);
        }
    
        return response()->json([
            'status' => $justification->status,
            'message' => $justification->message,
            'admin_decision' => $justification->admin_decision,
            'teacher_decision' => $justification->teacher_decision,
            'files' => $justification->files->pluck('file_path')
        ]);
        
    }

    public function inbox(Request $request) {
        $user = Auth::user();

        /** @var User $user */
        $conversations = $user->conversations()
            ->with(['messages.sender'])
            ->withPivot('last_read_at')
            ->latest('updated_at')
            ->get();

        return view('student.inbox', compact('conversations'));
    }

    public function profile(Request $request) {
        $student = Auth::user()->student;
        return view('student.profile', compact('student'));
    }

    public function update_profile(Request $request) {
        $student = Auth::user()->student;

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email,' . $student->id,
            'phone_number' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
        ]);

        if ($request->hasFile('avatar')) {
            if ($student->img_url && Storage::disk('public')->exists($student->img_url)) {
                Storage::disk('public')->delete($student->img_url);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $student->img_url = $path;
        }

        $student->fill($validated);
        $student->save();

        return back()->with('success', 'Profile updated successfully.');
    }

    public function update_password(Request $request) {
        $student = Auth::user()->student;

        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $student->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $student->password = Hash::make($request->new_password);
        $student->save();

        return back()->with('success', 'Password updated successfully.');
    }

    public function change_group(Request $request) {
        $student = Auth::user()->student;

        // unauthorize if change window is closed
        if(!$student->group->section->semester->promotion->department->is_group_change_allowed) {
            abort(403);
        }

        $requests = GroupChangeRequest::where('student_id', $student->id)
            ->latest()
            ->get();

        $currentGroup = $student->group;
        $availableGroups = Group::where('section_id', $currentGroup->section_id)
            ->where('id', '!=', $currentGroup->id)
            ->get();

        return view('student.change_group', compact('requests', 'availableGroups'));
    }

    public function submit_change_group(Request $request) {
        $student = Auth::user()->student;

        // unauthorize if change window is closed
        if(!$student->group->section->semester->promotion->department->is_group_change_allowed) {
            abort(403);
        }

        $request->validate([
            'target_group_id' => [
                'required',
                Rule::exists('groups', 'id')->where('section_id', $student->group->section_id),
            ],
            'reason' => 'required|string|max:1000',
        ]);

        $hasPending = GroupChangeRequest::where('student_id', $student->id)
            ->where('status', 'pending')
            ->exists();

        if ($hasPending) {
            return back()->withErrors(['You already have a pending group change request.']);
        }

        GroupChangeRequest::create([
            'student_id' => $student->id,
            'from_group_id' => $student->group_id,
            'to_group_id' => $request->target_group_id,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Group change request submitted successfully.');
    }

    public function announcements(Request $request) {
        $classe_id = $request->id;
        $student = Auth::user()->student;
        $classe = Classe::findOrFail($classe_id);

        $isClasseExists = $classe->group_id === $student->group_id;
        if(!$isClasseExists) {
            abort(403, 'Unauthorized access to this announcements.');
        }

        
        $announcements = $classe->announcements()->latest()->get();
        
        return view('student.announcement', compact('announcements', 'classe'));
    }

    public function view_tasks(Request $request) {
        $student = Auth::user()->student;

        $tasks = Task::with([
            'teacherFiles',
            'submissions' => fn($q) => $q->where('student_id', $student->id)->with('studentFiles')
        ])->where('classe_id', $request->id)->get();

        $module = $tasks?->first()?->classe?->module;

        return view('student.tasks', compact('tasks', 'module'));
    }

    public function submit_task(Request $request, Task $task) {
        $student = Auth::user()->student;

        $task->load(['teacherFiles', 'classe.teacher']);
        
        $submission = $task->submissions()->where('student_id', $student->id)
                        ->with('studentFiles')
                        ->latest()
                        ->first();

        return view('student.task_submission', compact('task', 'submission'));
    }

    public function store_task(Request $request, Task $task) {
        $student = Auth::user()->student;

        if ($task->submissions()->where('student_id', $student->id)->exists()) {
            return back()->withErrors('error', 'You have already submitted this task.');
        }

        if (now()->greaterThan($task->deadline)) {
            return back()->withErrors('error', 'The deadline for this task has passed. Submission is no longer allowed.');
        }

        $request->validate([
            'message' => 'nullable|string|max:1000',
            'files.*' => 'file|max:10240|mimes:pdf,png,jpeg,jpg,txt',
        ]);

        DB::transaction(function () use ($request, $task, $student) {
            
            $submission = TaskSubmission::create([
                'task_id' => $task->id,
                'student_id' => $student->id,
                'message' => $request->message,
                'status' => 'pending',
            ]);
    
            
            foreach ($request->file('files') as $file) {
                $path = $file->store('student_submissions', 'public');
    
                StudentFile::create([
                    'student_id' => $student->id,
                    'fileable_id' => $submission->id,
                    'fileable_type' => TaskSubmission::class,
                    'file_path' => $path,
                ]);
            }
        });

        return back()->with(['success', 'Task Successfully submited and its under review.']);
    }

    public function documents(Request $request) {
        $student = Auth::user()->student;

        $documentRequests = $student->documentRequests()->latest()->get();
        $documentTypes = DocumentRequest::DOCUMENT_TYPES;

        return view('student.documents', compact('documentRequests', 'documentTypes'));
    }

    public function store_document_request(Request $request) {
        $request->validate([
            'document_type' => ['required', Rule::in(array_keys(DocumentRequest::DOCUMENT_TYPES))],
        ]);

        $student = Auth::user()->student;

        DocumentRequest::create([
            'student_id' => $student->id,
            'document_type' => DocumentRequest::DOCUMENT_TYPES[$request->document_type],
            'status' => 'pending',
        ]);

        return back()->with('success', 'Your document request has been submitted.');
        
    }

    public function show_teachers(Request $request) {
        return view('student.teachers');
    }
    
}
