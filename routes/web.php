<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [App\Http\Controllers\HomeController::class, 'landingPage'])->name('home.landing');

Auth::routes(['register' => false]);

Route::group([
    'middleware' => ['auth:web']
], function () {
    Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'dashboardPage'])->name('home.dashboard');

    // admin routes
    Route::middleware(['role:admin'])->prefix('admin')->group(function(){
        // routes here
    });
});
