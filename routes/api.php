<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticlesController;
use App\Models\Store;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(ArticlesController::class)->prefix('articles')->group(function () {
    Route::get('/', 'list');
    Route::get('/{id}', 'show');
    Route::post('/store', 'store');
    Route::put('/edit/{id}', 'edit');
    Route::delete('/delete/{id}', 'delete');
    Route::get('/{id}/inventories', 'articleInventories');
});

Route::controller(Store::class)->prefix('stores')->group(function () {
    Route::get('/', 'list');
    Route::get('/{id}', 'show');
    Route::post('/store', 'store');
    Route::put('/edit/{id}', 'edit');
    Route::delete('/delete/{id}', 'delete');
    Route::get('/{id}/inventories', 'storeInventories');
});