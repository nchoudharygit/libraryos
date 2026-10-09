<?php

use Illuminate\Http\Request;


use App\Http\Controllers\Api\TransactionController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/health', function () {
        return response()->json([
            'service' => 'transaction-service',
            'status' => 'OK',
            'time' => now()->toDateTimeString(),
        ]);
    });

    Route::apiResource('transactions', TransactionController::class);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
