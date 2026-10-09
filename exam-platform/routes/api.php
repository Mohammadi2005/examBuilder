<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\CheckRole;

// controllers
use App\Http\Controllers\API\ExamController;
use App\Http\Controllers\API\QuestionController;
use App\Http\Controllers\API\StudentController;
use App\Http\Controllers\API\TeacherController;


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

    // api for admin
    Route::prefix('/admin')->middleware(CheckRole::class.':admin')->group(function () {

        // Route::post('upload-image', [UploadImageCKController::class, 'uploadImage'])->name('.upload-image');

        Route::prefix('/user')->name('user.')->group(function () {
            Route::get('/index', [UserController::class, 'index']);
            Route::post('change-role', [UserController::class, 'changeRole']);
            Route::post('change', [UserController::class, 'changeStatus']);
        });

        Route::prefix('/teacher')->name('teacher.')->group(function () {
            Route::post('/store', [TeacherController::class, 'store'])->name('.store');
            Route::post('/update', [TeacherController::class, 'update'])->name('.update');
            Route::get('/indexPanel', [TeacherController::class, 'indexPanel']);
            Route::get('/showPanel', [TeacherController::class, 'showPanel'])->name('.showPanel');
        });

        Route::prefix('/exam')->name('exam.')->group(function () {
            Route::get('/index', [ExamController::class, 'index'])->name('.index');
        });
    });

    Route::prefix('/teacher')->middleware(CheckRole::class.':teacher,admin')->group(function () {

        Route::prefix('/')->name('.')->group(function () {
            Route::post('/store', [TeacherController::class, 'store'])->name('.store');
            Route::post('/update', [TeacherController::class, 'update'])->name('.update');
        });

        Route::prefix('/exam')->name('exam.')->group(function () {
            Route::post('/store', [ExamController::class, 'store'])->name('.store');
            Route::post('/update', [ExamController::class, 'update'])->name('.update');
            Route::get('/show', [ExamController::class, 'show'])->name('.show');
        });

        Route::prefix('/question')->name('question.')->group(function () {
            Route::post('/store', [QuestionController::class, 'store'])->name('.store');
            // Route::post('/update', [TeacherController::class, 'update'])->name('.update');
        });
    });

    Route::prefix('/student')->middleware(CheckRole::class.':student,admin')->group(function () {

        Route::prefix('/')->name('.')->group(function () {
            Route::post('/store', [StudentController::class, 'store'])->name('.store');
            Route::post('/update', [StudentController::class, 'update'])->name('.update');
        });
    });
});

