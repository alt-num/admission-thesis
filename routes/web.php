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
use App\Http\Controllers\Admission\ApplicantPdfController;
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

    // Active Examinees Monitor
    Route::get('/active-examinees', [\App\Http\Controllers\Admission\ActiveExamineesController::class, 'index'])->name('active-examinees.index');
    Route::get('/active-examinees/fetch', [\App\Http\Controllers\Admission\ActiveExamineesController::class, 'fetch'])->name('active-examinees.fetch');

    // Exam Activity History
    Route::get('/exam-activity-history', [\App\Http\Controllers\Admission\ExamActivityHistoryController::class, 'index'])->name('exam-activity-history.index');
    Route::get('/exam-activity-history/{attempt}', [\App\Http\Controllers\Admission\ExamActivityHistoryController::class, 'show'])->name('exam-activity-history.show');

    // Applicants
    Route::get('/applicants', [ApplicantController::class, 'index'])->name('applicants.index');
    Route::get('/applicants/create', [ApplicantController::class, 'create'])->name('applicants.create');
    Route::post('/applicants', [ApplicantController::class, 'store'])->name('applicants.store');
    Route::get('/applicants/{applicant}', [ApplicantController::class, 'show'])->name('applicants.show');
    Route::get('/applicants/{id}/application-form', [ApplicantPdfController::class, 'generate'])->name('applicants.application_form');
    Route::get('/applicants/{applicant}/declaration', [ApplicantController::class, 'declarationViewing'])->name('applicants.declaration');
    Route::post('/applicants/{applicant}/declaration/remarks', [\App\Http\Controllers\ApplicantDeclarationController::class, 'saveRemarks'])->name('applicants.declaration.remarks');
    Route::post('/applicants/{applicant}/send-credentials', [\App\Http\Controllers\Admission\EmailController::class, 'sendCredentials'])->name('applicants.send-credentials');
    Route::post('/applicants/{applicant}/send-schedule', [\App\Http\Controllers\Admission\EmailController::class, 'sendSchedule'])->name('applicants.send-schedule');
    Route::post('/applicants/{applicant}/reset-credentials', [ApplicantController::class, 'resetCredentials'])->name('applicants.reset-credentials');
    Route::post('/applicants/{applicant}/request-new-photo', [ApplicantController::class, 'requestNewPhoto'])->name('applicants.request-new-photo');
    Route::post('/applicants/{applicant}/return-for-revision', [ApplicantController::class, 'returnForRevision'])->name('applicants.return-for-revision');
    Route::put('/applicants/{applicant}/email', [ApplicantController::class, 'updateEmail'])->name('applicants.updateEmail');

    // Exams
    Route::get('/exams', [ExamController::class, 'index'])->name('exams.index');
    Route::get('/exams/create', [ExamController::class, 'create'])->name('exams.create');
    Route::post('/exams', [ExamController::class, 'store'])->name('exams.store');
    Route::get('/exams/{exam}', [ExamController::class, 'show'])->name('exams.show');
    Route::post('/exams/{exam}/activate', [ExamController::class, 'activate'])->name('exams.activate');
    Route::post('/exams/{exam}/deactivate', [ExamController::class, 'deactivate'])->name('exams.deactivate');
    Route::get('/exams/{exam}/editor', [ExamEditorController::class, 'index'])->name('exams.editor');

    // Exam Schedules
    Route::prefix('exams/{exam}')->group(function () {
        Route::get('/schedules', [\App\Http\Controllers\Admission\ExamScheduleController::class, 'index'])->name('exams.schedules.index');
        Route::post('/schedules', [\App\Http\Controllers\Admission\ExamScheduleController::class, 'store'])->name('exams.schedules.store');
        Route::get('/schedules/{schedule}', [\App\Http\Controllers\Admission\ExamScheduleController::class, 'show'])->name('exams.schedules.show');
        Route::put('/schedules/{schedule}', [\App\Http\Controllers\Admission\ExamScheduleController::class, 'update'])->name('exams.schedules.update');
        Route::delete('/schedules/{schedule}', [\App\Http\Controllers\Admission\ExamScheduleController::class, 'destroy'])->name('exams.schedules.destroy');
        Route::post('/schedules/{schedule}/assign', [\App\Http\Controllers\Admission\ExamScheduleController::class, 'assignApplicants'])->name('exams.schedules.assign');
        Route::delete('/schedules/{schedule}/assigned/{applicant}', [\App\Http\Controllers\Admission\ExamScheduleController::class, 'unassignApplicant'])->name('exams.schedules.unassign');
        Route::post('/schedules/{schedule}/generate-code', [\App\Http\Controllers\Admission\ExamScheduleController::class, 'generateCode'])->name('exams.schedules.generate-code');
    });

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

    // Employees
    Route::prefix('employees')->name('employees.')->group(function () {
        Route::get('/', [\App\Http\Controllers\EmployeeController::class, 'index'])->name('index');
        Route::get('/{employee}', [\App\Http\Controllers\EmployeeController::class, 'show'])->name('show');
        Route::post('/{employee}/create-account', [\App\Http\Controllers\EmployeeController::class, 'createAccount'])->name('create-account');
        Route::post('/{employee}/reset-username', [\App\Http\Controllers\EmployeeController::class, 'resetUsername'])->name('reset-username');
        Route::post('/{employee}/reset-password', [\App\Http\Controllers\EmployeeController::class, 'resetPassword'])->name('reset-password');
        Route::post('/{employee}/toggle-account-status', [\App\Http\Controllers\EmployeeController::class, 'toggleAccountStatus'])->name('toggle-account-status');
    });

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    
    // Anti-Cheat Settings (Admin only)
    Route::get('/settings/anticheat', [\App\Http\Controllers\Admission\AntiCheatSettingsController::class, 'index'])->name('settings.anticheat');
    Route::put('/settings/anticheat', [\App\Http\Controllers\Admission\AntiCheatSettingsController::class, 'update'])->name('settings.anticheat.update');

    // My Account
    Route::get('/my-account', [\App\Http\Controllers\Admission\MyAccountController::class, 'edit'])->name('my-account.edit');
    Route::post('/my-account', [\App\Http\Controllers\Admission\MyAccountController::class, 'update'])->name('my-account.update');
});

