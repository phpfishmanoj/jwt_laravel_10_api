<?php

use App\Http\Controllers\UserApiController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'api'], function ($routes) {
    Route::post('/login', [UserApiController::class, 'login']);
    Route::post('/register', [UserApiController::class, 'register']);
    Route::get('/profile', [UserApiController::class, 'profile']);
    Route::post('/logout', [UserApiController::class, 'logout']);
    Route::post('/refresh', [UserApiController::class, 'refresh']);
    Route::post('/update_profile', [UserApiController::class, 'update_profile']);
});
