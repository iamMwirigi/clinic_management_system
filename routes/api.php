<?php

use App\Http\Controllers\Api\SuperAdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Universal Authentication Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/refresh', [AuthController::class, 'refresh']);

Route::middleware('auth:super_admin,admin,doctor,attendant')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('super-admins', SuperAdminController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::apiResource('admins', AdminController::class)->only(['index', 'show', 'update', 'destroy', 'store']);
    Route::apiResource('hospitals', \App\Http\Controllers\Api\HospitalController::class);
    Route::apiResource('doctors', \App\Http\Controllers\Api\DoctorController::class);
    Route::apiResource('attendants', \App\Http\Controllers\AttendantController::class);
    Route::apiResource('specialties', \App\Http\Controllers\SpecialtyController::class);
    Route::apiResource('genders', \App\Http\Controllers\GenderController::class);
});
