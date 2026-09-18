<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('trang-chu');

// Route tạm thời cho các trang đang phát triển (để route() hoạt động không bị lỗi)
Route::get('/dashboard', function () {
    return 'Trang Vé & Yêu thích của tôi (Đang phát triển)';
})->name('dashboard');

Route::get('/profile', function () {
    return 'Trang Hồ sơ cá nhân (Đang phát triển)';
})->name('profile');
