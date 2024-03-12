<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticlesController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImagesController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\StoresController;
use App\Http\Controllers\UsersResponsibilitiesController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::get('stores/list', [StoresController::class, 'list']);
Route::post('logs/delete_old', [LogController::class, 'deleteOldLogs']);


Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('delete_me',  [AuthController::class, 'delete_me']);
    Route::post('edit_my_profile', [AuthController::class, 'edit_my_profile']);
});


Route::controller(UsersResponsibilitiesController::class)
->prefix('users/responsibilities/')
->middleware('auth:sanctum', 'check.admin')
->group(function () {
    Route::post('attach/{user_id}', 'attachResponsibilities');
    Route::post('detach/{user_id}', 'detachResponsibilities');
    Route::get('show/{user_id}', 'showResponsibilities');
});


Route::controller(StoresController::class)
    ->prefix('stores')
    ->middleware('auth:sanctum', 'check.superuser')
    ->group(function () {
        Route::post('create', 'create');
        Route::post('edit{id}', 'edit');
        Route::post('delete/{id}', 'delete');
    });


Route::controller(DashboardController::class)
    ->prefix('dashboard')
    ->middleware('auth:sanctum', 'check.admin')
    ->group(function () {
        Route::get('users_list', 'users_list');
        Route::post('edit_user/{id}', 'edit_user');
        Route::post('delete_user/{id}', 'delete_user');
    });


Route::controller(ArticlesController::class)
    ->prefix('articles')
    ->middleware('auth:sanctum', 'check.role')
    ->group(function () {
        Route::get('/', 'list');
        Route::post('/store', 'store');
        Route::get('/{id}', 'show');
        Route::post('/edit/{id}', 'update');
        Route::post('/delete/{id}', 'delete');
        Route::get('/{id}/inventories', 'articleInventories');
    });


Route::controller(ImagesController::class)
    ->prefix('images')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::post('/delete', 'delete');
    });


Route::controller(LogController::class)
    ->prefix('logs')
    ->middleware('auth:sanctum', 'check.admin')
    ->group(function () {
        Route::get('/list', 'list');
    });
