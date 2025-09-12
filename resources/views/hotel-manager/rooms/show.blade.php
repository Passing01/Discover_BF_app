@extends('layouts.hotel-manager')

@section('content')
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lightgallery@2.0.0-beta.3/css/lightgallery-bundle.min.css" />
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
                border-radius: 9999px;
                font-size: 0.75rem;
                font-weight: 500;
                text-transform: capitalize;
            }
            .status-available {
                background-color: #dcfce7;
                color: #166534;
            }
            .status-unavailable {
                background-color: #fee2e2;
                color: #991b1b;
            }
            .stats-grid {
                display: grid;
                grid-template-columns: repeat(1, 1fr);
                gap: 1rem;
            }
            @media (min-width: 640px) {
                .stats-grid {
                    grid-template-columns: repeat(2, 1fr);
                }
            }
            @media (min-width: 1024px) {
                .stats-grid {
                    grid-template-columns: repeat(4, 1fr);
                }
            }
            .stat-card {
                background-color: white;
                border-radius: 0.5rem;
                padding: 1.5rem;
                box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
                transition: all 0.2s;
            }
            .stat-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            }
            .stat-value {
                font-size: 1.5rem;
                font-weight: 700;
                color: #111827;
                margin-top: 0.5rem;
            }
            .stat-label {
                font-size: 0.875rem;
                color: #6b7280;
                margin-top: 0.25rem;
            }
            .badge {
                display: inline-flex;
                align-items: center;
                padding: 0.25rem 0.5rem;
                border-radius: 0.25rem;
                font-size: 0.75rem;
                font-weight: 500;
                margin-right: 0.5rem;
                margin-bottom: 0.5rem;
            }
            .badge-primary {
                background-color: #e0e7ff;
                color: #4f46e5;
            }
            .badge-success {
                background-color: #dcfce7;
                color: #166534;
            }
            .badge-warning {
                background-color: #fef3c7;
                color: #92400e;
            }
            .badge-info {
                background-color: #dbeafe;
                color: #1e40af;
            }
        </style>
    @endpush

    <div name="actions">
        <a href="{{ route('hotel-manager.rooms.index', $hotel) }}" 
           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
            Retour à la liste
        </a>
        <div class="ml-3">
            <a href="{{ route('hotel-manager.rooms.edit', ['hotel' => $hotel, 'room' => $room]) }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                </svg>
                Modifier
            </a>
        </div>
        <div class="ml-3">
            <form action="{{ route('hotel-manager.rooms.destroy', ['hotel' => $hotel, 'room' => $room]) }}" method="POST" class="inline-block" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette chambre ? Cette action est irréversible.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    Supprimer
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <!-- En-tête avec statut et actions rapides -->
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        {{ $room->name }}
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        {{ $room->roomType->name }} • {{ $room->capacity }} personne(s) • {{ $room->size ?? 'N/A' }} m²
                    </p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <span class="status-badge {{ $room->is_available ? 'status-available' : 'status-unavailable' }}">
                        @if($room->is_available)
                            <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Disponible
                        @else
                            <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Indisponible
                        @endif
                    </span>
                    
                    <form action="{{ route('hotel-manager.rooms.toggle-availability', ['hotel' => $hotel, 'room' => $room]) }}" method="POST" class="inline-block ml-2">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="text-sm {{ $room->is_available ? 'text-yellow-600 hover:text-yellow-800' : 'text-green-600 hover:text-green-800' }} font-medium">
                            {{ $room->is_available ? 'Marquer comme indisponible' : 'Marquer comme disponible' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="px-4 py-5 sm:p-6">
            <!-- Galerie de photos -->
            @if($room->photos->isNotEmpty())
                <div class="mb-8">
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4" id="lightgallery">
                        @foreach($room->photos as $photo)
                            <a href="{{ Storage::url($photo->path) }}" class="gallery-item rounded-lg overflow-hidden shadow-sm border border-gray-200">
                                <img src="{{ Storage::url($photo->path) }}" alt="Photo de la chambre" class="w-full h-48 object-cover">
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Colonne de gauche - Détails de la chambre -->
                <div class="lg:col-span-2">
                    <!-- Statistiques -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Statistiques</h3>
                        <div class="stats-grid">
                            <div class="stat-card">
                                <div class="flex items-center">
                                    <div class="p-2 rounded-full bg-indigo-100 text-indigo-600">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <div class="stat-value">{{ $room->bookings_count ?? 0 }}</div>
                                        <div class="stat-label">Réservations totales</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="stat-card">
                                <div class="flex items-center">
                                    <div class="p-2 rounded-full bg-green-100 text-green-600">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <div class="stat-value">{{ number_format($room->occupancy_rate ?? 0, 1) }}%</div>
                                        <div class="stat-label">Taux d'occupation (30j)</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="stat-card">
                                <div class="flex items-center">
                                    <div class="p-2 rounded-full bg-yellow-100 text-yellow-600">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <div class="stat-value">{{ number_format($room->average_rating ?? 0, 1) }}/5</div>
                                        <div class="stat-label">Note moyenne</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="stat-card">
                                <div class="flex items-center">
                                    <div class="p-2 rounded-full bg-blue-100 text-blue-600">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <div class="stat-value">{{ $room->quantity }}</div>
                                        <div class="stat-label">Chambres disponibles</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Description</h3>
                        <div class="prose max-w-none text-gray-600">
                            {!! nl2br(e($room->description)) !!}
                        </div>
                    </div>

                    <!-- Caractéristiques -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Caractéristiques</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 mb-2">Type de lit</h4>
                                <p class="text-gray-900">{{ $room->bed_type ? ucfirst(str_replace('_', ' ', $room->bed_type)) : 'Non spécifié' }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 mb-2">Superficie</h4>
                                <p class="text-gray-900">{{ $room->size ? $room->size . ' m²' : 'Non spécifiée' }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 mb-2">Vue</h4>
                                <p class="text-gray-900">{{ $room->view ? ucfirst(str_replace('_', ' ', $room->view)) : 'Non spécifiée' }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 mb-2">Fumeurs</h4>
                                <p class="text-gray-900">{{ $room->is_smoking_allowed ? 'Autorisé' : 'Non autorisé' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Équipements -->
                    @if($room->amenities->isNotEmpty())
                        <div class="mb-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Équipements</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($room->amenities as $amenity)
                                    <span class="badge {{ ['wifi' => 'badge-primary', 'tv' => 'badge-info', 'ac' => 'badge-success', 'minibar' => 'badge-warning'][$amenity->slug] ?? 'badge-primary' }}">
                                        <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        {{ $amenity->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Politique d'annulation -->
                    @if($room->cancellation_policy)
                        <div class="mb-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Politique d'annulation</h3>
                            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-yellow-700">
                                            {!! nl2br(e($room->cancellation_policy)) !!}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Colonne de droite - Prix et réservation -->
                <div>
                    <div class="bg-gray-50 rounded-lg p-6 sticky top-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Prix</h3>
                            <span class="text-2xl font-bold text-gray-900">{{ number_format($room->price_per_night, 2, ',', ' ') }} €</span>
                        </div>
                        <p class="text-sm text-gray-500 mb-6">Prix par nuit, taxes comprises</p>
                        
                        <div class="space-y-4">
                            <div>
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Disponibilité</h4>
                                <div class="flex items-center">
                                    @if($room->is_available)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3" />
                                            </svg>
                                            Disponible
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-red-400" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3" />
                                            </svg>
                                            Indisponible
                                        </span>
                                    @endif
                                    <span class="ml-2 text-sm text-gray-500">{{ $room->quantity }} chambre(s)</span>
                                </div>
                            </div>
                            
                            <div>
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Capacité</h4>
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm text-gray-500">
                                        <span class="font-medium">{{ $room->max_adults ?? $room->capacity }}</span> adulte(s)
                                    </span>
                                    @if(($room->max_children ?? 0) > 0)
                                        <span class="text-gray-300">•</span>
                                        <span class="text-sm text-gray-500">
                                            <span class="font-medium">{{ $room->max_children }}</span> enfant(s)
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <div>
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Séjour minimum</h4>
                                <p class="text-sm text-gray-500">{{ $room->min_stay }} nuit(s) minimum</p>
                            </div>
                            
                            <div class="pt-4 border-t border-gray-200">
                                <a href="{{ route('hotel-manager.rooms.edit', ['hotel' => $hotel, 'room' => $room]) }}" 
                                   class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Modifier la chambre
                                </a>
                                
                                <div class="mt-3 flex justify-center">
                                    <a href="#" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                        Voir les disponibilités
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Dernières réservations -->
                    @if($recentBookings->isNotEmpty())
                        <div class="mt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Dernières réservations</h3>
                            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                                <ul class="divide-y divide-gray-200">
                                    @foreach($recentBookings as $booking)
                                        <li class="px-4 py-4 sm:px-6">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <p class="text-sm font-medium text-indigo-600 truncate">
                                                        #{{ $booking->reference }}
                                                    </p>
                                                    <div class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-6">
                                                        <div class="mt-2 flex items-center text-sm text-gray-500">
                                                            <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                                            </svg>
                                                            {{ $booking->check_in->format('d/m/Y') }} - {{ $booking->check_out->format('d/m/Y') }}
                                                        </div>
                                                        <div class="mt-2 flex items-center text-sm text-gray-500">
                                                            <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                                <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd" />
                                                            </svg>
                                                            {{ number_format($booking->total_amount, 2, ',', ' ') }} €
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="ml-2 flex-shrink-0">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ 
                                                        $booking->status === 'confirmed' ? 'bg-green-100 text-green-800' : 
                                                        ($booking->status === 'cancelled' ? 'bg-red-100 text-red-800' : 
                                                        'bg-yellow-100 text-yellow-800') 
                                                    }}">
                                                        {{ ucfirst($booking->status) }}
                                                    </span>
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
        <div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Avis des clients
                    <span class="text-sm font-normal text-gray-500">({{ $room->reviews->count() }} avis)</span>
                </h3>
                <div class="mt-2 flex items-center">
                    <div class="flex items-center">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= floor($room->average_rating))
                                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            @elseif($i == ceil($room->average_rating) && $room->average_rating - floor($room->average_rating) >= 0.5)
                                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            @else
                                <svg class="h-5 w-5 text-gray-300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            @endif
                        @endfor
                    </div>
                    <span class="ml-2 text-sm text-gray-600">{{ number_format($room->average_rating, 1) }} sur 5</span>
                </div>
            </div>
            
            <div class="px-4 py-5 sm:p-6">
                <div class="space-y-8">
                    @foreach($room->reviews->take(3) as $review)
                        <div class="border-b border-gray-200 pb-6 last:border-0 last:pb-0">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 font-medium">
                                    {{ substr($review->user->name, 0, 1) }}
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-sm font-medium text-gray-900">{{ $review->user->name }}</h4>
                                    <div class="flex items-center mt-1">
                                        <div class="flex items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $review->rating)
                                                    <svg class="h-4 w-4 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                @else
                                                    <svg class="h-4 w-4 text-gray-300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                @endif
                                            @endfor
                                        </div>
                                        <span class="ml-2 text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4">
                                <h5 class="text-sm font-medium text-gray-900">{{ $review->title }}</h5>
                                <p class="mt-1 text-sm text-gray-600">{{ $review->comment }}</p>
                            </div>
                            
                            @if($review->response)
                                <div class="mt-4 pl-4 border-l-4 border-gray-200">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm2 0h2v2H9V8zm6 0h-2v2h2V8z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3
                                            <p class="text-sm text-gray-700">
                                                <span class="font-medium text-gray-900">Réponse du propriétaire</span><br>
                                                {{ $review->response }}
                                            </p>
                                            <div class="mt-2 text-xs text-gray-500">
                                                Réponse du {{ $review->responded_at->format('d/m/Y') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="mt-4">
                                    <button type="button" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 focus:outline-none">
                                        Répondre à cet avis
                                    </button>
                                </div>
                            @endif
                        </div>
                    @endforeach
                    
                    @if($room->reviews->count() > 3)
                        <div class="text-center">
                            <a href="#" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
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
