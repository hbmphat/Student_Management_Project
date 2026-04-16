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
});