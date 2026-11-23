<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// Trang chủ & Danh sách sự kiện
Route::get('/', [HomeController::class, 'index'])->name('Home.index');
Route::get('/home', [HomeController::class, 'index'])->name('home');

// Chi tiết sự kiện (công khai)
Route::get('/event/{event}', [EventController::class, 'show'])->name('Event.show');

Route::middleware('auth.custom')->group(function () {
    // Đặt vé tham gia sự kiện
    Route::post('/event/{id}/book', [EventController::class, 'bookTicket'])->name('Event.book');

    // Vé của tôi & Hủy vé
    Route::get('/myTicket', [DashboardController::class, 'myTicket'])->name('Dashboard.myTicket');
    Route::post('/tickets/{id}/cancel', [DashboardController::class, 'cancelTicket'])->name('Tickets.cancel');

    // Sự kiện yêu thích & Toggle Bookmark
    Route::get('/favorite', [DashboardController::class, 'favorite'])->name('Dashboard.favorite');
    Route::post('/favorite/toggle/{eventId}', [DashboardController::class, 'toggleFavorite'])->name('Favorite.toggle');

    // Hồ sơ cá nhân & Đổi mật khẩu
    Route::get('/profile', [DashboardController::class, 'profile'])->name('Dashboard.profile');
    Route::post('/profile', [DashboardController::class, 'updateProfile'])->name('Dashboard.updateProfile');
    Route::post('/profile/change-password', [DashboardController::class, 'changePassword'])->name('Dashboard.changePassword');
});

Route::prefix('admin')->name('admin.')->middleware(['auth.custom', 'admin'])->group(function () {
    // Bảng quản trị Admin
    Route::get('/', [AdminController::class, 'index'])->name('index');

    // Quản lý danh mục sự kiện
    Route::post('/categories', [AdminController::class, 'storeCategory'])->name('categories.store');
    Route::put('/categories/{id}', [AdminController::class, 'updateCategory'])->name('categories.update');
    Route::delete('/categories/{id}', [AdminController::class, 'destroyCategory'])->name('categories.destroy');

    // Quản lý đăng ký, duyệt vé & Quét QR check-in
    Route::put('/registrations/{id}/status', [AdminController::class, 'updateRegistrationStatus'])->name('registrations.status');
    Route::post('/check-in', [AdminController::class, 'checkInTicket'])->name('checkin');

    // Quản lý sự kiện (CRUD qua modal)
    Route::resource('event', EventController::class)->except(['show', 'create', 'edit']);
});

Route::get('/auth', [AuthController::class, 'index'])->name('Auth.index');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('Auth.login');
Route::post('/register', [AuthController::class, 'register'])->name('Auth.register');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->middleware('throttle:5,1')->name('Auth.sendResetLink');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->middleware('throttle:5,1')->name('Auth.resetPassword');
Route::post('/logout', [AuthController::class, 'logout'])->name('Auth.logout');
