@extends('layouts.restau')

@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord')

@section('content')
<div class="container-fluid">

    <div class="row g-4 mb-4">
        <!-- Statistiques rapides -->
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted mb-2">Restaurants</h6>
                            <h2 class="mb-0">{{ $restaurants->count() }}</h2>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-utensils text-primary fs-4"></i>
                        </div>
                    </div>
                    <a href="{{ route('restaurant-manager.restaurants.index') }}" class="stretched-link"></a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted mb-2">Réservations aujourd'hui</h6>
                            <h2 class="mb-0">{{ $reservations->where('reservation_at', '>=', now()->startOfDay())->where('reservation_at', '<=', now()->endOfDay())->count() }}</h2>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-calendar-check text-success fs-4"></i>
                        </div>
                    </div>
                    <a href="{{ route('restaurant-manager.reservations.index', ['date' => now()->format('Y-m-d')]) }}" class="stretched-link"></a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted mb-2">Réservations à venir</h6>
                            <h2 class="mb-0">{{ $reservations->where('reservation_at', '>', now())->count() }}</h2>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-clock text-warning fs-4"></i>
                        </div>
                    </div>
                    <a href="{{ route('restaurant-manager.reservations.index', ['status' => 'upcoming']) }}" class="stretched-link"></a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted mb-2">Plats</h6>
                            <h2 class="mb-0">{{ $restaurants->sum(function($restaurant) { return $restaurant->dishes->count(); }) }}</h2>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-hamburger text-info fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Dernières réservations -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Dernières réservations</h5>
                        <a href="{{ route('restaurant-manager.reservations.index') }}" class="btn btn-sm btn-primary">
                            Voir tout <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($reservations->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Date/Heure</th>
                                        <th>Client</th>
                                        <th>Personnes</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reservations->take(5) as $reservation)
                                    <tr>
                                        <td>#{{ $reservation->id }}</td>
                                        <td>
                                            <div>{{ $reservation->reservation_at->format('d/m/Y') }}</div>
                                            <small class="text-muted">{{ $reservation->reservation_at->format('H:i') }}</small>
                                        </td>
                                        <td>{{ $reservation->user->name }}</td>
                                        <td>{{ $reservation->party_size }} pers.</td>
                                        <td>
                                            @php
                                                $statusClasses = [
                                                    'pending' => 'bg-warning',
                                                    'confirmed' => 'bg-success',
                                                    'cancelled' => 'bg-danger',
                                                    'completed' => 'bg-secondary'
                                                ][$reservation->status] ?? 'bg-secondary';
                                                
                                                $statusLabels = [
                                                    'pending' => 'En attente',
                                                    'confirmed' => 'Confirmée',
                                                    'cancelled' => 'Annulée',
                                                    'completed' => 'Terminée'
                                                ][$reservation->status] ?? $reservation->status;
                                            @endphp
                                            <span class="badge {{ $statusClasses }} text-white">
                                                {{ $statusLabels }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('restaurant-manager.reservations.show', $reservation) }}" 
                                               class="btn btn-sm btn-outline-primary" 
                                               data-bs-toggle="tooltip" 
                                               title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-calendar-times fa-3x text-muted"></i>
                            </div>
                            <p class="text-muted mb-0">Aucune réservation trouvée</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Mes restaurants -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Mes restaurants</h5>
                        <a href="{{ route('restaurant-manager.restaurants.create') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus me-1"></i> Ajouter
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($restaurants->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($restaurants as $restaurant)
                            <a href="{{ route('restaurant-manager.restaurants.edit', $restaurant) }}" 
                               class="list-group-item list-group-item-action border-0 border-bottom">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        @if($restaurant->cover_image)
                                            <img src="{{ Storage::url($restaurant->cover_image) }}" 
                                                 alt="{{ $restaurant->name }}" 
                                                 class="rounded" 
                                                 width="50" 
                                                 height="50" 
                                                 style="object-fit: cover;">
                                        @else
                                            <div class="bg-light d-flex align-items-center justify-content-center rounded" 
                                                 style="width: 50px; height: 50px;">
                                                <i class="fas fa-utensils text-muted"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ms-3 flex-grow-1">
                                        <h6 class="mb-0">{{ $restaurant->name }}</h6>
                                        <small class="text-muted">{{ $restaurant->city }}</small>
                                    </div>
                                    <div class="ms-2">
                                        <span class="badge {{ $restaurant->is_active ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $restaurant->is_active ? 'Actif' : 'Inactif' }}
                                        </span>
                                    </div>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-utensils fa-3x text-muted"></i>
                            </div>
                            <p class="text-muted mb-3">Aucun restaurant enregistré</p>
                            <a href="{{ route('restaurant-manager.restaurants.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Ajouter un restaurant
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Cartes de statistiques */
    .card {
        border: none;
        border-radius: 0.5rem;
        box-shadow: 0 0.125rem 0.5rem rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        overflow: hidden;
    }
    
    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
    }
    
    .card-header {
        background-color: #fff;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding: 1rem 1.25rem;
    }
    
    .card-title {
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0;
    }
    
    /* Tableau des réservations */
    .table {
        margin-bottom: 0;
    }
    
    .table thead th {
        border-top: none;
        border-bottom: 1px solid #e2e8f0;
        font-weight: 600;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
        padding: 0.75rem 1.25rem;
        background-color: #f8fafc;
    }
    
    .table tbody td {
        padding: 1rem 1.25rem;
        vertical-align: middle;
        border-color: #f1f5f9;
    }
    
    .table tbody tr:last-child td {
        border-bottom: none;
    }
    
    .table tbody tr:hover {
        background-color: #f8fafc;
    }
    
    /* Badges */
    .badge {
        font-weight: 500;
        padding: 0.4em 0.75em;
        font-size: 0.75em;
        border-radius: 4px;
        text-transform: capitalize;
    }
    
    .bg-success { background-color: #10b981 !important; }
    .bg-warning { background-color: #f59e0b !important; }
    .bg-danger { background-color: #ef4444 !important; }
    .bg-secondary { background-color: #64748b !important; }
    .bg-primary { background-color: #4f46e5 !important; }
    
    /* Boutons */
    .btn {
        border-radius: 0.375rem;
        font-weight: 500;
        transition: all 0.2s;
    }
    
    .btn-sm {
        padding: 0.35rem 0.65rem;
        font-size: 0.75rem;
    }
    
    .btn-primary {
        background-color: #4f46e5;
        border-color: #4f46e5;
    }
    
    .btn-primary:hover {
        background-color: #4338ca;
        border-color: #4338ca;
    }
    
    .btn-outline-primary {
        color: #4f46e5;
        border-color: #4f46e5;
    }
    
    .btn-outline-primary:hover {
        background-color: #4f46e5;
        border-color: #4f46e5;
    }
    
    /* Liste des restaurants */
    .list-group-item {
        padding: 1rem 1.25rem;
        border-color: #f1f5f9;
        transition: background-color 0.2s;
    }
    
    .list-group-item:hover {
        background-color: #f8fafc;
    }
    
    .list-group-item:first-child {
        border-top: none;
    }
    
    .list-group-item:last-child {
        border-bottom: none;
    }
    
    /* Icônes */
    .fs-4 {
        font-size: 1.25rem !important;
    }
    
    /* Cartes de statistiques */
    .bg-opacity-10 {
        opacity: 0.1;
    }
    
    .rounded-circle {
        border-radius: 50% !important;
    }
    
    /* Mise en page responsive */
    @media (max-width: 991.98px) {
        .col-lg-8 {
            margin-bottom: 1.5rem;
        }
    }
    
    @media (max-width: 767.98px) {
        .col-md-6, .col-md-3 {
            margin-bottom: 1rem;
        }
        
        .card-body {
            padding: 1rem;
        }
        
        .table-responsive {
            border: none;
        }
    }
    
    /* Animation de survol des cartes */
    .card-hover-effect {
        transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .card-hover-effect:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1) !important;
    }
    
    /* Amélioration de la lisibilité du texte */
    body {
        color: #334155;
        line-height: 1.6;
    }
    
    h1, h2, h3, h4, h5, h6 {
        color: #1e293b;
        font-weight: 600;
    }
    
    .text-muted {
        color: #64748b !important;
    }
</style>
@endpush
