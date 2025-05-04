<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{
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
        try {

            $request->validate([
                'userID' => 'required|integer|exists:users,id',
                'email' => 'nullable|string|email',
                'username' => 'nullable|string',
                'name' => 'nullable|string',
                'statusID' => 'nullable|integer',
            ]);

            $user = User::findOrFail($request->userID);

            if (!$user) {
                return response()->json([
                    'message' => "update user false."
                ], 404);
            }

            $user->update(array_filter([
                'email' => $request->email,
                'username' => $request->username,
                'name' => $request->username,
                'status_id' => $request->statusID,
                'updated_at' => now()
            ]));

            return response()->json([
                'message' => "update user successfully.",
                'user' => $user
            ], 200);

        } catch (\Exception $error) {
            return response()->json([
                'message' => "update user function error",
                'error' => $error->getMessage()
            ]);
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
