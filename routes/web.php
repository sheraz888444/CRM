<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AdmissionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CallLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PhoneSearchController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/about', [PublicController::class, 'about'])->name('about');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Authenticated Staff & Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Phone Search (Main Display §2)
    Route::get('/search', [PhoneSearchController::class, 'index'])->name('search.index');
    Route::get('/search/phone', [PhoneSearchController::class, 'searchByPhone'])->name('search.phone');

    // AJAX Endpoints
    Route::get('/students/check-duplicate', [StudentController::class, 'checkDuplicate'])->name('students.checkDuplicate');
    Route::get('/students/live-search', [StudentController::class, 'liveSearch'])->name('students.liveSearch');

    // Student / Inquiry CRUD
    Route::resource('students', StudentController::class);

    // Call Logs & Follow-ups
    Route::get('/calls', [CallLogController::class, 'index'])->name('calls.index');
    Route::post('/calls', [CallLogController::class, 'store'])->name('calls.store');
    Route::delete('/calls/{callLog}', [CallLogController::class, 'destroy'])->name('calls.destroy');
    Route::get('/follow-ups', [CallLogController::class, 'followUps'])->name('calls.followUps');

    // Admissions
    Route::get('/admissions', [AdmissionController::class, 'index'])->name('admissions.index');
    Route::get('/admissions/create/{student?}', [AdmissionController::class, 'create'])->name('admissions.create');
    Route::post('/admissions', [AdmissionController::class, 'store'])->name('admissions.store');
    Route::get('/admissions/{admission}', [AdmissionController::class, 'show'])->name('admissions.show');
    Route::get('/admissions/{admission}/edit', [AdmissionController::class, 'edit'])->name('admissions.edit');
    Route::put('/admissions/{admission}', [AdmissionController::class, 'update'])->name('admissions.update');
    Route::delete('/admissions/{admission}', [AdmissionController::class, 'destroy'])->name('admissions.destroy');

    // Payments
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/{payment}/slip', [PaymentController::class, 'slip'])->name('payments.slip');

    // Daily Tasks
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::post('/tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');
    Route::post('/tasks/{task}/reschedule', [TaskController::class, 'reschedule'])->name('tasks.reschedule');

    // User Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| Admin-Only Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Branch Management
    Route::resource('branches', BranchController::class);

    // Staff / User Management
    Route::resource('users', UserController::class);
    Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggleStatus');
    Route::patch('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.resetPassword');

    // Course & Mode Masters
    Route::resource('courses', CourseController::class);

    // Reports & Exports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/csv', [ReportController::class, 'exportCsv'])->name('reports.csv');
    Route::get('/reports/print', [ReportController::class, 'print'])->name('reports.print');
    Route::get('/reports/staff/{user}', [ReportController::class, 'staffReport'])->name('reports.staff');
    Route::get('/reports/staff/{user}/print', [ReportController::class, 'printStaffReport'])->name('reports.staff.print');
    Route::get('/reports/staff/{user}/csv', [ReportController::class, 'exportStaffCsv'])->name('reports.staff.csv');

    // System Activity Logs
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activityLogs.index');

    // Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
});
