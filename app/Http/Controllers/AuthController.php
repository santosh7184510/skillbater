<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Registered successfully'
        ]);
    }

    public function login(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Login success'
        ]);
    }
}