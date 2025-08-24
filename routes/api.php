<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
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

Route::post('signup', [AuthController::class, 'signup']);
Route::post('verify-otp', [AuthController::class, 'verifyOTP']);
Route::post('signin', [AuthController::class, 'signin'])->name('login');
Route::post('check-phone', [AuthController::class, 'checkPhone']);
Route::post('update-profile', [AuthController::class, 'updateProfile'])->middleware('auth:api');
Route::get('me', [AuthController::class, 'me'])->middleware('auth:api');
Route::get('search', [UserController::class, 'search'])->middleware('auth:api');
Route::get('/user/{userId}', [UserController::class, 'getUserById']);
