<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\UserProfilePopController;
use App\Http\Controllers\UserProfileFollowersController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\RewardController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\WalletCounterController;

use App\Models\UserStatus;
use App\Models\PostType;
use App\Models\RewardType;

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

// get start auth login
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    // return $request->user();
    try {

        $user_req = $request->user();
        $user_login = User::with([
            'user_status',
            'user_wallet',
            'user_wallet.wallet_counters',
            'user_profile',
            'user_profile.profile_image'

        ])->findOrFail(1);

        $token = $user_login->createToken($user_login->username)->plainTextToken;

        $user_login = [
            'id' => $user_login->id,
            'name' => $user_login->name,
            'email' => $user_login->email,
            'username' => $user_login->username,
            'status_id' => $user_login->status_id,
            'created_at' => $user_login->created_at,
            'updated_at' => $user_login->updated_at,

            'userStatus' => $user_login->user_status ? [
                'id' => $user_login->user_status->id,
                'name' => $user_login->user_status->name,
                'code' => $user_login->user_status->code,
            ] : null,


            'userProfile' => $user_login->user_profile ? [
                'id' => $user_login->user_profile->id,
                'titleName' => $user_login->user_profile->title_name,
                'firstName' => $user_login->user_profile->first_name,
                'lastName' => $user_login->user_profile->last_name,
                'nickName' => $user_login->user_profile->nick_name,
                'birthDay' => $user_login->user_profile->birth_day,
            ] : null,


            'profileImage' => $user_login->user_profile->profile_image ?
            $user_login->user_profile->profile_image->map(function ($image) {
                return [
                    'id' => $image->id,
                    'profile_id' => $image->profile_id,
                    'imageData' => $image->image_data,
                ];
            }) : null,


            'wallet' => $user_login->user_wallet ? [
                'id' => $user_login->user_wallet->id,
                'userID' => $user_login->user_wallet->user_id,
                'point' => $user_login->user_wallet->point,
                'status' => $user_login->user_wallet->status,
            ] : null,


            'walletCounters' => $user_login->user_wallet->wallet_counters ?
            $user_login->user_wallet->wallet_counters->map(function ($counter) {
                return [
                    'id' => $counter->id,
                    'walletID' => $counter->wallet_id,
                    'rewardID' => $counter->reward_id,
                    'point' => $counter->point,
                    'status' => $counter->reward_id,
                    'detail' => $counter->detail,
                    'createdAt' => $counter->created_at,
                    'updatedAt' => $counter->updated_at,
                ];
            }) : null,


        ];

        return response()->json([
            'message' => 'get user successfllry.',
            'userLogin' => $user_login,
            'token' => $token
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'get user function error',
            'error' => $e->getMessage()
        ], 500);
    }
});

Route::get('/get_user_status', function () {
    return response()->json(UserStatus::all());
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::prefix('/auth')->group(function () {

    // ********** Users *************
    Route::apiResource('/users', UserController::class);


    // ********** User Profiles *************
    Route::apiResource('/user_profiles', UserProfileController::class);


    // ********** User Profile Pops ***************
    Route::apiResource('/user_profile_pops', UserProfilePopController::class);
    Route::post('/pop_like/{postUserID}/{authUserID}', [UserProfilePopController::class, 'popLikeProfile']);

    // ********** User Profile Followers ***************
    Route::apiResource('/user_profile_followers', UserProfileFollowersController::class);
    Route::post('/followers/{postUserID}/{authUserID}', [UserProfileFollowersController::class, 'followersProfile']);

    // ********** Posts ***************
    Route::apiResource('posts', PostController::class);


    // ********** Rewards *************
    Route::apiResource('rewards', RewardController::class);


    // ********** Wallets *************
    Route::apiResource('wellets', WalletController::class);


    // ********** Wallet Counters *************
    Route::apiResource('wallet_counters', WalletCounterController::class);


})->middleware('auth:sanctum');
