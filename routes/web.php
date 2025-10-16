<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DestinationController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->name('auth.')->group(function() {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::get('register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin') 
    ->name('admin.')  
    ->group(function () {
        Route::get('/geocode', [DestinationController::class, 'geocodeAddress'])->name('geocode');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::prefix('managements')
            ->name('managements.')
            ->group(function () {
                Route::resource('destinations', DestinationController::class);
                Route::resource('packages', PackageController::class);
                Route::patch('packages/{package:slug}/status', [PackageController::class, 'updateStatus'])->name('packages.updateStatus');
            });
    });