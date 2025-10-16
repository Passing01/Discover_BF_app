@extends('layouts.hotel')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 text-dark">Toutes les chambres</h1>
        @if($hotels->isNotEmpty())
            <a href="{{ route('hotels.rooms.create', ['hotel' => $hotels->first()->id]) }}" 
               class="btn btn-primary text-uppercase">
                <i class="bi bi-plus-lg me-2"></i>
                Ajouter une chambre
            </a>
        @endif
    </div>

    @if($hotels->isEmpty())
        <div class="card shadow">
            <div class="card-body text-center p-5">
                <i class="bi bi-house-door text-muted" style="font-size: 3rem;"></i>
                <h3 class="h5 mt-3 text-dark">Aucun hôtel trouvé</h3>
                <p class="text-muted">Commencez par ajouter votre premier hôtel.</p>
                <div class="mt-4">
                    <a href="{{ route('hotel-manager.hotels.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-2"></i>
                        Ajouter un hôtel
                    </a>
                </div>
            </div>
        </div>
    @else
        @foreach($hotels as $hotel)
            @if($hotel->rooms->isNotEmpty())
                <div class="card shadow mb-5">
                    <div class="card-header bg-white border-bottom-0">
                        <h3 class="h5 mb-1 text-dark">
                            {{ $hotel->name }}
                            <small class="text-muted">({{ $hotel->rooms->count() }} chambre(s))</small>
                        </h3>
                        <p class="mb-0 text-muted">
                            {{ $hotel->address }}, {{ $hotel->postal_code }} {{ $hotel->city }}, {{ $hotel->country }}
                        </p>
                    </div>
                    
                    <div class="list-group list-group-flush">
                        @foreach($hotel->rooms as $room)
                            <a href="{{ route('hotels.rooms.show', ['hotel' => $hotel, 'room' => $room]) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        @if($room->photos->isNotEmpty())
                                            <div class="flex-shrink-0 me-3" style="width: 64px; height: 64px; overflow: hidden;">
                                                <img class="img-fluid h-100 w-100 object-fit-cover rounded" 
                                                     src="{{ Storage::url($room->photos->first()->path) }}" 
                                                     alt="{{ $room->name }}">
                                            </div>
                                        @else
                                            <div class="flex-shrink-0 me-3 d-flex align-items-center justify-content-center bg-light rounded" style="width: 64px; height: 64px;">
                                                <i class="bi bi-image text-muted" style="font-size: 1.5rem;"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="d-flex align-items-center">
                                                <h5 class="mb-0 text-primary">{{ $room->name }}</h5>
                                                @if(!$room->is_available)
                                                    <span class="badge bg-danger ms-2">
                                                        Indisponible
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="mb-1 text-muted">
                                                {{ $room->type }} • {{ $room->capacity }} personne(s)
                                            </p>
                                            <p class="mb-0">
                                                <i class="bi bi-currency-euro"></i> 
                                                {{ number_format($room->price_per_night, 2, ',', ' ') }} / nuit
                                            </p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="text-end me-3">
                                            <div class="fw-medium">{{ $room->quantity }} chambre(s)</div>
                                            <span class="badge {{ $room->is_available ? 'bg-success' : 'bg-danger' }}">
                                                {{ $room->is_available ? 'Disponible' : 'Indisponible' }}
                                            </span>
                                        </div>
                                        <i class="bi bi-chevron-right text-muted"></i>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    @endif
</div>
@endsection
