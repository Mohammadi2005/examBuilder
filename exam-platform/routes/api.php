<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


Route::get('/test', function (Request $request) {
    return 'test';
});

Route::prefix('/')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('admin/login', [AuthController::class, 'adminLogin']);
    Route::post('supplier/login', [AuthController::class, 'supplierLogin']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
    Route::get('/test', [AuthController::class, 'test']);
});



Route::middleware('auth:api')->group(function () {
    Route::get('getUser', [AuthController::class, 'getUser']);
});
