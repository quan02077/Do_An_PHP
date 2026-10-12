<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

// Trang chủ & Danh sách sự kiện
Route::get('/', [HomeController::class, 'index'])->name('Home.index');

// Trang và thao tác quản trị: phải đăng nhập và có vai trò admin.
Route::middleware(['auth.custom', 'admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('Admin.index');
    Route::post('/admin/categories', [AdminController::class, 'storeCategory'])->name('Admin.categories.store');
    Route::put('/admin/categories/{id}', [AdminController::class, 'updateCategory'])->name('Admin.categories.update');
    Route::delete('/admin/categories/{id}', [AdminController::class, 'destroyCategory'])->name('Admin.categories.destroy');
    Route::put('/admin/registrations/{id}/status', [AdminController::class, 'updateRegistrationStatus'])->name('Admin.registrations.status');
    Route::resource('event', EventController::class)->except(['show'])->names('Event');
});

// Chi tiết sự kiện là công khai; đăng ký vé yêu cầu đăng nhập.
Route::get('/event/{event}', [EventController::class, 'show'])->name('Event.show');
Route::post('/event/{id}/book', [EventController::class, 'bookTicket'])->middleware('auth.custom')->name('Event.book');

// Vé của tôi & Hủy vé
Route::middleware('auth.custom')->group(function () {
    Route::get('/myTicket', [DashboardController::class, 'myTicket'])->name('Dashboard.myTicket');
    Route::post('/tickets/{id}/cancel', [DashboardController::class, 'cancelTicket'])->name('Tickets.cancel');

    // Trang yêu thích & Lưu / Bỏ lưu yêu thích
    Route::get('/favorite', [DashboardController::class, 'favorite'])->name('Dashboard.favorite');
    Route::post('/favorite/toggle/{eventId}', [DashboardController::class, 'toggleFavorite'])->name('Favorite.toggle');

    // Hồ sơ cá nhân & Bảo mật tài khoản
    Route::get('/profile', [DashboardController::class, 'profile'])->name('Dashboard.profile');
    Route::post('/profile', [DashboardController::class, 'updateProfile'])->name('Dashboard.updateProfile');
    Route::post('/profile/change-password', [DashboardController::class, 'changePassword'])->name('Dashboard.changePassword');
});

// Xác thực & Tài khoản người dùng
Route::get('/auth', [AuthController::class, 'index'])->name('Auth.index');
Route::post('/login', [AuthController::class, 'login'])->name('Auth.login');
Route::post('/register', [AuthController::class, 'register'])->name('Auth.register');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('Auth.resetPassword');
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('Auth.logout');
