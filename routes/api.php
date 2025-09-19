<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HotelController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ApiTouristSiteController;
use App\Http\Controllers\Api\RestaurantController as ApiRestaurantController;
use App\Http\Controllers\Api\RestaurantReservationController;

// ==============================
// PROTECTED (auth:sanctum) APIs
// ==============================
// Toutes les routes ci-dessous nécessitent un header Authorization: Bearer <token>
Route::middleware('auth:sanctum')->group(function () {
    // GET /api/hotels — Lister tous les hôtels (avec leurs chambres)
    Route::get('/hotels', [HotelController::class, 'index']);
    // GET /api/hotels/{hotel}/rooms — Lister les chambres d'un hôtel
    Route::get('/hotels/{hotel}/rooms', [HotelController::class, 'showRooms']);

    // POST /api/bookings — Créer une réservation d'hôtel
    Route::post('/bookings', [BookingController::class, 'store']);
    // GET /api/bookings/{booking} — Voir une réservation d'hôtel
    Route::get('/bookings/{booking}', [BookingController::class, 'show']);

    // Sites touristiques
    // GET /api/sites — Lister les sites touristiques
    Route::get('/sites', [ApiTouristSiteController::class, 'index'])->name('api.sites.index');
    // GET /api/sites/{site} — Détails d'un site
    Route::get('/sites/{site}', [ApiTouristSiteController::class, 'show'])->name('api.sites.show');
    // POST /api/sites/{site}/contact-guide — Envoyer un message à un guide du site
    Route::post('/sites/{site}/contact-guide', [ApiTouristSiteController::class, 'contactGuide'])->name('api.sites.contact');

    // Restaurants — Réservations
    // POST /api/restaurants/{restaurant}/reserve — Créer une réservation restaurant
    Route::post('/restaurants/{restaurant}/reserve', [ApiRestaurantController::class, 'reserve'])->name('api.restaurants.reserve');

    // Mes réservations restaurant
    // GET /api/restaurant-reservations — Lister mes réservations restaurant
    Route::get('/restaurant-reservations', [RestaurantReservationController::class, 'index'])->name('api.restaurant_reservations.index');
    // GET /api/restaurant-reservations/{reservation} — Détails d'une réservation restaurant
    Route::get('/restaurant-reservations/{reservation}', [RestaurantReservationController::class, 'show'])->name('api.restaurant_reservations.show');
});

// ==============================
// PUBLIC APIs (sans token)
// ==============================
// Auth API (Sanctum tokens personnels):
// POST /api/login — Connexion (retourne un Bearer token)
Route::post('/login', [AuthController::class, 'login']);
// POST /api/register — Inscription (retourne un Bearer token)
Route::post('/register', [AuthController::class, 'register']);

// Restaurants (public):
// GET /api/restaurants — Lister les restaurants actifs (paginés)
Route::get('/restaurants', [ApiRestaurantController::class, 'index'])->name('api.restaurants.index');
// GET /api/restaurants/{restaurant} — Détails d'un restaurant (+ plats disponibles si chargés)
Route::get('/restaurants/{restaurant}', [ApiRestaurantController::class, 'show'])->name('api.restaurants.show');
// GET /api/restaurants/{restaurant}/dishes — Lister les plats disponibles d'un restaurant
Route::get('/restaurants/{restaurant}/dishes', [ApiRestaurantController::class, 'dishes'])->name('api.restaurants.dishes');