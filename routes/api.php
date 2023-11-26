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


Route::controller(ArticlesController::class)->prefix('articles')->group(function () {
    Route::get('/', 'list');
    Route::post('/store', 'store');
    Route::get('/{id}', 'show');
    Route::post('/edit/{id}', 'update');
    Route::delete('/delete/{id}', 'delete');
    Route::get('/trashed', 'getTrashed');
    Route::post('/empty-trash', 'pruneModel');
    Route::get('/{id}/inventories', 'articleInventories');
});


Route::controller(StoresController::class)->prefix('stores')->group(function () {
    Route::get('/', 'list');
    Route::get('/{id}', 'show');
    Route::get('/{id}/value', 'depotInventoriesValue');
    // Route::post('/store', 'store');
    // Route::put('/edit/{id}', 'edit');
    // Route::delete('/delete/{id}', 'delete');
    Route::get('/{id}/inventories', 'depotInventories');
});


Route::delete('images/delete', [ImagesController::class, 'delete']);