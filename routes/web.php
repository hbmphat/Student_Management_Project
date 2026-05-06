<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// ================= AUTH =================
Auth::routes();

Route::get('/', function () {
    return Auth::check() ? redirect('/home') : redirect('/login');
});

// ================= PUBLIC ROUTES =================
Route::get('/teachers', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard.public');

// ================= PROTECTED ROUTES =================
Route::middleware('auth')->group(function () {

    // ===== DASHBOARD =====
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    // ===== PROFILE =====
    Route::post('/profile/change-password', [App\Http\Controllers\HomeController::class, 'changePassword'])->name('profile.change-password');

    // ===== COURSES =====
    Route::get('/courses/export-pdf', [App\Http\Controllers\CourseController::class, 'exportPdf'])->name('courses.export-pdf');
    Route::resource('courses', App\Http\Controllers\CourseController::class);

    // ===== TEACHERS =====
    Route::get('/teachers/export-pdf', [App\Http\Controllers\TeacherController::class, 'exportPdf'])->name('teachers.export-pdf');
    Route::resource('teachers', App\Http\Controllers\TeacherController::class);

    // ===== REGISTRATION CODES =====
    Route::resource('registration-codes', App\Http\Controllers\RegistrationCodeController::class)->only(['index', 'store', 'destroy']);

    Route::post(
        '/registration-codes/{id}/toggle-block',
        [App\Http\Controllers\RegistrationCodeController::class, 'toggleBlock'])->name('registration-codes.toggle-block');

    // ===== CLASS ROOMS =====
    Route::resource('class-rooms', App\Http\Controllers\ClassRoomController::class);

    // --- SHIFT ---
    Route::post('/shifts', [App\Http\Controllers\ClassRoomController::class, 'storeShift'])->name('shifts.store');
    Route::delete('/shifts/{id}', [App\Http\Controllers\ClassRoomController::class, 'destroyShift'])->name('shifts.destroy');

    // --- STUDENT IN CLASS ---
    Route::get('/class-rooms/{id}/students', [App\Http\Controllers\ClassRoomController::class, 'getStudents']);

    Route::get('/class-rooms/{id}/search-students', [App\Http\Controllers\ClassRoomController::class, 'searchStudents']);

    Route::post('/class-rooms/{id}/add-student', [App\Http\Controllers\ClassRoomController::class, 'addStudent']);

    Route::delete('/class-rooms/{classId}/students/{studentId}', [App\Http\Controllers\ClassRoomController::class, 'removeStudent']);

    // ===== STUDENTS =====
    Route::resource('students', App\Http\Controllers\StudentController::class);

    // --- SEARCH (AJAX) ---
    Route::get('/api/students-search', [App\Http\Controllers\StudentController::class, 'search'])->name('students.search');

    // --- PRINT CARD ---
    Route::get('/students/{id}/print-card', [App\Http\Controllers\StudentCardController::class, 'generateCard'])->name('students.print-card');

    // ===== LOCATION API =====
    Route::get('/api/provinces/{id}/districts', function ($id) {
        return response()->json(
            App\Models\District::where('province_id', $id)->orderBy('name')->get()
        );
    });

    Route::get('/api/districts/{id}/wards', function ($id) {
        return response()->json(
            App\Models\Ward::where('district_id', $id)->orderBy('name')->get()
        );
    });

    // ===== TUITIONS =====
    Route::get('/tuitions', [App\Http\Controllers\TuitionController::class, 'index'])->name('tuitions.index');
    //===== PAYMENT PROCESSING =====
    Route::post('/tuitions/pay', [App\Http\Controllers\TuitionController::class, 'processPayment']);
    Route::post('/tuitions/extend', [App\Http\Controllers\TuitionController::class, 'processExtend']);

    //===== PROMOTIONS =====
    Route::resource('promotions', \App\Http\Controllers\PromotionController::class)->except(['create', 'edit']);
Route::post('promotions/{id}/toggle', [\App\Http\Controllers\PromotionController::class, 'toggleActive']);
Route::get('/tuitions/receipt/{code}', [App\Http\Controllers\TuitionController::class, 'printReceipt']);
});
