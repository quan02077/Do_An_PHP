<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/auth', [AuthController::class, 'index'])->name('auth');
Route::redirect('/auth.html', '/auth');
Route::redirect('/index.html', '/');

Route::get('/', [HomeController::class, 'index'])->name('trang-chu');
Route::get('/myTicket', [DashboardController::class, 'myTicket'])->name('myTicket');
Route::get('/Favorite', [DashboardController::class, 'favorite'])->name('favorite');
Route::redirect('/dashboard', '/myTicket');
Route::redirect('/favorite', '/Favorite');
