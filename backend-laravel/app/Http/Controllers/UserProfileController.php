<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\UserProfile;

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
        try {

            $validated = $request->validate([
                'profileID' => 'required|integer',
                'titleName' => 'required|string',
                'fullName' => 'required|string',
                'nickName' => 'required|string',
                'telPhone' => 'required|string',
                'birthDay' => 'required|date',

            ]);

            $dateTime = Carbon::now('Asia/Bangkok')->format('Y-m-d H:i:s');
            $birthDay = Carbon::parse($validated['birthDay'])->format('Y-m-d');
            $userProfile = UserProfile::findOrFail($validated['profileID']);

            if (!empty($userProfile)) {

                $userProfile->update([
                    'title_name' => $validated['titleName'],
                    'full_name' => $validated['fullName'],
                    'nick_name' => $validated['nickName'],
                    'tel_phone' => $validated['telPhone'],
                    'birth_day' => $birthDay,
                    'updated_at' => $dateTime,
                ]);

                return response()->json([
                    'message' => 'update user profile success',
                    'userProfile' => $userProfile,
                    'status' => true
                ], 200);
            }

            return response()->json([
                'message' => "update user profile false",
                'status' => false
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'store user profile function error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {

            return response()->json([
                'message' => 'show user profile successfully.'
            ], 200);

            // $user = User::with(
            //     'userImage',
            //     'userProfile',
            //     'userProfile.ProfileContact',
            //     'userProfileContact',
            //     'userProfileImage',
            //     'userLogin',
            //     'userStatus',
            //     'posts',
            //     'userPoint',
            //     'userPoint.userPointCounter',
            // )->findOrFail($id);



        } catch (\Exception $e) {
            return response()->json([
                'message' => "laravel user profile function show error",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {

            $validated = $request->validate([
                'userID' => 'required|integer',
                'name' => 'nullable|string',
                'email' => 'nullable|string',
                'userName' => 'nullable|string',
                'statusID' => 'nullable|integer',
                'profileID' => 'nullable|integer',
                'titleName' => 'nullable|string',
                'fullName' => 'nullable|string',
                'nickName' => 'nullable|string',
                'telPhone' => 'nullable|string',
                'birthDay' => 'nullable|date',
            ]);

            $userProfile = UserProfile::findOrFail($validated['profileID']);
            $dateTime = Carbon::now('Asia/Bangkok')->format('Y-m-d H:i:s');
            $birthDay = Carbon::parse($validated['birthDay'])->format('Y-m-d');

            if ($userProfile) {

                $userProfile->update([
                    'title_name' => $validated['titleName'],
                    'full_name' => $validated['fullName'],
                    'nick_name' => $validated['nickName'],
                    'tel_phone' => $validated['telPhone'],
                    'birth_day' => $birthDay,
                    'updated_at' => $dateTime
                ]);

                $user = User::findOrFail('id', $userProfile->user_id);

                if ($user) {
                    $user->update([
                        'name' => $validated['name'],
                        'email' => $validated['email'],
                        'username' => $validated['userName'],
                        'status_id' => $validated['statusID'],
                    ]);

                    return response()->json([
                        'message' => 'Profile updated successfully.',
                        'userProfile' => $userProfile,
                        'status' => true
                    ], 200);
                } else {
                    return response()->json([
                        'message' => "Profile update not success.",
                        'status' => false
                    ]);
                }
            }

            return response()->json([
                'message' => 'update user profile successfully.'
            ], 201);

        } catch (\Exception $e) {

            return response()->json([
                'message' => 'update user profile function error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
