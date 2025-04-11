<?php

use App\Http\Controllers\Api\FundController;
use App\Http\Controllers\Api\FundReturnController;
use Illuminate\Support\Facades\Route;




Route::apiResource('funds', FundController::class);

Route::prefix('funds/{fund}')->group(function () {
    Route::get('returns', [FundReturnController::class, 'index']);
    Route::post('returns', [FundReturnController::class, 'store']);
    Route::get('returns/history', [FundController::class, 'returnHistory']);
    Route::get('value-at-date', [FundController::class, 'valueAtDate']);
    Route::get('projection', [FundController::class, 'projection']);
});

Route::prefix('returns/{fundReturn}')->group(function () {
    Route::post('revert', [FundReturnController::class, 'revert']);
});
