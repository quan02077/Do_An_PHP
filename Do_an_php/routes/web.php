<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/auth', [AuthController::class, 'index'])->name('auth');
Route::redirect('/auth.html', '/auth');
Route::redirect('/index.html', '/');

Route::get('/', [HomeController::class, 'index'])->name('trang-chu');

Route::get('/dashboard', function () {
    return 'Trang Vé & Yêu thích của tôi (Đang phát triển)';
})->name('dashboard');

Route::get('/profile', function () {
    return 'Trang Hồ sơ cá nhân (Đang phát triển)';
})->name('profile');
