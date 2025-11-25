<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdmissionDashboardController;
use App\Http\Controllers\ApplicantController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\Admission\ExamEditorController;
use App\Http\Controllers\Admission\ExamEditor\SectionController;
use App\Http\Controllers\Admission\ExamEditor\SubsectionController;
use App\Http\Controllers\Admission\ExamEditor\QuestionController;
use App\Http\Controllers\Admission\ExamEditor\ChoiceController;
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
    Route::post('/exams/{exam}/activate', [ExamController::class, 'activate'])->name('exams.activate');
    Route::post('/exams/{exam}/deactivate', [ExamController::class, 'deactivate'])->name('exams.deactivate');
    Route::get('/exams/{exam}/editor', [ExamEditorController::class, 'index'])->name('exams.editor');

    // Exam Editor API Routes
    Route::prefix('exams/{exam}')->group(function () {
        // Sections
        Route::post('/sections/create', [SectionController::class, 'store'])->name('exams.sections.store');
        Route::post('/sections/{section}/update', [SectionController::class, 'update'])->name('exams.sections.update');
        Route::delete('/sections/{section}/delete', [SectionController::class, 'destroy'])->name('exams.sections.destroy');
        Route::post('/sections/reorder', [SectionController::class, 'reorder'])->name('exams.sections.reorder');

        // Subsections
        Route::post('/sections/{section}/subsections/create', [SubsectionController::class, 'store'])->name('exams.subsections.store');
        Route::post('/subsections/{subsection}/update', [SubsectionController::class, 'update'])->name('exams.subsections.update');
        Route::delete('/subsections/{subsection}/delete', [SubsectionController::class, 'destroy'])->name('exams.subsections.destroy');
        Route::post('/subsections/reorder', [SubsectionController::class, 'reorder'])->name('exams.subsections.reorder');

        // Questions
        Route::post('/sections/{section}/questions/create', [QuestionController::class, 'storeToSection'])->name('exams.questions.storeToSection');
        Route::post('/subsections/{subsection}/questions/create', [QuestionController::class, 'store'])->name('exams.questions.store');
        Route::post('/questions/{question}/update', [QuestionController::class, 'update'])->name('exams.questions.update');
        Route::delete('/questions/{question}/delete', [QuestionController::class, 'destroy'])->name('exams.questions.destroy');
        Route::post('/questions/reorder', [QuestionController::class, 'reorder'])->name('exams.questions.reorder');

        // Choices
        Route::post('/questions/{question}/choices', [ChoiceController::class, 'store'])->name('exams.choices.store');
        Route::post('/choices/{choice}/update', [ChoiceController::class, 'update'])->name('exams.choices.update');
        Route::delete('/choices/{choice}/delete', [ChoiceController::class, 'destroy'])->name('exams.choices.destroy');
        Route::post('/questions/{question}/toggle-true-false', [ChoiceController::class, 'toggleTrueFalse'])->name('exams.choices.toggleTrueFalse');
    });

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
