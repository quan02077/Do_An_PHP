<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;

Route::get('/auth', [AuthController::class, 'index'])->name('auth');
Route::get('/', [HomeController::class, 'index'])->name('trang-chu');
Route::get('/myTicket', [DashboardController::class, 'myTicket'])->name('myTicket');
Route::get('/favorite', [DashboardController::class, 'favorite'])->name('favorite');
Route::get('/event/{id}', [EventController::class, 'show'])->name('events_show');
