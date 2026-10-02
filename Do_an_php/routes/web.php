<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// Trang chủ & Danh sách sự kiện
Route::get('/', [HomeController::class, 'index'])->name('Home.index');

// Chi tiết sự kiện & Đặt vé
Route::get('/event/{id}', [EventController::class, 'show'])->name('Event.show');
Route::post('/event/{id}/book', [EventController::class, 'bookTicket'])->name('Event.book');

// Vé của tôi & Hủy vé
Route::get('/myTicket', [DashboardController::class, 'myTicket'])->name('Dashboard.myTicket');
Route::post('/tickets/{id}/cancel', [DashboardController::class, 'cancelTicket'])->name('Tickets.cancel');

// Trang yêu thích & Lưu / Bỏ lưu yêu thích
Route::get('/favorite', [DashboardController::class, 'favorite'])->name('Dashboard.favorite');
Route::post('/favorite/remove/{eventId}', [DashboardController::class, 'removeFavorite'])->name('Favorite.remove');
Route::post('/favorite/toggle/{eventId}', [DashboardController::class, 'toggleFavorite'])->name('Favorite.toggle');

// Xác thực & Tài khoản người dùng
Route::get('/auth', [AuthController::class, 'index'])->name('Auth.index');
Route::post('/login', [AuthController::class, 'login'])->name('Auth.login');
Route::post('/register', [AuthController::class, 'register'])->name('Auth.register');
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('Auth.logout');
Route::get('/switch-user/{id}', [AuthController::class, 'switchUser'])->name('Auth.switch');
