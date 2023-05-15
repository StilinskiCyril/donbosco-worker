<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DonorsController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MpesaController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\PledgeController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SubGroupController;
use App\Http\Controllers\TreasurerController;
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

Route::get('/', [HomeController::class, 'landingPage'])->name('home.landing-page');
Route::get('donate', [HomeController::class, 'donatePage'])->name('home.donate-page');
Route::post('donate-now', [HomeController::class, 'donateNow'])->name('home.donate-now');

Auth::routes();

// pledge
Route::post('create-pledge', [PledgeController::class, 'create'])->name('pledge.create');

// login otp view
Route::middleware(['verified', 'auth'])->prefix('otp')->group(function () {
    Route::get('/two-factor', [OtpController::class, 'loginOtpPage'])->name('otp.two-factor-auth-page');
    Route::post('/send-login-otp', [OtpController::class, 'send'])->name('otp.send');
    Route::post('/verify-login-otp', [OtpController::class, 'verify'])->name('otp.verify');
});

Route::group([
    'middleware' => ['auth:web', 'two.factor.auth']
], function () {
    Route::get('logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('dashboard', [HomeController::class, 'dashboardPage'])->name('home.dashboard');
    Route::post('load-stats', [HomeController::class, 'loadStats'])->name('home.load-stats');

    Route::middleware(['role:admin|super-admin|treasurer'])->group(function(){
        /**
         * Manage Donors
         */
        Route::get('donors', [DonorsController::class, 'managePage'])->name('donor.manage-page');
        Route::post('load-donors', [DonorsController::class, 'load'])->name('donor.load');
    });

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

        /**
         * Manage Treasurers
         */
        Route::get('treasurers', [TreasurerController::class, 'managePage'])->name('treasurer.manage-page');
        Route::post('create-treasurer/{account}', [TreasurerController::class, 'create'])->name('treasurer.create');
        Route::post('load-treasurers', [TreasurerController::class, 'load'])->name('treasurer.load');
        Route::post('update-treasurer/{treasurer}/{user}/{account}', [TreasurerController::class, 'update'])->name('treasurer.update');

        /**
         * Manage Groups
         */
        Route::get('groups', [GroupController::class, 'managePage'])->name('group.manage-page');
        Route::post('create-group', [GroupController::class, 'create'])->name('group.create');
        Route::post('load-groups', [GroupController::class, 'load'])->name('group.load');
        Route::post('update-group/{group}', [GroupController::class, 'update'])->name('group.update');

        /**
         * Manage Sub-Groups
         */
        Route::get('sub-groups', [SubGroupController::class, 'managePage'])->name('sub-group.manage-page');
        Route::post('create-sub-group/{group}', [SubGroupController::class, 'create'])->name('sub-group.create');
        Route::post('load-sub-groups', [SubGroupController::class, 'load'])->name('sub-group.load');
        Route::post('update-sub-group/{group}/{subGroup}', [SubGroupController::class, 'update'])->name('sub-group.update');

        /**
         * Manage Expenses
         */
        Route::get('expenses', [ExpenseController::class, 'managePage'])->name('expense.manage-page');
        Route::post('create-expense', [ExpenseController::class, 'create'])->name('expense.create');
        Route::post('load-expenses', [ExpenseController::class, 'load'])->name('expense.load');
        Route::post('update-expense/{expense}', [ExpenseController::class, 'update'])->name('expense.update');

        /**
         * Manage M-pesa
         */
        Route::get('manage-mpesa', [MpesaController::class, 'managePage'])->name('mpesa.manage-page');
        Route::post('load-unknown-donations', [MpesaController::class, 'loadUnknownDonations'])->name('mpesa.load-unknown-donations');
        Route::post('upload-mpesa-statement', [MpesaController::class, 'uploadMpesaStatement'])->name('mpesa.upload-mpesa-statement');

        /**
         * Manage SMS's
         */
        Route::get('send-sms', [HomeController::class, 'sendSmsPage'])->name('home.send-sms-page');
        Route::post('send-sms', [HomeController::class, 'sendSms'])->name('home.send-sms');

        /**
         * Manage Reports
         */
        Route::get('account-donations-report', [ReportController::class, 'accountDonationsManagePage'])->name('report.account-donations-manage-page');
        Route::post('generate-account-donations-report/{project}', [ReportController::class, 'accountDonationsReport'])->name('report.account-donations-report');
        Route::get('project-donations-report', [ReportController::class, 'projectDonationsManagePage'])->name('report.project-donations-manage-page');
        Route::post('generate-project-donations-report', [ReportController::class, 'projectDonationsReport'])->name('report.project-donations-report');
        Route::get('all-donations-report', [ReportController::class, 'allDonationsManagePage'])->name('report.all-donations-manage-page');
        Route::post('generate-all-donations-report', [ReportController::class, 'allDonationsReport'])->name('report.all-donations-report');
        Route::get('fund-distribution-report', [ReportController::class, 'fundDistributionManagePage'])->name('report.fund-distribution-manage-page');
        Route::post('generate-fund-distribution-report', [ReportController::class, 'fundDistributionReport'])->name('report.fund-distribution-report');
        Route::get('pledge-donations-report', [ReportController::class, 'pledgeDonationsManagePage'])->name('report.pledge-donations-manage-page');
        Route::post('generate-pledge-donations-report', [ReportController::class, 'pledgeDonationsReport'])->name('report.pledge-donations-report');
        Route::get('pledge-donations/{pledge}', [ReportController::class, 'pledgeDonations'])->name('report.pledge-donations');
    });
});
