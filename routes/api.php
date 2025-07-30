<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\LineController;
use App\Http\Controllers\LineWebhookController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/send-line-message', [LineController::class, 'sendMessage']);
Route::post('/send-line-file', [LineController::class, 'sendFileMessage']);
Route::post('/line/webhook', [LineWebhookController::class, 'webhook']);


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
