@extends('layouts.hotel-manager')
@section('title', 'Mes Hôtels')

@section('content')
    <div class="d-flex justify-content-end mb-4">
        <a href="{{ route('hotel-manager.hotels.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>
            Ajouter un hôtel
        </a>
    </div>

    <div class="card">
        <div class="card-header bg-white">
            <h3 class="h5 mb-1">Liste de mes hôtels</h3>
            <p class="text-muted mb-0">Gérez tous vos établissements à partir de cette interface.</p>
        </div>
        
        @if($hotels->isEmpty())
            <div class="card-body text-center py-5">
                <i class="fas fa-hotel fa-3x text-muted mb-3"></i>
                <h4 class="h5">Aucun hôtel</h4>
                <p class="text-muted mb-4">Commencez par ajouter votre premier hôtel.</p>
                <a href="{{ route('hotel-manager.hotels.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    Ajouter un hôtel
                </a>
            </div>
        @else
            <div class="list-group list-group-flush">
                @foreach($hotels as $hotel)
                    <a href="{{ route('hotel-manager.hotels.show', $hotel) }}" class="list-group-item list-group-item-action">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="d-flex">
                                @if($hotel->photos->isNotEmpty())
                                    <div class="flex-shrink-0 me-3" style="width: 80px; height: 80px; overflow: hidden;">
                                        <img src="{{ Storage::url($hotel->photos->first()->path) }}" 
                                             alt="{{ $hotel->name }}"
                                             class="img-fluid rounded" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center rounded me-3" style="width: 80px; height: 80px;">
                                        <i class="fas fa-hotel fa-2x text-muted"></i>
                                    </div>
                                @endif
                                <div>
                                    <h5 class="mb-1 text-primary">{{ $hotel->name }}</h5>
                                    <div class="d-flex align-items-center text-muted mb-1">
                                        <i class="fas fa-map-marker-alt me-2"></i>
                                        {{ $hotel->city }}, {{ $hotel->country }}
                                    </div>
                                    <div class="mb-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $hotel->stars ? 'text-warning' : 'text-muted' }}"></i>
                                        @endfor
                                    </div>
                                    <div class="d-flex flex-wrap gap-3">
                                        <small class="text-muted">
                                            <i class="far fa-calendar-alt me-1"></i>
                                            Créé le {{ $hotel->created_at->format('d/m/Y') }}
                                        </small>
                                        <small class="text-muted">
                                            <i class="far fa-star me-1"></i>
                                            {{ $hotel->reviews_avg_rating ? number_format($hotel->reviews_avg_rating, 1) : 'Aucune' }} avis
                                        </small>
                                        <small class="text-muted">
                                            <i class="fas fa-door-open me-1"></i>
                                            {{ $hotel->rooms_count }} chambres
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex flex-column align-items-end">
                                <span class="badge bg-{{ $hotel->is_active ? 'success' : 'danger' }} mb-2">
                                    {{ $hotel->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                                <div class="text-end" style="width: 120px;">
                                    <small class="d-block text-muted mb-1">Taux d'occupation</small>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-primary" role="progressbar" 
                                             style="width: {{ $hotel->occupancy_rate ?? 0 }}%" 
                                             aria-valuenow="{{ $hotel->occupancy_rate ?? 0 }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                    <small class="text-muted">{{ number_format($hotel->occupancy_rate ?? 0, 1) }}%</small>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="card-footer bg-white border-top">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                    <div class="mb-3 mb-md-0">
                        <p class="mb-0 text-muted">
                            Affichage de <span class="fw-semibold">{{ $hotels->firstItem() }}</span>
                            à <span class="fw-semibold">{{ $hotels->lastItem() }}</span>
                            sur <span class="fw-semibold">{{ $hotels->total() }}</span> résultats
                        </p>
                    </div>
                    
                    <nav aria-label="Navigation des hôtels">
                        <ul class="pagination mb-0">
                            <!-- Previous Page Link -->
                            @if($hotels->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link">
                                        <i class="fas fa-chevron-left"></i>
                                    </span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $hotels->previousPageUrl() }}" aria-label="Précédent">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                </li>
                            @endif
                            
                            <!-- Pagination Elements -->
                            @foreach($hotels->getUrlRange(1, $hotels->lastPage()) as $page => $url)
                                @if($page == $hotels->currentPage())
                                    <li class="page-item active" aria-current="page">
                                        <span class="page-link">{{ $page }}</span>
                                    </li>
                                @elseif($page == 1 || $page == $hotels->lastPage() || ($page >= $hotels->currentPage() - 1 && $page <= $hotels->currentPage() + 1))
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                    </li>
                                @elseif(($page == $hotels->currentPage() - 2 && $page > 1) || ($page == $hotels->currentPage() + 2 && $page < $hotels->lastPage()))
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                @endif
                            @endforeach
                            
                            <!-- Next Page Link -->
                            @if($hotels->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $hotels->nextPageUrl() }}" aria-label="Suivant">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            @else
                                <li class="page-item disabled">
                                    <span class="page-link">
                                        <i class="fas fa-chevron-right"></i>
                                    </span>
                                </li>
                            @endif
                        </ul>
                    </nav>
                </div>
            </div>
            </div>
        @endif
    </div>
@endsection

