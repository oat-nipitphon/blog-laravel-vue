<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
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
                'message' => 'get user profiles successfllry.',
                'userLogin' => $user_login,
                'token' => $token
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'get user profile function error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
