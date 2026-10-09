<?php

use App\Http\Controllers\Api\MemberController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Health check - ALB/Gateway will ping this
    Route::get('/health', function () {
        return response()->json([
            'service' => 'consumer-service',
            'status' => 'OK',
            'time' => now()->toDateTimeString(),
        ]);
    });

    Route::apiResource('members', MemberController::class);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');