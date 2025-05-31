<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function index() {
        $user = Auth::user();
        //dd($user);
        if($user) {
            if($user->role === "student") return redirect()->route("student_dashboard");
            if($user->role === "teacher") return redirect()->route("teacher_dashboard");
            if($user->role === "admin") return redirect()->route("admin_dashboard");
        }

        return view('auth.login');
    }

    public function process(Request $request) {
        //dd($request);
        // request validation
        $request->validate([
            'role' => 'required|in:s,t,a',
            'password' => 'required|string',
        ]);

        $credentials = null;

        switch ($request->role) {
            case 's':
                $request->validate([
                    'registration_num' => 'required|string',
                ]);
                $student = Student::where('registration_num', $request->registration_num)->first();
                if ($student && Hash::check($request->password, $student->password)) $credentials = $student;
                break;
            case 't':
                $request->validate([
                    'email_adr' => 'required|email',
                ]);
                $teacher = Teacher::where('email', $request->email_adr)->first();
                if ($teacher && Hash::check($request->password, $teacher->password)) $credentials = $teacher;
                break;
            case 'a':
                $request->validate([
                    'username' => 'required|string',
                ]);
                $admin = Admin::where('username', $request->username)->first();
                if ($admin && Hash::check($request->password, $admin->password)) $credentials = $admin;
                break;
        }

        if($credentials) {
            //dd($credentials->user);
            Auth::login($credentials->user, $request->remember);
            if($request->role == 's') return redirect()->route('student_dashboard');
            if($request->role == 't') return redirect()->route('teacher_dashboard');
            if($request->role == 'a') return redirect()->route('admin_dashboard');
        }

        return back()->withErrors(['login' => 'Invalid credentials'])->withInput();
    }

    public function logout() {
        Auth::logout();

        return redirect()->route('login');
    }

    public function update_password(Request $request) {
        $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'confirmed', 'min:8'],
        ]);
    
        $user = Auth::user();
        /** @var User $user*/
        $roleModel = $user->roleModel();
    
        if (!$roleModel) {
            abort(403);
        }
    
        if (!Hash::check($request->current_password, $roleModel->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.'])->withInput();
        }

        $roleModel->password = Hash::make($request->new_password);
        $roleModel->save();
        
        // update updated_at
        $roleModel->touch();

        return match ($user->role) {
            'student' => redirect()->route('student_dashboard')->with('success', 'Password updated successfully.'),
            'teacher' => redirect()->route('teacher_dashboard')->with('success', 'Password updated successfully.'),
            'admin'   => redirect()->route('admin_dashboard')->with('success', 'Password updated successfully.'),
        };
    }
}
