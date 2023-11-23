<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticlesController;
use App\Http\Controllers\ImagesController;
use App\Http\Controllers\StoresController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(ArticlesController::class)->prefix('articles')->group(function () {
    Route::get('/', 'list');
    Route::post('/store', 'store');
    Route::get('/{id}', 'show');
    Route::post('/edit/{id}', 'update');
    Route::delete('/delete/{id}', 'delete');
    Route::get('/deleted-articles', 'showDeleted');
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