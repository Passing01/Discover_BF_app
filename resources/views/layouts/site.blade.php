<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Discover_BF</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="{{ asset('assets/img/favicon.png') }}" rel="icon">
  <link href="{{ asset('assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/aos/aos.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="{{ asset('assets/css/main.css') }}" rel="stylesheet">
  <style>
    /* Offset for fixed header so content isn't hidden */
    body.has-fixed-header { padding-top: 80px; }
    @media (min-width: 1200px) { body.has-fixed-header { padding-top: 96px; } }
    /* Transparent header styling when over hero */
    .header.header-transparent { background: transparent; box-shadow: none; }
    .header.header-transparent .navmenu a { color: #fff; }
    .header.header-transparent .navmenu a:hover, .header.header-transparent .navmenu a.active { color: #ffc107; }
    .header.header-transparent .sitename { color: #fff; }
    /* Gradient header for scrolled / non-hero pages */
    .header { transition: background-color .25s ease, box-shadow .25s ease; }
    .header.header-colored,
    .header.scrolled { background: linear-gradient(90deg, #ff7e5f 0%, #feb47b 100%); box-shadow: 0 4px 16px rgba(0,0,0,.08); }
    .header.header-colored .navmenu a,
    .header.scrolled .navmenu a { color: #fff; }
    .header.header-colored .navmenu a:hover, .header.header-colored .navmenu a.active,
    .header.scrolled .navmenu a:hover, .header.scrolled .navmenu a.active { color: #212529; opacity: .9; }
    .header.header-colored .sitename,
    .header.scrolled .sitename { color: #fff; }
    /* Hero overlay for readability */
    .hero.hero-overlay { position: relative; }
    .hero.hero-overlay::before { content: ""; position: absolute; inset: 0; background: linear-gradient(180deg, rgba(0,0,0,.45) 0%, rgba(0,0,0,.25) 50%, rgba(0,0,0,.1) 100%); z-index: 0; }
    .hero.hero-overlay > * { position: relative; z-index: 1; }
    /* Logo sizing for professional look */
    .logo-img{ height:36px; width:auto; object-fit:contain; display:block; }
    @media (min-width: 1200px){ .logo-img{ height:44px; } }
    .logo{ gap:10px; }
  </style>
  {{-- Page-specific styles injected by child views --}}
  @stack('styles')
</head>
<body class="index-page has-fixed-header">

  <!-- Header -->
  @php($isAuthPage = \Illuminate\Support\Facades\Route::is('login','register','password.*','verification.*','logout','password.confirm'))
  @php($showHero = !$isAuthPage)
  <header id="header" class="header d-flex align-items-center fixed-top header-colored">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">

      <a href="{{ (Auth::check() && method_exists(Auth::user(), 'isAdmin') && Auth::user()->isAdmin()) ? route('admin.dashboard') : url('/') }}" class="logo d-flex align-items-center me-auto">
        <img src="{{ asset('assets/img/Logo_Discover_BF_blanc.png') }}" alt="Logo" class="logo-img">
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="{{ url('/#hero') }}" class="active">Accueil</a></li>
          <li><a href="{{ url('/#about') }}">À propos</a></li>
          <li><a href="{{ url('/#services') }}">Services</a></li>
          <li><a href="{{ url('/#contact') }}">Contact</a></li>

          @auth
            @php($role = Auth::user()->role ?? 'tourist')
            @if(Auth::user()->isAdmin())
              <li><a href="{{ route('admin.events') }}">Admin Événements</a></li>
              <li><a href="{{ route('admin.users') }}">Admin Utilisateurs</a></li>
              <li><a href="{{ route('transport.taxi.index') }}">Taxis</a></li>
              <li><a href="{{ route('transport.bus.index') }}">Bus</a></li>
              <li><a href="{{ route('air.flights.index') }}">Vols</a></li>
              <li><a href="{{ route('assistant.index') }}">Assistant</a></li>
            @else
              @if($role === 'tourist')
                <li class="dropdown"><a href="#"><span>Mon espace</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                  <ul>
                    <li><a href="{{ route('tourist.plan') }}">Planifier</a></li>
                    <li><a href="{{ route('tourist.itinerary') }}">Mon itinéraire</a></li>
                    <li><a href="{{ route('tourist.hotels.index') }}">Hébergements</a></li>
                    <li><a href="{{ route('sites.index') }}">Sites touristiques</a></li>
                    <li><a href="{{ route('transport.taxi.index') }}">Taxis</a></li>
                    <li><a href="{{ route('transport.bus.index') }}">Bus</a></li>
                    <li><a href="{{ route('air.flights.index') }}">Vols</a></li>
                    <li><a href="{{ route('assistant.index') }}">Assistant</a></li>
                    <li><a href="{{ route('events.index') }}">Agenda culturel</a></li>
                    <li><a href="{{ route('tourist.bookings.index') }}">Mes réservations</a></li>
                    <li><a href="{{ route('partner.apply') }}">Devenir partenaire</a></li>
                  </ul>
                </li>
              @elseif($role === 'guide')
                <li><a href="{{ route('guide.dashboard') }}">Espace Guide</a></li>
              @elseif($role === 'event_organizer')
                <li><a href="{{ route('organizer.dashboard') }}">Tableau de bord</a></li>
                <li><a href="{{ route('organizer.events.index') }}">Mes évènements</a></li>
                <li><a href="{{ route('organizer.templates.index') }}">Gérer les modèles</a></li>
              @elseif($role === 'hotel_manager')
                <li class="dropdown"><a href="#"><span>Espace Hôtel</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                  <ul>
                    <li><a href="{{ route('hotel.dashboard') }}">Tableau de bord</a></li>
                    <li><a href="{{ route('agency.hotels.index') }}">Mes hôtels</a></li>
                    <li><a href="{{ route('agency.reservations.index') }}">Réservations</a></li>
                  </ul>
                </li>
              @elseif($role === 'driver')
                <li class="dropdown"><a href="#"><span>Espace Chauffeur</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                  <ul>
                    <li><a href="{{ route('driver.dashboard') }}">Tableau de bord</a></li>
                    <li><a href="{{ route('transport.taxi.index') }}">Taxis</a></li>
                  </ul>
                </li>
              @endif
              @if($role !== 'tourist' && empty(Auth::user()->role_onboarded_at))
                <li><a href="{{ route('onboarding.start') }}">Compléter profil</a></li>
              @endif
            @endif
          @endauth
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

      @guest
        <a class="cta-btn me-2" href="{{ route('login') }}">Connexion</a>
        <a class="cta-btn cta-btn-alt" href="{{ route('register') }}">Créer un compte</a>
      @endguest

      @auth
        @if(Auth::user()->isAdmin())
          <a class="cta-btn" href="{{ route('admin.dashboard') }}">Aller au Dashboard</a>
        @endif
        <form method="POST" action="{{ route('logout') }}" class="ms-3 d-inline">
          @csrf
          <button class="cta-btn cta-btn-alt" type="submit">Se déconnecter</button>
        </form>
      @endauth

    </div>
  </header>

  <!-- Main content -->
  <main class="main">
    @yield('content')
  </main>

  <!-- Optionally add a footer here -->
  <footer class="footer mt-auto">
    <!-- Footer content could go here -->
  </footer>
  
  <!-- Preloader and Back-to-top -->
  <div id="preloader"></div>
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/purecounter/purecounter_vanilla.js') }}"></script>
  <script src="{{ asset('assets/vendor/aos/aos.js') }}"></script>
  <script src="{{ asset('assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/swiper/swiper-bundle.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/imagesloaded/imagesloaded.pkgd.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/isotope-layout/isotope.pkgd.min.js') }}"></script>

  <!-- Main JS File -->
  <script src="{{ asset('assets/js/main.js') }}"></script>
  <script>
    // Toggle header color on scroll
    document.addEventListener('DOMContentLoaded', function() {
      var header = document.getElementById('header');
      var toggle = function() {
        if (window.scrollY > 10) header.classList.add('scrolled');
        else header.classList.remove('scrolled');
      };
      toggle();
      window.addEventListener('scroll', toggle, { passive: true });
    });
  </script>

  {{-- Page-specific scripts injected by child views --}}
  @stack('scripts')

</body>
</html>
