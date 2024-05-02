<?php

use App\Http\Controllers\Api\SubscriptionPlanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ImageController;
use App\Http\Controllers\Api\MatchController;
use App\Http\Controllers\Api\AnswerController;
use App\Http\Controllers\Api\ConnectionController;
use App\Http\Controllers\Api\ForgotController;
use App\Http\Controllers\Api\SignInController;
use App\Http\Controllers\Api\SignUpController;
use App\Http\Controllers\Api\TwilioController;
use App\Http\Controllers\Api\InterestController;
use App\Http\Controllers\api\LocationController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\PreferenceController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\UserSubscriptionController;
use App\Http\Controllers\Api\VerificationController;
use Twilio\Rest\Verify\V2\Service\VerificationContext;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('user/sign-in', [SignInController::class, 'login']);
// Route::post('user/sign-in', [SignInController::class, 'login'])->middleware(['verified']);
Route::post('user/sign-up', [SignUpController::class, 'register']);
Route::post('user/forgot-password', [UserController::class, 'forgot']);

Route::middleware('auth:api')->group(function () {
    //Question
    Route::post('questions', [QuestionController::class, 'index']);
    Route::post('save-answers', [QuestionController::class, 'saveAnswers']);
    Route::get('fetch-user-answers', [AnswerController::class, 'fetchUserAnswers']);

    //Interest
    Route::post('interests', [InterestController::class, 'index']);
    Route::post('save-user-interest', [InterestController::class, 'saveUserInterest']);

    //User preference
    Route::get('user-preference', [PreferenceController::class, 'index']);
    Route::post('save-cliQ-mode', [PreferenceController::class, 'saveCliQMode']);
    Route::post('save-user-preference', [PreferenceController::class, 'saveUserPreference']);

    //recommendations
    Route::get('view-recommendations', [MatchController::class, 'index'])->middleware('check_lat_long');

    //location
    Route::get('location', [PreferenceController::class, 'location']);
    Route::post('save-location', [PreferenceController::class, 'saveLocation']);

    //user update
    Route::post('update-user', [UserController::class, 'updateUser']);
    Route::get('get-user', [UserController::class, 'getUser']);
    //user connect request
    Route::post('connect-request', [ConnectionController::class, 'connectRequest']);

    // all connections
    Route::get('connections', [ConnectionController::class, 'connections']);

    //disconnect user
    Route::post('disconnect-user', [ConnectionController::class, 'disconnectUser']);

    //add-favorite connection
    Route::post('add-favorite', [ConnectionController::class, 'addFavorite']);

    //not interested user
    Route::post('not-interested-user', [MatchController::class, 'notInterested']);

    //report user
    Route::post('report-user', [ReportController::class, 'reportUser']);

    //update user FCM device token
    Route::post('update-device-token', [UserController::class, 'updateUserDeviceToken']);

    //user connect request
    Route::post('declined-request', [ConnectionController::class, 'declinedRequest']);

    //Subscription Plans
    Route::get('subscription-plans', [SubscriptionPlanController::class, 'index']);
    Route::post('user-subscription', [UserSubscriptionController::class, 'saveUserSubscription']);
    Route::get('user-subscription', [UserSubscriptionController::class, 'getUserSubscriptionDetails']);
    Route::get('getStates',[LocationController::class,'getStates']);
    Route::get('getSuburbs',[LocationController::class,'getSuburbs']);
    Route::post('save-user-location',[LocationController::class,'saveUserLocation']);


});

Route::group(
    [
        'prefix' => 'mobile',
        'as' => 'mobile.',
        'middleware' => ['auth:api']
    ],
    function () {
        Route::post('/sendOTP', [UserController::class, 'sendCode']);
        Route::post('/verify', [UserController::class, 'verifyCode']);
        // Route::post('/sendCode', [UserController::class, 'sendCode']);
        // Route::post('/verifyCode', [UserController::class, 'verifyCode']);
        Route::post('/reSendOTP', [UserController::class, 'reSendCode']);
    }
);
Route::group(
    [
        'prefix' => 'user',
        'as' => 'profile.',
        'middleware' => ['auth:api']
    ],
    function () {
        Route::get('profile', [UserController::class, 'show'])->name('show');
        Route::post('profile/avatar', [ImageController::class, 'create'])->name('create');
        Route::get('profile/avatar', [ImageController::class, 'show'])->name('index');
        Route::post('profile/photos', [ImageController::class, 'store'])->name('store');
        Route::get('profile/photos', [ImageController::class, 'index'])->name('index');
        // Route::post('profile/delete-photo', [ImageController::class, 'deletePhoto'])->name('deletePhoto');
        Route::delete('profile/photos', [ImageController::class, 'delete'])->name('delete');
        Route::post('profile/set-profile-picture', [ImageController::class, 'setProfilePicture'])->name('setprofilepicture');
    }
);
