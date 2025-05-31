<?php

namespace App\Http\Controllers\Admin;

use App\Exports\StudentsExport;
use App\Exports\TeachersExport;
use App\Http\Controllers\Controller;
use App\Models\Classe;
use App\Models\Group;
use App\Models\GroupChangeRequest;
use App\Models\Justification;
use App\Models\Module;
use App\Models\Promotion;
use App\Models\Section;
use App\Models\Semester;
use App\Models\Session;
use App\Models\SessionTemplate;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Notifications\JustificationReviewed;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException as ValidatorsValidationException;

use function Laravel\Prompts\error;

class AdminController extends Controller
{
    public function dashboard(Request $request) {
        
        return view('admin.dashboard');
    }

    public function profile(Request $request) {

        return view('admin.profile');
    }

    public function update_profile(Request $request) {
        $admin = Auth::user()->admin;

        $validated = $request->validate([
            'first_name'    => 'nullable|string|max:50',
            'last_name'     => 'nullable|string|max:50',
            'username'      => 'required|string|max:50|unique:admins,username,' . $admin->id,
            'email'         => 'required|email|max:100|unique:admins,email,' . $admin->id,
            'phone_number'  => 'nullable|string|max:20',
            'address'       => 'nullable|string|max:255',
            'avatar'        => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240',
        ]);

        if($request->hasFile('avatar')) {
            if($admin->img_url && Storage::disk('public')->exists($admin->img_url)) {
                Storage::disk('public')->delete($admin->img_url);
            }
            
            $file = $request->file('avatar');
            $fileName = time().'_'.Str::random(10).'.'.$file->getClientOriginalExtension();
            $path = $request->file('avatar')->storeAs('avatars', $fileName, 'public');
            //dd($path);
            $admin->img_url = $path;
        }

        $admin->first_name = $validated['first_name'];
        $admin->last_name = $validated['last_name'];
        $admin->username = $validated['username'];
        $admin->email = $validated['email'];
        $admin->phone_number = $validated['phone_number'];
        $admin->address = $validated['address'];

        $admin->save();

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    public function update_password(Request $request) {
        $admin = Auth::user()->admin;

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $admin->password)) {
            throw ValidationException::withMessages([
                'error' => 'Your current password is incorrect.',
            ]);
        }

        $admin->update([
            'password' => Hash::make($request->new_password),
        ]);
    
