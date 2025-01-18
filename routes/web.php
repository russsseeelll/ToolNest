<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ToolController;
use App\Http\Middleware\CheckGuid;

Route::middleware([CheckGuid::class])->group(function () {
    Route::get('/', [ToolController::class, 'index'])->name('home');

    Route::get('/tools/preferences', [ToolController::class, 'getUserPreferences'])->name('tools.preferences');
    Route::post('/tools/preferences', [ToolController::class, 'saveUserPreferences'])->name('tools.preferences.save');


    Route::get('/fetch-news', [ToolController::class, 'fetchNewsAjax'])->name('fetch.news');

    // Manage Route (For both tools and users management)
    Route::get('/manage', [ToolController::class, 'manage'])->name('manage');

    // Manage tool route (pass tool id to manage function)
    Route::get('/manage/tool/{tool}', [ToolController::class, 'manage'])->name('manage.tool');

    // Manage user route (pass user id to manage function)
    Route::get('/manage/user/{user}', [UserController::class, 'manage'])->name('manage.user');

    // Tool Routes
    Route::post('/tools', [ToolController::class, 'store'])->name('tools.store');
    Route::put('/tools/{tool}', [ToolController::class, 'update'])->name('tools.update');
    Route::delete('/tools/{tool}', [ToolController::class, 'destroy'])->name('tools.destroy');

    // User Routes
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

Route::get('/unauthorized', function () {
    return response()->view('unauthorized', [], 403);
})->name('unauthorized');
