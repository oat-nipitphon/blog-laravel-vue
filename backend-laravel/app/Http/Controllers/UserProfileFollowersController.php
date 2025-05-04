<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserProfileFollowers;

class UserProfileFollowersController extends Controller
{

    public function followersProfile(Request $request, string $postUserID, string $authUserID)
    {
        try {
            $checkFollowers = UserProfileFollowers::where('profile_id', $postUserID)
                ->where('profile_id_followers', $authUserID)
                ->first();
            $check = '';

            if ($checkFollowers) {
                if ($checkFollowers->status_followers === 'true') {
                    $checkFollowers->delete();
                    $check = 'false';
                } else {
                    $checkFollowers->update([
                        'status' => 'true',
                        'updated_at' => now(),
                    ]);
                    $check = 'true';
                }
            } else {
                UserProfileFollowers::create([
                    'profile_id' => $postUserID,
                    'profile_id_followers' => $authUserID,
                    'status' => 'true',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $check = 'true';
            }

            return response()->json([
                'message' => 'user profile followers successfullry.',
                'checkFollowers' => $check
            ], 200);

        } catch (\Exception $error) {
            return response()->json([
                'message' => "user profile followers function error",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
