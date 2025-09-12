@extends('layouts.site-manager')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Tableau de bord</h1>
        <p class="text-muted mb-0">Bienvenue, {{ auth()->user()->first_name }} ! Voici un aperçu de votre activité.</p>
    </div>
    <div>
        <a href="{{ route('site-manager.sites.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Ajouter un site
        </a>
    </div>
</div>

<!-- Statistiques -->
<div class="row g-4 mb-4">
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="stat-label">Sites gérés</h6>
                        <h2 class="stat-value text-primary mb-0">{{ $stats['total_sites'] }}</h2>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                        <i class="bi bi-geo-alt fs-4 text-primary"></i>
                    </div>
                </div>
                <div class="mt-2">
                    <span class="badge bg-primary bg-opacity-10 text-primary">{{ $stats['active_sites'] }} actifs</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="stat-label">Réservations</h6>
                        <h2 class="stat-value text-success mb-0">{{ $stats['total_bookings'] }}</h2>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                        <i class="bi bi-calendar-check fs-4 text-success"></i>
                    </div>
                </div>
                <div class="mt-2">
                    <span class="badge bg-success bg-opacity-10 text-success">{{ $stats['pending_bookings'] }} en attente</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="stat-label">Chiffre d'affaires</h6>
                        <h2 class="stat-value text-info mb-0">{{ number_format($stats['revenue'] ?? 0, 0, ',', ' ') }} FCFA</h2>
                    </div>
                    <div class="bg-info bg-opacity-10 p-3 rounded-circle">
                        <i class="bi bi-currency-exchange fs-4 text-info"></i>
                    </div>
                </div>
                <div class="mt-2">
                    <span class="badge bg-info bg-opacity-10 text-info">Ce mois-ci</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="stat-label">Visiteurs</h6>
                        <h2 class="stat-value text-warning mb-0">{{ $stats['total_visitors'] ?? 0 }}</h2>
                    </div>
                    <div class="bg-warning bg-opacity-10 p-3 rounded-circle">
                        <i class="bi bi-people fs-4 text-warning"></i>
                    </div>
                </div>
                <div class="mt-2">
                    <span class="badge bg-warning bg-opacity-10 text-warning">30 derniers jours</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Sites récemment ajoutés -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Mes sites récents</h5>
                <a href="{{ route('site-manager.sites.index') }}" class="btn btn-sm btn-outline-primary">
                    Voir tout <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                @if($sites->isEmpty())
                    <div class="text-center p-4">
                        <div class="mb-3">
                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                        </div>
                        <p class="text-muted mb-0">Aucun site enregistré pour le moment</p>
                        <a href="{{ route('site-manager.sites.create') }}" class="btn btn-primary mt-3">
                            <i class="bi bi-plus-circle me-1"></i> Ajouter un site
                        </a>
                    </div>
                @else
                    <div class="list-group list-group-flush">
                        @foreach($sites as $site)
                            <a href="{{ route('site-manager.sites.show', $site) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex align-items-center">
                                    @if($site->photo_url)
                                        <img src="{{ Storage::url($site->photo_url) }}" alt="{{ $site->name }}" class="site-photo me-3">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center rounded me-3" style="width: 60px; height: 60px;">
                                            <i class="bi bi-image text-muted"></i>
                                        </div>
                                    @endif
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-1">{{ $site->name }}</h6>
                                            <span class="badge {{ $site->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $site->is_active ? 'Actif' : 'Inactif' }}
                                            </span>
                                        </div>
                                        <p class="small text-muted mb-0">
                                            <i class="bi bi-geo-alt-fill text-primary"></i> {{ $site->city }}
                                        </p>
                                        <p class="small text-muted mb-0">
                                            <i class="bi bi-calendar-check text-primary"></i> {{ $site->bookings_count }} réservation(s)
                                        </p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Dernières réservations -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Dernières réservations</h5>
                <a href="{{ route('site-manager.bookings.index') }}" class="btn btn-sm btn-outline-primary">
                    Voir tout <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                @if($recentBookings->isEmpty())
                    <div class="text-center p-4">
                        <div class="mb-3">
                            <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                        </div>
                        <p class="text-muted mb-0">Aucune réservation pour le moment</p>
                    </div>
                @else
                    <div class="list-group list-group-flush">
                        @foreach($recentBookings as $booking)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">{{ $booking->site->name }}</h6>
                                        <p class="small text-muted mb-1">
                                            <i class="bi bi-calendar-event"></i> 
                                            {{ $booking->visit_date->format('d/m/Y') }}
                                            <span class="mx-2">•</span>
                                            <i class="bi bi-people"></i> {{ $booking->visitors_count }} personne(s)
                                        </p>
                                        <p class="small mb-0">
                                            <span class="badge {{ 
                                                $booking->status === 'confirmed' ? 'bg-success' : 
                                                ($booking->status === 'pending' ? 'bg-warning' : 'bg-secondary')
                                            }}">
                                                {{ ucfirst($booking->status) }}
                                            </span>
                                        </p>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold">{{ number_format($booking->total_amount, 0, ',', ' ') }} FCFA</div>
                                        <a href="{{ route('site-manager.bookings.show', $booking) }}" class="btn btn-sm btn-outline-primary mt-2">
                                            Détails
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Initialisation des tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush
@endsection
