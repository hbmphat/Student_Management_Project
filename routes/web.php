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
});
