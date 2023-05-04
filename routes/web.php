<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\ProjectController;
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

// login otp view
Route::middleware(['verified', 'auth'])->prefix('otp')->group(function () {
    Route::get('/two-factor', [OtpController::class, 'loginOtpPage'])->name('otp.two-factor-auth-page');
    Route::post('/send-login-otp', [OtpController::class, 'send'])->name('otp.send');
    Route::post('/verify-login-otp', [OtpController::class, 'verify'])->name('otp.verify');
});

Route::group([
    'middleware' => ['auth:web', 'two.factor.auth']
], function () {
    Route::get('logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
    Route::get('dashboard', [HomeController::class, 'dashboardPage'])->name('home.dashboard');
    Route::post('load-stats', [HomeController::class, 'loadStats'])->name('home.load-stats');

    // admin routes
    Route::middleware(['role:admin|super-admin'])->group(function(){
        Route::get('projects', [ProjectController::class, 'managePage'])->name('project.manage-page');
    });
});
