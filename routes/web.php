<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('login');
});

Route::get('/login', function () {
    return view('login');
});

// TEST ROUTE (must work)
Route::get('/test', function () {
    return "OK WORKING";
});

// AUTH
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// DASHBOARD
Route::get('/dashboard', function () {
    return "Dashboard working SUCCESS";
});