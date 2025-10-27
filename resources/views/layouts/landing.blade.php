<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Discover_BF — Landing</title>
  <meta name="description" content="Découvrez le Burkina Faso: sites, activités, événements et hébergements.">
  <meta name="keywords" content="Burkina Faso, tourisme, sites, activités, événements, hébergements">

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
    /* Landing: header overlaps hero, no top padding */
    body.has-fixed-header { padding-top: 0; }
    @media (min-width: 1200px) { body.has-fixed-header { padding-top: 0; } }

    .header.header-transparent { background: transparent; box-shadow: none; }
    .header.header-transparent .navmenu a { color: #fff; }
    .header.header-transparent .navmenu a:hover, .header.header-transparent .navmenu a.active { color: #fff; background: rgba(255,255,255,.15); }

    .header { transition: background-color .25s ease, box-shadow .25s ease; }
    /* Water (glass) style when not over hero */
    .header.header-water { 
      background: rgba(255,255,255,.70); 
      backdrop-filter: blur(14px); 
      -webkit-backdrop-filter: blur(14px); 
      box-shadow: 0 10px 34px rgba(0,0,0,.10); 
      border-bottom: 1px solid rgba(0,0,0,.08);
      transition: background .25s ease, backdrop-filter .25s ease, box-shadow .25s ease;
    }
    .header.header-water .navmenu a { color: #111827; }
    .header.header-water .navmenu a:hover, .header.header-water .navmenu a.active { color: #111827; background: rgba(0,0,0,.06); }

    .navmenu ul{ gap:6px; }
    .navmenu a{ padding:8px 12px; border-radius:999px; font-weight:500; }

    .logo-img{ height:36px; width:auto; object-fit:contain; display:block; }
    @media (min-width: 1200px){ .logo-img{ height:44px; } }

    .cta-btn{ border-radius:999px; padding:.45rem .95rem; font-weight:600; }
    .cta-btn-alt{ background: transparent; border:1px solid rgba(255,255,255,.8); color:#fff; }
    .header-water .cta-btn-alt{ color:#111827; border-color: rgba(0,0,0,.25); }

    .hero.hero-overlay { position: relative; }
    .hero.hero-overlay::before { content: ""; position: absolute; inset: 0; background: linear-gradient(180deg, rgba(0,0,0,.45) 0%, rgba(0,0,0,.25) 50%, rgba(0,0,0,.1) 100%); z-index: 0; }
    .hero.hero-overlay > * { position: relative; z-index: 1; }

    /* Fullscreen fixed background slider */
    .bg-slider{ inset:0; width:100vw; height:100vh; z-index:-1; overflow:hidden; }
    .bg-slider .bg-overlay{ position:absolute; inset:0; background: radial-gradient(ellipse at center, rgba(0,0,0,.15), rgba(0,0,0,.35)); z-index:1; pointer-events:none; }
    .bg-swiper, .bg-swiper .swiper-wrapper, .bg-swiper .swiper-slide{ width:100%; height:100%; }
    .bg-swiper .swiper-slide img{ position:absolute; inset:0; width:100%; height:100%; object-fit:cover; filter: saturate(105%) contrast(102%); }

    /* Make sections transparent so background remains visible while scrolling */
    .landing-page .main{ background: transparent !important; }
    .landing-page .section{ background: transparent !important; }
    .landing-page .light-background{ background: transparent !important; }
    .landing-page .dark-background{ background: transparent !important; }

    /* Avoid double hero backgrounds from child view */
    .landing-page #hero .hero-bg-img{ display:none !important; }
  </style>
  @stack('styles')
</head>
<body class="index-page landing-page">

  <!-- Fixed Background Carousel -->
  <div class="bg-slider position-fixed" aria-hidden="true">
    <div class="bg-overlay"></div>
    <div class="swiper bg-swiper">
      <div class="swiper-wrapper">
        <div class="swiper-slide"><img src="{{ asset('assets/img/Ouagadougou.jpg') }}" alt=""></div>
        <div class="swiper-slide"><img src="{{ asset('assets/img/place_cineastre.jpg') }}" alt=""></div>
        <div class="swiper-slide"><img src="{{ asset('assets/img/sindou.jpg') }}" alt=""></div>
        <div class="swiper-slide"><img src="{{ asset('assets/img/village.jpg') }}" alt=""></div>
      </div>
    </div>
  </div>

  <!-- Header -->
  <header id="header" class="header d-flex align-items-center fixed-top header-transparent">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">

      <a href="{{ url('/') }}" class="logo d-flex align-items-center me-auto">
        <img src="{{ asset('assets/img/Logo_Discover_BF_blanc.png') }}" alt="Logo" class="logo-img">
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="{{ url('/#hero') }}" class="active">Accueil</a></li>
          <li><a href="{{ route('sites.index') }}">Destinations</a></li>
          <li><a href="{{ route('events.index') }}">Événements</a></li>
          <li><a href="{{ route('tourist.hotels.index') }}">Hébergements</a></li>
          <li><a href="{{ url('/#contact') }}">Contact</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

      @guest
        <a class="cta-btn me-2" href="{{ route('login') }}">Connexion</a>
        <a class="cta-btn cta-btn-alt" href="{{ route('register') }}">Créer un compte</a>
      @endguest

      @auth
        @if(Auth::user()->isAdmin())
          <a class="cta-btn me-2" href="{{ route('admin.dashboard') }}">Dashboard</a>
        @endif
        <form method="POST" action="{{ route('logout') }}" class="ms-2 d-inline">
          @csrf
          <button class="cta-btn cta-btn-alt" type="submit">Se déconnecter</button>
        </form>
      @endauth

    </div>
  </header>

  <main class="main">
    @yield('content')
  </main>

  <footer class="footer mt-auto border-top">
    <div class="container py-5">
      <div class="row g-4">
        <div class="col-lg-4">
          <div class="d-flex align-items-center mb-3">
            <img src="{{ asset('assets/img/Logo_Discover_BF_noir.png') }}" alt="Discover BF" style="height:40px;" class="me-2">
          </div>
          <p class="text-muted mb-3">Découvrez des sites uniques, des activités mémorables et le meilleur de la culture au Burkina Faso.</p>
          <div class="d-flex gap-3">
            <a class="text-muted" href="#" aria-label="Facebook"><i class="bi bi-facebook fs-5"></i></a>
            <a class="text-muted" href="#" aria-label="Instagram"><i class="bi bi-instagram fs-5"></i></a>
            <a class="text-muted" href="#" aria-label="Twitter"><i class="bi bi-twitter-x fs-5"></i></a>
            <a class="text-muted" href="#" aria-label="Youtube"><i class="bi bi-youtube fs-5"></i></a>
          </div>
        </div>
        <div class="col-6 col-lg-2">
          <h6 class="fw-bold mb-3">Découvrir</h6>
          <ul class="list-unstyled text-muted small mb-0">
            <li class="mb-2"><a class="text-reset text-decoration-none" href="{{ route('sites.index') }}">Destinations</a></li>
            <li class="mb-2"><a class="text-reset text-decoration-none" href="{{ route('events.index') }}">Événements</a></li>
            <li class="mb-2"><a class="text-reset text-decoration-none" href="{{ route('tourist.hotels.index') }}">Hébergements</a></li>
            <li class="mb-2"><a class="text-reset text-decoration-none" href="{{ route('transport.taxi.index') }}">Transport</a></li>
          </ul>
        </div>
        <div class="col-6 col-lg-2">
          <h6 class="fw-bold mb-3">Entreprise</h6>
          <ul class="list-unstyled text-muted small mb-0">
            <li class="mb-2"><a class="text-reset text-decoration-none" href="{{ url('/#about') }}">À propos</a></li>
            <li class="mb-2"><a class="text-reset text-decoration-none" href="{{ route('partner.apply') }}">Devenir partenaire</a></li>
            <li class="mb-2"><a class="text-reset text-decoration-none" href="{{ url('/legal/privacy') }}">Confidentialité</a></li>
            <li class="mb-2"><a class="text-reset text-decoration-none" href="{{ url('/legal/terms') }}">Conditions</a></li>
          </ul>
        </div>
        <div class="col-lg-4">
          <h6 class="fw-bold mb-3">Newsletter</h6>
          <p class="text-muted small">Recevez des idées de voyages et des offres exclusives.</p>
          <form class="d-flex gap-2" action="#" method="POST" onsubmit="event.preventDefault();">
            <input type="email" class="form-control" placeholder="Votre email" required>
            <button class="btn btn-primary" type="submit">S'inscrire</button>
          </form>
        </div>
      </div>
    </div>
    <div style="height:6px; background:linear-gradient(90deg, #ff7e5f 0%, #feb47b 100%);"></div>
  </footer>

  <div id="preloader"></div>
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/purecounter/purecounter_vanilla.js') }}"></script>
  <script src="{{ asset('assets/vendor/aos/aos.js') }}"></script>
  <script src="{{ asset('assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/swiper/swiper-bundle.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/imagesloaded/imagesloaded.pkgd.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/isotope-layout/isotope.pkgd.min.js') }}"></script>

  <script src="{{ asset('assets/js/main.js') }}"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      var header = document.getElementById('header');
      if (!header) return;

      var toggleShadow = function() {
        if (window.scrollY > 10) header.classList.add('scrolled');
        else header.classList.remove('scrolled');
      };
      toggleShadow();
      window.addEventListener('scroll', toggleShadow, { passive: true });

      var hero = document.getElementById('hero');
      if (hero) {
        var setTransparent = function(){ header.classList.add('header-transparent'); header.classList.remove('header-water'); };
        var setWater = function(){ header.classList.add('header-water'); header.classList.remove('header-transparent'); };
        setTransparent();
        if ('IntersectionObserver' in window) {
          var io = new IntersectionObserver(function(entries){
            entries.forEach(function(entry){
              if (entry.intersectionRatio > 0.3) setTransparent(); else setWater();
            });
          }, { threshold: [0, 0.3, 0.6, 1] });
          io.observe(hero);
        } else {
          var onScroll = function(){
            var rect = hero.getBoundingClientRect();
            var vh = window.innerHeight || document.documentElement.clientHeight;
            if (rect.top < vh*0.7 && rect.bottom > vh*0.3) setTransparent(); else setWater();
          };
          window.addEventListener('scroll', onScroll, { passive:true });
          onScroll();
        }
      }
    });

    // Background Swiper (fixed, behind content)
    document.addEventListener('DOMContentLoaded', function(){
      if (window.Swiper) {
        new Swiper('.bg-swiper', {
          loop: true,
          speed: 900,
          autoplay: { delay: 4500, disableOnInteraction: false },
          effect: 'fade',
          fadeEffect: { crossFade: true },
        });
      }
    });
  </script>

  @stack('scripts')
</body>
</html>
