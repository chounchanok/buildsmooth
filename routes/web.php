<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PageController;

// --- Controllers for New Menu ---
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\MyWorkController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ClientReportController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('dark-mode-switcher', [DarkModeController::class, 'switch'])->name('dark-mode-switcher');
Route::get('color-scheme-switcher/{color_scheme}', [ColorSchemeController::class, 'switch'])->name('color-scheme-switcher');

Route::controller(AuthController::class)->middleware('loggedin')->group(function() {
    Route::get('login', 'loginView')->name('login.index');
    Route::post('login', 'login')->name('login.check');
});

Route::middleware('auth')->group(function() {
    Route::get('logout', [AuthController::class, 'logout'])->name('logout');
    Route::controller(PageController::class)->group(function() {
        // --- Dashboard ---
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // --- Management (Admin & PM) ---
        Route::prefix('management')->group(function () {
            Route::resource('projects', ProjectController::class); // สร้าง route สำหรับ projects.index, .create, .store, .show, .edit, .update, .destroy
            Route::resource('teams', TeamController::class);
            Route::resource('assets', AssetController::class);
        });

        // --- User Management (Super Admin only) ---
        Route::prefix('users-management')->group(function () {
            Route::resource('users', UserController::class);
            Route::resource('roles', RoleController::class);
        });

        // --- My Work (Staff only) ---
        Route::prefix('my-work')->group(function () {
            Route::get('projects', [MyWorkController::class, 'projects'])->name('my-projects.index');
            Route::get('timesheets', [MyWorkController::class, 'timesheets'])->name('my-timesheets.index');
        });

        // --- Reports ---
        Route::prefix('reports')->group(function () {
            Route::get('timesheet', [ReportController::class, 'timesheet'])->name('reports.timesheet');
            Route::get('client', [ClientReportController::class, 'index'])->name('reports.client');
        });
    });
});
