<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Discover_BF — Espace Gestionnaire de Sites</title>
  <meta name="description" content="Tableau de bord gestionnaire de sites touristiques">
  <meta name="keywords" content="gestion, sites touristiques, réservations, tourisme, burkina">

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
  <link href="{{ asset('assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/aos/aos.css') }}" rel="stylesheet">
  
  <!-- Template Main CSS File -->
  <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
  
  <style>
    :root {
      --site-manager-primary: #2c3e50;
      --site-manager-secondary: #3498db;
      --site-manager-light: #ecf0f1;
      --site-manager-dark: #2c3e50;
      --site-manager-success: #2ecc71;
      --site-manager-warning: #f39c12;
      --site-manager-danger: #e74c3c;
      --site-manager-info: #3498db;
    }
    
    body {
      background-color: #f8f9fa;
    }
    
    .site-manager-header {
      background-color: var(--site-manager-primary);
      color: white;
      padding: 1rem 0;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .site-manager-sidebar {
      background-color: white;
      min-height: calc(100vh - 60px);
      box-shadow: 2px 0 5px rgba(0,0,0,0.1);
      padding: 1.5rem 0;
    }
    
    .site-manager-main {
      padding: 2rem 0;
    }
    
    .nav-link {
      color: var(--site-manager-dark);
      padding: 0.5rem 1.5rem;
      border-radius: 0.25rem;
      margin: 0.25rem 1rem;
      transition: all 0.3s;
    }
    
    .nav-link:hover, .nav-link.active {
      background-color: rgba(52, 152, 219, 0.1);
      color: var(--site-manager-secondary);
    }
    
    .nav-link i {
      margin-right: 0.5rem;
      width: 1.25rem;
      text-align: center;
    }
    
    .card {
      border: none;
      border-radius: 0.5rem;
      box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
      margin-bottom: 1.5rem;
      transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1);
    }
    
    .card-header {
      background-color: white;
      border-bottom: 1px solid rgba(0,0,0,0.05);
      font-weight: 600;
      padding: 1rem 1.25rem;
    }
    
    .stat-card {
      border-left: 4px solid var(--site-manager-secondary);
    }
    
    .stat-card .card-body {
      padding: 1.5rem;
    }
    
    .stat-card .stat-value {
      font-size: 1.75rem;
      font-weight: 700;
      color: var(--site-manager-dark);
      margin: 0.5rem 0;
    }
    
    .stat-card .stat-label {
      color: #6c757d;
      font-size: 0.875rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    
    .table th {
      font-weight: 600;
      text-transform: uppercase;
      font-size: 0.75rem;
      letter-spacing: 0.5px;
      color: #6c757d;
      border-top: none;
    }
    
    .badge {
      font-weight: 500;
      padding: 0.35em 0.65em;
      font-size: 0.75em;
    }
    
    .badge-pending {
      background-color: #fff3cd;
      color: #856404;
    }
    
    .badge-confirmed {
      background-color: #d4edda;
      color: #155724;
    }
    
    .badge-cancelled {
      background-color: #f8d7da;
      color: #721c24;
    }
    
    .badge-completed {
      background-color: #d1ecf1;
      color: #0c5460;
    }
    
    .btn-primary {
      background-color: var(--site-manager-secondary);
      border-color: var(--site-manager-secondary);
    }
    
    .btn-primary:hover {
      background-color: #2980b9;
      border-color: #2980b9;
    }
    
    .btn-outline-primary {
      color: var(--site-manager-secondary);
      border-color: var(--site-manager-secondary);
    }
    
    .btn-outline-primary:hover {
      background-color: var(--site-manager-secondary);
      border-color: var(--site-manager-secondary);
    }
    
    .site-photo {
      width: 60px;
      height: 60px;
      object-fit: cover;
      border-radius: 0.25rem;
    }
    
    @media (max-width: 991.98px) {
      .site-manager-sidebar {
        min-height: auto;
        margin-bottom: 1.5rem;
      }
    }
  </style>
  
  @stack('styles')
</head>

<body>
  <!-- ======= Header ======= -->
  <header class="site-manager-header">
    <div class="container">
      <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
          <a href="{{ route('site-manager.dashboard') }}" class="d-flex align-items-center text-white text-decoration-none">
            <img src="{{ asset('assets/img/Logo_Discover_BF_blanc.png') }}" alt="Discover BF" height="40" class="me-2">
            <span class="h4 mb-0 d-none d-md-inline">Espace Gestionnaire</span>
          </a>
        </div>
        
        <div class="d-flex align-items-center">
          <!-- User Dropdown -->
          <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
              <div class="me-2 d-none d-md-block">
                <div class="fw-medium">{{ Auth::user()->name }}</div>
                <div class="small text-white-50">Gestionnaire de sites</div>
              </div>
              <div class="rounded-circle bg-light text-primary d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i class="bi bi-person-fill" style="font-size: 1.25rem;"></i>
              </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
              <li><a class="dropdown-item" href="{{ route('site-manager.profile.edit') }}"><i class="bi bi-person me-2"></i> Mon profil</a></li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <form method="POST" action="{{ route('logout') }}">
                  @csrf
                  <button type="submit" class="dropdown-item">
                    <i class="bi bi-box-arrow-right me-2"></i> Déconnexion
                  </button>
                </form>
              </li>
            </ul>
          </div>
          
          <!-- Mobile menu button -->
          <button class="btn btn-link text-white d-md-none ms-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu" aria-controls="mobileMenu">
            <i class="bi bi-list" style="font-size: 1.5rem;"></i>
          </button>
        </div>
      </div>
    </div>
  </header>
  
  <!-- ======= Main Content ======= -->
  <div class="container-fluid">
    <div class="row">
      <!-- Desktop Sidebar -->
      <div class="col-md-3 col-lg-2 d-none d-md-block site-manager-sidebar">
        <div class="d-flex flex-column">
          <div class="mb-4 px-3">
            <h5 class="text-muted text-uppercase small mb-3">Menu Principal</h5>
            <ul class="nav flex-column">
              <li class="nav-item">
                <a href="{{ route('site-manager.dashboard') }}" class="nav-link {{ request()->routeIs('site-manager.dashboard') ? 'active' : '' }}">
                  <i class="bi bi-speedometer2"></i> Tableau de bord
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('site-manager.sites.index') }}" class="nav-link {{ request()->routeIs('site-manager.sites.*') ? 'active' : '' }}">
                  <i class="bi bi-geo-alt"></i> Mes sites
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('site-manager.bookings.index') }}" class="nav-link {{ request()->routeIs('site-manager.bookings.*') ? 'active' : '' }}">
                  <i class="bi bi-calendar-check"></i> Réservations
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('site-manager.calendar') }}" class="nav-link {{ request()->routeIs('site-manager.calendar') ? 'active' : '' }}">
                  <i class="bi bi-calendar-week"></i> Calendrier
                </a>
              </li>
            </ul>
          </div>
          
          <div class="px-3 mt-auto">
            <div class="card bg-light border-0">
              <div class="card-body p-3">
                <h6 class="card-title text-muted small text-uppercase mb-2">Support</h6>
                <p class="card-text small text-muted mb-2">Besoin d'aide ? Contactez notre équipe de support.</p>
                <a href="mailto:support@discover-bf.com" class="btn btn-sm btn-outline-primary w-100">
                  <i class="bi bi-headset me-1"></i> Contacter
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Main Content -->
      <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4 site-manager-main">
        @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif
        
        @if($errors->any())
          <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <ul class="mb-0">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif
        
        @yield('content')
      </main>
    </div>
  </div>
  
  <!-- Mobile Menu Offcanvas -->
  <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileMenu" aria-labelledby="mobileMenuLabel">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title" id="mobileMenuLabel">Menu</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
      <div class="list-group list-group-flush">
        <a href="{{ route('site-manager.dashboard') }}" class="list-group-item list-group-item-action {{ request()->routeIs('site-manager.dashboard') ? 'active' : '' }}">
          <i class="bi bi-speedometer2 me-2"></i> Tableau de bord
        </a>
        <a href="{{ route('site-manager.sites.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('site-manager.sites.*') ? 'active' : '' }}">
          <i class="bi bi-geo-alt me-2"></i> Mes sites
        </a>
        <a href="{{ route('site-manager.bookings.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('site-manager.bookings.*') ? 'active' : '' }}">
          <i class="bi bi-calendar-check me-2"></i> Réservations
        </a>
        <a href="{{ route('site-manager.calendar') }}" class="list-group-item list-group-item-action {{ request()->routeIs('site-manager.calendar') ? 'active' : '' }}">
          <i class="bi bi-calendar-week me-2"></i> Calendrier
        </a>
        <div class="list-group-item">
          <a href="{{ route('site-manager.profile.edit') }}" class="d-flex align-items-center text-decoration-none text-dark">
            <i class="bi bi-person me-2"></i> Mon profil
          </a>
        </div>
        <div class="list-group-item">
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-link p-0 text-decoration-none text-dark">
              <i class="bi bi-box-arrow-right me-2"></i> Déconnexion
            </button>
          </form>
        </div>
      </div>
      
      <div class="p-3 mt-3">
        <div class="card bg-light border-0">
          <div class="card-body p-3">
            <h6 class="card-title text-muted small text-uppercase mb-2">Support</h6>
            <p class="card-text small text-muted mb-2">Besoin d'aide ? Contactez notre équipe de support.</p>
            <a href="mailto:support@discover-bf.com" class="btn btn-sm btn-outline-primary w-100">
              <i class="bi bi-headset me-1"></i> Contacter
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Vendor JS Files -->
  <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/aos/aos.js') }}"></script>
  
  <!-- Template Main JS File -->
  <script src="{{ asset('assets/js/main.js') }}"></script>
  
  <script>
    // Activer les tooltips
    document.addEventListener('DOMContentLoaded', function() {
      var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
      var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
      });
      
      // Activer les popovers
      var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
      var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
      });
    });
  </script>
  
  @stack('scripts')
</body>
</html>
