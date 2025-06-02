<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\StudentsImport;
use App\Imports\TeachersImport;
use App\Models\Classe;
use App\Models\Group;
use App\Models\Module;
use App\Models\Section;
use App\Models\Semester;
use App\Models\Session as ModelsSession;
use App\Models\SessionTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException as ValidatorsValidationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Session;

use function Termwind\render;

class AjaxController extends Controller
{
    public function get_semesters($promotion_id) {
        $semesters = Semester::where('promotion_id', $promotion_id)->get();
        return response()->json($semesters);
    }

    public function get_sections($semester_id) {
        $sections = Section::where('semester_id', $semester_id)->get();
        return response()->json($sections);
    }

    public function get_groups($section_id) {
        $groups = Group::where('section_id', $section_id)->get();
        return response()->json($groups);
    }

    public function get_modules($semester_id) {
        $modules = Module::where('semester_id', $semester_id)->get();
        return response()->json($modules);
    }

    public function import_teacher(Request $request) {
        try {
            Excel::import(new TeachersImport, $request->file('excel_file'));
        
            return response(200);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
    
            $errors = [];
    
            foreach ($failures as $failure) {
                
                $value = $failure->values()[$failure->attribute()];
    
                $errors[] = [
                    'row' => $failure->row(),
                    'attribute' => $failure->attribute(),
                    'errors' => $failure->errors(),
                    'value' => $value,
                ];
            }
    
            return response()->json([
                'success' => false,
                'validation_errors' => $errors
            ], 422);
        }
    }

    public function import_student(Request $request) {
        try {
            Excel::import(new StudentsImport, $request->file('excel_file'));

            $failedImportFile = Session::get('failed_import_file', null);
            Session::forget("failed_import_file");
        
            return response()->json([
                'success' => true,
                'failed_import_file' => $failedImportFile,
            ]);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
    
            $errors = [];
    
            foreach ($failures as $failure) {
                
                $value = $failure->values()[$failure->attribute()];
    
                $errors[] = [
                    'row' => $failure->row(),
                    'attribute' => $failure->attribute(),
                    'errors' => $failure->errors(),
                    'value' => $value,
                ];
            }
            
            
            return response()->json([
                'success' => false,
                'validation_errors' => $errors,
                
            ], 422);
        }
    }

    public function search_classes(Request $request) {
        $admin = Auth::user()->admin;
        $query = Classe::with(['group.section.semester.promotion', 'module', 'teacher', 'sessionTemplate']);

        if($request->filled('query')) {
            $search = $request->input('query');
            $query->whereHas('module', function ($q) use ($search) {
                $q->where('name', 'like', "%".$search."%");
            });
        }

        if($request->filled('promotion_id')) {
            $query->whereHas('group.section.semester.promotion', function ($q) use ($request) {
                $q->where('id', $request->promotion_id);
            });
        }

        if ($request->filled('semester_id')) {
            $query->whereHas('group.section.semester', function ($q) use ($request) {
                $q->where('id', $request->semester_id); 
            });
        }
    
        if ($request->filled('section_id')) {
            $query->whereHas('group.section', function ($q) use ($request) {
                $q->where('id', $request->section_id);
            });
        }
    
        if ($request->filled('group_id')) {
            $query->whereHas('group', function ($q) use ($request) {
                $q->where('id', $request->group_id);
            });
        }

        // extra check to make sure we only return current department classes
        $query->whereHas('group.section.semester.promotion', function ($q) use ($admin) {
            $q->where('department_id', $admin->department_id);
        });

        $perPage = 10; // element per page;
        $classes = $query->paginate($perPage);

        foreach ($classes as $classe) {
            $classe->sessionTemplate = null;
        }

        return response()->json([
            'html' => view('admin.partials.result_classe', compact('classes'))->render(),
            'hasMore' => $classes->hasMorePages(),
        ]);
    }

    public function classe_sessions(Request $request) {
        $classe_id = $request->classe_id;
        $template = SessionTemplate::with('classe')->where('classe_id', $classe_id)->get();
        $sessions = ModelsSession::where('classe_id', $classe_id)->get();

        return response()->json([
            'template' => view('admin.partials._session_template_form', compact(['template', 'classe_id']))->render(),
            'table' => view('admin.partials._sessions_table', compact('sessions'))->render(),
        ]);
    }

    public function toggle_group_change(Request $request) {
        $admin = Auth::user()->admin;
        $department = $admin->department;

        $department->is_group_change_allowed = $request->group_change_open;
        $department->save();

        return response()->json(['success' => true]);
    }
}
