<?php

use App\Http\Controllers\AccountController;
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
        /**
         * Manage Projects
         */
        Route::get('projects', [ProjectController::class, 'managePage'])->name('project.manage-page');
        Route::post('create-project', [ProjectController::class, 'create'])->name('project.create');
        Route::post('load-projects', [ProjectController::class, 'load'])->name('project.load');
        Route::post('update-project/{project}', [ProjectController::class, 'update'])->name('project.update');

        /**
         * Manage Accounts
         */
        Route::get('accounts', [AccountController::class, 'managePage'])->name('account.manage-page');
        Route::post('create-account/{project}', [AccountController::class, 'create'])->name('account.create');
        Route::post('load-accounts', [AccountController::class, 'load'])->name('account.load');
        Route::post('update-account/{account}/{project}', [AccountController::class, 'update'])->name('account.update');
        Route::post('create-account-existing/{account}', [AccountController::class, 'createExisting'])->name('account.create-existing');
    });
});
