<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\UserProfileFollowers;
use App\Models\UserProfilePop;
use Illuminate\Http\Request;

class UserProfilePopController extends Controller
{


    public function popLikeProfile(Request $request, string $postUserID, string $authUserID)
    {
        try {

            $checkPopLike = UserProfilePop::where('profile_id', $postUserID)
                ->where('profile_id_pop', $authUserID)->first();
            $check = '';

            if ($checkPopLike) {
                if ($checkPopLike->status === 'like') {
                    $checkPopLike->delete();
                    $check = 'false';
                } else {
                    $checkPopLike->update([
                        'status' => 'like',
                        'updated_at' => now(),
                    ]);
                    $check = 'true';
                }
            } else {
                UserProfilePop::create([
                    'profile_id' => $postUserID,
                    'profile_id_pop' => $authUserID,
                    'status' => 'like',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $check = 'true';
            }

            return response()->json([
                'message' => 'user profile pop successfllry.',
                'checkPopLike' => $check
            ], 200);

        } catch (\Exception $error) {
            return response()->json([
                'message' => "user profile pop function error",
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
