<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Master\UserController;

Route::get('/', function () {
    return auth()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('welcome');
    })->name('dashboard');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::middleware('auth')->group(function () {
    Route::middleware('permission:manage_master')->group(function () {
        Route::prefix('master')->group(function () {
            Route::get('user', [UserController::class, 'index'])->name('master-user.index');
            Route::get('user/tambah', [UserController::class, 'create'])->name('master-user.create');
            Route::post('user', [UserController::class, 'store'])->name('master-user.store');
            Route::get('user/{id}/edit', [UserController::class, 'edit'])->name('master-user.edit');
            Route::put('user/{id}/update', [UserController::class, 'update'])->name('master-user.update');
            Route::put('user/{id}/delete', [UserController::class, 'delete'])->name('master-user.destroy');
            Route::get('/sync', [UserController::class, 'syncEmployeeData'])->name('master-user.sync');
        });
    });

    // Logout Route
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
