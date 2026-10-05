<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// Trang chủ & Danh sách sự kiện
Route::get('/', [HomeController::class, 'index'])->name('Home.index');

// Quản lý Sự kiện (Resource Controller) & Đặt vé
Route::resource('event', EventController::class)->names('Event');
Route::post('/event/{id}/book', [EventController::class, 'bookTicket'])->name('Event.book');

// Vé của tôi & Hủy vé
Route::get('/myTicket', [DashboardController::class, 'myTicket'])->name('Dashboard.myTicket');
Route::post('/tickets/{id}/cancel', [DashboardController::class, 'cancelTicket'])->name('Tickets.cancel');

// Trang yêu thích & Lưu / Bỏ lưu yêu thích
Route::get('/favorite', [DashboardController::class, 'favorite'])->name('Dashboard.favorite');
Route::post('/favorite/toggle/{eventId}', [DashboardController::class, 'toggleFavorite'])->name('Favorite.toggle');

// Hồ sơ cá nhân & Bảo mật tài khoản
Route::get('/profile', [DashboardController::class, 'profile'])->name('Dashboard.profile');
Route::post('/profile', [DashboardController::class, 'updateProfile'])->name('Dashboard.updateProfile');
Route::post('/profile/change-password', [DashboardController::class, 'changePassword'])->name('Dashboard.changePassword');

// Xác thực & Tài khoản người dùng
Route::get('/auth', [AuthController::class, 'index'])->name('Auth.index');
Route::post('/login', [AuthController::class, 'login'])->name('Auth.login');
Route::post('/register', [AuthController::class, 'register'])->name('Auth.register');
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('Auth.logout');
Route::get('/switch-user/{id}', [AuthController::class, 'switchUser'])->name('Auth.switch');
