<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserLogLogin;
use App\Models\UserWallet;

class AuthController extends Controller
{

    // Register
    public function register(Request $request)
    {
        try {

            $validate = $request->validate([
                'email' => 'required|string|email|max:255|unique:users',
                'username' => 'required|string|max:255|unique:users',
                'password' => 'required|string|min:3',
                'status_id' => 'required|integer',
            ]);

            $user = User::create([
                'name' => $validate['username'],
                'username' => $validate['username'],
                'email' => $validate['email'],
                'password' => Hash::make($validate['password']),
                'status_id' => $validate['status_id'],
                'created_at' => now()
            ]);

            if (empty($user)) {
                return response()->json([
                    'message' => 'register create user false.'
                ]);
            }

            $user_profile = UserProfile::create([
                'user_id' => $user->id,
                'created_at' => now()
            ]);

            $user_wallet = UserWallet::create([
                'user_id' => $user->id,
                'point' => 0,
                'status' => 'active',
                'created_at' => now(),
            ]);

            if (empty($user_profile) && empty($user_wallet)) {
                return response()->json([
                    'message' => 'register create profile false.'
                ], 404);
            }

            return response()->json([
                'message' => 'register successfully.',
                'user' => $user,
                'userProfile' => $user_profile,
                'userWallet' => $user_wallet
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => "register function error",
                'error' => $e->getMessage()
            ], 500);
        }
    }


    // Login
    public function login(Request $request)
    {
        try {

            $request->validate([
                'emailUsername' => 'required|string',
                'password' => 'required|string',
            ]);

            $user = User::where('email', $request->emailUsername)
                ->orWhere('username', $request->emailUsername)
                ->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'message' => "login check username or email and password false.",
                ], 401);
            }

            $token = $user->createToken($user->username)->plainTextToken;

            if ($user && $token) {

                $log_login = UserLogLogin::create([
                    'user_id' => $user->id,
                    'time_in' => now(),
                    'status_login' => "online",
                    'created_at' => now(),
                ]);

                if (!$log_login) {
                    return response()->json([
                        'message' => "Login not success."
                    ], 404);
                }

                return response()->json([
                    'message' => "Login successfullry.",
                    'token' => $token,
                ], 200);
            }

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'login function error',
                'error' => $e->getMessage()
            ], 500);
        }

    }

    // Logout
    public function logout(Request $request)
    {
        try {


            if ($user = $request->user()) {

                $log_logout = UserLogLogin::where('user_id', $user->id)->first();


                if (empty($log_logout) && empty($user)) {
                    return response()->json([
                        'message' => "Laravel function logout request user false"
                    ], 404);
                }

                $log_logout->update([
                    'user_id' => $user->id,
                    'status' => "offline",
                    'time_out' => now(),
                    'updated_at' => now()
                ]);

                $user->tokens()->delete();

                return response()->json([
                    'message' => "logout successfullry.",
                ], 200);
            }

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'logout function error.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
