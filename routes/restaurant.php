<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RestaurantManager\DashboardController;
use App\Http\Controllers\RestaurantManager\RestaurantController;
use App\Http\Controllers\RestaurantManager\ReservationController;
use App\Http\Controllers\RestaurantManager\DishController;

Route::middleware(['auth', 'active'])
    ->prefix('restaurant-manager')
    ->name('restaurant-manager.')
    ->group(function () {
        // Tableau de bord
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Gestion des restaurants
        Route::resource('restaurants', RestaurantController::class);
        
        // Gestion des plats
        Route::resource('restaurants.dishes', DishController::class)->except(['show']);
        
        // Gestion des réservations
        Route::get('reservations', [ReservationController::class, 'index'])->name('reservations.index');
        Route::get('reservations/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');
        Route::patch('reservations/{reservation}/status', [ReservationController::class, 'updateStatus'])->name('reservations.update-status');
    });
