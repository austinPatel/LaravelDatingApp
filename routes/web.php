<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SubscriptionPlanController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ResetPasswordController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VerificationController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\ImportLocationsController;
use App\Http\Controllers\LocationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Auth::routes(['verify' => true]);
// Route::middleware('auth')->group(function () {
Route::get('email/verify/{id}', [VerificationController::class, 'verifyEmail'])->name('verification.verify');
Route::post('email/resend', [VerificationController::class, 'resend'])->name('verification.resend');
// });
// Route::post('password/reset', [ResetPasswordController::class, 'resetPassword'])->name('password.update');
// Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetPasswordForm'])->name('password.reset');

Auth::routes(['register' => false]);

// Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::group(
    [
        'prefix' => 'admin',
        'as' => 'admin.',
        'middleware' => ['auth', 'role:super-admin']
    ],
    function () {
        Route::get('/', [AdminController::class, 'index'])->name('dashboard');

        Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
        Route::get('/profileEdit', [AdminController::class, 'profileEdit'])->name('profileEdit');
        Route::post('/profileUpdate', [AdminController::class, 'profileUpdate'])->name('profileUpdate');

        Route::get('/changePassword', [AdminController::class, 'changePassword'])->name('changePassword');
        Route::post('/changePassword', [AdminController::class, 'changePasswordStore'])->name('changePasswordStore');

        Route::resource('users', UserController::class);
        Route::post('users/getAllUsers', [UserController::class, 'getAllUsers'])->name('users.getAllUsers');
        Route::get('reportUsers', [UserController::class, 'reportUsers'])->name('reportUsers');
        Route::post('getReportUsers', [UserController::class, 'getReportUsers'])->name('getReportUsers');
        Route::post('deleteReportUser', [UserController::class, 'deleteReportUser'])->name('deleteReportUser');

        Route::resource('subscriptionPlans', SubscriptionPlanController::class);
        Route::post('subscriptionPlans/getAllSubscriptionPlans', [SubscriptionPlanController::class, 'getAllSubscriptionPlans'])->name('subscriptionPlans.getAllSubscriptionPlans');
        Route::get('/exportUsers', [UserController::class, 'exportUsers'])->name('exportUsers');
    }
);
Route::get('/upload-location', [ImportLocationsController::class, 'index'])->name('upload.location');
Route::post('/import-locations', [ImportLocationsController::class, 'importLocations'])->name('import.locations');
Route::get('/getSuburb',[LocationController::class,'getSuburbs'])->name('location.getSuburb');
