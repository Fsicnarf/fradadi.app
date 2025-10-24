<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\MaterialController;
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
    Route::get('/appointments/registry', [AppointmentController::class, 'registry'])->name('appointments.registry');
    Route::get('/appointments/stats', [AppointmentController::class, 'stats'])->name('appointments.stats');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::post('/appointments/{appointment}/update', [AppointmentController::class, 'update'])->name('appointments.update');
    Route::post('/appointments/{appointment}/delete', [AppointmentController::class, 'destroy'])->name('appointments.destroy');

    // Materials inventory
    Route::get('/inventory', [MaterialController::class, 'index'])->name('materials.index');
    Route::post('/inventory', [MaterialController::class, 'store'])->name('materials.store');
    Route::get('/inventory/low-count', [MaterialController::class, 'lowCount'])->name('materials.low');
    Route::get('/inventory/stats', [MaterialController::class, 'stats'])->name('materials.stats');
    Route::put('/inventory/{material}', [MaterialController::class, 'update'])->name('materials.update');
    Route::post('/inventory/{material}/delete', [MaterialController::class, 'destroy'])->name('materials.destroy');

    // Patient history
    Route::get('/patients/{key}/history', [\App\Http\Controllers\PatientHistoryController::class, 'show'])->name('patients.history');
    Route::post('/patients/{key}/history', [\App\Http\Controllers\PatientHistoryController::class, 'store'])->name('patients.history.store');
    Route::post('/patients/{key}/history/{file}/update', [\App\Http\Controllers\PatientHistoryController::class, 'update'])->name('patients.history.update');
    Route::post('/patients/{key}/history/{file}/delete', [\App\Http\Controllers\PatientHistoryController::class, 'destroy'])->name('patients.history.delete');
    Route::post('/patients/{key}/history/{file}/restore', [\App\Http\Controllers\PatientHistoryController::class, 'restore'])->name('patients.history.restore');
    Route::post('/patients/{key}/history/bulk-delete', [\App\Http\Controllers\PatientHistoryController::class, 'bulkDestroy'])->name('patients.history.bulk_delete');

    // Dental record (odontograma)
    Route::get('/patients/{key}/dental-record', [\App\Http\Controllers\DentalRecordController::class, 'show'])
        ->name('patients.dental.show');
    Route::post('/patients/{key}/dental-record', [\App\Http\Controllers\DentalRecordController::class, 'save'])
        ->name('patients.dental.save');

    // Bot knowledge (admin)
    Route::get('/admin/bot/knowledge', [\App\Http\Controllers\BotKnowledgeController::class, 'index'])->name('admin.bot.knowledge');
    Route::post('/admin/bot/knowledge', [\App\Http\Controllers\BotKnowledgeController::class, 'store'])->name('admin.bot.knowledge.store');
    Route::post('/admin/bot/knowledge/{doc}/delete', [\App\Http\Controllers\BotKnowledgeController::class, 'destroy'])->name('admin.bot.knowledge.delete');
    // Bot search endpoint for widget
    Route::get('/bot/search', [\App\Http\Controllers\BotKnowledgeController::class, 'search'])->name('bot.search');
});

