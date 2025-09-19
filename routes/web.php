<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItineraryController;
use App\Http\Controllers\GuideDashboardController;
use App\Http\Controllers\AdminEventController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\RoleOnboardingController;
use App\Http\Controllers\RoleApplicationController;
use App\Http\Controllers\TouristHotelController;
use App\Http\Controllers\TouristEventController;
use App\Http\Controllers\EventCreatorController;
use App\Http\Controllers\HotelAgencyController;
use App\Http\Controllers\TicketTemplateController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\EventBookingController;
use App\Http\Controllers\TouristBookingController;
use App\Http\Controllers\AgencyBookingController;
use App\Http\Controllers\TaxiController;
use App\Http\Controllers\BusController;
use App\Http\Controllers\SiteManagerDashboardController;
use App\Http\Controllers\BusBookingController;
use App\Http\Controllers\FlightController;
use App\Http\Controllers\TravelAssistantController;
use App\Http\Controllers\TouristCalendarController;
use App\Http\Controllers\TouristSiteController;
use App\Http\Controllers\OrganizerProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SiteManagerSiteController;
use App\Http\Controllers\SiteManagerBookingController;

Route::get('/', function () {
    return view('welcome');
});

// Free AI Travel Assistant (public)
Route::get('/assistant', [TravelAssistantController::class, 'index'])->name('assistant.index');
Route::post('/assistant/plan', [TravelAssistantController::class, 'plan'])->name('assistant.plan');
Route::post('/assistant/ai', [TravelAssistantController::class, 'aiSuggest'])->name('assistant.ai');
Route::get('/assistant/ai/stream', [TravelAssistantController::class, 'aiStream'])->name('assistant.ai.stream');
Route::get('/assistant/export', [TravelAssistantController::class, 'export'])->name('assistant.export');
Route::get('/assistant/preview', [TravelAssistantController::class, 'preview'])->name('assistant.preview');
Route::match(['get','post'],'/assistant/add', [TravelAssistantController::class, 'addItem'])->name('assistant.add');
Route::get('/assistant/recent', [TravelAssistantController::class, 'recent'])->name('assistant.recent');
Route::get('/assistant/options', [TravelAssistantController::class, 'options'])->name('assistant.options');

// Legal (public)
Route::view('/legal/terms', 'legal.terms')->name('legal.terms');
Route::view('/legal/privacy', 'legal.privacy')->name('legal.privacy');

// Public ticket preview (QR scan)
Route::get('/t/{uuid}', [TicketController::class, 'showByUuid'])->name('tickets.show.uuid');
Route::get('/t/{uuid}/download', [TicketController::class, 'downloadByUuid'])->name('tickets.download.uuid');

// Explore Map
Route::get('/explore', [App\Http\Controllers\MapController::class, 'explore'])->name('explore.map');

// Public Event Catalog & Details
Route::get('/events', [TouristEventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [TouristEventController::class, 'show'])->name('events.show');

// Public Tourist Sites Catalog & Details
Route::get('/sites', [TouristSiteController::class, 'index'])->name('sites.index');
Route::get('/sites/{site}', [TouristSiteController::class, 'show'])->name('sites.show');
Route::post('/sites/{site}/contact-guide', [TouristSiteController::class, 'contactGuide'])->name('sites.contact');

// Public Event Booking
Route::get('/events/{event}/book', [EventBookingController::class, 'create'])->middleware(['auth','active','tourist'])->name('bookings.create');
Route::post('/events/{event}/book', [EventBookingController::class, 'store'])->middleware(['auth','active','tourist'])->name('bookings.store');
Route::get('/bookings/{booking}', [EventBookingController::class, 'show'])->name('bookings.show');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'active'])->name('dashboard');

