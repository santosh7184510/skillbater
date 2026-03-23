<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Registration
    public function register(Request $request)
    {
        try {
            $request->validate([
                'username' => 'required|string|min:3|max:50',
                'email' => 'required|email|unique:users,email',
                'mobile' => 'required|digits:10|unique:users,mobile',
                'gender' => 'nullable|string',
                'password' => 'required|string|min:6',
                'captcha' => 'required|string'
            ]);

            if (session('captcha') !== $request->captcha) {
                return response()->json([
                    'success' => false,
                    'message' => 'Captcha does not match. Try again.'
                ], 422);
            }

            do {
                $prefix = strtoupper(substr($request->username, 0, 3));
                $randomNumber = rand(100, 999);
                $userId = $prefix . '-' . $randomNumber;
            } while (User::where('user_id', $userId)->exists());

            $user = new User();
            $user->user_id = $userId;
            $user->username = $request->username;
            $user->email = $request->email;
            $user->mobile = $request->mobile;
            $user->gender = $request->gender;
            $user->password = Hash::make($request->password);
            $user->save();

            session()->forget('captcha');

            return response()->json([
                'success' => true,
                'user_id' => $userId,
                'message' => 'Registration successful'
            ]);

        } catch (ValidationException $ve) {
            return response()->json([
                'success' => false,
                'message' => $ve->errors()
            ], 422);
        } catch (\Throwable $e) {
            Log::error("Registration Error: ".$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error'
            ], 500);
        }
    }

    // Captcha
    public function getCaptcha()
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $captcha = '';
        for ($i = 0; $i < 6; $i++) {
            $captcha .= $chars[rand(0, strlen($chars) - 1)];
        }

        session(['captcha' => $captcha]);

        return response()->json(['captcha' => $captcha]);
    }

    // Login
    public function login(Request $request)
{
    $request->validate([
        'user_id_or_email' => 'required|string',
        'password' => 'required|string',
    ]);

    // Find user by email OR user_id
    $user = \App\Models\User::where('email', $request->user_id_or_email)
                ->orWhere('user_id', $request->user_id_or_email)
                ->first();

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found'
        ], 404);
    }

    // Check password
    if (!Hash::check($request->password, $user->password)) {
        return response()->json([
            'success' => false,
            'message' => 'Password does not match'
        ], 422);
    }

    // Login the user
    \Illuminate\Support\Facades\Auth::login($user);

    return response()->json([
        'success' => true,
        'user_id' => $user->user_id,
        'message' => 'Login successful'
    ]);
}


    // Dashboard
    public function dashboard()
    {
        $userId = Auth::check() ? Auth::user()->user_id : null;
        return view('dashboard', compact('userId'));
    }

    // Forgot Password Form
    public function forgotPasswordForm()
    {
        return view('auth.forgot-password'); // create a Blade for forgot password
    }

    // Send Reset Link
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        // Implement email later
        return response()->json([
            'success' => true,
            'message' => 'Password reset link sent to your email!'
        ]);
    }


    public function myskill()
    {
        return view('myskill');
    }

    public function request()
    {
        return view('request');
    }
    public function messages()
    {
        return view('messages');
    }

    public function index()
    {
        return view('index');
    }


public function profile()
{
    $user = Auth::user();  // get the logged-in user
    return view('profile', compact('user'));  // pass it to Blade
}

public function savePhoto(Request $request)
{
    $user = Auth::user();
    if (!$user) return response()->json(['error' => 'User not found'], 404);

    $photoData = $request->input('photo'); // data:image/png;base64,...
    $photo = explode(',', $photoData)[1];
    $photo = base64_decode($photo);

    $filename = 'profile_'.$user->id.'_'.time().'.png';
    Storage::disk('public')->put($filename, $photo);

    $user->profile_photo = $filename;
    $user->save();

    return response()->json(['success' => true, 'photo_url' => asset('storage/'.$filename)]);
}

public function userProfile($id)
{
    $user = \App\Models\User::with('skills')->find($id);

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    return response()->json([
        'id' => $user->user_id,
        'name' => $user->username,      // ✅ send name
        'skills' => $user->skills   // ✅ send skills
    ]);
}






}