// Applicant routes (protected)
Route::middleware('auth:applicant')->prefix('applicant')->name('applicant.')->group(function () {
    // Profile completion routes (NO middleware - must be accessible to complete profile)
    Route::get('/profile', [\App\Http\Controllers\Applicant\ApplicantProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/complete', [\App\Http\Controllers\Applicant\ApplicantProfileController::class, 'edit'])->name('profile.complete');
    Route::post('/profile/complete', [\App\Http\Controllers\Applicant\ApplicantProfileController::class, 'update'])->name('profile.complete.update');
    
    // Profile edit routes (limited fields: username, password, email, mobile)
    Route::get('/profile/edit', [\App\Http\Controllers\Applicant\ApplicantProfileController::class, 'editProfile'])->name('profile.edit');
    Route::post('/profile/update', [\App\Http\Controllers\Applicant\ApplicantProfileController::class, 'updateProfile'])->name('profile.update');

    // Declaration routes (NO middleware - must be accessible to complete declaration)
    Route::get('/declaration', [\App\Http\Controllers\Applicant\ApplicantDeclarationController::class, 'edit'])->name('declaration.edit');
    Route::post('/declaration', [\App\Http\Controllers\Applicant\ApplicantDeclarationController::class, 'update'])->name('declaration.update');

    // Routes that require completed profile and declaration
    Route::middleware('applicant.profile.complete')->group(function () {
        // Dashboard
        Route::get('/dashboard', [\App\Http\Controllers\Applicant\ApplicantDashboardController::class, 'index'])->name('dashboard');
        
        // Schedule
        Route::get('/schedule', [\App\Http\Controllers\Applicant\ApplicantScheduleController::class, 'index'])->name('schedule');
        
        // Exam Access (start only - access logic moved to schedule page)
        Route::post('/exam/check-code', [\App\Http\Controllers\Applicant\ApplicantExamAccessController::class, 'checkCode'])->name('exam.check-code');
        Route::post('/exam/start', [\App\Http\Controllers\Applicant\ApplicantExamAccessController::class, 'start'])->name('exam.start');
        
        // Exam Taking
        Route::get('/exam/take', [\App\Http\Controllers\Applicant\ApplicantExamController::class, 'index'])->name('exam.take');
        Route::post('/exam/answer', [\App\Http\Controllers\Applicant\ApplicantExamController::class, 'saveAnswer'])->name('exam.answer');
        Route::post('/exam/finish', [\App\Http\Controllers\Applicant\ApplicantExamController::class, 'finishExam'])->name('exam.finish');
        
        // Anti-Cheat Logging
        Route::post('/exam/anticheat/log', [\App\Http\Controllers\Applicant\AntiCheatController::class, 'logEvent'])->name('exam.anticheat.log');
        
        // Exam Results
        Route::get('/exam/results', [\App\Http\Controllers\Applicant\ApplicantExamResultController::class, 'index'])->name('exam.results');
    });
});
