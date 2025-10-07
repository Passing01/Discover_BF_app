@extends('layouts.hotel-manager')
@section('title', $hotel->name)
@section('content')
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
              integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/m5Z5SaKGOGn+3qR4YQ="
              crossorigin=""/>
        <style>
            #map {
                height: 400px;
                width: 100%;
                border-radius: 8px;
                box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            }
            .amenity-icon {
                margin-right: 0.5rem;
                color: #4f46e5;
            }
            .card {
                border: none;
                box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
                margin-bottom: 1.5rem;
                border-radius: 8px;
                overflow: hidden;
            }
            .card-header {
                background-color: #f8f9fa;
                border-bottom: 1px solid rgba(0, 0, 0, 0.05);
                padding: 1rem 1.5rem;
            }
            .card-body {
                padding: 1.5rem;
            }
            .hotel-title {
                color: #2c3e50;
                font-weight: 600;
                margin-bottom: 0.5rem;
            }
            .hotel-subtitle {
                color: #6c757d;
                font-size: 0.9rem;
            }
            .btn-action {
                margin-left: 0.5rem;
                font-weight: 500;
                transition: all 0.2s ease-in-out;
            }
            .btn-edit {
                background-color: #f8f9fa;
                border: 1px solid #dee2e6;
                color: #495057;
            }
            .btn-edit:hover {
                background-color: #e9ecef;
                color: #212529;
            }
            .btn-add {
                background-color: #4f46e5;
                color: white;
            }
            .btn-add:hover {
                background-color: #4338ca;
                color: white;
            }
            .info-label {
                color: #6c757d;
                font-weight: 500;
            }
            .info-value {
                color: #2c3e50;
            }
            .section-title {
                color: #4f46e5;
                font-size: 1.25rem;
                font-weight: 600;
                margin: 1.5rem 0 1rem;
                padding-bottom: 0.5rem;
                border-bottom: 2px solid #f0f0f0;
            }
        </style>
    @endpush

    <div class="d-flex justify-content-end mb-4">
        <a href="{{ route('hotel-manager.hotels.edit', $hotel) }}" 
           class="btn btn-action btn-edit d-flex align-items-center">
            <i class="bi bi-pencil me-2"></i>
            Modifier
        </a>
        <a href="{{ route('hotels.rooms.create', ['hotel' => $hotel]) }}" 
           class="btn btn-action btn-add d-flex align-items-center">
            <i class="bi bi-plus-circle me-2"></i>
            Ajouter une chambre
        </a>
    </div>

    <div class="card">
        <!-- En-tête avec statut et actions rapides -->
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h1 class="hotel-title">
                    {{ $hotel->name }}
                </h1>
                <div class="hotel-subtitle">
                    <i class="bi bi-geo-alt-fill me-1"></i>{{ $hotel->city }}, {{ $hotel->country }}
                    @if($hotel->is_featured)
                        <span class="badge bg-warning text-dark ms-2">
                            <i class="bi bi-star-fill me-1"></i>En vedette
                        </span>
                    @endif
                    @if(!$hotel->is_active)
                        <span class="badge bg-danger ms-2">
                            <i class="bi bi-x-circle-fill me-1"></i>Inactif
                        </span>
                    @endif
                </div>
            </div>
            <div class="d-flex">
                <form action="{{ route('hotel-manager.hotels.toggle-status', $hotel) }}" method="POST" class="me-2">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-sm {{ $hotel->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}">
                        <i class="bi {{ $hotel->is_active ? 'bi-x-circle' : 'bi-check-circle' }} me-1"></i>
                        {{ $hotel->is_active ? 'Désactiver' : 'Activer' }}
                    </button>
                </form>
                <form action="{{ route('hotel-manager.hotels.toggle-featured', $hotel) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-sm {{ $hotel->is_featured ? 'btn-warning' : 'btn-outline-warning' }}">
                        <i class="bi {{ $hotel->is_featured ? 'bi-star-fill' : 'bi-star' }} me-1"></i>
                        {{ $hotel->is_featured ? 'Retirer des vedettes' : 'Mettre en vedette' }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Galerie de photos -->
        @if($hotel->photos->isNotEmpty())
            <div class="card-body p-0">
                <div class="row g-0">
                    @foreach($hotel->photos as $index => $photo)
                        <div class="{{ $index === 0 ? 'col-12' : 'col-6 col-md-3' }}">
                            <div class="ratio {{ $index === 0 ? 'ratio-16x9' : 'ratio-4x3' }} bg-light">
                                <img src="{{ Storage::url($photo->path) }}" 
                                     alt="Photo de l'hôtel {{ $hotel->name }}" 
                                     class="img-fluid h-100 w-100" 
                                     style="object-fit: cover;">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="card-body">
            <h3 class="section-title">
                <i class="bi bi-info-circle me-2"></i>Informations générales
            </h3>
            <div class="row mb-4">
                <div class="col-12">
                    <p class="mb-0">{{ $hotel->description }}</p>
                </div>
            </div>
                
            <h3 class="section-title">
                <i class="bi bi-geo-alt me-2"></i>Localisation
            </h3>
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="bi bi-geo-alt-fill me-2 text-primary"></i>Adresse
                            </h5>
                            <address class="mb-0">
                                {{ $hotel->address }}<br>
                                {{ $hotel->postal_code }} {{ $hotel->city }}<br>
                                {{ $hotel->country }}
                            </address>
                        </div>
                    </div>
                </div>
                @if($hotel->latitude && $hotel->longitude)
                    <div class="col-md-8">
                        <div class="card h-100">
                            <div class="card-body p-0">
                                <div id="map" class="h-100"></div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
                
            <h3 class="section-title">
                <i class="bi bi-telephone me-2"></i>Contact
            </h3>
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="bi bi-telephone-fill me-2 text-primary"></i>Coordonnées
                            </h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bi bi-telephone-fill text-muted me-3"></i>
                                    <a href="tel:{{ $hotel->phone }}" class="text-decoration-none text-dark">
                                        {{ $hotel->phone }}
                                    </a>
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bi bi-envelope-fill text-muted me-3"></i>
                                    <a href="mailto:{{ $hotel->email }}" class="text-decoration-none text-dark">
                                        {{ $hotel->email }}
                                    </a>
                                </li>
                                @if($hotel->website)
                                    <li class="list-group-item d-flex align-items-center">
                                        <i class="bi bi-globe text-muted me-3"></i>
                                        <a href="{{ $hotel->website }}" target="_blank" class="text-decoration-none">
                                            {{ $hotel->website }}
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="bi bi-clock-fill me-2 text-primary"></i>Horaires
                            </h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-box-arrow-in-right text-success me-2"></i>
                                        <div>
                                            <div class="small text-muted">Arrivée</div>
                                            <div class="fw-bold">À partir de {{ $hotel->check_in_time }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-box-arrow-right text-danger me-2"></i>
                                        <div>
                                            <div class="small text-muted">Départ</div>
                                            <div class="fw-bold">Jusqu'à {{ $hotel->check_out_time }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            @if($hotel->cancellation_policy)
                                <div class="mt-3 pt-3 border-top">
                                    <h6 class="d-flex align-items-center">
                                        <i class="bi bi-info-circle-fill text-primary me-2"></i>
                                        Politique d'annulation
                                    </h6>
                                    <p class="mb-0 small">{{ $hotel->cancellation_policy }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
                
            @if($hotel->amenities->isNotEmpty())
                <h3 class="section-title">
                    <i class="bi bi-stars me-2"></i>Équipements et services
                </h3>
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    @foreach($hotel->amenities as $amenity)
                                        <div class="col-md-4 col-sm-6 mb-3">
                                            <div class="d-flex align-items-center">
                                                <span class="amenity-icon">{!! $amenity->icon !!}</span>
                                                <span class="text-muted">{{ $amenity->name }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
                
                <!-- Règles de l'hôtel -->
                @if($hotel->rules->isNotEmpty())
                    <div class="row py-3 border-bottom">
                        <dt class="col-md-3 text-muted">
                            Règles de l'hôtel
                        </dt>
                        <dd class="col-md-9">
                            <ul class="list-unstyled mb-0">
                                @foreach($hotel->rules as $rule)
                                    <li class="mb-2">
                                        <div class="d-flex">
                                            <i class="bi bi-check-circle text-success me-2 mt-1"></i>
                                            <span>{{ $rule->description }}</span>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </dd>
                    </div>
                @endif
                
                <!-- Statistiques -->
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Statistiques
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="bg-light p-3 rounded-3 h-100">
                                    <p class="text-muted small mb-1">Taux d'occupation</p>
                                    <h4 class="mb-0">{{ $occupancyRate }}%</h4>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="bg-light p-3 rounded-3 h-100">
                                    <p class="text-muted small mb-1">Chambres</p>
                                    <h4 class="mb-0">{{ $hotel->rooms_count ?? $hotel->rooms->count() }}</h4>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="bg-light p-3 rounded-3 h-100">
                                    <p class="text-muted small mb-1">Note moyenne</p>
                                    <div class="d-flex align-items-center">
                                        <div class="text-warning me-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= floor($hotel->average_rating))
                                                    <i class="bi bi-star-fill"></i>
                                                @elseif($i === ceil($hotel->average_rating) && $hotel->average_rating - floor($hotel->average_rating) >= 0.5)
                                                    <i class="bi bi-star-half"></i>
                                                @else
                                                    <i class="bi bi-star"></i>
                                                @endif
                                            @endfor
                                        </div>
                                        <span class="text-muted small">{{ number_format($hotel->average_rating, 1) }} ({{ $hotel->reviews_count ?? $hotel->reviews->count() }} avis)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </dd>
                </div>
                
                <!-- Dernières réservations -->
                @if($recentBookings->isNotEmpty())
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Dernières réservations
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 rounded-lg">
                                <table class="min-w-full divide-y divide-gray-300">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">ID</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Client</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Chambre</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Dates</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Montant</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 bg-white">
                                        @foreach($recentBookings as $booking)
                                            <tr class="hover:bg-gray-50">
                                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                                    <a href="{{ route('hotel-manager.bookings.show', $booking) }}" class="text-indigo-600 hover:text-indigo-900">
                                                        #{{ $booking->id }}
                                                    </a>
                                                </td>
                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                    {{ $booking->user->name }}
                                                </td>
                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                    {{ $booking->room->name }}
                                                </td>
                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                    {{ $booking->check_in->format('d/m/Y') }} - {{ $booking->check_out->format('d/m/Y') }}
                                                </td>
                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900">
                                                    {{ number_format($booking->total_amount, 2, ',', ' ') }} €
                                                </td>
                                                <td class="whitespace-nowrap px-3 py-4">
                                                    @php
                                                        $statusColors = [
                                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                                            'confirmed' => 'bg-blue-100 text-blue-800',
                                                            'cancelled' => 'bg-red-100 text-red-800',
                                                            'checked_in' => 'bg-green-100 text-green-800',
                                                            'checked_out' => 'bg-gray-100 text-gray-800',
                                                            'completed' => 'bg-indigo-100 text-indigo-800',
                                                        ][$booking->status] ?? 'bg-gray-100 text-gray-800';
                                                    @endphp
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors }}">
                                                        {{ ucfirst($booking->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4 text-right">
                                <a href="{{ route('hotel-manager.bookings.index', ['hotel' => $hotel]) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                    Voir toutes les réservations
                                    <span aria-hidden="true"> &rarr;</span>
                                </a>
                            </div>
                        </dd>
                    </div>
                @endif
                
                <!-- Avis des clients -->
                @if($hotel->reviews->isNotEmpty())
                    <div id="reviews" class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Avis des clients
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <div class="space-y-6">
                                @foreach($hotel->reviews as $review)
                                    <div class="border-b border-gray-200 pb-6">
                                        <div class="flex items-center">
                                            <div class="flex items-center">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="h-5 w-5 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                @endfor
                                            </div>
                                            <div class="ml-4">
                                                <h4 class="text-sm font-bold text-gray-900">{{ $review->user->name }}</h4>
                                                <p class="text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-4 space-y-2">
                                            <h4 class="text-sm font-medium text-gray-900">{{ $review->title }}</h4>
                                            <p class="text-sm text-gray-600">{{ $review->comment }}</p>
                                        </div>
                                        
                                        @if($review->response)
                                            <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                                                <div class="flex">
                                                    <div class="flex-shrink-0">
                                                        <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-indigo-100">
                                                            <span class="text-sm font-medium leading-none text-indigo-800">R</span>
                                                        </span>
                                                    </div>
                                                    <div class="ml-3">
                                                        <div class="text-sm text-gray-500">
                                                            <span class="font-medium text-gray-900">Réponse du propriétaire</span>
                                                            <p class="mt-1">{{ $review->response }}</p>
                                                            <div class="mt-2 text-xs text-gray-500">
                                                                {{ $review->responded_at->format('d/m/Y') }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </dd>
                    </div>
                @endif
            </dl>
        </div>
    </div>

    @push('scripts')
        @if($hotel->latitude && $hotel->longitude)
            <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
                    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
                    crossorigin=""></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const map = L.map('map').setView([{{ $hotel->latitude }}, {{ $hotel->longitude }}], 15);
                    
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    }).addTo(map);
                    
                    L.marker([{{ $hotel->latitude }}, {{ $hotel->longitude }}])
                        .addTo(map)
                        .bindPopup('{{ $hotel->name }}')
                        .openPopup();
                });
            </script>
        @endif
    @endpush
@endsection
