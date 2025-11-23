<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

//Route::get('/', function () {
//    return view('welcome');
//});
Route::redirect('/', '/login');

// Login routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Admission dashboard (protected)
Route::middleware('auth:admission')->group(function () {
    Route::get('/admission/dashboard', function () {
        return view('admission.dashboard');
    })->name('admission.dashboard');
});

// Applicant dashboard (protected)
Route::middleware('auth:applicant')->group(function () {
    Route::get('/applicant/dashboard', function () {
        return view('applicant.dashboard');
    })->name('applicant.dashboard');
});
