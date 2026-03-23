<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Models\SkillRequest;
use App\Http\Controllers\SkillController;

// Group routes with web middleware so session works
Route::middleware('web')->group(function() {

    // Registration
    Route::post('/register', [AuthController::class, 'register']);

    // Captcha
    Route::get('/captcha', [AuthController::class, 'getCaptcha']);

    // Password update (optional, you said we removed it)
    // Route::post('/update-password', [AuthController::class, 'updatePassword']);

    // Login (passwordless)
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/ai-chat', [AIChatController::class, 'chat']);
    Route::get('/myskill', [SkillController::class, 'index'])->name('myskill');

    Route::get('/my-requests', function() {
    return SkillRequest::with(['skill','fromUser'])
        ->where('to_user_id', auth()->id())
        ->get();
});

// web.php version with JSON response
Route::get('/user/{id}', [AuthController::class, 'userProfile'])
    ->name('user.profile')
    ->middleware('auth'); // optional if user must be logged in


});
