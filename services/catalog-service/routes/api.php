<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthorController;  
use App\Http\Controllers\Api\BookController;

Route::prefix('v1')->group(function () {
    // Health check - ALB will ping this
    Route::get('/health', function () {
        return response()->json([
            'service' => 'catalog-service',
            'status' => 'OK',
            'time' => now()->toDateTimeString()
            ]);
    });

    // Author and Book API routes
    Route::apiResource('authors', \App\Http\Controllers\Api\AuthorController::class);
    Route::apiResource('books', \App\Http\Controllers\Api\BookController::class);

});