        return back()->with('success', 'Password updated successfully!');
    }

    public function manage_teachers(Request $request) {
        $teachers = Teacher::where('department_id', Auth::user()->admin->department_id)->get();
        return view('admin.manage_teachers', compact([
            'teachers',
        ]));
    }

    public function create_teacher(Request $request) {
        return view('admin.create_teacher');
    }

    public function store_teacher(Request $request) {
        $validated = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|unique:teachers,email',
            'phone_number' => 'required|string|max:15',
            'grade' => 'required|in:Maître Assistant B, Maître Assistant A, Maître de Conférences A, Maître de Conférences B, Professeur',
            'address' => 'required|string|max:255',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:10240',
        ]);

        $path = null;
        if($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $fileName = time().'_'.Str::random(10).'.'.$file->getClientOriginalExtension();
            $path = $request->file('avatar')->storeAs('avatars', $fileName, 'public');
        }

        $user = User::create([
            'role' => 'teacher',
        ]);

        // Send it in email later
        $password = Str::random(10);

        $admin = Auth::user()->admin;
        //dd($admin->department_id);
        Teacher::create([
            'user_id' => $user->id,
            'department_id' => $admin->department_id,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'password' => Hash::make($password),
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'grade' => $validated['grade'],
            'address' => $validated['address'],
            'img_url' => $path,
        ]);

        return back()->with('success', 'Professor added successfully!');
    }

    public function export_teachers(Request $request) {
        $request->validate([
            'q' => 'required|array'
        ]);

        $emails = $request->input('q');

        $teachers = Teacher::whereIn('email', $emails)->with('department')->get();

        return Excel::download(new TeachersExport($teachers), time().'teachers_list.xlsx');
    }
    
    public function edit_teacher(Request $request) {
        // $request->validate([
        //     'id' => 'required|exists:teachers,id'
        // ]);

        $teacher = Teacher::findOrFail($request->id);
        return view('admin.edit_teacher', compact([
            'teacher',
        ]));
    }

    public function update_teacher(Request $request) {
        $teacher = Teacher::find($request->id);

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'email' => [
                'required',
                'email',
                Rule::unique('teachers', 'email')->ignore($teacher->id, 'id'),
            ],
            'phone_number' => ['required', 'string', 'max:15'],
            'grade' => ['required', 'in:Maître Assistant B, Maître Assistant A, Maître de Conférences A, Maître de Conférences B, Professeur'],
            'address' => ['required', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:10240'],
        ]);

        $path = $teacher->img_url;
        if($request->hasFile('avatar')) {
            if($teacher->img_url && Storage::disk('public')->exists($teacher->img_url)) {
                Storage::delete('public/' . $teacher->img_url);
            }
            $file = $request->file('avatar');
            $fileName = time().'_'.Str::random(10).'.'.$file->getClientOriginalExtension();
            $path = $request->file('avatar')->storeAs('avatars', $fileName, 'public');
        }

        $teacher->first_name = $validated['first_name'];
        $teacher->last_name = $validated['last_name'];
        $teacher->email = $validated['email'];
        $teacher->phone_number = $validated['phone_number'];
        $teacher->grade = $validated['grade'];
        $teacher->address = $validated['address'];
        $teacher->img_url = $path;

        $teacher->save();

        return back()->with('success', 'Professor Profile Updated');
    }

    public function delete_teacher($id) {
        $teacher = Teacher::find($id);

        if($teacher->img_url && Storage::disk('public')->exists($teacher->img_url)) {
            Storage::delete('public/' . $teacher->img_url);
        }

        $teacher->delete();
        return back();
    }

    public function manage_students(Request $request) {
        $admin = Auth::user()->admin;
        $department = $admin->department;
        $promotion = $department->promotions()->with('semesters.sections.groups.students')->first();
        $students = [];

        foreach ($promotion->semesters as $semester) {
            foreach ($semester->sections as $section) {
                foreach ($section->groups as $group) {
                    foreach ($group->students as $student) {
                        $students[] = $student;
                    }
                }
            }
        }

        //dd($students);

        return view('admin.manage_students', compact([
            'students'
        ]));
    }

    public function create_student(Request $request) {
        $admin = Auth::user()->admin;
        //dd($admin->department_id);
        $promotions = Promotion::where('department_id', $admin->department_id)->get();
        return view('admin.create_student', compact([
            'promotions'
        ]));
    }

    public function store_student(Request $request) {
        $validated = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|unique:students,email',
            'registration_num' => 'required|string|unique:students,registration_num',
            'phone_number' => 'required|string|max:20',
            'address' => 'nullable|string|max:500',
            'gender' => 'nullable|in:male,female',
            'birth_date' => 'required|date|before:today',
            'promotion_id' => 'required|exists:promotions,id',
            'semester_id' => 'required|exists:semesters,id',
            'section_id' => 'required|exists:sections,id',
            'group_id' => 'required|exists:groups,id',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        $path = null;
        if($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $fileName = time().'_'.Str::random(10).$file->getClientOriginalExtension();
            $path = $request->file('avatar')->storeAs('avatars', $fileName, 'public');
        }

        // email it later
        $password = Str::random(20);

        $user = User::create([
            'role' => 'student',
        ]);

        Student::create([
            'user_id' => $user->id,
            'group_id' => $validated['group_id'],
            'registration_num' => $validated['registration_num'],
            'password' => Hash::make($password),
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'address' => $validated['address'],
            'gender' => $validated['gender'],
            'birth_date' => $validated['birth_date'],
            'img_url' => $path,
        ]);

        return back()->with('success', 'Student added successfully!');
    }

    public function export_students(Request $request) {
        $request->validate([
            'q' => 'required|array'
        ]);

        $registration_nums = $request->input('q');

        $students = Student::whereIn('registration_num', $registration_nums)
                    ->with('group.section.semester.promotion')
                    ->get();
        //dd($students);
        return Excel::download(new StudentsExport($students), time().'students_list.xlsx');
    }

    public function edit_student(Request $request) {
        // $request->validate([
        //     'id' => 'required|exists:students,id'
        // ]);

        $promotions = Promotion::where('department_id', Auth::user()->admin->department_id)->get();
        $student = Student::find($request->id);
        return view('admin.edit_student', compact([
            'student',
            'promotions'
        ]));
    }

    public function update_student(Request $request) {
        $student = Student::findOrFail($request->id);
        //dd($student);
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'email' => [
                'required',
                'email',
                Rule::unique('students', 'email')->ignore($student->id),
            ],
            'registration_num' => [
                'required',
                'string',
                Rule::unique('students', 'registration_num')->ignore($student->id),
            ],
            'phone_number' => ['required', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'gender' => ['nullable', Rule::in(['male', 'female'])],
            'birth_date' => ['required', 'date', 'before:today'],
            'promotion_id' => ['required', Rule::exists('promotions', 'id')],
            'semester_id' => ['required', Rule::exists('semesters', 'id')],
            'section_id' => ['required', Rule::exists('sections', 'id')],
            'group_id' => ['required', Rule::exists('groups', 'id')],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:10240'],
        ]);
        
        

        $path = $student->img_url;
        if($request->hasFile('avatar')) {
            if($student->img_url && Storage::disk('public')->exists($student->img_url)) {
                Storage::delete('public/' . $student->img_url);
            }
            $file = $request->file('avatar');
            $fileName = time().'_'.Str::random(10).'.'.$file->getClientOriginalExtension();
            $path = $request->file('avatar')->storeAs('avatars', $fileName, 'public');
        }

        $student->first_name = $validated["first_name"];
        $student->last_name = $validated["last_name"];
        $student->email = $validated["email"];
        $student->registration_num = $validated["registration_num"];
        $student->phone_number = $validated["phone_number"];
        $student->address = $validated["address"];
        $student->gender = $validated["gender"];
        $student->birth_date = $validated["birth_date"];
        $student->group_id = $validated["group_id"];
        $student->img_url = $path;

        $student->save();

        return back()->with('success', 'Student Profile Updated');
    }

    public function delete_student(Request $request) {
        $student = Student::findOrFail($request->id);
        $student->delete();
        return back();
    }

    public function academic_structure(Request $request) {
        $promotions = Promotion::with(['semesters.modules', 'semesters.sections.groups'])
                                ->where('department_id', Auth::user()->admin->department_id)
                                ->get();
        
        $department = Auth::user()->admin->department;
        $teachers = $department->teachers;

        //dd($promotions);
        return view('admin.academic_structure', compact([
            'promotions',
            'teachers',
            'department'
        ]));
    }

    public function store_promotion(Request $request) {
        $admin = Auth::user()->admin;
        $request->validate([
            'name' => [
                'required',
                'string',
                Rule::unique('promotions')->where(function ($query) use ($admin) {
                    return $query->where('department_id', $admin->department_id);
                }),
            ],
        ]);
        //dd($admin->department_id);
        Promotion::create([
            'department_id' => $admin->department_id,
            'name' => $request->name,
        ]);

        return back()->with('success', 'Promotion add successfuly !');
    }

    public function update_promotion(Request $request) {
        $admin = Auth::user()->admin;

        $request->validate([
            'name' => [
                'required',
                'string',
                Rule::unique('promotions')->where(function ($query) use ($admin) {
                    return $query->where('department_id', $admin->department_id);
                }),
            ],
        ]);

        $promo = Promotion::where('id', $request->id)
                            ->where('department_id', $admin->department_id)
                            ->first();
        
        $promo->name = $request->name;
        $promo->department_id = $admin->department_id;
        $promo->save();

        return back()->with('success', 'Promotion Update Successfuly');
    }

    public function store_semester(Request $request) {
        $admin = Auth::user()->admin;
        //dd($request);
        $request->validate([
            'promotion_id' => [
                'required',
                'integer',
                Rule::exists('promotions', 'id')->where(function ($query) use ($admin) {
                    return $query->where('department_id', $admin->department_id);
                })
            ],
            'name' => [
                'required',
                'string',
                Rule::unique('semesters')->where(function ($query) use ($request) {
                    return $query->where('promotion_id', $request->promo_id);
                }),
            ],

        ]);

        Semester::create([
            'name' => $request->name,
            'promotion_id' => $request->promotion_id,
        ]);

        return back()->with('success', 'Semester Added Successfuly !');
    }

    public function update_semester(Request $request) {
        $admin = Auth::user()->admin;
        $request->validate([
            'promo_id' => [
                'required',
                'integer',
                Rule::exists('promotions', 'id')->where(function ($query) use ($admin) {
                    return $query->where('department_id', $admin->department_id);
                })
            ],
            'name' => [
                'required',
                'string',
                Rule::unique('semesters')->where(function ($query) use ($request) {
                    return $query->where('promotion_id', $request->promo_id);
                }),
            ],

        ]);

        $semester = Semester::findOrFail($request->id);

        $semester->name = $request->name;
        $semester->save();

        //dd($semester);
        return back()->with('success', 'Semester Updated Successfuly !');
    }

    public function store_section(Request $request) {
        $admin = Auth::user()->admin;
        $request->validate([
            'promotion_id' => [
                'required',
                'integer',
                Rule::exists('semesters', 'promotion_id')
            ],
            'semester_id' => [
                'required',
                'integer',
                Rule::exists('semesters', 'id')
            ],
            'name' => [
                'required',
                'string',
                Rule::unique('sections')->where(function ($query) use ($request) {
                    return $query->where('semester_id', $request->semester_id);
                }),
            ],

        ]);

        //dd($admin);

        Section::create([
            'name' => $request->name,
            'semester_id' => $request->semester_id,
        ]);

        return back()->with('success', 'Section Added Successfuly');
    }

    public function update_section(Request $request) {
        $request->validate([
            'semester_id' => [
                'required',
                'integer',
                Rule::exists('semesters', 'id')
            ],
            'name' => [
                'required',
                'string',
                Rule::unique('sections')->where(function ($query) use ($request) {
                    return $query->where('semester_id', $request->semester_id);
                }),
            ],
        ]);

        $section = Section::findOrFail($request->id);

        $section->name = $request->name;
        $section->save();

        return back()->with('success', 'Section Updated Successfuly !');
    }

    public function store_group(Request $request) {
        $request->validate([
            'promotion_id' => [
                'required',
                'integer',
                Rule::exists('promotions', 'id')
            ],
            'semester_id' => [
                'required',
                'integer',
                Rule::exists('semesters', 'id')->where(function ($query) use ($request) {
                    return $query->where('promotion_id', $request->promotion_id);
                })
            ],
            'section_id' => [
                'required',
                'integer',
                Rule::exists('sections', 'id')->where(function ($query) use ($request) {
                    return $query->where('semester_id', $request->semester_id);
                })
            ],
            'name' => [
                'required',
                'string',
                Rule::unique('groups', 'name')->where(function ($query) use ($request) {
                    return $query->where('section_id', $request->section_id);
                })
            ]
        ]);

        Group::create([
            'section_id' => $request->section_id,
            'name' => $request->name,
        ]);

        return back()->with('success', 'Group Added Successfuly !');
    }

    public function update_group(Request $request) {
        $request->validate([
            'section_id' => [
                'required',
                'integer',
                Rule::exists('groups', 'section_id')->where(function ($query) use ($request) {
                    return $query->where('id', $request->id);
                })
            ],
            'name' => [
                'required',
                'string',
                Rule::unique('groups', 'name')->where(function ($query) use ($request) {
                    return $query->where('section_id', $request->section_id);
                })
            ]
        ]);

        $group = Group::findOrFail($request->id);
        $group->name = $request->name;
        $group->section_id = $request->section_id;

        return back()->with('success', 'Group Updated Successfuly !');
    }

    public function store_module(Request $request) {
        //dd($request->color);
        $request->validate([
            'promotion_id' => [
                'required',
                'integer',
                Rule::exists('promotions', 'id')
            ],
            'semester_id' => [
                'required',
                'integer',
                Rule::exists('semesters', 'id')
            ],
            'name' => [
                'required',
                'string',
                Rule::unique('modules', 'name')->where(function ($query) use ($request) {
                    $query->where('semester_id', $request->semester_id);
                })
            ],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240',
            'color' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $value = strtolower(trim($value));

                    $isHex = preg_match('/^#([a-f0-9]{3}|[a-f0-9]{6})$/i', $value);
                    $isNamedColor = in_array($value, [
                        'black', 'white', 'red', 'green', 'blue', 'yellow', 'purple', 'orange',
                        'gray', 'grey', 'pink', 'cyan', 'magenta', 'lime', 'maroon',
                        'navy', 'olive', 'teal', 'aqua', 'silver', 'fuchsia', 'brown'
                    ]);

                    if (!$isHex && !$isNamedColor) {
                        $fail("The $attribute must be a valid hex color (e.g., #ff0000) or a CSS color name (e.g., red).");
                    }
                }
            ]
        ]);

        $path = null;
        if($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time().'_'.Str::random(10).$file->getClientOriginalExtension();
            $path = $request->file('image')->storeAs('modules', $fileName, 'public');
        }

        Module::create([
            'name' => $request->name,
            'semester_id' => $request->semester_id,
            'img_url' => $path,
            'color' => $request->color,
        ]);

        return back()->with('success', 'Module Added Successfuly');
    }

    public function update_module(Request $request) {
        $request->validate([
            'semester_id' => [
                'required',
                'integer',
                Rule::exists('semesters', 'id')
            ],
            'name' => [
                'required',
                'string',
                Rule::unique('modules', 'name')->where(function ($query) use ($request) {
                    $query->where('semester_id', $request->semester_id);
                })->ignore($request->id)
            ],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240',
            'color' => [
                'required',
                'string',
                'nullable',
                function ($attribute, $value, $fail) {
                    $value = strtolower(trim($value));

                    $isHex = preg_match('/^#([a-f0-9]{3}|[a-f0-9]{6})$/i', $value);
                    $isNamedColor = in_array($value, [
                        'black', 'white', 'red', 'green', 'blue', 'yellow', 'purple', 'orange',
                        'gray', 'grey', 'pink', 'cyan', 'magenta', 'lime', 'maroon',
                        'navy', 'olive', 'teal', 'aqua', 'silver', 'fuchsia', 'brown'
                    ]);

                    if (!$isHex && !$isNamedColor) {
                        $fail("The $attribute must be a valid hex color (e.g., #ff0000) or a CSS color name (e.g., red).");
                    }
                }
            ]
        ]);

        $module = Module::findOrFail($request->id);

        $path = null;
        if($request->hasFile('image')) {
            if($module->img_url && Storage::disk('public')->exists($module->img_url)) {
                Storage::disk('public')->delete($module->img_url);
            }
            
            $file = $request->file('image');
            $fileName = time().'_'.Str::random(10).'.'.$file->getClientOriginalExtension();
            $path = $request->file('image')->storeAs('modules', $fileName, 'public');
        }

        if(!($module->name == $request->name)) $module->name = $request->name;
        $module->semester_id = $request->semester_id;
        $module->img_url = $path ?? $module->img_url;
        $module->color = $request->color ?? $module->color;

        $module->save();

        return back()->with('success', 'Module updated successfuly');
    }

    public function store_classe(Request $request) {
        $admin = Auth::user()->admin;

        // we need to verify two things 
        // first verify the form (basic form validation)
        // two must validate the database constraintes
        
        // basic form validation
        $validated = $request->validate([
            'promotion_id' => [
                'required',
                'integer',
                Rule::exists('promotions', 'id')->where(function ($query) use ($admin) {
                    return $query->where('department_id', $admin->department_id);
                })
            ],
            'semester_id' => [
                'required',
                'integer',
                Rule::exists('semesters', 'id')
            ],
            'section_id' => [
                'required',
                'integer',
                Rule::exists('sections', 'id')
            ],
            'group_id' => [
                'required',
                'integer',
                Rule::exists('groups', 'id')->where(function ($query) use ($request) {
                    return $query->where('section_id', $request->section_id);
                })
            ],
            'teacher_id' => [
                'required',
                'integer',
                Rule::exists('teachers', 'id')->where(function ($query) use ($admin) {
                    return $query->where('department_id', $admin->department_id);
                })
            ],
            'module_id' => [
                'required',
                'integer',
                Rule::exists('modules', 'id')->where(function ($query) use ($request) {
                    return $query->where('semester_id', $request->semester_id);
                })
            ],
            'class_type' => 'required|in:tp,td'
        ]);

        
        // database constraintes validation
        $exists1 = Classe::where([
            'group_id' => $validated['group_id'],
            'module_id' => $validated['module_id'],
            'class_type' => $validated['class_type'],
        ])->exists();

        if($exists1) {
            return back()->withErrors([
                'class_type' => 'This class already exists for this group and module.'
            ]);
        }

        Classe::create([
            'group_id' => $validated['group_id'],
            'module_id' => $validated['module_id'],
            'teacher_id' => $validated['teacher_id'],
            'class_type' => $validated['class_type'],
        ]);

        return back()->with('success', 'Class created successfuly !');
    }

    public function manage_sessions() {
        $admin = Auth::user()->admin;
        // dd($admin->department_id);
        $promotions = Promotion::where('department_id', $admin->department_id)->get();
        return view('admin.manage_sessions', compact([
            'promotions',
        ]));
    }

    public function store_template(Request $request) {
        $validated = $request->validate([
            'classe_id' => [
                'required',
                'integer',
                Rule::exists('classes', 'id')
            ],
            'weekday' => 'required|integer',
            'time_slot' => 'required|string',
            'location' => 'required|string',
            'status' => 'required|in:active,inactive',
            'notes' => 'string|nullable',
        ]);

        $classe = Classe::findOrFail($request->classe_id);

        list($start_time, $end_time) = explode(' - ', $request->input('time_slot'));

        SessionTemplate::create([
            'classe_id' => $validated['classe_id'],
            'weekday' => $validated['weekday'],
            'start_time' => $start_time,
            'end_time' => $end_time,
            'location' => $validated['location'],
            'notes' => $validated['notes'],
            'status' => $validated['status'],
            'type' => $classe->class_type,
        ]);

        return back()->with('success', 'Template created successfuly !');
    }

    public function update_template(Request $request) {
        $validated = $request->validate([
            'classe_id' => [
                'required',
                'integer',
                Rule::exists('session_templates', 'classe_id')
            ],
            'weekday' => 'required|integer',
            'time_slot' => 'required|string',
            'location' => 'required|string',
            'status' => 'required|in:active,inactive',
            'notes' => 'string|nullable',
        ]);
        
        $classe = Classe::findOrFail($request->classe_id);

        list($start_time, $end_time) = explode(' - ', $request->input('time_slot'));

        $template = SessionTemplate::findOrFail($request->id);

        $template->classe_id = $validated['classe_id'];
        $template->weekday = $validated['weekday'];
        $template->start_time = $start_time;
        $template->end_time = $end_time;
        $template->location = $validated['location'];
        $template->type = $classe->class_type;
        $template->status = $validated['status'];
        $template->notes = $validated['notes'];

        $template->save();
        
        return back()->with('success', 'Template updated successfuly !');
    }

    public function edit_session(Request $request) {
        $session = Session::findOrFail($request->id);

        return view('admin.edit_session', compact([
            'session'
        ]));
    }

    public function update_session(Request $request) {
        $session = Session::findOrFail($request->id);
        
        $validated = $request->validate([
            'session_date' => 'required|date',
            'start_time' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            'end_time' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/', 'after:start_time'],
            'location' => 'required|string|max:20',
            'type' => 'required|string|max:3',
            'status' => 'required|in:scheduled,completed,canceled,rescheduled',
            'notes' => 'nullable|string',
        ]);
    
        $session->update($validated);
    
        return back()->with('success', 'Session updated successfully!');
    }

    public function add_session(Request $request) {
        $classId = $request->id;
        return view('admin.add_session', compact('classId'));
    }

    public function store_session(Request $request) {
        $validated = $request->validate([
            'session_date' => 'required|date',
            'start_time' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            'end_time' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/', 'after:start_time'],
            'location' => 'required|string|max:20',
            'type' => 'required|string|max:3',
            'status' => 'required|in:scheduled,completed,canceled,rescheduled',
            'notes' => 'nullable|string',
        ]);

        Session::create($request->all());

        return back()->with('success', 'Session Created Successfully !');
    }

    public function inbox(Request $request) {
        $admin = Auth::user()->admin;
        $newJustifications = Justification::with(['student', 'session.classe.module'])
            ->whereHas('session.classe.module.semester.promotion', function ($q) use ($admin) {
                $q->where('department_id', $admin->department_id);
            })
            ->where('admin_decision', '2') // '2' means under review / not handled
            ->orderByDesc('created_at')
            ->get();
        
        $groupChanges = GroupChangeRequest::with(['student.group', 'toGroup'])
        ->whereHas('student.group.section.semester.promotion', function ($query) use ($admin) {
            $query->where('department_id', $admin->department_id);
        })
        ->where('status', 'pending')
        ->latest()
        ->get();

        //dd($newJustifications);
        return view('admin.inbox', compact('newJustifications', 'groupChanges'));
    }

    public function update_justification(Request $request) {
        $request->validate([
            'justification_id' => 'required|exists:justifications,id',
            'action' => 'required|in:approve,refuse',
        ]);
    
        $justification = Justification::findOrFail($request->justification_id);
        $attendance = $justification->attendance;
    
        $justification->admin_decision = $request->action === 'approve' ? '1' : '0';
        
        // Determine final status if teahcer has already reviewed
        if ($justification->teacher_decision !== '2') {
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

    public function update_group_change(Request $request) {
        $request->validate([
            'change_id' => 'required|exists:group_change_requests,id',
            'action' => 'required|in:approve,refuse',
        ]);
    
        $changeRequest = GroupChangeRequest::with('student')->findOrFail($request->change_id);
    
        $student = $changeRequest->student;

        if(!($student->group->section->semester->promotion->department_id === Auth::user()->admin->department_id)) {
            abort(403);
        }
    
        if ($changeRequest->status !== 'pending') {
            return back()->withErrors('This request has already been processed.');
        }
    
        if ($request->action === 'approve') {
            $student->group_id = $changeRequest->to_group_id;
            $student->save();
    
            $changeRequest->status = 'approved';
        } else {
            $changeRequest->status = 'refused';
        }
    
        $changeRequest->save();
    
        return back()->with('success', 'Group change request ' . $request->action . 'd successfully.');
    }

    // public function test_store_teacher(Request $request)
    // {
    //     try {
    //         $validated = $request->validate([
    //             'first_name' => 'required|string|max:50',
    //             'last_name' => 'required|string|max:50',
    //             'email' => 'required|email|unique:teachers,email',
    //             'phone_number' => 'required|string|max:15',
    //             'grade' => 'required|in:Maître Assistant B,Maître Assistant A,Maître de Conférences A,Maître de Conférences B,Professeur',
    //             'address' => 'required|string|max:255',
    //         ]);

    //         $path = null;
    //         if ($request->hasFile('avatar')) {
    //             $file = $request->file('avatar');
    //             $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
    //             $path = $file->storeAs('avatars', $fileName, 'public');
    //         }

    //         $user = User::create([
    //             'role' => 'teacher',
    //         ]);

    //         $password = Str::random(10);

    //         $teacher = Teacher::create([
    //             'user_id' => $user->id,
    //             'department_id' => 2, // Hardcoded for now
    //             'first_name' => $validated['first_name'],
    //             'last_name' => $validated['last_name'],
    //             'password' => Hash::make($password),
    //             'email' => $validated['email'],
    //             'phone_number' => $validated['phone_number'],
    //             'grade' => $validated['grade'],
    //             'address' => $validated['address'],
    //             'img_url' => $path,
    //         ]);

    //         Log::info('Teacher created', ['teacher' => $teacher]);

    //         return response()->json([
    //             'status' => 'success',
    //             'message' => 'Teacher created',
    //             'teacher_id' => $teacher->id
    //         ], 200);
    //     } catch (\Exception $e) {
    //         Log::error('Error creating teacher', ['error' => $e->getMessage()]);
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => $e->getMessage()
    //         ], 500);
    //     }
    // }

}
