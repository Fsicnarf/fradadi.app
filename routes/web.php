<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AppointmentController;
use Illuminate\Support\Facades\Auth;

// Landing page
Route::get('/', function () {
    return view('home');
})->name('home');

// User dashboard (protected)
Route::middleware('auth')->get('/dashboard', function () {
    return view('user.dashboard');
})->name('dashboard');

// Auth routes (username based)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

// Admin routes
Route::middleware('auth')->group(function () {
    Route::get('/admin/pending', [AdminController::class, 'pending'])->name('admin.pending');
    Route::post('/admin/approve/{user}', [AdminController::class, 'approve'])->name('admin.approve');
    Route::get('/admin/history/{user}', [AdminController::class, 'history'])->name('admin.history');
    Route::get('/admin/history/{user}/export', [AdminController::class, 'exportHistory'])->name('admin.history.export');
    Route::get('/admin/history', [AdminController::class, 'historyAll'])->name('admin.history.all');
    Route::get('/admin/history/export/all', [AdminController::class, 'exportHistoryAll'])->name('admin.history.all.export');
    Route::post('/admin/users/{user}/toggle-active', [AdminController::class, 'toggleActive'])->name('admin.users.toggle');
    Route::post('/admin/users/{user}/kill-sessions', [AdminController::class, 'killSessions'])->name('admin.users.killSessions');
    // Appointments (user)
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/events', [AppointmentController::class, 'events'])->name('appointments.events');
    Route::get('/appointments/patient', [AppointmentController::class, 'patientLookup'])->name('appointments.patient');
    Route::get('/appointments/registry', [AppointmentController::class, 'registry'])->name('appointments.registry');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::post('/appointments/{appointment}/update', [AppointmentController::class, 'update'])->name('appointments.update');
    Route::post('/appointments/{appointment}/delete', [AppointmentController::class, 'destroy'])->name('appointments.destroy');
});

