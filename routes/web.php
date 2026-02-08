<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\SkillMatchController;
use App\Http\Controllers\Api\SkillMatchApiController;

// -------------------- Public Routes --------------------
Route::middleware(['web'])->group(function () {

    // Home / Login page
    Route::get('/', function () { return view('index'); })->name('home');
    Route::get('/login', function () { return view('login'); });

    // Captcha
    Route::get('/captcha', [AuthController::class, 'getCaptcha'])->name('captcha');

    // Auth
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    // Forgot Password
    Route::get('/forgot-password', [AuthController::class, 'forgotPasswordForm'])->name('forgot.password');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('forgot.password.send');

    // AI Chatbot (public)
    Route::post('/ai/chat', function (Request $request) {
        $text = $request->input('text');
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('QROKE_API_KEY'),
            ])->post('https://api.qroke.ai/v1/chat/completions', [
                "model" => "gpt-3.5-turbo",
                "messages" => [["role" => "user", "content" => $text]],
                "temperature" => 0.7,
                "max_tokens" => 500
            ]);

            $data = $response->json();
            $reply = $data['choices'][0]['message']['content'] ?? "⚠️ No response from AI";

            return response()->json(['response' => $reply]);

        } catch (\Exception $e) {
            return response()->json([
                'response' => '⚠️ AI server not reachable',
                'error' => $e->getMessage()
            ], 500);
        }
    })->name('ai.chat');
});

// -------------------- Protected Routes --------------------
Route::middleware(['auth'])->group(function () {

    // Dashboard & Profile
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::post('/profile/photo', [AuthController::class, 'savePhoto'])->name('profile.photo');

    // Pages
    Route::get('/messages', [AuthController::class,'messages'])->name('messages');
    Route::get('/myskill', [AuthController::class,'myskill'])->name('myskill');
    Route::get('/request', [AuthController::class,'request'])->name('request');

    // Skills CRUD
    Route::post('/skills', [SkillController::class, 'store'])->name('skills.store');
    Route::get('/search-skills', [SkillController::class, 'searchSkills'])->name('skills.search');

    // Send skill request
    Route::post('/api/send-request', [SkillController::class, 'sendRequest'])->name('skills.sendRequest');

    // API endpoint to fetch users with skills (for JS)
    Route::get('/api/users', [SkillController::class, 'getUsersWithSkills'])->name('api.users');

    // Skill Match Page
    Route::get('/skill-match', [SkillMatchController::class, 'index'])->name('skill.match');

    // API routes (Sanctum protected)
    Route::middleware('auth:sanctum')->group(function() {
        Route::get('/users', [SkillMatchApiController::class, 'index'])->name('api.users');
        Route::post('/skill-request', [SkillMatchApiController::class, 'requestSkill'])->name('api.skill.request');
        Route::get('/user/{id}', [SkillMatchApiController::class, 'profile'])->name('api.user.profile');
    });

});
