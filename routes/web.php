<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\SkillMatchController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/', fn () => view('index'))->name('home');
Route::get('/login', fn () => view('login'))->name('login.page');

Route::get('/captcha', [AuthController::class, 'getCaptcha'])->name('captcha');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::get('/forgot-password', [AuthController::class, 'forgotPasswordForm'])->name('forgot.password');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('forgot.password.send');


/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // Dashboard & Profile
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::post('/profile/photo', [AuthController::class, 'savePhoto'])->name('profile.photo');

    // Pages
    Route::get('/messages', [AuthController::class, 'messages'])->name('messages');
    Route::get('api//myskill', [SkillController::class, 'index'])->name('myskill'); // ✅ single source
    Route::get('/request', [AuthController::class, 'request'])->name('request');

    // Skills
    Route::post('/skills', [SkillController::class, 'store'])->name('skills.store');
    Route::get('/search-skills', [SkillController::class, 'search'])->name('skills.search');
    Route::post('/send-request', [SkillController::class, 'sendRequest'])->name('send.request');
    Route::post('/accept-request', [SkillController::class, 'acceptRequest'])->name('accept.request');

    // Users
    Route::get('/users', [SkillController::class, 'users'])->name('users.list');
    Route::get('/user/{id}', [AuthController::class, 'userProfile'])->name('user.profile');

    // Skill Match
    Route::get('/skill-match', [SkillMatchController::class, 'index'])->name('skill.match');
});