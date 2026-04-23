<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Auth::routes();

Route::get('/teachers', [App\Http\Controllers\HomeController::class, 'index'])->name('teachers.index');

Route::middleware('auth')->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/courses/export-pdf', [App\Http\Controllers\CourseController::class, 'exportPdf'])->name('courses.export-pdf');
    Route::resource('courses', App\Http\Controllers\CourseController::class);
    Route::get('/teachers/export-pdf', [App\Http\Controllers\TeacherController::class, 'exportPdf'])->name('teachers.export-pdf');
    Route::resource('teachers', App\Http\Controllers\TeacherController::class);
    Route::resource('registration-codes', App\Http\Controllers\RegistrationCodeController::class)->only(['index', 'store', 'destroy']);
    Route::resource('class-rooms', App\Http\Controllers\ClassRoomController::class);
    Route::post('/shifts', [App\Http\Controllers\ClassRoomController::class, 'storeShift'])->name('shifts.store');
    Route::delete('/shifts/{id}', [App\Http\Controllers\ClassRoomController::class, 'destroyShift'])->name('shifts.destroy');
    Route::post('/class-rooms/{id}/add-student', [App\Http\Controllers\ClassRoomController::class, 'addStudent'])->name('class-rooms.add-student');
    Route::post('/class-rooms/{id}/remove-student', [App\Http\Controllers\ClassRoomController::class, 'removeStudent'])->name('class-rooms.remove-student');
    Route::get('/api/students-search', [App\Http\Controllers\ClassRoomController::class, 'searchStudents']);

    Route::resource('class-rooms', App\Http\Controllers\ClassRoomController::class);

    // Route API tìm kiếm realtime cho DataTable
    Route::get('/api/students-search', [App\Http\Controllers\StudentController::class, 'search'])->name('students.search');

    // Nút In Thẻ PDF
    Route::get('/students/{id}/print-card', [App\Http\Controllers\StudentCardController::class, 'generateCard'])->name('students.print-card');

    // Quản lý học viên chuẩn CRUD
    Route::resource('students', App\Http\Controllers\StudentController::class);

    // API lấy danh sách Quận/Huyện và Phường/Xã
    Route::get('/api/provinces/{id}/districts', function ($id) {
        return response()->json(App\Models\District::where('province_id', $id)->orderBy('name')->get());
    });

    Route::get('/api/districts/{id}/wards', function ($id) {
        return response()->json(App\Models\Ward::where('district_id', $id)->orderBy('name')->get());
    });

    // Cụm Route API xử lý Học viên trong Lớp học
    Route::get('/class-rooms/{id}/students', [App\Http\Controllers\ClassRoomController::class, 'getStudents']);
    Route::get('/class-rooms/{id}/search-students', [App\Http\Controllers\ClassRoomController::class, 'searchStudents']);
    Route::post('/class-rooms/{id}/add-student', [App\Http\Controllers\ClassRoomController::class, 'addStudent']);
    Route::delete('/class-rooms/{classId}/students/{studentId}', [App\Http\Controllers\ClassRoomController::class, 'removeStudent']);
});
