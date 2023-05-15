<?php

use App\Http\Controllers\MpesaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1/c2b')->group(function () {
    //validation url
    Route::post('validation', [MpesaController::class, 'validation'])->name('mpesa.validation');
    //c2b callback url
    Route::post('689c-4ff2-a5dd-3650/confirmation', [MpesaController::class, 'confirmationUrl'])->name('mpesa.confirmation-url');
    //initialize stk push
    Route::post('stk/push', [MpesaController::class, 'stKPush'])->name('mpesa.stk-push');
    //stk callback url
    Route::post('stk/transaction/callback', [MpesaController::class, 'stkCallback'])->name('mpesa.stk-callback');
    //transaction check callback url
    Route::post('transaction-check/callback', [MpesaController::class, 'transactionCheck'])->name('mpesa.transaction-check');
    //transaction check timeout url
    Route::post('transaction-check/timeout', [MpesaController::class, 'transactionCheckTimeout'])->name('mpesa.transaction-check-timeout');
});
