@extends('layouts.site-manager')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">{{ $site->name }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('site-manager.dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item"><a href="{{ route('site-manager.sites.index') }}">Mes sites</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $site->name }}</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="{{ route('site-manager.sites.edit', $site) }}" class="btn btn-outline-primary me-2">
            <i class="bi bi-pencil me-1"></i> Modifier
        </a>
        <a href="{{ route('site-manager.sites.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Retour
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-body">
                @if($site->photo_url)
                    <img src="{{ Storage::url($site->photo_url) }}" alt="{{ $site->name }}" class="img-fluid rounded mb-4">
                @endif
                
                <div class="d-flex align-items-center mb-4">
                    <div class="badge bg-{{ $site->is_active ? 'success' : 'secondary' }} me-3">
                        {{ $site->is_active ? 'Actif' : 'Inactif' }}
                    </div>
                    <div class="text-muted">
                        <i class="bi bi-geo-alt me-1"></i> {{ $site->city }}
                    </div>
                    <div class="ms-3 text-muted">
                        <i class="bi bi-tag me-1"></i> {{ ucfirst($site->category) }}
                    </div>
                </div>
                
                <h5 class="mb-3">Description</h5>
                <div class="mb-4">
                    {!! nl2br(e($site->description)) !!}
                </div>
                
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="card-title text-muted mb-3">
                                    <i class="bi bi-clock-history me-2"></i>Horaires d'ouverture
                                </h6>
                                <div class="opening-hours">
                                    @php
                                        $openingDays = json_decode($site->opening_days, true) ?? [];
                                        $days = [
                                            'monday' => 'Lundi',
                                            'tuesday' => 'Mardi',
                                            'wednesday' => 'Mercredi',
                                            'thursday' => 'Jeudi',
                                            'friday' => 'Vendredi',
                                            'saturday' => 'Samedi',
                                            'sunday' => 'Dimanche'
                                        ];
                                    @endphp
                                    
                                    @foreach($days as $key => $day)
                                        <div class="d-flex justify-content-between py-2 border-bottom">
                                            <span class="text-muted">{{ $day }}</span>
                                            <span>
                                                @if(in_array($key, $openingDays))
                                                    {{ date('H:i', strtotime($site->opening_time)) }} - {{ date('H:i', strtotime($site->closing_time)) }}
                                                @else
                                                    <span class="text-muted">Fermé</span>
                                                @endif
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="card-title text-muted mb-3">
                                    <i class="bi bi-currency-euro me-2"></i>Tarifs
                                </h6>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Prix minimum:</span>
                                        <strong>{{ number_format($site->price_min, 0, ',', ' ') }} FCFA</strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Prix maximum:</span>
                                        <strong>{{ number_format($site->price_max, 0, ',', ' ') }} FCFA</strong>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <h6 class="card-title text-muted mb-3">
                                        <i class="bi bi-telephone me-2"></i>Contact
                                    </h6>
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <i class="bi bi-telephone-fill text-primary me-2"></i>
                                            <a href="tel:{{ $site->phone }}" class="text-decoration-none">{{ $site->phone }}</a>
                                        </li>
                                        <li class="mb-2">
                                            <i class="bi bi-envelope-fill text-primary me-2"></i>
                                            <a href="mailto:{{ $site->email }}" class="text-decoration-none">{{ $site->email }}</a>
                                        </li>
                                        @if($site->website)
                                            <li class="mb-2">
                                                <i class="bi bi-globe text-primary me-2"></i>
                                                <a href="{{ $site->website }}" target="_blank" class="text-decoration-none">Site web</a>
                                            </li>
                                        @endif
                                        <li class="mb-2">
                                            <i class="bi bi-geo-alt-fill text-primary me-2"></i>
                                            {{ $site->address }}, {{ $site->city }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                @if($site->gallery && count(json_decode($site->gallery)) > 0)
                    <div class="mb-4">
                        <h5 class="mb-3">Galerie photos</h5>
                        <div class="row g-3">
                            @foreach(json_decode($site->gallery) as $image)
                                <div class="col-6 col-md-4 col-lg-3">
                                    <a href="{{ Storage::url($image) }}" data-fancybox="gallery">
                                        <img src="{{ Storage::url($image) }}" alt="Galerie {{ $loop->iteration }}" class="img-fluid rounded">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                <div class="mt-4">
                    <h5 class="mb-3">Localisation</h5>
                    <div class="ratio ratio-16x9 rounded border">
                        <div id="map" style="width: 100%; height: 100%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">Statistiques</h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary bg-opacity-10 p-3 rounded me-3">
                        <i class="bi bi-calendar-check text-primary"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">{{ $site->bookings_count }}</h6>
                        <small class="text-muted">Réservations totales</small>
                    </div>
                </div>
                
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-success bg-opacity-10 p-3 rounded me-3">
                        <i class="bi bi-currency-euro text-success"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">{{ number_format($site->bookings->sum('total_amount'), 0, ',', ' ') }} FCFA</h6>
                        <small class="text-muted">Chiffre d'affaires</small>
                    </div>
                </div>
                
                <div class="d-flex align-items-center">
                    <div class="bg-info bg-opacity-10 p-3 rounded me-3">
                        <i class="bi bi-people text-info"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">{{ $site->bookings->sum('visitor_count') }}</h6>
                        <small class="text-muted">Visiteurs totaux</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Dernières réservations</h6>
                <a href="{{ route('site-manager.bookings.index') }}?site_id={{ $site->id }}" class="btn btn-sm btn-outline-primary">
                    Voir tout
                </a>
            </div>
            <div class="card-body p-0">
                @if($site->bookings->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($site->bookings->sortByDesc('created_at')->take(5) as $booking)
                            <a href="{{ route('site-manager.bookings.show', $booking) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">Réservation #{{ $booking->id }}</h6>
                                    <span class="badge bg-{{ 
                                        $booking->status == 'confirmed' ? 'success' : 
                                        ($booking->status == 'cancelled' ? 'danger' : 'warning')
                                    }}">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </div>
                                <p class="mb-1">{{ $booking->visitor_count }} personne(s) • {{ number_format($booking->total_amount, 0, ',', ' ') }} FCFA</p>
                                <small class="text-muted">
                                    <i class="bi bi-calendar3 me-1"></i> {{ $booking->visit_date->format('d/m/Y') }}
                                </small>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center p-4">
                        <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                        <p class="text-muted mb-0 mt-2">Aucune réservation pour le moment</p>
                    </div>
                @endif
            </div>
        </div>
        
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="mb-0">Actions rapides</h6>
            </div>
            <div class="list-group list-group-flush">
                <a href="#" class="list-group-item list-group-item-action">
                    <i class="bi bi-calendar-plus me-2"></i> Créer une offre spéciale
                </a>
                <a href="#" class="list-group-item list-group-item-action">
                    <i class="bi bi-graph-up me-2"></i> Voir les statistiques détaillées
                </a>
                <a href="#" class="list-group-item list-group-item-action">
                    <i class="bi bi-image me-2"></i> Gérer les photos
                </a>
                <a href="#" class="list-group-item list-group-item-action text-danger" data-bs-toggle="modal" data-bs-target="#deleteSiteModal">
                    <i class="bi bi-trash me-2"></i> Supprimer ce site
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal de suppression -->
<div class="modal fade" id="deleteSiteModal" tabindex="-1" aria-labelledby="deleteSiteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteSiteModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer le site "{{ $site->name }}" ? Cette action est irréversible.
                @if($site->bookings_count > 0)
                    <div class="alert alert-warning mt-2">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Ce site a {{ $site->bookings_count }} réservation(s) associée(s). La suppression du site supprimera également toutes les réservations associées.
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('site-manager.sites.destroy', $site) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Inclure Leaflet pour la carte -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

<!-- Inclure Fancybox pour la galerie -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>

<script>
    // Initialisation de la carte
    document.addEventListener('DOMContentLoaded', function() {
        // Coordonnées du site
        const lat = {{ $site->latitude }};
        const lng = {{ $site->longitude }};
        
        // Création de la carte centrée sur les coordonnées du site
        const map = L.map('map').setView([lat, lng], 15);
        
        // Ajout de la couche de tuiles OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        
        // Ajout d'un marqueur pour le site
        L.marker([lat, lng]).addTo(map)
            .bindPopup('<b>{{ addslashes($site->name) }}</b><br>{{ addslashes($site->address) }}')
            .openPopup();
        
        // Initialisation de Fancybox pour la galerie
        Fancybox.bind("[data-fancybox]", {
            // Options personnalisées ici
        });
        
        // Initialisation des tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush

@push('styles')
<style>
    .opening-hours {
        max-height: 300px;
        overflow-y: auto;
    }
    .opening-hours::-webkit-scrollbar {
        width: 5px;
    }
    .opening-hours::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .opening-hours::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }
    .opening-hours::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
    .site-photo {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 4px;
    }
</style>
@endpush
