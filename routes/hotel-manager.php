<?php

use App\Http\Controllers\HotelManagerController;
use App\Http\Controllers\HotelBookingController;
use App\Http\Controllers\HotelManager\ClientController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'active'])->group(function () {
    // Tableau de bord
    Route::get('/dashboard', [HotelManagerController::class, 'dashboard'])->name('dashboard');
    
    // Gestion des hôtels
    Route::resource('hotels', HotelManagerController::class);
    Route::get('hotels/{hotel}/toggle-status', [HotelManagerController::class, 'toggleStatus'])->name('hotels.toggle-status');
    Route::get('hotels/{hotel}/toggle-featured', [HotelManagerController::class, 'toggleFeatured'])->name('hotels.toggle-featured');
    
    // Gestion des chambres
    Route::prefix('hotels/{hotel}')->group(function () {
        // Routes pour les chambres
        Route::get('rooms', [HotelManagerController::class, 'rooms'])->name('hotels.rooms.index');
        Route::get('rooms/create', [HotelManagerController::class, 'createRoom'])->name('hotels.rooms.create');
        Route::post('rooms', [HotelManagerController::class, 'storeRoom'])->name('hotels.rooms.store');
        Route::get('rooms/{room}', [HotelManagerController::class, 'showRoom'])->name('hotels.rooms.show');
        Route::get('rooms/{room}/edit', [HotelManagerController::class, 'editRoom'])->name('hotels.rooms.edit');
        Route::put('rooms/{room}', [HotelManagerController::class, 'updateRoom'])->name('hotels.rooms.update');
        Route::delete('rooms/{room}', [HotelManagerController::class, 'destroyRoom'])->name('hotels.rooms.destroy');
        
        // Gestion des photos des chambres
        Route::post('rooms/{room}/photos', [HotelManagerController::class, 'storeRoomPhoto'])->name('hotels.rooms.photos.store');
        Route::delete('photos/{photo}', [HotelManagerController::class, 'destroyPhoto'])->name('hotels.photos.destroy');
        
        // Gestion des réservations
        Route::prefix('bookings')->name('hotels.bookings.')->group(function() {
            Route::get('/', [HotelBookingController::class, 'index'])->name('index');
            Route::get('/create', [HotelBookingController::class, 'create'])->name('create');
            Route::post('/', [HotelBookingController::class, 'store'])->name('store');
            Route::get('/{booking}', [HotelBookingController::class, 'show'])->name('show');
            Route::get('/{booking}/edit', [HotelBookingController::class, 'edit'])->name('edit');
            Route::put('/{booking}', [HotelBookingController::class, 'update'])->name('update');
            Route::delete('/{booking}', [HotelBookingController::class, 'destroy'])->name('destroy');
            
            // Mise à jour du statut d'une réservation
            Route::put('/{booking}/status', [HotelBookingController::class, 'updateStatus'])->name('update-status');
            
            // Export des réservations
            Route::get('/export', [HotelBookingController::class, 'export'])->name('export');
            
            // Facture
            Route::get('/{booking}/invoice', [HotelBookingController::class, 'invoice'])->name('invoice');
            Route::get('/{booking}/invoice/download', [HotelBookingController::class, 'downloadInvoice'])->name('invoice.download');
            
            // Envoi de confirmation
            Route::post('/{booking}/send-confirmation', [HotelBookingController::class, 'sendConfirmation'])->name('send-confirmation');
        });
        
        // Calendrier
        Route::get('calendar', [HotelManagerController::class, 'calendar'])->name('hotels.calendar');
        Route::get('calendar/events', [HotelManagerController::class, 'getCalendarEvents'])->name('hotels.calendar.events');
        
        // Rapports
        Route::get('reports', [HotelManagerController::class, 'reports'])->name('hotels.reports');
        
        // Gestion des clients
        Route::prefix('clients')->name('hotels.clients.')->group(function() {
            Route::get('/', [ClientController::class, 'index'])->name('index');
            Route::get('/create', [ClientController::class, 'create'])->name('create');
            Route::post('/', [ClientController::class, 'store'])->name('store');
            Route::get('/{client}', [ClientController::class, 'show'])->name('show');
            Route::get('/{client}/edit', [ClientController::class, 'edit'])->name('edit');
            Route::put('/{client}', [ClientController::class, 'update'])->name('update');
        });
        Route::get('reports/export', [HotelManagerController::class, 'exportReports'])->name('hotels.reports.export');
        
        // Paramètres
        Route::get('settings', [HotelManagerController::class, 'settings'])->name('hotels.settings');
        Route::put('settings', [HotelManagerController::class, 'updateSettings'])->name('hotels.settings.update');
    });
});
