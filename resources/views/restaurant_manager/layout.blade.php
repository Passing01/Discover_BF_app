<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Espace Restaurateur')</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets_restaurant/images/favicon.ico') }}">
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets_restaurant/css/bootstrap.min.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets_restaurant/css/style.css') }}">
    
    @stack('styles')
</head>
<body>
    <!-- Preloader -->
    <div class="preloader">
        <div class="spinner"></div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <a href="{{ route('restaurant-manager.dashboard') }}">
                <img src="{{ asset('assets_restaurant/images/logo.png') }}" alt="Logo">
            </a>
        </div>
        
        <ul class="sidebar-menu">
            <li class="{{ request()->routeIs('restaurant-manager.dashboard') ? 'active' : '' }}">
                <a href="{{ route('restaurant-manager.dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i> Tableau de bord
                </a>
            </li>
            <li class="{{ request()->routeIs('restaurant-manager.restaurants.*') ? 'active' : '' }}">
                <a href="{{ route('restaurant-manager.restaurants.index') }}">
                    <i class="fas fa-utensils"></i> Mes Restaurants
                </a>
            </li>
            <li class="{{ request()->routeIs('restaurant-manager.reservations.*') ? 'active' : '' }}">
                <a href="{{ route('restaurant-manager.reservations.index') }}">
                    <i class="fas fa-calendar-check"></i> Réservations
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fas fa-chart-line"></i> Statistiques
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <nav class="top-bar">
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Rechercher...">
            </div>
            
            <div class="user-menu">
                <div class="dropdown">
                    <a href="#" class="dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="{{ Auth::user()->profile_photo_url ?? asset('assets_restaurant/images/avatar.png') }}" alt="User" class="user-avatar">
                        <span>{{ Auth::user()->name }}</span>
                        <i class="fas fa-chevron-down"></i>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user"></i> Mon Profil</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog"></i> Paramètres</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <div class="page-content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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

            @yield('content')
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="{{ asset('assets_restaurant/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets_restaurant/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets_restaurant/js/main.js') }}"></script>
    
    @stack('scripts')
</body>
</html>
