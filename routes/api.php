<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Users\UserBlogController;
use App\Http\Controllers\Admin\AdminBlogController;
use App\Http\Controllers\Users\MessageController;
use App\Http\Controllers\Admin\ActionMessageController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::get('/blogs', [UserBlogController::class, 'userIndex']);
Route::post('/forgot-password', [UserController::class, 'forgotPassword']);
Route::post('/reset-password', [UserController::class, 'resetPassword']);


/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', [UserController::class, 'getUserInfo']);
    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('/blogs/{id}', [UserBlogController::class, 'details']);

    Route::middleware('role:user')->group(function () {
        Route::post('/messages', [MessageController::class, 'create']);
        Route::put('/messages/{id}', [MessageController::class, 'update']);
        Route::delete('/messages/{id}', [MessageController::class, 'delete']);
        Route::get('/messages', [MessageController::class, 'index']);
        Route::get('/messages/{id}', [MessageController::class, 'details']);
    });

    Route::middleware('role:admin')->group(function () {
        Route::post('/admin/blogs', [AdminBlogController::class,'store']);
        Route::put('/admin/blogs/{id}', [AdminBlogController::class,'update']);
        Route::delete('/admin/blogs/{id}', [AdminBlogController::class,'destroy']);
        Route::get('/admin/blogs', [AdminBlogController::class,'index']);
        Route::get('/admin/blogs/{id}', [AdminBlogController::class,'show']);
        Route::get('/admin/messages', [ActionMessageController::class,'listMessages']);
        Route::get('/admin/messages/{id}', [ActionMessageController::class,'details']);
        Route::post('/admin/messages/{id}/review', [ActionMessageController::class,'review']);
        Route::get('/admin/messages/reviewed-approved', [ActionMessageController::class,'reviewedApproved']);
        Route::get('/admin/messages/reviewed-rejected', [ActionMessageController::class,'reviewedRejected']);
    });
});

