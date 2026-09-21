<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;

Route::get('/auth', [AuthController::class, 'index'])->name('Auth.index');
Route::get('/', [HomeController::class, 'index'])->name('Home.index');
Route::get('/myTicket', [DashboardController::class, 'myTicket'])->name('Dashboard.myTicket');
Route::get('/favorite', [DashboardController::class, 'favorite'])->name('Dashboard.favorite');
Route::get('/event/{id}', [EventController::class, 'show'])->name('Event.show');
