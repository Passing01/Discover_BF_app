<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ApiTouristSiteController;

// Routes pour les hôtels et réservations protégées par Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/hotels', [HotelController::class, 'index']);
    Route::get('/hotels/{hotel}/rooms', [HotelController::class, 'showRooms']);

    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/bookings/{booking}', [BookingController::class, 'show']);
});

Route::prefix('api')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/sites', [ApiTouristSiteController::class, 'index'])->name('api.sites.index');
        Route::get('/sites/{site}', [ApiTouristSiteController::class, 'show'])->name('api.sites.show');
        Route::post('/sites/{site}/contact-guide', [ApiTouristSiteController::class, 'contactGuide'])->name('api.sites.contact');
    });
});

// Routes publiques pour l'authentification
// Route::post('/login', [AuthController::class, 'login']);
// Route::post('/register', [AuthController::class, 'register']);