Route::middleware(['auth','active'])->group(function () {
    // Routes pour les gestionnaires de sites
    Route::prefix('site-manager')->name('site-manager.')->middleware(['site_manager'])->group(function () {
        // Tableau de bord
        Route::get('/dashboard', [SiteManagerDashboardController::class, 'index'])->name('dashboard');
        
        // Gestion des sites
        Route::resource('sites', 'App\Http\Controllers\SiteManagerSiteController');
        Route::post('sites/{site}/toggle-status', [SiteManagerSiteController::class, 'toggleStatus'])->name('sites.toggle-status');
        
        // Gestion des réservations
        Route::get('bookings', [SiteManagerBookingController::class, 'index'])->name('bookings.index');
        Route::get('bookings/{booking}', [SiteManagerBookingController::class, 'show'])->name('bookings.show');
        Route::put('bookings/{booking}/status', [SiteManagerBookingController::class, 'updateStatus'])->name('bookings.update-status');
        Route::get('calendar', [SiteManagerBookingController::class, 'calendar'])->name('calendar');
        Route::get('calendar/events', [SiteManagerBookingController::class, 'calendarEvents'])->name('calendar.events');
        Route::get('bookings/export', [SiteManagerBookingController::class, 'export'])->name('bookings.export');
        
        // Profil
        Route::get('/profile', [SiteManagerProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [SiteManagerProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [SiteManagerProfileController::class, 'updatePassword'])->name('profile.password.update');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // User notifications (common to all roles)
    Route::get('/notifications', [\App\Http\Controllers\UserNotificationController::class, 'index'])->name('user.notifications.index');
    Route::post('/notifications/{notification}/read', [\App\Http\Controllers\UserNotificationController::class, 'markRead'])->name('user.notifications.read');

    // Tourist web flow
    Route::get('/tourist/plan', [ItineraryController::class, 'create'])->name('tourist.plan');
    Route::post('/tourist/plan', [ItineraryController::class, 'store'])->name('tourist.plan.store');
    Route::get('/tourist/itinerary', [ItineraryController::class, 'show'])->name('tourist.itinerary');

    // Tourist Dashboard
    Route::get('/tourist/dashboard', [\App\Http\Controllers\TouristDashboardController::class, 'index'])->name('tourist.dashboard');
    Route::get('/tourist/community', [\App\Http\Controllers\Community\CommunityPostController::class, 'index'])->name('tourist.community');

    // Tourist Calendar
    Route::get('/tourist/calendar', [TouristCalendarController::class, 'index'])->name('tourist.calendar');
    Route::get('/tourist/calendar/feed', [TouristCalendarController::class, 'feed'])->name('tourist.calendar.feed');

    // Tourist: Hotels & Events
    Route::get('/tourist/hotels', [TouristHotelController::class, 'index'])->name('tourist.hotels.index');
    Route::get('/tourist/hotels/{hotel}', [TouristHotelController::class, 'show'])->name('tourist.hotels.show');
    Route::post('/tourist/rooms/{room}/book', [TouristHotelController::class, 'book'])->middleware('tourist')->name('tourist.rooms.book');

    Route::get('/tourist/events', [TouristEventController::class, 'index'])->name('tourist.events.index');
    Route::get('/tourist/events/{event}', [TouristEventController::class, 'show'])->name('tourist.events.show');
    // Back-compat: redirect old tourist booking endpoint to new public booking flow
    Route::match(['get','post'],'/tourist/events/{event}/book', function(\App\Models\Event $event) {
        return redirect()->route('bookings.create', $event);
    })->name('tourist.events.book');

    // Tourist: Mes réservations
    Route::get('/tourist/bookings', [TouristBookingController::class, 'index'])->name('tourist.bookings.index');
    Route::get('/tourist/bookings/{booking}', [TouristBookingController::class, 'show'])->name('tourist.bookings.show');
    Route::post('/tourist/bookings/{booking}/cancel', [TouristBookingController::class, 'cancel'])->name('tourist.bookings.cancel');

    // Routes pour les gestionnaires de sites
    Route::prefix('site-manager')->name('site-manager.')->middleware(['auth', 'active', 'site_manager'])->group(function () {
        // Tableau de bord
        Route::get('/dashboard', [SiteManagerDashboardController::class, 'index'])->name('dashboard');
        
        // Profil
        Route::get('/profile', [\App\Http\Controllers\SiteManagerProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [\App\Http\Controllers\SiteManagerProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [\App\Http\Controllers\SiteManagerProfileController::class, 'updatePassword'])->name('profile.password.update');
        
        // Notifications
        Route::get('/notifications', [\App\Http\Controllers\SiteManagerNotificationController::class, 'index'])->name('notifications.index');
        Route::put('/notifications/preferences', [\App\Http\Controllers\SiteManagerNotificationController::class, 'updatePreferences'])->name('notifications.preferences.update');
        Route::post('/notifications/mark-all-read', [\App\Http\Controllers\SiteManagerNotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
        Route::post('/notifications/{notification}/mark-read', [\App\Http\Controllers\SiteManagerNotificationController::class, 'markAsRead'])->name('notifications.mark-read');
        Route::post('/notifications/subscribe-push', [\App\Http\Controllers\SiteManagerNotificationController::class, 'subscribePush'])->name('notifications.push.subscribe');
        
        // Paramètres
        Route::get('/settings/dashboard', [\App\Http\Controllers\SiteManagerSettingsController::class, 'index'])->name('settings.dashboard');
        Route::put('/settings/dashboard', [\App\Http\Controllers\SiteManagerSettingsController::class, 'update'])->name('settings.dashboard.update');
        Route::post('/settings/dashboard/reset', [\App\Http\Controllers\SiteManagerSettingsController::class, 'resetToDefaults'])->name('settings.dashboard.reset');
        Route::put('/settings/theme', [\App\Http\Controllers\SiteManagerSettingsController::class, 'updateTheme'])->name('settings.theme.update');
        Route::put('/settings/timezone', [\App\Http\Controllers\SiteManagerSettingsController::class, 'updateTimezone'])->name('settings.timezone.update');
        Route::get('/settings/export', [\App\Http\Controllers\SiteManagerSettingsController::class, 'export'])->name('settings.export');
    });

    // Routes pour les guides
    Route::prefix('guide')->name('guide.')->middleware(['auth', 'active', 'guide'])->group(function () {
        Route::get('/dashboard', [GuideDashboardController::class, 'index'])->name('dashboard');
        Route::post('/availability', [GuideDashboardController::class, 'updateAvailability'])->name('availability.update');
        
        // Routes pour la messagerie des guides
        Route::get('/messages', [GuideDashboardController::class, 'messages'])->name('messages.index');
        Route::get('/messages/{contact}', [GuideDashboardController::class, 'showMessage'])->name('messages.show');
        Route::post('/messages/{contact}/read', [GuideDashboardController::class, 'markAsRead'])->name('messages.read');

        Route::get('/profile', [GuideDashboardController::class, 'editProfile'])->name('profile.edit');
        Route::post('/profile', [GuideDashboardController::class, 'updateProfile'])->name('profile.update');
    });

    // Admin web flow (protected)
    Route::prefix('admin')->name('admin.')->middleware(['admin'])->group(function () {
        // Dashboard
        Route::get('/', [\App\Http\Controllers\AdminDashboardController::class, 'index'])->name('dashboard');

        // Events
        Route::get('/events', [AdminEventController::class, 'index'])->name('events');
        Route::post('/events/alerts', [AdminEventController::class, 'sendFestivalAlert'])->name('events.alerts');

        // Users management
        Route::get('/users', [AdminUserController::class, 'users'])->name('users');
        Route::post('/users/{id}/role', [AdminUserController::class, 'updateRole'])->name('users.role');
        Route::post('/users/{id}/reset-onboarding', [AdminUserController::class, 'resetOnboarding'])->name('users.reset_onboarding');
        Route::post('/users/{id}/activate', [AdminUserController::class, 'activate'])->name('users.activate');
        Route::post('/users/{id}/deactivate', [AdminUserController::class, 'deactivate'])->name('users.deactivate');
        Route::get('/users/{id}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
        Route::patch('/users/{id}', [AdminUserController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [AdminUserController::class, 'destroy'])->name('users.destroy');

        // Role applications (review)
        Route::get('/role-applications', [AdminUserController::class, 'index'])->name('role_apps');
        Route::post('/role-applications/{id}/approve', [AdminUserController::class, 'approve'])->name('role_apps.approve');
        Route::post('/role-applications/{id}/reject', [AdminUserController::class, 'reject'])->name('role_apps.reject');

        // Moderation
        Route::get('/moderation', [\App\Http\Controllers\AdminModerationController::class, 'index'])->name('moderation');
        Route::post('/moderation/restaurants/{restaurant}/toggle', [\App\Http\Controllers\AdminModerationController::class, 'toggleRestaurant'])->name('moderation.restaurant.toggle');
        Route::post('/moderation/dishes/{dish}/toggle', [\App\Http\Controllers\AdminModerationController::class, 'toggleDish'])->name('moderation.dish.toggle');
        
        // Gestion des publications de la communauté
        Route::prefix('community/posts')->name('community.posts.')->group(function () {
            // Liste des publications
            Route::get('/', [\App\Http\Controllers\Community\CommunityPostController::class, 'adminIndex'])->name('index');
            
            // Publications désactivées
            Route::get('/trashed', [\App\Http\controllers\Community\CommunityPostController::class, 'trashed'])->name('trashed');
            
            // Désactiver une publication
            Route::post('/{post}/deactivate', [\App\Http\Controllers\Community\CommunityPostController::class, 'deactivate'])
                ->name('deactivate')
                ->where('post', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');
                
            // Réactiver une publication
            Route::post('/{post}/activate', [\App\Http\Controllers\Community\CommunityPostController::class, 'activate'])
                ->name('activate')
                ->where('post', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');
                
            // Supprimer définitivement une publication
            Route::delete('/{post}/force-delete', [\App\Http\Controllers\Community\CommunityPostController::class, 'forceDelete'])
                ->name('force-delete')
                ->where('post', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');
        });

        // Ads
        Route::get('/ads', [\App\Http\Controllers\AdminAdController::class, 'index'])->name('ads');
        Route::post('/ads', [\App\Http\Controllers\AdminAdController::class, 'store'])->name('ads.store');
        Route::post('/ads/{ad}/toggle', [\App\Http\Controllers\AdminAdController::class, 'toggle'])->name('ads.toggle');

        // Notifications
        Route::get('/notifications', [\App\Http\Controllers\AdminNotificationController::class, 'index'])->name('notifications');
        Route::post('/notifications/send', [\App\Http\Controllers\AdminNotificationController::class, 'sendToRole'])->name('notifications.send');
    });

    // Role dashboards (auth protected; each controller enforces role)
    Route::middleware('auth')->group(function () {
        Route::get('/organizer/dashboard', [\App\Http\Controllers\OrganizerDashboardController::class, 'index'])->name('organizer.dashboard');
        
        // Routes pour les gestionnaires d'hôtel
        Route::prefix('hotel-manager')->name('hotel-manager.')->group(function () {
            Route::get('/dashboard', [\App\Http\Controllers\HotelManagerController::class, 'dashboard'])->name('dashboard');
            Route::get('/hotels', [\App\Http\Controllers\HotelManagerController::class, 'index'])->name('hotels.index');
            Route::get('/hotels/create', [\App\Http\Controllers\HotelManagerController::class, 'create'])->name('hotels.create');
            Route::post('/hotels', [\App\Http\Controllers\HotelManagerController::class, 'store'])->name('hotels.store');
            Route::get('/hotels/{hotel}', [\App\Http\Controllers\HotelManagerController::class, 'show'])->name('hotels.show');
            Route::get('/hotels/{hotel}/edit', [\App\Http\Controllers\HotelManagerController::class, 'edit'])->name('hotels.edit');
            Route::patch('/hotels/{hotel}', [\App\Http\Controllers\HotelManagerController::class, 'update'])->name('hotels.update');
            Route::get('/hotels/{hotel}/rooms/create', [\App\Http\Controllers\HotelManagerController::class, 'createRoom'])->name('rooms.create');
            Route::post('/hotels/{hotel}/rooms', [\App\Http\Controllers\HotelManagerController::class, 'store'])->name('rooms.store');
            Route::get('/rooms', [\App\Http\Controllers\HotelManagerController::class, 'rooms'])->name('rooms.index');
            Route::get('/bookings', [\App\Http\Controllers\HotelManagerController::class, 'bookings'])->name('bookings.index');
            Route::get('/calendar', [\App\Http\Controllers\HotelManagerController::class, 'calendar'])->name('calendar');
            Route::get('/reports', [\App\Http\Controllers\HotelManagerController::class, 'reports'])->name('reports.index');
            Route::post('/hotels/{hotel}/toggle-status', [\App\Http\Controllers\HotelManagerController::class, 'toggleStatus'])->name('hotels.toggle-status');
            Route::post('/hotels/{hotel}/toggle-featured', [\App\Http\Controllers\HotelManagerController::class, 'toggleFeatured'])->name('hotels.toggle-featured');
        });
        
        Route::get('/driver/dashboard', [\App\Http\Controllers\DriverDashboardController::class, 'index'])->name('driver.dashboard');
    });

    // Role Onboarding
    Route::get('/onboarding', [RoleOnboardingController::class, 'start'])->name('onboarding.start');
    Route::post('/onboarding', [RoleOnboardingController::class, 'store'])->name('onboarding.store');

    // Devenir partenaire (demande de rôle)
    Route::get('/devenir-partenaire', [RoleApplicationController::class, 'create'])->name('partner.apply');
    Route::post('/devenir-partenaire', [RoleApplicationController::class, 'store'])->name('partner.apply.store');

    // Event Creator (Organisateur)
    Route::get('/organizer/events', [EventCreatorController::class, 'index'])->name('organizer.events.index');
    Route::get('/organizer/events/create', [EventCreatorController::class, 'create'])->name('organizer.events.create');
    Route::post('/organizer/events', [EventCreatorController::class, 'store'])->name('organizer.events.store');
    Route::get('/organizer/events/{event}/edit', [EventCreatorController::class, 'edit'])->name('organizer.events.edit');
    Route::patch('/organizer/events/{event}', [EventCreatorController::class, 'update'])->name('organizer.events.update');
    Route::delete('/organizer/events/{event}', [EventCreatorController::class, 'destroy'])->name('organizer.events.destroy');

    // Organizer: Event Creation Wizard (multi-étapes)
    Route::prefix('organizer/events/wizard')->name('organizer.events.wizard.')->group(function () {
        Route::get('/', [\App\Http\Controllers\EventWizardController::class, 'start'])->name('start');
        Route::get('/{step}', [\App\Http\Controllers\EventWizardController::class, 'show'])->name('show');
        Route::post('/{step}', [\App\Http\Controllers\EventWizardController::class, 'submit'])->name('submit');
        Route::get('/api/suggestions', [\App\Http\Controllers\EventWizardController::class, 'suggestions'])->name('suggestions');
        Route::post('/choose/{template}', [\App\Http\Controllers\EventWizardController::class, 'choosePoster'])
            ->whereUuid('template')->name('choose');
        Route::post('/choose-external', [\App\Http\Controllers\EventWizardController::class, 'chooseExternal'])->name('choose_external');
    });

    // Organizer: Ticket Templates
    Route::get('/organizer/templates', [TicketTemplateController::class, 'index'])->name('organizer.templates.index');
    Route::get('/organizer/templates/pdf/new', [TicketTemplateController::class, 'createPdf'])->name('organizer.templates.pdf.create');
    Route::post('/organizer/templates/pdf', [TicketTemplateController::class, 'storePdf'])->name('organizer.templates.pdf.store');
    Route::get('/organizer/templates/create', [TicketTemplateController::class, 'create'])->name('organizer.templates.create');
    Route::post('/organizer/templates', [TicketTemplateController::class, 'store'])->name('organizer.templates.store');
    Route::get('/organizer/templates/{template}/edit', [TicketTemplateController::class, 'edit'])->name('organizer.templates.edit');
    Route::patch('/organizer/templates/{template}', [TicketTemplateController::class, 'update'])->name('organizer.templates.update');
    Route::get('/organizer/templates/{template}/preview', [TicketTemplateController::class, 'preview'])->name('organizer.templates.preview');
    Route::get('/organizer/templates/{template}/download', [TicketTemplateController::class, 'download'])->name('organizer.templates.download');
    Route::get('/organizer/templates/{template}/overlay', [TicketTemplateController::class, 'editor'])->name('organizer.templates.overlay');
    Route::post('/organizer/templates/{template}/overlay', [TicketTemplateController::class, 'saveOverlay'])->name('organizer.templates.overlay.save');

    // Organizer: Sales (Event bookings)
    Route::get('/organizer/sales', [EventCreatorController::class, 'salesIndex'])->name('organizer.sales.index');
    Route::get('/organizer/sales/export', [EventCreatorController::class, 'salesExport'])->name('organizer.sales.export');
    Route::get('/organizer/sales/{booking}', [EventCreatorController::class, 'salesShow'])->name('organizer.sales.show');

    // Organizer: Profile Logo
    Route::get('/organizer/profile/logo', [OrganizerProfileController::class, 'editLogo'])->name('organizer.profile.logo.edit');
    Route::post('/organizer/profile/logo', [OrganizerProfileController::class, 'updateLogo'])->name('organizer.profile.logo.update');

    // Hotel Agency (Gestionnaire d'hôtel)
    Route::get('/agency/hotels', [HotelAgencyController::class, 'index'])->name('agency.hotels.index');
    Route::get('/agency/hotels/create', [HotelAgencyController::class, 'create'])->name('agency.hotels.create');
    Route::post('/agency/hotels', [HotelAgencyController::class, 'store'])->name('agency.hotels.store');
    Route::get('/agency/hotels/{hotel}', [HotelAgencyController::class, 'show'])->name('agency.hotels.show');
    Route::get('/agency/hotels/{hotel}/edit', [HotelAgencyController::class, 'edit'])->name('agency.hotels.edit');
    Route::patch('/agency/hotels/{hotel}', [HotelAgencyController::class, 'update'])->name('agency.hotels.update');
    Route::get('/agency/hotels/{hotel}/rooms/create', [HotelAgencyController::class, 'createRoom'])->name('agency.rooms.create');
    Route::post('/agency/hotels/{hotel}/rooms', [HotelAgencyController::class, 'storeRoom'])->name('agency.rooms.store');
    
    // Gestion des clients de l'hôtel
    Route::get('/agency/hotels/{hotel}/customers', [\App\Http\Controllers\HotelManager\CustomerController::class, 'index'])->name('agency.hotels.customers.index');
    Route::get('/agency/hotels/{hotel}/customers/{user}', [\App\Http\Controllers\HotelManager\CustomerController::class, 'show'])->name('agency.hotels.customers.show');

    // Agency: Reservations management
    Route::get('/agency/reservations', [AgencyBookingController::class, 'index'])->name('agency.reservations.index');
    Route::get('/agency/reservations/{booking}', [AgencyBookingController::class, 'show'])->name('agency.reservations.show');
    Route::post('/agency/reservations/{booking}/status', [AgencyBookingController::class, 'updateStatus'])->name('agency.reservations.status');

    // Transport: Taxi
    Route::get('/transport/taxis', [TaxiController::class, 'index'])->name('transport.taxi.index');
    Route::get('/transport/taxis/{taxi}/ride', [TaxiController::class, 'createRide'])->name('transport.taxi.ride.create');
    Route::post('/transport/taxis/{taxi}/ride', [TaxiController::class, 'storeRide'])->name('transport.taxi.ride.store');
    Route::get('/transport/rides/{ride}', [TaxiController::class, 'showRide'])->name('transport.taxi.ride.show');

    // Transport: Bus
    Route::get('/transport/bus', [BusController::class, 'index'])->name('transport.bus.index');
    Route::get('/transport/bus/trips/{trip}', [BusController::class, 'show'])->name('transport.bus.show');
    Route::get('/transport/bus/trips/{trip}/book', [BusBookingController::class, 'create'])->middleware('tourist')->name('transport.bus.book');
    Route::post('/transport/bus/trips/{trip}/book', [BusBookingController::class, 'store'])->middleware('tourist')->name('transport.bus.book.store');
    Route::get('/transport/bus/bookings/{booking}', [BusBookingController::class, 'show'])->name('transport.bus.booking.show');

    // Air Travel: Flights to Burkina Faso
    Route::prefix('air')->name('air.')->group(function() {
        Route::get('/flights', [FlightController::class, 'index'])->name('flights.index');
        Route::get('/flights/{flight}', [FlightController::class, 'show'])->name('flights.show');
        Route::get('/flights/{flight}/book', [FlightController::class, 'book'])->middleware('tourist')->name('flights.book');
        Route::post('/flights/{flight}/book', [FlightController::class, 'storeBooking'])->middleware('tourist')->name('flights.book.store');
        Route::get('/bookings/{booking}', [FlightController::class, 'showBooking'])->name('bookings.show');
        Route::delete('/bookings/{booking}', [FlightController::class, 'destroyBooking'])->name('bookings.destroy');

        // Flight round-trip wizard and bookings list
        Route::get('/book', [FlightController::class, 'wizard'])->name('flights.wizard');
        Route::post('/book/select', [FlightController::class, 'selectLeg'])->name('flights.select');
        Route::get('/book/details', [FlightController::class, 'details'])->name('flights.details');
        Route::get('/bookings', [FlightController::class, 'bookingsIndex'])->name('bookings.index');
        Route::get('/bookings/export', [FlightController::class, 'exportBookings'])->name('bookings.export');
    });

    // Food: Restaurants
    Route::prefix('food')->name('food.')->group(function() {
        Route::get('/restaurants', [\App\Http\Controllers\RestaurantController::class, 'index'])->name('restaurants.index');
        Route::get('/restaurants/{restaurant}', [\App\Http\Controllers\RestaurantController::class, 'show'])->name('restaurants.show');
        Route::get('/restaurants/{restaurant}/reserve', [\App\Http\Controllers\RestaurantController::class, 'createReservation'])->name('restaurants.reserve');
        Route::post('/restaurants/{restaurant}/reserve', [\App\Http\Controllers\RestaurantController::class, 'storeReservation'])->name('restaurants.reserve.store');
        Route::get('/reservations/{reservation}', [\App\Http\Controllers\RestaurantController::class, 'showReservation'])->name('restaurants.reservations.show');
        Route::get('/reservations', [\App\Http\Controllers\RestaurantController::class, 'myReservations'])->middleware(['auth'])->name('restaurants.reservations.index');

        // Dishes (details & delivery orders)
        Route::get('/dishes/{dish}', [\App\Http\Controllers\DishController::class, 'show'])->name('dishes.show');
        Route::get('/dishes/{dish}/order', [\App\Http\Controllers\DishController::class, 'orderCreate'])->middleware(['auth','active','tourist'])->name('dishes.orders.create');
        Route::post('/dishes/{dish}/order', [\App\Http\Controllers\DishController::class, 'orderStore'])->middleware(['auth','active','tourist'])->name('dishes.orders.store');
        Route::get('/orders/{order}', [\App\Http\Controllers\DishController::class, 'orderShow'])->middleware(['auth','active'])->name('dishes.orders.show');

        // Owner management (Restaurant owner)
        Route::prefix('owner')->name('owner.')->group(function() {
            // Edit restaurant profile & media
            Route::get('/restaurant', [\App\Http\Controllers\RestaurantOwnerController::class, 'editRestaurant'])->name('restaurant.edit');
            Route::post('/restaurant', [\App\Http\Controllers\RestaurantOwnerController::class, 'updateRestaurant'])->name('restaurant.update');

            // Dishes management
            Route::get('/dishes', [\App\Http\Controllers\RestaurantOwnerController::class, 'dishesIndex'])->name('dishes.index');
            Route::post('/dishes', [\App\Http\Controllers\RestaurantOwnerController::class, 'dishesStore'])->name('dishes.store');
            Route::get('/dishes/{dish}/edit', [\App\Http\Controllers\RestaurantOwnerController::class, 'dishesEdit'])->name('dishes.edit');
            Route::patch('/dishes/{dish}', [\App\Http\Controllers\RestaurantOwnerController::class, 'dishesUpdate'])->name('dishes.update');
            Route::delete('/dishes/{dish}', [\App\Http\Controllers\RestaurantOwnerController::class, 'dishesDestroy'])->name('dishes.destroy');
        });
    });

    // [moved] Free AI Travel Assistant is public
});

require __DIR__.'/auth.php';

// Inclure les routes de la communauté
require __DIR__.'/community.php';

require __DIR__.'/api.php';

// Routes de la communauté
Route::middleware(['auth'])->group(function () {
    // Routes pour les publications
    Route::get('community/posts', 'App\Http\Controllers\Community\CommunityPostController@index')
        ->name('community.posts.index');
    Route::get('community/posts/create', 'App\Http\Controllers\Community\CommunityPostController@create')
        ->name('community.posts.create');
    Route::post('community/posts', 'App\Http\Controllers\Community\CommunityPostController@store')
        ->name('community.posts.store');
    Route::get('community/posts/{post}', 'App\Http\Controllers\Community\CommunityPostController@show')
        ->name('community.posts.show');
    Route::get('community/posts/{post}/edit', 'App\Http\Controllers\Community\CommunityPostController@edit')
        ->name('community.posts.edit');
    Route::put('community/posts/{post}', 'App\Http\Controllers\Community\CommunityPostController@update')
        ->name('community.posts.update');
    Route::delete('community/posts/{post}', 'App\Http\Controllers\Community\CommunityPostController@destroy')
        ->name('community.posts.destroy');
    
    // Routes pour les commentaires
    Route::post('community/posts/{post}/comments', 'App\Http\Controllers\Community\CommentController@store')
        ->name('community.comments.store');
    Route::delete('community/comments/{comment}', 'App\Http\Controllers\Community\CommentController@destroy')
        ->name('community.comments.destroy');
    
    // Routes pour les likes
    Route::post('community/posts/{post}/like', 'App\Http\Controllers\Community\LikeController@toggleLike')
        ->name('community.posts.like');
});
