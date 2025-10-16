@extends('layouts.hotel')

@section('content')
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lightgallery@2.0.0-beta.3/css/lightgallery-bundle.min.css" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
        <style>
            .gallery-item {
                cursor: pointer;
                transition: transform 0.2s;
            }
            .gallery-item:hover {
                transform: scale(1.02);
            }
            .amenity-icon {
                color: #4f46e5;
                margin-right: 0.5rem;
            }
            .status-badge {
                display: inline-flex;
                align-items: center;
                padding: 0.25rem 0.75rem;
                border-radius: 50rem;
                font-size: 0.75rem;
                font-weight: 500;
                text-transform: capitalize;
            }
            .status-available {
                background-color: #d1e7dd;
                color: #0f5132;
            }
            .status-unavailable {
                background-color: #f8d7da;
                color: #842029;
            }
            .stat-card {
                background-color: white;
                border-radius: 0.5rem;
                padding: 1.5rem;
                box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
                transition: all 0.2s;
                height: 100%;
            }
            .stat-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            }
            .stat-value {
                font-size: 1.5rem;
                font-weight: 700;
                color: #212529;
                margin-top: 0.5rem;
            }
            .stat-label {
                font-size: 0.875rem;
                color: #6c757d;
                margin-top: 0.25rem;
            }
            .custom-badge {
                display: inline-flex;
                align-items: center;
                padding: 0.25rem 0.5rem;
                border-radius: 0.25rem;
                font-size: 0.75rem;
                font-weight: 500;
                margin-right: 0.5rem;
                margin-bottom: 0.5rem;
            }
            .badge-primary-custom {
                background-color: #e0e7ff;
                color: #4f46e5;
            }
            .badge-success-custom {
                background-color: #d1e7dd;
                color: #0f5132;
            }
            .badge-warning-custom {
                background-color: #fff3cd;
                color: #664d03;
            }
            .badge-info-custom {
                background-color: #cff4fc;
                color: #055160;
            }
        </style>
    @endpush

    <div class="mb-4" name="actions">
        <div class="d-flex flex-wrap align-items-center">
            <a href="{{ route('hotel-manager.rooms.index', $hotel) }}" 
               class="btn btn-outline-secondary me-2 mb-2 d-inline-flex align-items-center">
                <i class="bi bi-arrow-left me-2"></i>
                Retour à la liste
            </a>
            <div class="me-2 mb-2">
                <a href="{{ route('hotels.rooms.edit', ['hotel' => $hotel, 'room' => $room]) }}" 
                   class="btn btn-primary d-inline-flex align-items-center">
                    <i class="bi bi-pencil me-2"></i>
                    Modifier
                </a>
            </div>
            <div class="mb-2">
                <form action="{{ route('hotels.rooms.destroy', ['hotel' => $hotel, 'room' => $room]) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette chambre ? Cette action est irréversible.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger d-inline-flex align-items-center">
                        <i class="bi bi-trash me-2"></i>
                        Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg mb-6">
        <!-- En-tête avec statut et actions rapides -->
        <div class="card-header bg-light py-3 px-4 border-bottom">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <div class="mb-3 mb-md-0">
                    <h3 class="h5 mb-1 text-dark">
                        {{ $room->name }}
                    </h3>
                    <p class="mb-0 text-muted">
                        {{ ucfirst($room->type) }} • {{ $room->capacity }} personne(s) • {{ $room->size ?? 'N/A' }} m²
                    </p>
                </div>
                <div class="d-flex flex-column flex-sm-row align-items-start align-items-md-center">
                    <span class="badge {{ $room->is_available ? 'bg-success' : 'bg-danger' }} d-inline-flex align-items-center mb-2 mb-sm-0 me-sm-3">
                        <i class="bi {{ $room->is_available ? 'bi-check-circle' : 'bi-x-circle' }} me-1"></i>
                        {{ $room->is_available ? 'Disponible' : 'Indisponible' }}
                    </span>
                    
                    <form action="{{ route('hotel-manager.hotels.rooms.toggle-availability', ['hotel' => $hotel, 'room' => $room]) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm {{ $room->available ? 'btn-warning' : 'btn-success' }}">
                            <i class="bi {{ $room->available ? 'bi-x-circle' : 'bi-check-circle' }} me-1"></i>
                            {{ $room->available ? 'Marquer comme indisponible' : 'Marquer comme disponible' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="card-body p-4">
            <!-- Galerie de photos -->
            @if($room->photos->isNotEmpty())
                <div class="mb-5">
                    <div class="row g-3" id="lightgallery">
                        @foreach($room->photos as $photo)
                            <div class="col-6 col-md-4 col-lg-3">
                                <a href="{{ Storage::url($photo->path) }}" class="d-block gallery-item rounded overflow-hidden shadow-sm border">
                                    <img src="{{ Storage::url($photo->path) }}" alt="Photo de la chambre" class="img-fluid w-100" style="height: 180px; object-fit: cover;">
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="row g-4">
                <!-- Colonne de gauche - Détails de la chambre -->
                <div class="col-lg-8">
                    <!-- Statistiques -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h3 class="h5 mb-4">Statistiques</h3>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="p-2 rounded-circle bg-primary bg-opacity-10 text-primary me-3">
                                                    <i class="bi bi-calendar-check fs-4"></i>
                                                </div>
                                                <div>
                                                    <div class="h4 mb-0">{{ $room->bookings_count ?? 0 }}</div>
                                                    <div class="text-muted small">Réservations totales</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="p-2 rounded-circle bg-success bg-opacity-10 text-success me-3">
                                                    <i class="bi bi-graph-up-arrow fs-4"></i>
                                                </div>
                                                <div>
                                                    <div class="h4 mb-0">{{ number_format($room->occupancy_rate ?? 0, 1) }}%</div>
                                                    <div class="text-muted small">Taux d'occupation (30j)</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="p-2 rounded-circle bg-warning bg-opacity-10 text-warning me-3">
                                                    <i class="bi bi-star-fill fs-4"></i>
                                                </div>
                                                <div>
                                                    <div class="h4 mb-0">{{ number_format($room->average_rating ?? 0, 1) }}/5</div>
                                                    <div class="text-muted small">Note moyenne</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            
                                <div class="col-md-6">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="p-2 rounded-circle bg-info bg-opacity-10 text-info me-3">
                                                    <i class="bi bi-people fs-4"></i>
                                                </div>
                                                <div>
                                                    <div class="h4 mb-0">{{ $room->capacity }}</div>
                                                    <div class="text-muted small">Capacité maximale</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="p-2 rounded-circle bg-primary bg-opacity-10 text-primary me-3">
                                                    <i class="bi bi-door-open fs-4"></i>
                                                </div>
                                                <div>
                                                    <div class="h4 mb-0">{{ $room->quantity }}</div>
                                                    <div class="text-muted small">Chambres disponibles</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h3 class="h5 mb-3">Description</h3>
                            <div class="text-muted">
                                @if(is_array($room->description))
                                    @php
                                        $description = '';
                                        foreach($room->description as $key => $value) {
                                            if (is_array($value) || is_object($value)) continue;
                                            $label = str_replace('_', ' ', ucfirst($key));
                                            $description .= "<strong>{$label}:</strong> " . e($value) . "<br>";
                                        }
                                    @endphp
                                    {!! $description ?: 'Aucune description disponible.' !!}
                                @else
                                    {{ $room->description ?? 'Aucune description disponible.' }}
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Caractéristiques -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h3 class="h5 mb-4">Caractéristiques</h3>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <h4 class="small text-muted mb-1">Type de lit</h4>
                                    <p class="mb-0">{{ $room->bed_type ? ucfirst(str_replace('_', ' ', $room->bed_type)) : 'Non spécifié' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h4 class="small text-muted mb-1">Superficie</h4>
                                    <p class="mb-0">{{ $room->size ? $room->size . ' m²' : 'Non spécifiée' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h4 class="small text-muted mb-1">Vue</h4>
                                    <p class="mb-0">{{ $room->view ? ucfirst(str_replace('_', ' ', $room->view)) : 'Non spécifiée' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h4 class="small text-muted mb-1">Fumeurs</h4>
                                    <p class="mb-0">{{ $room->is_smoking_allowed ? 'Autorisé' : 'Non autorisé' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Équipements -->
                    @if($room->amenities->isNotEmpty())
                        <div class="card mb-4">
                            <div class="card-body">
                                <h3 class="h5 mb-4">Équipements</h3>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($room->amenities as $amenity)
                                        @php
                                            $badgeClasses = [
                                                'wifi' => 'bg-primary bg-opacity-10 text-primary',
                                                'tv' => 'bg-info bg-opacity-10 text-info',
                                                'ac' => 'bg-success bg-opacity-10 text-success',
                                                'minibar' => 'bg-warning bg-opacity-10 text-warning',
                                                'default' => 'bg-light text-dark'
                                            ];
                                            $badgeClass = $badgeClasses[$amenity->slug] ?? $badgeClasses['default'];
                                        @endphp
                                        <span class="badge d-flex align-items-center {{ $badgeClass }} px-3 py-2">
                                            <i class="bi bi-{{ $amenity->icon ?? 'check-circle' }} me-1"></i>
                                            {{ $amenity->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Politique d'annulation -->
                    @if($room->cancellation_policy)
                        <div class="card mb-4 border-warning">
                            <div class="card-header bg-warning bg-opacity-10 d-flex align-items-center">
                                <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>
                                <h3 class="h5 mb-0">Politique d'annulation</h3>
                            </div>
                            <div class="card-body">
                                <p class="mb-0">
                                    {!! nl2br(e($room->cancellation_policy)) !!}
                                </p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Colonne de droite - Prix et réservation -->
                <div class="col-lg-4">
                    <div class="card shadow-sm sticky-top" style="top: 20px;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h3 class="h5 mb-0">Prix</h3>
                                <span class="h4 fw-bold text-primary">{{ number_format($room->price_per_night, 2, ',', ' ') }} €</span>
                            </div>
                            <p class="text-muted small mb-4">Prix par nuit, taxes comprises</p>
                            
                            <div class="mb-4">
                                <h4 class="h6 mb-2">Disponibilité</h4>
                                <div class="d-flex align-items-center">
                                    @if($room->is_available)
                                        <span class="badge bg-success bg-opacity-10 text-success d-flex align-items-center">
                                            <i class="bi bi-check-circle-fill me-1"></i>
                                            Disponible
                                        </span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger d-flex align-items-center">
                                            <i class="bi bi-x-circle-fill me-1"></i>
                                            Indisponible
                                        </span>
                                    @endif
                                    <span class="ms-2 small text-muted">{{ $room->quantity }} chambre(s)</span>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <h4 class="h6 mb-2">Capacité</h4>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="d-flex align-items-center">
                                        <i class="bi bi-people me-1 text-muted"></i>
                                        <span class="small">
                                            <span class="fw-medium">{{ $room->max_adults ?? $room->capacity }}</span> adulte(s)
                                        </span>
                                    </span>
                                    @if(($room->max_children ?? 0) > 0)
                                        <span class="text-muted">•</span>
                                        <span class="d-flex align-items-center">
                                            <i class="bi bi-emoji-smile me-1 text-muted"></i>
                                            <span class="small">
                                                <span class="fw-medium">{{ $room->max_children }}</span> enfant(s)
                                            </span>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <h4 class="h6 mb-2">Séjour minimum</h4>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-calendar-week me-2 text-muted"></i>
                                    <span class="small">{{ $room->min_stay }} nuit(s) minimum</span>
                                </div>
                            </div>
                            
                            <div class="pt-4 border-top">
                                <a href="{{ route('hotels.rooms.edit', ['hotel' => $hotel, 'room' => $room]) }}" 
                                   class="btn btn-primary w-100 d-flex align-items-center justify-content-center">
                                    <i class="bi bi-pencil-square me-2"></i>
                                    Modifier la chambre
                                </a>
                                
                                <div class="text-center mt-3">
                                    <a href="#" class="text-decoration-none small text-primary">
                                        <i class="bi bi-calendar-check me-1"></i>
                                        Voir les disponibilités
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Dernières réservations -->
                    @if($recentBookings->isNotEmpty())
                        <div class="card mt-4">
                            <div class="card-header bg-light">
                                <h3 class="h5 mb-0">Dernières réservations</h3>
                            </div>
                            <div class="list-group list-group-flush">
                                @foreach($recentBookings as $booking)
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span class="fw-medium text-primary">#{{ $booking->reference }}</span>
                                                    <span class="badge {{ 
                                                        $booking->status === 'confirmed' ? 'bg-success bg-opacity-10 text-success' : 
                                                        ($booking->status === 'cancelled' ? 'bg-danger bg-opacity-10 text-danger' : 
                                                        'bg-warning bg-opacity-10 text-warning') 
                                                    }} rounded-pill small">
                                                        {{ ucfirst($booking->status) }}
                                                    </span>
                                                </div>
                                                
                                                <div class="d-flex flex-column flex-md-row gap-3">
                                                    <div class="d-flex align-items-center text-muted small">
                                                        <i class="bi bi-calendar3 me-2"></i>
                                                        {{ $booking->check_in->format('d/m/Y') }} - {{ $booking->check_out->format('d/m/Y') }}
                                                    </div>
                                                    <div class="d-flex align-items-center text-muted small">
                                                        <i class="bi bi-currency-euro me-2"></i>
                                                        {{ number_format($booking->total_amount, 2, ',', ' ') }} €
                                                    </div>
                                                </div>
                                            </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                                @if($room->bookings_count > 5)
                                    <div class="px-4 py-4 sm:px-6 border-t border-gray-200">
                                        <a href="#" class="block text-center text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                            Voir toutes les réservations ({{ $room->bookings_count }})
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Avis des clients -->
    @if($room->reviews->isNotEmpty())
        <div class="card mt-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h3 class="h5 mb-0">
                    Avis des clients
                    <span class="text-muted small fw-normal">({{ $room->reviews->count() }} avis)</span>
                </h3>
                <div class="d-flex align-items-center">
                    <div class="star-rating me-2">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= floor($room->average_rating))
                                <i class="bi bi-star-fill text-warning"></i>
                            @elseif($i == ceil($room->average_rating) && $room->average_rating - floor($room->average_rating) >= 0.5)
                                <i class="bi bi-star-half text-warning"></i>
                            @else
                                <i class="bi bi-star text-warning"></i>
                            @endif
                        @endfor
                    </div>
                    <span class="text-muted small">{{ number_format($room->average_rating, 1, ',', ' ') }}/5</span>
                </div>
            </div>
            
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @foreach($room->reviews->take(3) as $review)
                        <div class="list-group-item border-0 px-0">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center">
                                        <span class="text-muted">{{ substr($review->user->name, 0, 1) }}</span>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h6 class="mb-0">{{ $review->user->name }}</h6>
                                        <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                                    </div>
                                    <div class="mb-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $review->rating)
                                                <i class="bi bi-star-fill text-warning"></i>
                                            @else
                                                <i class="bi bi-star text-warning"></i>
                                            @endif
                                        @endfor
                                        <span class="text-muted small ms-1">{{ $review->rating }}/5</span>
                                    </div>
                                    @if($review->title)
                                        <h6 class="fw-bold mb-2">{{ $review->title }}</h6>
                                    @endif
                                    <p class="mb-0">{{ $review->comment }}</p>
                                    
                                    @if($review->response)
                                        <div class="mt-3 p-3 bg-light rounded">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-2 text-muted">
                                                    <i class="bi bi-chat-square-quote"></i>
                                                </div>
                                                <div>
                                                    <p class="mb-1 fw-medium small">Réponse du propriétaire</p>
                                                    <p class="mb-1">{{ $review->response }}</p>
                                                    <small class="text-muted">
                                                        <i class="bi bi-clock me-1"></i>
                                                        Réponse du {{ $review->responded_at->format('d/m/Y') }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                    
                    @if($room->reviews->count() > 3)
                        <div class="text-center mt-3">
                            <a href="#" class="text-decoration-none">
                                <i class="bi bi-chat-square-text me-1"></i>
                                Voir tous les avis ({{ $room->reviews->count() }})
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/lightgallery@2.0.0-beta.3/lightgallery.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lightgallery@2.0.0-beta.3/plugins/zoom/lg-zoom.umd.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialisation de la galerie d'images
            const lightGallery = document.getElementById('lightgallery');
            if (lightGallery) {
                window.lightGallery(lightGallery, {
                    selector: 'a',
                    plugins: [lgZoom],
                    speed: 500,
                    download: false,
                    counter: false,
                });
            }
            
            // Gestion des onglets
            const tabs = document.querySelectorAll('[data-tab]');
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const target = this.dataset.tab;
                    
                    // Mettre à jour l'onglet actif
                    document.querySelectorAll('[data-tab]').forEach(t => {
                        t.classList.remove('border-indigo-500', 'text-indigo-600');
                        t.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
                    });
                    this.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
                    this.classList.add('border-indigo-500', 'text-indigo-600');
                    
                    // Afficher le contenu correspondant
                    document.querySelectorAll('[data-tab-content]').forEach(content => {
                        content.classList.add('hidden');
                    });
                    document.querySelector(`[data-tab-content="${target}"]`).classList.remove('hidden');
                });
            });
            
            // Initialiser le premier onglet comme actif
            if (tabs.length > 0) {
                tabs[0].click();
            }
        });
    </script>
@endpush
