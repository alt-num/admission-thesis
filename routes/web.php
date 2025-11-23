<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdmissionDashboardController;
use App\Http\Controllers\ApplicantController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\SettingsController;

Route::redirect('/', '/login');

// Login routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Admission routes (protected)
Route::middleware('auth:admission')->prefix('admission')->name('admission.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdmissionDashboardController::class, 'index'])->name('dashboard');

    // Applicants
    Route::get('/applicants', [ApplicantController::class, 'index'])->name('applicants.index');
    Route::get('/applicants/create', [ApplicantController::class, 'create'])->name('applicants.create');
    Route::post('/applicants', [ApplicantController::class, 'store'])->name('applicants.store');
    Route::get('/applicants/{applicant}', [ApplicantController::class, 'show'])->name('applicants.show');
    Route::get('/applicants/{applicant}/edit', [ApplicantController::class, 'edit'])->name('applicants.edit');
    Route::put('/applicants/{applicant}', [ApplicantController::class, 'update'])->name('applicants.update');
    Route::get('/applicants/{applicant}/declaration', [ApplicantController::class, 'declarationViewing'])->name('applicants.declaration');
    Route::post('/applicants/{applicant}/declaration/remarks', [\App\Http\Controllers\ApplicantDeclarationController::class, 'saveRemarks'])->name('applicants.declaration.remarks');

    // Exams
    Route::get('/exams', [ExamController::class, 'index'])->name('exams.index');
    Route::get('/exams/create', [ExamController::class, 'create'])->name('exams.create');
    Route::post('/exams', [ExamController::class, 'store'])->name('exams.store');
    Route::get('/exams/{exam}', [ExamController::class, 'show'])->name('exams.show');
    Route::get('/exams/{exam}/editor', [ExamController::class, 'editor'])->name('exams.editor');

    // Master Data
    Route::get('/courses', [MasterDataController::class, 'courses'])->name('courses.index');
    Route::get('/campuses', [MasterDataController::class, 'campuses'])->name('campuses.index');
    Route::get('/departments', [MasterDataController::class, 'departments'])->name('departments.index');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
});

// Applicant dashboard (protected)
Route::middleware('auth:applicant')->group(function () {
    Route::get('/applicant/dashboard', function () {
        return view('applicant.dashboard');
    })->name('applicant.dashboard');
});
