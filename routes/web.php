<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AjaxController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\MessagingController;
use App\Http\Controllers\Student\StudentController;
use App\Http\Controllers\Teacher\TeacherController;
use App\Models\Admin;
use App\Models\Teacher;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Auth Routes
Route::get('/', [LoginController::class, 'index'])->name('login');
Route::post('/', [LoginController::class, 'process'])->name('login_submit');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/student/force_password_change', function () {
    return view('auth.force_password_change');
})->name('force_password_change')->middleware(['auth', 'role:student']);

// Global Routes
Route::post('/notifications/read-all', function () {
    auth()->user()->unreadNotifications->markAsRead();
    return back();
})->name('notifications.markAllRead');

Route::post('/update_password', [LoginController::class, 'update_password'])->name('global_update_password')->middleware(['auth', 'force.student.password']);


// Student Routes
Route::middleware(['auth', 'role:student', 'force.student.password'])->prefix('student')->group(function () {

    Route::get('/', [StudentController::class, 'dashboard'])->name('student_dashboard');
    Route::get('/inbox', [StudentController::class, 'inbox'])->name('student_inbox');
    Route::get('/profile', [StudentController::class, 'profile'])->name('student_profile');
    Route::post('/profile', [StudentController::class, 'update_profile'])->name('student_update_profile');
    Route::post('/update_password', [StudentController::class, 'update_password'])->name('student_update_password');
    Route::get('/classes', [StudentController::class, 'classes'])->name('student_classes');
    Route::get('/classes/class_details/{id}', [StudentController::class, 'class_details'])->name('student_class_details');
    Route::get('/schedule', [StudentController::class, 'schedule'])->name('student_schedule');
    Route::get('/justifications', [StudentController::class, 'justifications'])->name('student_justifications');
    Route::post('/justifications/store', [StudentController::class, 'store_justification'])->name('student_store_justification');
    Route::get('/justifications/view/{absence_id}', [StudentController::class, 'view_justification'])->name('student_view_justification');
    Route::get('/compose_message/{id?}', [MessagingController::class, 'compose'])->name('student_compose_message');
    Route::post('/compose_message', [MessagingController::class, 'store'])->name('student_store_message');
    Route::get('/conversation/{conversation}', [MessagingController::class, 'show'])->name('student_show_conversation');
    Route::post('/conversation/{conversation}', [MessagingController::class, 'reply'])->name('student_reply_conversation');
    Route::get('/change_group', [StudentController::class, 'change_group'])->name('student_change_group');
    Route::post('/change_group', [StudentController::class, 'submit_change_group'])->name('student_submit_group_change');
    Route::get('/classes/announcements/{id}', [StudentController::class, 'announcements'])->name('student_announcements');
    Route::get('/classes/tasks/{id}', [StudentController::class, 'view_tasks'])->name('student_view_tasks');
    Route::get('/classes/tasks/submit/{task}', [StudentController::class, 'submit_task'])->name('student_submit_task');
    Route::post('/classes/tasks/submit/{task}', [StudentController::class, 'store_task'])->name('student_submit_task');
    Route::get('/documents', [StudentController::class, 'documents'])->name('student_documents');
    Route::post('/documents', [StudentController::class, 'store_document_request'])->name('student_store_document_request');
    Route::get('/my_teachers', [StudentController::class, 'show_teachers'])->name('student_show_teachers');
});


// Teacher Routes
Route::middleware(['auth', 'role:teacher'])->prefix('teacher')->group(function () {

    Route::get('/', [TeacherController::class, 'dashboard'])->name('teacher_dashboard');
    Route::get('/inbox', [TeacherController::class, 'inbox'])->name('teacher_inbox');
    Route::get('/profile', [TeacherController::class, 'profile'])->name('teacher_profile');
    Route::post('/profile', [TeacherController::class, 'update_profile'])->name('teacher_update_profile');
    Route::post('/update_password', [TeacherController::class, 'update_password'])->name('teacher_update_password');
    Route::get('/announcements/{id?}', [TeacherController::class, 'announcement'])->name('teacher_announcement');
    Route::post('/announcements', [TeacherController::class, 'add_announcement'])->name('teacher_announcement_add');
    Route::delete('/delete_announcement/{id}', [TeacherController::class, 'delete_announcement'])->name('teacher_delete_announcement');
    Route::get('/classes_overview', [TeacherController::class, 'classes_overview'])->name('teacher_classes_overview');
    Route::get('/manage_classe/{id}', [TeacherController::class, 'manage_classe'])->name('teacher_manage_classe');
    Route::get('/attendance_sheet/{id}', [TeacherController::class, 'attendance_sheet'])->name('teacher_attendance_sheet');
    Route::post('/store_attendance/{session}', [TeacherController::class, 'store_attendance'])->name('teacher_store_attendance');
    Route::post('/attendance_sheet/export_list', [TeacherController::class, 'export_attendance'])->name('teacher_export_attendance');
    Route::post('/justification_update', [TeacherController::class, 'update_justification'])->name('teacher_update_justification');
    Route::get('/conversation/{conversation}', [MessagingController::class, 'show'])->name('teacher_show_conversation');
    Route::post('/conversation/{conversation}', [MessagingController::class, 'reply'])->name('teacher_reply_conversation');
    Route::get('/create_task', [TeacherController::class, 'create_task'])->name('teacher_create_task');
    Route::post('/create_task', [TeacherController::class, 'store_task'])->name('teacher_store_task');

});


// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin_dashboard');
    Route::get('/inbox', [AdminController::class, 'inbox'])->name('admin_inbox');
    Route::get('/profile', [AdminController::class, 'profile'])->name('admin_dashboard_profile');
    Route::post('/profile',[AdminController::class, 'update_profile'])->name('admin_dashboard_update_profile');
    Route::post('/update_password', [AdminController::class, 'update_password'])->name('admin_dashboard_update_password');
    Route::get('/manage_teachers', [AdminController::class, 'manage_teachers'])->name('admin_manage_teachers');
    Route::get('/create_teacher', [AdminController::class, 'create_teacher'])->name('admin_create_teacher');
    Route::post('/store_teacher', [AdminController::class, 'store_teacher'])->name('admin_store_teacher');
    Route::get('/export_teachers', [AdminController::class, 'export_teachers'])->name('admin_export_teachers');
    Route::get('/edit_teacher/{id}', [AdminController::class, 'edit_teacher'])->name('admin_edit_teacher');
    Route::post('/update_teacher/{id}', [AdminController::class, 'update_teacher'])->name('admin_update_teacher');
    Route::post('/delete_teacher/{id}', [AdminController::class, 'delete_teacher'])->name('admin_delete_teacher');
    Route::get('/manage_students', [AdminController::class, 'manage_students'])->name('admin_manage_students');
    Route::get('/create_student', [AdminController::class, 'create_student'])->name('admin_create_student');
    Route::post('/store_student', [AdminController::class, 'store_student'])->name('admin_store_student');
    Route::get('/export_students', [AdminController::class, 'export_students'])->name('admin_export_students');
    Route::get('/edit_student/{id}', [AdminController::class, 'edit_student'])->name('admin_edit_student');
    Route::post('/update_student/{id}',[AdminController::class, 'update_student'])->name('admin_update_student');
    Route::post('/delete_student/{id}', [AdminController::class, 'delete_student'])->name('admin_delete_student');
    Route::get('/academic_structure', [AdminController::class, 'academic_structure'])->name('admin_academic_structure');
    Route::post('/store_promotion', [AdminController::class, 'store_promotion'])->name('admin_store_promotion');
    Route::post('/update_promotion/{id}', [AdminController::class, 'update_promotion'])->name('admin_update_promotion');
    Route::post('/update_semester/{id}', [AdminController::class, 'update_semester'])->name('admin_update_semester');
    Route::post('/store_semester', [AdminController::class, 'store_semester'])->name('admin_store_semester');
    Route::post('/store_section', [AdminController::class, 'store_section'])->name('admin_store_section');
    Route::post('/update_section/{id}', [AdminController::class, 'update_section'])->name('admin_update_section');
    Route::post('/store_group', [AdminController::class, 'store_group'])->name('admin_store_group');
    Route::post('/update_group/{id}', [AdminController::class, 'update_group'])->name('admin_update_group');
    Route::post('/store_module', [AdminController::class, 'store_module'])->name('admin_store_module');
    Route::post('/update_module/{id}', [AdminController::class, 'update_module'])->name('admin_update_module');
    Route::post('/store_class', [AdminController::class, 'store_classe'])->name('admin_store_class');
    Route::get('/manage_sessions', [AdminController::class, 'manage_sessions'])->name('admin_manage_sessions');
    Route::post('/classe/store_template', [AdminController::class, 'store_template'])->name('admin_store_template');
    Route::post('/classe/update_template/{id}', [AdminController::class, 'update_template'])->name('admin_update_template');
    Route::get('/edit_session/{id}', [AdminController::class, 'edit_session'])->name('admin_edit_session');
    Route::post('/update_session/{id}', [AdminController::class, 'update_session'])->name('admin_update_session');
    Route::get('/add_session/{id}', [AdminController::class, 'add_session'])->name('admin_add_session');
    Route::post('/store_session', [AdminController::class, 'store_session'])->name('admin_store_session');
    Route::post('/justification_update', [AdminController::class, 'update_justification'])->name('admin_update_justification');
    Route::post('/group_change_update', [AdminController::class, 'update_group_change'])->name('admin_update_group_change');

    // AJAX ROUTES
    Route::get('/get_semesters/{promotion_id}', [AjaxController::class, 'get_semesters'])->name('admin_ajax_get_semesters');
    Route::get('/get_sections/{semester_id}', [AjaxController::class, 'get_sections'])->name('admin_ajax_get_sections');
    Route::get('/get_groups/{section_id}', [AjaxController::class, 'get_groups'])->name('admin_ajax_get_groups');
    Route::get('/get_modules/{semester_id}', [AjaxController::class, 'get_modules'])->name('admin_ajax_get_modules');
    Route::get('/classes/search', [AjaxController::class, 'search_classes'])->name('admin_ajax_search_classes');
    Route::get('/classes/classe_sessions', [AjaxController::class, 'classe_sessions'])->name('admin_classe_sessions');
    Route::post('/import_teacher', [AjaxController::class, 'import_teacher'])->name('admin_import_teacher');
    Route::post('/import_student', [AjaxController::class, 'import_student'])->name('admin_import_student');
    Route::post('/toggle_group_change', [AjaxController::class, 'toggle_group_change'])->name('admin_toggle_group_change');
});