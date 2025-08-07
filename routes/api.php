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
|
| API routes for the Flutter application.
|
*/

// --- Public Routes ---
// เส้นทางสำหรับ Register และ Login ไม่ต้องผ่านการยืนยันตัวตน
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/roles', [AuthController::class, 'roles']);

// --- Protected Routes (Requires Authentication) ---
// เส้นทางทั้งหมดในกลุ่มนี้ต้องใช้ Token ที่ได้จากการ Login
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);

    // User Profile
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::put('/profile/change-password', [ProfileController::class, 'changePassword']);
    Route::get('/notifications', [ProfileController::class, 'notifications']);

    // Projects (CRUD)
    Route::apiResource('projects', ProjectController::class);

    // Assets (CRUD)
    Route::apiResource('assets', AssetController::class);
});
