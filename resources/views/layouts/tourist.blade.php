<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Discover_BF — Espace Touriste</title>
  <meta name="description" content="Tableau de bord touriste">
  <meta name="keywords" content="tourisme, burkina, voyage">

  <!-- Favicons -->
  <link href="{{ asset('assets/img/favicon.png') }}" rel="icon">
  <link href="{{ asset('assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&family=Raleway:wght@400;600;700;800;900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/aos/aos.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="{{ asset('assets/css/main.css') }}" rel="stylesheet">

  <style>
    :root{
      --tourist-grad-start:#ff7e5f; /* adapter à la maquette si besoin */
      --tourist-grad-end:#feb47b;
    }
    body { background: #f8f9fb; }
    body.has-fixed-tourist { padding-top: 80px; }
    @media (min-width: 1200px) { body.has-fixed-tourist { padding-top: 92px; } }
    /* Left fixed sidenav spacing on desktop */
    @media (min-width: 992px){ body.has-fixed-tourist { padding-left: 260px; } }
    .t-header{ background: linear-gradient(90deg, var(--tourist-grad-start) 0%, var(--tourist-grad-end) 100%); color:#fff; z-index:1030; }
    .t-header .brand { font-weight:800; letter-spacing:.3px; }
    .t-nav a{ color: rgba(255,255,255,.95); text-decoration:none; }
    .t-nav a:hover{ color:#212529; opacity:.9; }
    .t-cta.btn-light{ color:#222; }
    .container-main{ padding-top: 18px; padding-bottom: 24px; }
    /* Logo sizing for professional look */
    .logo-img{ height:36px; width:auto; object-fit:contain; display:block; }
    @media (min-width: 1200px){ .logo-img{ height:44px; } }

    /* Utilities for cream panels/buttons used in tourist air pages */
    .panel-cream{
      background: #fff7e6; /* light cream */
      border: 1px solid #ffe1ad;
      box-shadow: 0 6px 18px rgba(0,0,0,.06);
      transition: box-shadow .2s ease, transform .2s ease, border-color .2s ease;
      position: relative; /* create stacking context for z-index */
      z-index: 0;
      overflow: hidden; /* evite que le contenu (dont la barre d'actions) dépasse sur d'autres cartes */
    }
    .panel-cream:hover{ box-shadow: 0 10px 24px rgba(0,0,0,.10); transform: translateY(-2px); border-color:#ffdaa0; z-index: 2; }
    .rounded-20{ border-radius:20px !important; }
    .btn-cream{
      background: #fff0cc;
      border: 1px solid #ffd88a;
      color: #8a5a00;
      transition: background-color .15s ease, border-color .15s ease, color .15s ease, box-shadow .15s ease;
    }
    .btn-cream:hover{ background:#ffe6ad; border-color:#ffc766; color:#6b4400; }
    .btn-cream:focus{ outline:0; box-shadow: 0 0 0 .2rem rgba(255, 200, 100, .35); }
    .btn-cream:active{ background:#ffd88a; border-color:#f5b94f; }
    .btn-orange{
      background: #ff7e5f;
      border-color: #ff7e5f;
      color: #fff;
      transition: background-color .15s ease, border-color .15s ease, color .15s ease, box-shadow .15s ease;
    }
    .btn-orange:hover{ background:#ff6a47; border-color:#ff6a47; color:#fff; }
    .btn-orange:focus{ outline:0; box-shadow: 0 0 0 .2rem rgba(255, 126, 95, .35); }
    .btn-orange:active{ background:#ff5c34; border-color:#ff5c34; }
    .badge-soft{ background:#f6f7f9; border:1px solid #e9ecef; color:#6c757d; }
    .text-orange{ color:#ff7e5f !important; }

    /* Status badges */
    .badge-status{ border:1px solid transparent; font-weight:600; letter-spacing:.2px; }
    .badge-status.confirmed{ background:#e6fff2; border-color:#b6efcf; color:#1b8f4d; }
    .badge-status.pending{ background:#fff9e6; border-color:#ffe4a3; color:#a86b00; }
    .badge-status.cancelled{ background:#ffefef; border-color:#ffcbcb; color:#b23a3a; }

    /* Images */
    .img-elevate{ box-shadow: 0 8px 22px rgba(0,0,0,.08); transition: transform .2s ease, box-shadow .2s ease; }
    .img-elevate:hover{ transform: translateY(-2px); box-shadow: 0 12px 28px rgba(0,0,0,.12); }

    /* (removed) floating quick actions */

    /* Left fixed sidenav */
    .t-sidenav{ position: fixed; top: 72px; left: 0; bottom: 0; width: 240px; padding: 12px 12px 24px; border-right: 1px solid #eee; background:#fff; z-index: 1029; overflow-y:auto; }
    @media (min-width: 1200px){ .t-sidenav{ top: 84px; } }
    @media (max-width: 991.98px){ .t-sidenav{ display:none; } }
    .t-sidenav .title{ font-weight:700; color:#333; letter-spacing:.2px; }
    .t-sidenav a{ display:flex; align-items:center; gap:8px; padding:8px 10px; border-radius:10px; color:#495057; text-decoration:none; }
    .t-sidenav a:hover{ background:#fff7e6; color:#8a5a00; }
    .t-sidenav a.active{ background:#ffe6ad; color:#6b4400; border:1px solid #ffd88a; }
  </style>
  @stack('styles')
</head>
<body class="has-fixed-tourist">
  <header class="t-header py-3 shadow-sm fixed-top">
    <div class="container d-flex align-items-center justify-content-between">
      <a href="{{ route('tourist.dashboard') }}" class="brand h4 mb-0 text-white text-decoration-none"><img src="{{ asset('assets/img/Logo_Discover_BF_blanc.png') }}" alt="Logo" class="logo-img"></a>
      <!-- Mobile sidenav toggle -->
      <button class="btn btn-light btn-sm d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidenav" aria-controls="mobileSidenav">
        <i class="bi bi-list"></i>
      </button>
      <nav class="t-nav d-none d-md-flex align-items-center gap-3">
        <a href="{{ route('tourist.dashboard') }}"><i class="bi bi-house me-1"></i>Accueil</a>
        <a href="{{ route('tourist.itinerary') }}"><i class="bi bi-map me-1"></i>Itinéraire</a>
        <a href="{{ route('events.index') }}"><i class="bi bi-calendar-event me-1"></i>Évènements</a>
        <a href="{{ route('assistant.index') }}"><i class="bi bi-robot me-1"></i>Assistant</a>
        @if(auth()->check() && ((auth()->user()->role ?? null) === 'event_organizer'))
          <div class="vr mx-1 opacity-50"></div>
          <a href="{{ route('organizer.events.index') }}"><i class="bi bi-collection me-1"></i>Mes évènements</a>
          <!-- <a href="{{ route('organizer.events.wizard.start') }}"><i class="bi bi-magic me-1"></i>Wizard</a> -->
          <a href="{{ route('organizer.templates.index') }}"><i class="bi bi-ticket-perforated me-1"></i>Modèles</a>
          <a href="{{ route('organizer.sales.index') }}"><i class="bi bi-receipt me-1"></i>Ventes</a>
          <!-- <a href="{{ route('organizer.profile.logo.edit') }}"><i class="bi bi-award me-1"></i>Profil</a> -->
        @endif
        @if(auth()->check() && ((auth()->user()->role ?? null) === 'guide'))
          @php $__newGuideMsg = \App\Models\GuideContact::where('guide_id', auth()->id())->where('status','new')->count(); @endphp
          <a href="{{ route('guide.dashboard') }}"><i class="bi bi-compass me-1"></i>Guide</a>
          <a href="{{ route('guide.messages.index') }}"><i class="bi bi-inbox me-1"></i>Messages
            @if($__newGuideMsg)
              <span class="badge bg-warning text-dark ms-1">{{ $__newGuideMsg }}</span>
            @endif
          </a>
        @endif
      </nav>
      <div class="d-flex align-items-center gap-2">
        <a href="{{ route('profile.edit') }}" class="btn btn-light btn-sm t-cta"><i class="bi bi-person"></i></a>
        <form method="POST" action="{{ route('logout') }}" class="m-0">
          @csrf
          <button class="btn btn-outline-light btn-sm" type="submit">Déconnexion</button>
        </form>
      </div>
    </div>
  </header>

  <!-- Left fixed side navigation (Quick Actions) -->
  <aside class="t-sidenav">
    @hasSection('tourist_sidenav')
      @yield('tourist_sidenav')
    @else
      <div class="mb-2 title small text-uppercase text-muted">Actions rapides</div>
      <nav class="vstack gap-1">
        @php
            $dashboardRoute = 'tourist.dashboard';
            $isDashboardActive = false;
            if (auth()->check()) {
                switch(auth()->user()->role) {
                    case 'guide':
                        $dashboardRoute = 'guide.dashboard';
                        $isDashboardActive = request()->routeIs('guide.dashboard');
                        break;
                    case 'event_organizer':
                        $dashboardRoute = 'organizer.events.index';
                        $isDashboardActive = request()->routeIs('organizer.events.index');
                        break;
                    default:
                        $isDashboardActive = request()->routeIs('tourist.dashboard');
                }
            } else {
                $isDashboardActive = request()->routeIs('tourist.dashboard');
            }
        @endphp
        <a href="{{ route($dashboardRoute) }}" class="{{ $isDashboardActive ? 'active' : '' }}"><i class="bi bi-speedometer2"></i> Tableau de bord</a>
        <a href="{{ route('assistant.index') }}" class="@if(request()->routeIs('assistant.*')) active @endif"><i class="bi bi-robot"></i> Planifier un voyage</a>
        <a href="{{ route('tourist.itinerary') }}" class="@if(request()->routeIs('tourist.itinerary')) active @endif"><i class="bi bi-map"></i> Itinéraire</a>
        <a href="{{ route('air.flights.index') }}" class="@if(request()->routeIs('air.flights.*')) active @endif"><i class="bi bi-airplane"></i> Vols</a>
        <a href="{{ route('sites.index') }}" class="@if(request()->routeIs('sites.*')) active @endif"><i class="bi bi-building"></i> Sites touristiques</a>
        <a href="{{ route('air.bookings.index') }}" class="@if(request()->routeIs('air.bookings.*')) active @endif"><i class="bi bi-journal-check"></i> Mes réservations de vol</a>
        <a href="{{ route('tourist.bookings.index') }}" class="@if(request()->routeIs('tourist.bookings.*')) active @endif"><i class="bi bi-journal-check"></i> Mes réservations</a>
        <a href="{{ route('tourist.calendar') }}" class="@if(request()->routeIs('tourist.calendar*')) active @endif"><i class="bi bi-calendar3"></i> Calendrier</a>
        <a href="{{ route('events.index') }}" class="@if(request()->routeIs('events.*')) active @endif"><i class="bi bi-calendar-event"></i> Évènements</a>
        <a href="{{ route('food.restaurants.index') }}" class="@if(request()->routeIs('food.restaurants.*')) active @endif"><i class="bi bi-egg-fried"></i> Restaurants</a>
        @if(auth()->check() && ((auth()->user()->role ?? null) === 'event_organizer'))
          <div class="mt-2 mb-1 small text-uppercase text-muted">Organisateur</div>
          <a href="{{ route('organizer.events.index') }}" class="@if(request()->routeIs('organizer.events.index')) active @endif"><i class="bi bi-collection"></i> Mes évènements</a>
          <a href="{{ route('organizer.events.wizard.start') }}" class="@if(request()->routeIs('organizer.events.wizard.*')) active @endif"><i class="bi bi-magic"></i> Wizard</a>
          <a href="{{ route('organizer.templates.index') }}" class="@if(request()->routeIs('organizer.templates.*')) active @endif"><i class="bi bi-ticket-perforated"></i> Modèles</a>
          <a href="{{ route('organizer.sales.index') }}" class="@if(request()->routeIs('organizer.sales.*')) active @endif"><i class="bi bi-receipt"></i> Ventes</a>
          <a href="{{ route('organizer.profile.logo.edit') }}" class="@if(request()->routeIs('organizer.profile.*')) active @endif"><i class="bi bi-award"></i> Profil</a>
        @endif
        @if(auth()->user() && method_exists(auth()->user(), 'isRestaurant') && auth()->user()->isRestaurant())
          <div class="mt-2 mb-1 small text-uppercase text-muted">Gérant</div>
          <a href="{{ route('food.owner.restaurant.edit') }}" class="@if(request()->routeIs('food.owner.restaurant.*')) active @endif"><i class="bi bi-shop"></i> Mon restaurant</a>
          <a href="{{ route('food.owner.dishes.index') }}" class="@if(request()->routeIs('food.owner.dishes.*')) active @endif"><i class="bi bi-egg-fried"></i> Mes plats</a>
        @endif
        @if(auth()->user() && ((method_exists(auth()->user(), 'isHotelManager') && auth()->user()->isHotelManager()) || ((auth()->user()->role ?? null) === 'hotel_manager')))
          <div class="mt-2 mb-1 small text-uppercase text-muted">Gestion hôtel</div>
          <a href="{{ route('agency.hotels.index') }}" class="@if(request()->routeIs('agency.hotels.index')) active @endif"><i class="bi bi-building"></i> Mes hôtels</a>
          <a href="{{ route('agency.hotels.create') }}" class="@if(request()->routeIs('agency.hotels.create')) active @endif"><i class="bi bi-plus-circle"></i> Ajouter un hôtel</a>
          <a href="{{ route('agency.reservations.index') }}" class="@if(request()->routeIs('agency.reservations.*')) active @endif"><i class="bi bi-journal-text"></i> Réservations</a>
        @endif
        <a class="@if(request()->routeIs('transport.taxi.index')) active @endif" href="{{ route('transport.taxi.index') }}"> <i class="bi bi-bus"></i> Réserver un taxi</a>
        <a class="@if(request()->routeIs('transport.bus.index')) active @endif" href="{{ route('transport.bus.index') }}"> <i class="bi bi-bus"></i> Réserver un bus</a>
        <a href="{{ route('profile.edit') }}" class="@if(request()->routeIs('profile.edit')) active @endif"><i class="bi bi-person"></i> Profil</a>
        <!-- <a class="@if(request()->routeIs('assistant.*')) active @endif" href="{{ route('assistant.index') }}">Planifier un voyage</a> -->
        <!-- <a class="@if(request()->routeIs('air.flights.wizard')) active @endif" href="{{ route('air.flights.wizard') }}">Réserver un vol</a> -->
        <a class="@if(request()->routeIs('tourist.hotels.index')) active @endif" href="{{ route('tourist.hotels.index') }}"> <i class="bi bi-building"></i> Réserver un séjour</a>
        <!-- <a class="@if(request()->routeIs('events.index')) active @endif" href="{{ route('events.index') }}"> <i class="bi bi-calendar-event"></i> Explorer la carte</a> -->
        <div class="@if(request()->routeIs('assistant.*')) active @endif">Assistant</div>
        <a class="@if(request()->routeIs('assistant.index')) active @endif" href="{{ route('assistant.index') }}"> <i class="bi bi-robot"></i> Assistant vocal</a>
        <a class="@if(request()->routeIs('tourist.community')) active @endif" href="{{ route('tourist.community') }}"> <i class="bi bi-people"></i> Communauté</a>
        @if(auth()->check() && ((auth()->user()->role ?? null) === 'guide'))
          <div class="mt-2 mb-1 small text-uppercase text-muted">Guide</div>
          @php $__newGuideMsg = \App\Models\GuideContact::where('guide_id', auth()->id())->where('status','new')->count(); @endphp
          <a href="{{ route('guide.dashboard') }}" class="@if(request()->routeIs('guide.dashboard')) active @endif"><i class="bi bi-compass"></i> Tableau de bord guide</a>
          <a href="{{ route('guide.messages.index') }}" class="@if(request()->routeIs('guide.messages.*')) active @endif"><i class="bi bi-inbox"></i> Messages
            @if($__newGuideMsg)
              <span class="badge bg-warning text-dark ms-1">{{ $__newGuideMsg }}</span>
            @endif
          </a>
          <a href="{{ route('guide.profile.edit') }}" class="@if(request()->routeIs('guide.profile.*')) active @endif"><i class="bi bi-person-badge"></i> Mon profil guide</a>
        @endif
      </nav>
    @endif
  </aside>

  <main class="container container-main">
    @yield('content')
  </main>

  <footer class="py-4">
    <div class="container d-flex justify-content-between small text-muted">
      <span>© {{ date('Y') }} Discover_BF</span>
      <span><a href="{{ route('legal.terms') }}" class="text-muted text-decoration-none">Conditions</a> · <a href="{{ route('legal.privacy') }}" class="text-muted text-decoration-none">Confidentialité</a></span>
    </div>
  </footer>

  <!-- Mobile Offcanvas Sidenav -->
  <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileSidenav" aria-labelledby="mobileSidenavLabel">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title" id="mobileSidenavLabel">Navigation</h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
      @hasSection('tourist_sidenav')
        @yield('tourist_sidenav')
      @else
        <nav class="vstack gap-1">
          @php
            $dashboardRoute = 'tourist.dashboard';
            $isDashboardActive = false;
            if (auth()->check()) {
                switch(auth()->user()->role) {
                    case 'guide':
                        $dashboardRoute = 'guide.dashboard';
                        $isDashboardActive = request()->routeIs('guide.dashboard');
                        break;
                    case 'event_organizer':
                        $dashboardRoute = 'organizer.events.index';
                        $isDashboardActive = request()->routeIs('organizer.events.index');
                        break;
                    default:
                        $isDashboardActive = request()->routeIs('tourist.dashboard');
                }
            } else {
                $isDashboardActive = request()->routeIs('tourist.dashboard');
            }
        @endphp
        <a href="{{ route($dashboardRoute) }}" class="{{ $isDashboardActive ? 'active' : '' }}"><i class="bi bi-speedometer2"></i> Tableau de bord</a>
          <a href="{{ route('assistant.index') }}" class="@if(request()->routeIs('assistant.*')) active @endif"><i class="bi bi-robot"></i> Planifier un voyage</a>
          <a href="{{ route('tourist.itinerary') }}" class="@if(request()->routeIs('tourist.itinerary')) active @endif"><i class="bi bi-map"></i> Itinéraire</a>
          <a href="{{ route('air.flights.index') }}" class="@if(request()->routeIs('air.flights.*')) active @endif"><i class="bi bi-airplane"></i> Vols</a>
          <a href="{{ route('air.bookings.index') }}" class="@if(request()->routeIs('air.bookings.*')) active @endif"><i class="bi bi-journal-check"></i> Mes réservations</a>
          <a href="{{ route('tourist.hotels.index') }}" class="@if(request()->routeIs('tourist.hotels.*')) active @endif"><i class="bi bi-building"></i> Séjours</a>
          <a href="{{ route('tourist.calendar') }}" class="@if(request()->routeIs('tourist.calendar*')) active @endif"><i class="bi bi-calendar3"></i> Calendrier</a>
          <a href="{{ route('events.index') }}" class="@if(request()->routeIs('events.*')) active @endif"><i class="bi bi-calendar-event"></i> Évènements</a>
          <a href="{{ route('food.restaurants.index') }}" class="@if(request()->routeIs('food.restaurants.*')) active @endif"><i class="bi bi-egg-fried"></i> Restaurants</a>
          @if(auth()->check() && ((auth()->user()->role ?? null) === 'event_organizer'))
            <div class="mt-2 mb-1 small text-uppercase text-muted">Organisateur</div>
            <a href="{{ route('organizer.events.index') }}" class="@if(request()->routeIs('organizer.events.index')) active @endif"><i class="bi bi-collection"></i> Mes évènements</a>
            <a href="{{ route('organizer.events.wizard.start') }}" class="@if(request()->routeIs('organizer.events.wizard.*')) active @endif"><i class="bi bi-magic"></i> Wizard</a>
            <a href="{{ route('organizer.templates.index') }}" class="@if(request()->routeIs('organizer.templates.*')) active @endif"><i class="bi bi-ticket-perforated"></i> Modèles</a>
            <a href="{{ route('organizer.sales.index') }}" class="@if(request()->routeIs('organizer.sales.*')) active @endif"><i class="bi bi-receipt"></i> Ventes</a>
            <a href="{{ route('organizer.profile.logo.edit') }}" class="@if(request()->routeIs('organizer.profile.*')) active @endif"><i class="bi bi-award"></i> Profil</a>
          @endif
          @if(auth()->user() && method_exists(auth()->user(), 'isRestaurant') && auth()->user()->isRestaurant())
            <div class="mt-2 mb-1 small text-uppercase text-muted">Gérant</div>
            <a href="{{ route('food.owner.restaurant.edit') }}" class="@if(request()->routeIs('food.owner.restaurant.*')) active @endif"><i class="bi bi-shop"></i> Mon restaurant</a>
            <a href="{{ route('food.owner.dishes.index') }}" class="@if(request()->routeIs('food.owner.dishes.*')) active @endif"><i class="bi bi-egg-fried"></i> Mes plats</a>
          @endif
          @if(auth()->user() && ((method_exists(auth()->user(), 'isHotelManager') && auth()->user()->isHotelManager()) || ((auth()->user()->role ?? null) === 'hotel_manager')))
            <div class="mt-2 mb-1 small text-uppercase text-muted">Gestion hôtel</div>
            <a href="{{ route('agency.hotels.index') }}" class="@if(request()->routeIs('agency.hotels.index')) active @endif"><i class="bi bi-building"></i> Mes hôtels</a>
            <a href="{{ route('agency.hotels.create') }}" class="@if(request()->routeIs('agency.hotels.create')) active @endif"><i class="bi bi-plus-circle"></i> Ajouter un hôtel</a>
            <a href="{{ route('agency.reservations.index') }}" class="@if(request()->routeIs('agency.reservations.*')) active @endif"><i class="bi bi-journal-text"></i> Réservations</a>
          @endif
          <a href="{{ route('profile.edit') }}" class="@if(request()->routeIs('profile.edit')) active @endif"><i class="bi bi-person"></i> Profil</a>
          @if(auth()->check() && ((auth()->user()->role ?? null) === 'guide'))
            <div class="mt-2 mb-1 small text-uppercase text-muted">Guide</div>
            @php $__newGuideMsg = \App\Models\GuideContact::where('guide_id', auth()->id())->where('status','new')->count(); @endphp
            <a href="{{ route('guide.dashboard') }}" class="@if(request()->routeIs('guide.dashboard')) active @endif"><i class="bi bi-compass"></i> Tableau de bord guide</a>
            <a href="{{ route('guide.messages.index') }}" class="@if(request()->routeIs('guide.messages.*')) active @endif"><i class="bi bi-inbox"></i> Messages
              @if($__newGuideMsg)
                <span class="badge bg-warning text-dark ms-1">{{ $__newGuideMsg }}</span>
              @endif
            </a>
            <a href="{{ route('guide.profile.edit') }}" class="@if(request()->routeIs('guide.profile.*')) active @endif"><i class="bi bi-person-badge"></i> Mon profil guide</a>
          @endif
        </nav>
      @endif
    </div>
  </div>

  <!-- Vendor JS Files -->
  <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/aos/aos.js') }}"></script>
  <script src="{{ asset('assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/swiper/swiper-bundle.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/imagesloaded/imagesloaded.pkgd.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/isotope-layout/isotope.pkgd.min.js') }}"></script>
  <script src="{{ asset('assets/js/main.js') }}"></script>

  @stack('scripts')
  <script>
    // Helper: submit first form when clicking any element with data-submit-first-form
    document.addEventListener('click', function(e){
      var t = e.target.closest('[data-submit-first-form]');
      if(t){
        e.preventDefault();
        var f = document.querySelector('form');
        if(f){ f.submit(); }
      }
    });
  </script>
</body>
</html>
