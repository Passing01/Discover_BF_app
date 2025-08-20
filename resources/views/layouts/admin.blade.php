<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Admin • Discover_BF</title>
  <!-- Icon Font Stylesheet -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Libraries Stylesheet -->
  <link href="{{ asset('assets_admin/lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets_admin/lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css') }}" rel="stylesheet" />
  <!-- Customized Bootstrap Stylesheet -->
  <link href="{{ asset('assets_admin/css/bootstrap.min.css') }}" rel="stylesheet">
  <!-- Template Stylesheet -->
  <link href="{{ asset('assets_admin/css/style.css') }}" rel="stylesheet">
  <style>
    /* Map legacy utility classes to DarkPan look to avoid rewriting all views */
    .card-glass { background: var(--bs-secondary-bg); border-radius:.5rem; padding:1.25rem; }
    .card-glass .card-title { display:flex; align-items:center; gap:.5rem; margin:0; font-weight:600; }
    .section-title { display:flex; align-items:center; gap:.5rem; margin:0; font-weight:600; }
  </style>
  @stack('styles')
</head>
<body>
  <div class="container-fluid position-relative d-flex p-0">
    <!-- Spinner -->
    <div id="spinner" class="bg-dark position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center" style="display:none;">
      <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
        <span class="sr-only">Loading...</span>
      </div>
    </div>

    <!-- Sidebar Start -->
    <div class="sidebar pe-4 pb-3">
      <nav class="navbar bg-secondary navbar-dark">
        <a href="{{ route('admin.dashboard') }}" class="navbar-brand mx-4 mb-3">
          <h3 class="text-primary"><i class="fa fa-user-edit me-2"></i>Discover BF</h3>
        </a>
        <div class="navbar-nav w-100">
          <a href="{{ route('admin.dashboard') }}" class="nav-item nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="fa fa-tachometer-alt me-2"></i>Dashboard</a>
          <a href="{{ route('admin.users') }}" class="nav-item nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}"><i class="fa fa-users me-2"></i>Utilisateurs</a>
          <a href="{{ route('admin.role_apps') }}" class="nav-item nav-link {{ request()->routeIs('admin.role_apps*') ? 'active' : '' }}"><i class="fa fa-user-check me-2"></i>Demandes de rôles</a>
          <a href="{{ route('admin.events') }}" class="nav-item nav-link {{ request()->routeIs('admin.events') ? 'active' : '' }}"><i class="fa fa-calendar me-2"></i>Événements</a>
          <a href="{{ route('admin.moderation') }}" class="nav-item nav-link {{ request()->routeIs('admin.moderation') ? 'active' : '' }}"><i class="fa fa-shield-alt me-2"></i>Modération</a>
          <a href="{{ route('admin.ads') }}" class="nav-item nav-link {{ request()->routeIs('admin.ads*') ? 'active' : '' }}"><i class="fa fa-bullhorn me-2"></i>Publicités</a>
          <a href="{{ route('admin.notifications') }}" class="nav-item nav-link {{ request()->routeIs('admin.notifications*') ? 'active' : '' }}"><i class="fa fa-bell me-2"></i>Notifications</a>
          <a href="{{ route('assistant.index') }}" class="nav-item nav-link"><i class="bi bi-stars me-2"></i>Assistant IA</a>
          <a href="{{ url('/') }}" target="_blank" class="nav-item nav-link"><i class="fa fa-home me-2"></i>Accueil site</a>
        </div>
      </nav>
    </div>
    <!-- Sidebar End -->

    <!-- Content Start -->
    <div class="content">
      <!-- Navbar Start -->
      <nav class="navbar navbar-expand bg-secondary navbar-dark sticky-top px-4 py-0">
        <a href="#" class="sidebar-toggler flex-shrink-0">
          <i class="fa fa-bars"></i>
        </a>
        <div class="navbar-nav align-items-center ms-auto">
          @auth
          <div class="nav-item dropdown">
            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
              <span class="d-none d-lg-inline-flex">{{ Auth::user()->name }}</span>
            </a>
            <div class="dropdown-menu dropdown-menu-end bg-secondary border-0 rounded-0 rounded-bottom m-0">
              <form method="POST" action="{{ route('logout') }}" class="px-3 py-2">@csrf <button class="btn btn-sm btn-primary w-100">Déconnexion</button></form>
            </div>
          </div>
          @endauth
        </div>
      </nav>
      <!-- Navbar End -->

      <div class="container-fluid pt-4 px-4">
        @yield('content')
      </div>

      <!-- Footer Start -->
      <div class="container-fluid pt-4 px-4">
        <div class="bg-secondary rounded-top p-4">
          <div class="row">
            <div class="col-12 text-center text-sm-start">
              &copy; <a href="#">Discover_BF</a>
            </div>
          </div>
        </div>
      </div>
      <!-- Footer End -->
    </div>
    <!-- Content End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
  </div>

  <!-- JavaScript Libraries -->
  <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('assets_admin/lib/chart/chart.min.js') }}"></script>
  <script src="{{ asset('assets_admin/lib/easing/easing.min.js') }}"></script>
  <script src="{{ asset('assets_admin/lib/waypoints/waypoints.min.js') }}"></script>
  <script src="{{ asset('assets_admin/lib/owlcarousel/owl.carousel.min.js') }}"></script>
  <script src="{{ asset('assets_admin/lib/tempusdominus/js/moment.min.js') }}"></script>
  <script src="{{ asset('assets_admin/lib/tempusdominus/js/moment-timezone.min.js') }}"></script>
  <script src="{{ asset('assets_admin/lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js') }}"></script>
  <!-- Template Javascript -->
  <script src="{{ asset('assets_admin/js/main.js') }}"></script>
  @stack('scripts')
</body>
</html>
