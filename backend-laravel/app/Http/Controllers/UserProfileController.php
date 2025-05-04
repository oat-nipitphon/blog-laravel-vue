<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{

    public function getUserProfile () {

        try {

            return response()->json([
                'message' => 'get user profile successfllry.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'get user profile function error',
                'error' => $e->getMessage()
            ], 500);
        }

    }

}
