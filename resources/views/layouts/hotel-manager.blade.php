<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Discover_BF — Espace Gestionnaire d'Hôtel</title>
  <meta name="description" content="Tableau de bord gestionnaire d'hôtel - Gestion des chambres et réservations">
  <meta name="keywords" content="hôtel, gestion, réservations, chambres, tourisme, burkina">

  <!-- Favicons -->
  <link href="{{ asset('assets/img/favicon.png') }}" rel="icon">
  <link href="{{ asset('assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background-color: #f8f9fa;
      font-family: 'Roboto', sans-serif;
    }

    .hotel-header {
      background-color: #2c3e50;
      color: white;
      padding: 1rem 0;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .hotel-sidebar {
      background-color: white;
      min-height: 100vh;
      box-shadow: 2px 0 5px rgba(0,0,0,0.1);
      padding: 1.5rem 0;
    }

    .hotel-main {
      padding: 2rem;
    }

    .nav-link {
      color: #2c3e50;
      padding: 0.75rem 1.5rem;
      border-radius: 0.25rem;
      margin: 0.25rem 1rem;
      transition: all 0.3s;
      font-weight: 500;
    }

    .nav-link:hover, .nav-link.active {
      background-color: rgba(230, 126, 34, 0.1);
      color: #e67e22;
    }
  </style>
</head>

<body>
  <!-- Header -->
  <header class="hotel-header">
    <div class="container d-flex justify-content-between align-items-center">
      <a href="{{ route('hotel-manager.dashboard') }}" class="text-white text-decoration-none">
        <h1 class="fs-4 fw-bold">Discover_BF Hôtel</h1>
      </a>
      <div class="dropdown">
        <a href="#" class="text-white text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
          <img src="{{ auth()->user()->profile_photo_url }}" alt="Profile" width="32" height="32" class="rounded-circle me-2">
          <span>{{ auth()->user()->name }}</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
          <li><hr class="dropdown-divider"></li>
          <li>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="dropdown-item">Déconnexion</button>
            </form>
          </li>
        </ul>
      </div>
    </div>
  </header>

  <div class="container-fluid">
    <div class="row">
      <!-- Sidebar -->
      <nav class="col-md-3 col-lg-2 d-md-block hotel-sidebar">
        <ul class="nav flex-column">
          <li class="nav-item">
            <a href="{{ route('hotel-manager.dashboard') }}" class="nav-link {{ request()->routeIs('hotel-manager.dashboard') ? 'active' : '' }}">Tableau de bord</a>
          </li>
          <li class="nav-item">
            <a href="{{ route('hotel-manager.hotels.index') }}" class="nav-link {{ request()->routeIs('hotel-manager.hotels.*') ? 'active' : '' }}">Mes hôtels</a>
          </li>
          <li class="nav-item">
            <a href="{{ route('hotel-manager.rooms.index') }}" class="nav-link {{ request()->routeIs('hotel-manager.rooms.*') ? 'active' : '' }}">Chambres</a>
          </li>
          <li class="nav-item">
            <a href="{{ route('hotel-manager.bookings.index') }}" class="nav-link {{ request()->routeIs('hotel-manager.bookings.*') ? 'active' : '' }}">Réservations</a>
          </li>
          <li class="nav-item">
            <a href="{{ route('hotel-manager.calendar') }}" class="nav-link {{ request()->routeIs('hotel-manager.calendar') ? 'active' : '' }}">Calendrier</a>
          </li>
          <li class="nav-item">
            <a href="{{ route('hotel-manager.reports.index') }}" class="nav-link {{ request()->routeIs('hotel-manager.reports.*') ? 'active' : '' }}">Rapports</a>
          </li>
        </ul>
      </nav>

      <!-- Main Content -->
      <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 hotel-main">
        @if(session('success'))
          <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
          <div class="alert alert-danger">
            <ul>
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        @yield('content')
      </main>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
