<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\AssetController;
use App\Http\Controllers\Api\ProfileController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// --- Public Routes ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/roles', [AuthController::class, 'roles']);
Route::get('/team_members', [AuthController::class, 'team_members']);
Route::get('/customer_contacts', [AuthController::class, 'customer_contacts']);
Route::post('update_projects_un/{project_id}', [ProjectController::class, 'update_projects']);

// --- Protected Routes (Requires Authentication) ---
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);

    // User Profile
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::put('/profile/change-password', [ProfileController::class, 'changePassword']);
    Route::get('/notifications', [ProfileController::class, 'notifications']);
    
    // Projects (CRUD) - ย้ายเข้ามาในนี้เพื่อความปลอดภัย
    Route::post('projects/generate-code', [ProjectController::class, 'generateCode']);
    Route::apiResource('projects', ProjectController::class)->parameters([
        'projects' => 'project:project_id'
    ]);
    Route::post('update_projects/{project_id}', [ProjectController::class, 'update_projects']);

    // Assets (CRUD) - ย้ายเข้ามาในนี้เพื่อความปลอดภัย
    Route::apiResource('assets', AssetController::class);
});
