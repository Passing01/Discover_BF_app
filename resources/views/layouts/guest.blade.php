<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Discover_BF') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Bootstrap CSS (no Vite) -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://unpkg.com/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Local auth styles (with fallback utilities) -->
        <link href="{{ asset('css/auth.css') }}" rel="stylesheet">
    </head>
    <body class="bg-light">
        <div class="container-fluid min-vh-100">
            <div class="row min-vh-100">
                <!-- Left visual panel -->
                <div class="col-12 col-lg-6 p-0 split-left" style="background-image: url('{{ asset('assets/img/monument.jpg') }}'), url('https://images.unsplash.com/photo-1501785888041-af3ef285b470?q=80&w=1600&auto=format&fit=crop'); background-position:center; background-size:cover; min-height:40vh;">
                    <div class="split-overlay"></div>
                    <div class="position-absolute top-0 start-0 w-100 p-4 p-xl-5 text-white">
                        <div class="brand-title fs-3">Voyage</div>
                    </div>
                    <div class="position-absolute bottom-0 start-0 w-100 text-white">
                        <div class="p-4 p-xl-5">
                            <h2 class="welcome-title display-6 mb-2">Bienvenue sur Discover_BF!</h2>
                            <p class="mb-4">Connecter vous et accéder a votre espace.</p>
                            <div class="d-flex flex-wrap gap-3 small">
                                <a class="muted-link" href="#">Termes et Services</a>
                                <a class="muted-link" href="#">Politique de confidentialité</a>
                                <span class="text-white-50 ms-lg-auto">Discover_BF 2024. Tous droit réservé.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right auth panel -->
                <div class="col-12 col-lg-6 d-flex align-items-center justify-content-center py-4 py-lg-0">
                    <div class="auth-card bg-white rounded-4 shadow p-3 p-md-4">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </body>
</html>

