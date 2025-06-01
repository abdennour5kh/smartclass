<?php

namespace App\Http\Controllers\Teacher;

use App\Exports\AttendanceExport;
use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\Classe;
use App\Models\Group;
use App\Models\Justification;
use App\Models\Session;
use App\Models\Task;
use App\Models\TaskSubmission;
use App\Models\Teacher;
use App\Notifications\JustificationReviewed;
use App\Services\AttendanceMatrixBuilder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class TeacherController extends Controller
{
    public function dashboard(Request $request) {
        $teacher = auth()->user()->teacher;

        // Classes count
        $classCount = Classe::where('teacher_id', $teacher->id)->count();

        // Include sessions with status 'scheduled' or 'rescheduled'
        $validStatuses = ['scheduled', 'rescheduled'];

        // Next upcoming session
        $nextSession = Session::whereHas('classe', function ($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->whereIn('status', $validStatuses)
            ->whereDate('session_date', '>=', now())
            ->orderBy('session_date')
            ->orderBy('start_time')
            ->first();

        // Today’s sessions
        $todaySessions = Session::whereHas('classe', function ($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->whereIn('status', $validStatuses)
            ->where('session_date', Carbon::today()->toDateString())
            ->orderBy('start_time')
            ->get();
            //dd($todaySessions);
            

        // Ungraded submissions
        $pendingSubmissions = TaskSubmission::whereHas('task.classe', function ($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->whereNull('grade')
            ->count();

        $pendingJustifications = Justification::whereHas('attendance.session.classe', function ($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })
        ->where('status', 'pending')
        ->count();

        $attendanceStats = Attendance::whereHas('session.classe', function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })
        ->whereHas('session', function ($q) {
            $q->whereMonth('session_date', now()->month);
        })
        ->selectRaw("status, COUNT(*) as total")
        ->groupBy('status')
        ->pluck('total', 'status');

        //dd(now()->month);

        return view('teacher.dashboard', compact(
            'classCount',
            'nextSession',
            'todaySessions',
            'pendingSubmissions',
            'pendingJustifications',
            'attendanceStats',
        ));
        return view('teacher.dashboard');
    }

    public function announcement(Request $request, $id = null) {
        $teacher = Auth::user()->teacher;

        if($id) {
            $classeExists = $teacher->classes()->where('id', $id)->exists();
            if (!$classeExists) {
                abort(403, 'Unauthorized access to this class.');
            }

            $announcements = $teacher->announcements()->where('classe_id', $id)->with('classe.group', 'classe.module')
                ->latest()->get();

            $classe = Classe::find($id);
            $classes = null;

            return view('teacher.announcements', compact([
                'announcements',
                'classe',
                'classes'
            ]));
            
        }

        $classes = $teacher->classes()->with('group', 'module')->get();

        $announcements = $teacher->announcements()->with('classe.group', 'classe.module')
        ->latest()->get();

        $classe = null;
        
        return view('teacher.announcements', compact([
            'announcements',
            'classes',
            'classe'
        ]));
    }

    public function add_announcement(Request $request) {
        $teacher = Auth::user()->teacher;

        $validated = $request->validate([
            'classes_id' => 'required|exists:classes,id', // very required, id must exist
            'content' => 'required|string|min:5',
        ]);

        Announcement::create([
            'classe_id' => $validated["classes_id"],
            'content' => $validated["content"],
        ]);

        return redirect()->back()->with('success', 'Announcement posted successfully!');
    }

    public function delete_announcement($id) {
        $teacher = Auth::user()->teacher;
        
        $announcement = Announcement::find($id);
        // Check if the teacher owns the announcement
        if ($announcement->Classe->teacher_id !== $teacher->id) {
            abort(403, 'You do not have permission to delete this announcement.');
        }

        $announcement->delete();

        return redirect()->back()->with('success', 'Announcement deleted successfully!');
    }

    public function classes_overview(Request $request) {
        $teacher = Auth::user()->teacher;

        $classes = Classe::with(['group', 'module'])
                            ->where('teacher_id', $teacher->id)
                            ->get();

        return view('teacher.classes_overview', compact([
            'teacher',
            'classes'
        ]));
    }

    public function manage_classe(Request $request) {
        $classe_id = $request->id;
        
        $sessions = Session::where('classe_id', $classe_id)->get();

        return view('teacher.manage_classe', compact([
            'sessions',
            'classe_id'
        ]));
    }

    public function update_session_note(Request $request) {
        $request->validate([
            'session_id' => 'required|exists:sessions,id',
            'note' => 'required|string|max:1000',
        ]);

        $session = Session::findOrFail($request->session_id);
        $session->notes = $request->note;
        $session->save();

        return response()->json(['success' => true]);
    }

    public function session_end(Request $request) {
        $request->validate([
            'session_id' => 'required|exists:sessions,id',
        ]);

        $session = Session::findOrFail($request->session_id);
        $session->status = 'completed';
        $session->save();

        return response()->json(['success' => true]);
    }

    public function attendance_sheet(Request $request) {
        $session_id = $request->id;
        $session = Session::findOrFail($session_id);

        return view('teacher.attendance_sheet', compact([
            'session'
        ]));
    }

    public function store_attendance(Request $request, Session $session) {
        $data = $request->validate([
            'attendance'              => 'required|array',
            'attendance.*.student_id' => 'required|integer|exists:students,id',
            'attendance.*.status'     => 'nullable|in:present,absent,late,excused,justified',
            'attendance.*.notes'      => 'nullable|string',
        ]);

        foreach ($data['attendance'] as $row) {
            // find existing or start a new one
            $att = Attendance::firstOrNew([
                'session_id' => $session->id,
                'student_id' => $row['student_id'],
            ]);

            // only overwrite status if you sent one; else keep old (or default to 'absent' on create)
            $att->status = $row['status'] 
                ?? $att->status 
                ?? 'absent';

            // only overwrite notes if you sent them; else keep whatever was there
            if (array_key_exists('notes', $row)) {
                $att->notes = $row['notes'];
            }

            $att->save();
        }

        return response()->json([
            'success' => true,
        ]);
    }

    public function export_attendance(Request $request, AttendanceMatrixBuilder $attendanceMatrixBuilder) {
        //dd($request);
        $sessionIds = $request->input('sessions');
        $sessions = Session::with('classe')->whereIn('id', $sessionIds)->get();

        if($sessions->isEmpty()) {
            abort(404, "No sessions found.");
        }

        $groupId = $request->input('group_id');
        $group = Group::findOrFail($groupId);

        [$orderedSessions, $rows] = $attendanceMatrixBuilder->getAttendanceMatrixForSessions($sessions->all(), $group);

        $sessionDates = collect($orderedSessions)
                        ->pluck('session_date')
                        ->toArray();


        return Excel::download(new AttendanceExport($sessionDates, $rows), $group->name.'attendance_sheet'.time().'.xlsx');
    }

    public function inbox(Request $request) {
        $teacherUser = Auth::user();
        $teacher = $teacherUser->teacher;
        
        $newJustifications = Justification::with(['student', 'session.classe.module'])
            ->whereHas('session.classe', fn($q) => $q->where('teacher_id', $teacher->id))
            ->where('teacher_decision', '2') // '2' means under review / not handled
            ->orderByDesc('created_at')
            ->get();

        /** @var User $teacherUser */
        $conversations = $teacherUser->conversations()
            ->with(['messages.sender'])
            ->withPivot('last_read_at')
            ->latest('updated_at')
            ->get();

        
        //dd($newJustifications);
        return view('teacher.inbox', compact('newJustifications', 'conversations'));
    }

    public function update_justification(Request $request) {
        $request->validate([
            'justification_id' => 'required|exists:justifications,id',
            'action' => 'required|in:approve,refuse',
        ]);
    
        $justification = Justification::findOrFail($request->justification_id);
        $attendance = $justification->attendance;
        $teacherId = Auth::user()->teacher->id;
        if ($justification->session->classe->teacher_id !== $teacherId) {
            abort(403);
        }
    
        $justification->teacher_decision = $request->action === 'approve' ? '1' : '0';
        
        // if asent is passed 3 days or more , he can not justify his absence
        //////////////////////////////////
        /////////////////////////////////
        

        // Determine final status if admin has already reviewed
        if ($justification->admin_decision !== '2') {
            $approved = $justification->teacher_decision === '1' && $justification->admin_decision === '1';

            $justification->status = $approved ? 'approved' : 'refused';
            $attendance->status = $approved ? 'justified' : 'absent';
        }
    
        $justification->save();
        $attendance->save();

        // Send notif
        $justification->student->user->notify(new JustificationReviewed($justification));
    
        return back()->with('success', 'Justification updated successfully.');
    }

    public function profile(Request $request) {
        $teacher = Auth::user()->teacher;

        return view('teacher.profile', compact('teacher'));
    }

    public function update_profile(Request $request) {
        $teacher = Auth::user()->teacher;

        $validated = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|unique:teachers,email,' . $teacher->id,
            'phone_number' => 'required|string|max:20',
            'grade' => 'required|in:Maître Assistant B,Maître Assistant A,Maître de Conférences A,Maître de Conférences B,Professeur',
            'address' => 'required|string|max:255',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:10240',
        ]);

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('avatars', $fileName, 'public');
            $teacher->img_url = $path;
        }

        $teacher->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'address' => $validated['address'],
        ]);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function update_password(Request $request) {
        $teacher = Auth::user()->teacher;

        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $teacher->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $teacher->password = Hash::make($request->new_password);
        $teacher->save();

        return back()->with('success', 'Password updated successfully.');
    }

    public function create_task(Request $request) {
        $teacher = Auth::user()->teacher;

        $classes = Classe::where('teacher_id', $teacher->id)->get();
        //dd($classes);
        $tasks = Task::where('teacher_id', $teacher->id)
        ->with(['submissions'])
        ->orderBy('created_at', 'desc')->get();
        
        return view('teacher.create_task', compact(['tasks', 'classes']));
    }

    public function store_task(Request $request) {
        $validated = $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'deadline' => 'required|date|after:now',
            'attachments.*' => 'nullable|file|max:10240|mimes:pdf,png,jpeg,jpg,txt',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $teacherId = Auth::user()->teacher->id;
    
            $task = Task::create([
                'classe_id' => $validated['classe_id'],
                'teacher_id' => $teacherId,
                'title' => $validated['title'],
                'description' => $validated['description'],
                'deadline' => $validated['deadline'],
            ]);
    
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('tasks', 'public');
    
                    $task->teacherFiles()->create([
                        'file_path' => $path,
                        'teacher_id' => $teacherId,
                    ]);
                }
            }
        });

        return back()->with('success', 'Task Published Successfully');

    }

    public function delete_task(Request $request, Task $task) {
        $teacher = Auth::user()->teacher;

        if($teacher->id != $task->teacher_id) {
            abort(403);
        }

        $task->delete();
        return back()->with('success', 'Task Deleted Successfully');
    }

    public function task_submissions(Request $request, Task $task) {
        $teacher = Auth::user()->teacher;

        if($teacher->id != $task->teacher_id) {
            abort(403);
        }

        $task->load(['submissions.studentFiles', 'submissions.student']);
        $submissions = $task->submissions;

        return view('teacher.submitted_tasks', compact([
            'task',
            'submissions'
        ]));
    }

    public function grade_submission(Request $request) {
        $request->validate([
        'submission_id' => 'required|exists:task_submissions,id',
        'grade' => 'nullable|string',
        ]);

        $submission = TaskSubmission::findOrFail($request->submission_id);
        $submission->grade = $request->grade;
        $submission->save();

        return response()->json(['success' => true]);
    }

    public function feedback_submission(Request $request) {
        $request->validate([
        'submission_id' => 'required|exists:task_submissions,id',
        'feedback' => 'nullable|string',
        ]);

        $submission = TaskSubmission::findOrFail($request->submission_id);
        $submission->feedback = $request->feedback;
        $submission->save();

        return response()->json(['success' => true]);
    }

    public function approve_submission(Request $request, TaskSubmission $submission) {
        $submission->status = 'approved';
        $submission->save();

        return back()->with('success', 'Task Approved !');
    }

    public function refuse_submission(Request $request, TaskSubmission $submission) {
        $submission->status = 'refused';
        $submission->save();

        return back()->with('success', 'Task refused !');
    }

}
