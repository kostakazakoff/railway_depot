<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticlesController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ImagesController;
use App\Http\Controllers\StoresController;


Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('me',  [AuthController::class, 'me']);
});


Route::controller(ArticlesController::class)
    ->prefix('articles')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('/', 'list');
        Route::post('/store', 'store');
        Route::get('/{id}', 'show');
        Route::post('/edit/{id}', 'update');
        Route::post('/delete/{id}', 'delete');
        Route::post('/restore/{id}', 'restoreArticle');
        Route::get('/show-trash', 'getTrashed');
        Route::post('/empty-trash', 'emptyTrash');
        Route::get('/{id}/inventories', 'articleInventories');
    });


Route::controller(StoresController::class)
    ->prefix('stores')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('/', 'list');
        Route::get('/{id}/inventories', 'depotInventories');
    });


Route::controller(ImagesController::class)
    ->prefix('images')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::post('/delete', 'delete');
    });
