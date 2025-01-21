<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ToolController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\CheckUsername;

Route::get('/saml/login', function () {
    return redirect()->route('home');
})->middleware(CheckUsername::class)->name('saml.login');

if (env('FORCED_SAML_LOGIN', false)) {
    Route::get('/login', fn() => redirect()->route('saml.login'))->name('login');
    Route::post('/login', fn() => redirect()->route('saml.login'));
    Route::get('/register', fn() => redirect()->route('saml.login'))->name('register');
    Route::post('/register', fn() => redirect()->route('saml.login'));
    Route::get('/forgot-password', fn() => redirect()->route('saml.login'))->name('forgot.password');
    Route::post('/forgot-password', fn() => redirect()->route('saml.login'));
    Route::post('/logout', fn() => redirect()->route('saml.login'))->name('logout');
} else {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'processLogin']);
    if (env('REGISTER_ENABLED', false)) {
        Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
        Route::post('/register', [AuthController::class, 'processRegister']);
    }
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('forgot.password');
    Route::post('/forgot-password', [AuthController::class, 'processForgotPassword']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
}

// Define password reset routes outside FORCED_SAML_LOGIN block
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'processResetPassword'])->name('password.update');

// Protected routes with CheckUsername middleware
Route::middleware(['auth', CheckUsername::class])->group(function () {
    Route::get('/', [ToolController::class, 'index'])->name('home');
    Route::get('/tools/preferences', [ToolController::class, 'getUserPreferences'])->name('tools.preferences');
    Route::post('/tools/preferences', [ToolController::class, 'saveUserPreferences'])->name('tools.preferences.save');
    Route::get('/fetch-news', [ToolController::class, 'fetchNewsAjax'])->name('fetch.news');
    Route::get('/manage', [ToolController::class, 'manage'])->name('manage');
    Route::get('/manage/tool/{tool}', [ToolController::class, 'manage'])->name('manage.tool');
    Route::get('/manage/user/{user}', [UserController::class, 'manage'])->name('manage.user');
    Route::post('/tools', [ToolController::class, 'store'])->name('tools.store');
    Route::put('/tools/{tool}', [ToolController::class, 'update'])->name('tools.update');
    Route::delete('/tools/{tool}', [ToolController::class, 'destroy'])->name('tools.destroy');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

// Unauthorized access route
Route::get('/unauthorized', function () {
    return response()->view('unauthorized', [], 403);
})->name('unauthorized');
