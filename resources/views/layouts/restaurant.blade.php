<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Espace Restaurateur - Discover BF')</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets_restaurant/images/favicon.ico') }}">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets_restaurant/images/favicon.ico') }}">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets_restaurant/css/bootstrap.min.css') }}">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets_restaurant/css/style.css') }}">

    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    @stack('styles')

<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 bg-dark text-white min-vh-100 p-0">
                <div class="p-3 text-center">
                    <a href="{{ route('restaurant-manager.dashboard') }}" class="text-white text-decoration-none">
                        <h4>Discover BF</h4>
                        <p class="mb-0">Espace Restaurateur</p>
                    </a>
                </div>
                <hr class="bg-secondary">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="{{ route('restaurant-manager.dashboard') }}" class="nav-link text-white {{ request()->routeIs('restaurant-manager.dashboard') ? 'bg-primary' : '' }}">
                            <i class="fas fa-tachometer-alt me-2"></i> Tableau de bord
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('restaurant-manager.restaurants.index') }}" class="nav-link text-white {{ request()->routeIs('restaurant-manager.restaurants.*') ? 'bg-primary' : '' }}">
                            <i class="fas fa-utensils me-2"></i> Mes Restaurants
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('restaurant-manager.reservations.index') }}" class="nav-link text-white {{ request()->routeIs('restaurant-manager.reservations.*') ? 'bg-primary' : '' }}">
                            <i class="fas fa-calendar-check me-2"></i> Réservations
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <!-- Top Bar -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>@yield('page-title', 'Tableau de bord')</h2>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Page Content -->
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    @stack('scripts')
    
    <script>
        // Enable tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
        
        // Enable popovers
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
        var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl)
        });
    </script>
</body>
</html>
