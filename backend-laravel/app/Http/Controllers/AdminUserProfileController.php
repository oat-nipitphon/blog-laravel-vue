<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminUserProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // try {

        //     $userProfiles = User::with(
        //         'userImage',
        //         'userStatus',
        //         'latestUserLogin',
        //         'userProfile',
        //         'userProfileContact'
        //     )->get()->map(function ($user) {
        //         return $user ? [
        //             'id' => $user->id,
        //             'name' => $user->name,
        //             'email' => $user->email,
        //             'username' => $user->username,
        //             'status_id' => $user->status_id,
        //             'created_at' => $user->created_at,
        //             'updated_at' => $user->updated_at,
        //             'userStatus' => $user->userStatus ? [
        //                 'id' => $user->userStatus->id,
        //                 'status_code' => $user->userStatus->status_code,
        //                 'status_name' => $user->userStatus->status_name,
        //             ] : null,
        //             'userLogin' => $user->latestUserLogin ? [
        //                 'id' => $user->latestUserLogin->id,
        //                 'user_id' => $user->latestUserLogin->user_id,
        //                 'status_login' => $user->latestUserLogin->status_login,
        //                 'created_at' => $user->latestUserLogin->created_at,
        //                 'updated_at' => $user->latestUserLogin->updated_at,
        //                 'total_time_login' => $user->latestUserLogin->total_time_login,
        //             ] : null,

        //             'userProfile' => $user->userProfile ? [
        //                 'id' => $user->userProfile->id,
        //                 'user_id' => $user->userProfile->user_id,
        //                 'title_name' => $user->userProfile->title_name,
        //                 'full_name' => $user->userProfile->full_name,
        //                 'nick_name' => $user->userProfile->nick_name,
        //                 'tel_phone' => $user->userProfile->tel_phone,
        //                 'birth_day' => $user->userProfile->birth_day,
        //                 'created_at' => $user->userProfile->created_at,
        //                 'updated_at' => $user->userProfile->updated_at,
        //             ] : null,

        //             'userImage' => $user->userImage->map(function ($userImage) {
        //                 return [
        //                     'id' => $userImage->id,
        //                     'user_id' => $userImage->user_id,
        //                     'imageData' => $userImage->image_data,
        //                     'created_at' => $userImage->created_at,
        //                     'updated_at' => $userImage->updated_at,
        //                 ];
        //             }),

        //             'userProfileContact' => $user->userProfileContact->map(function ($contact) {
        //                 return $contact ? [
        //                     'id' => $contact->id,
        //                     'user_id' => $contact->user_id,
        //                     'contact_name' => $contact->contact_name,
        //                     'contact_link_address' => $contact->contact_link_address,
        //                     'contact_link_path' => $contact->contact_link_path,
        //                     'contact_icon_name' => $contact->contact_icon_name,
        //                     'contact_icon_url' => $contact->contact_icon_url,
        //                     'contact_icon_data' => $contact->contact_icon_data ? 'data:image/png;base64,'
        //                                             . base64_encode($contact->contact_icon_data) : null,
        //                     'created_at' => $contact->created_at,
        //                     'updated_at' => $contact->updated_at,
        //                 ] : null;
        //             }),


        //         ] : null;
        //     });

        //     if ($userProfiles) {
        //         return response()->json([
        //             'message' => "Laravel get user profile detail success",
        //             'userProfiles' => $userProfiles
        //         ], 200);
        //     }

        //     return response()->json([
        //         'message' => "Laravel admin manager user profile false.",
        //         'userProfiles' => $userProfiles
        //     ], 204);

        // } catch (\Exception $e) {
        //     return response()->json([
        //         'message' => "Laravel admin manager user profile error.",
        //         'error' => $e->getMessage()
        //     ], 500);
        // }
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
        // try {

        //     $userProfile = UserProfile::with(
        //         'user',
        //         'user.userProfileImage'
        //     )->findOrFail($userProfileID);


        // } catch (\Exception $error) {
        //     return response()->json([
        //         'message' => "laravel controller function show error" . $error->getMessage()
        //     ]);
        // }
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
