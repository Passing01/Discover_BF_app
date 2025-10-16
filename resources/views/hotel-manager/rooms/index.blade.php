@extends('layouts.hotel')

@section('title', 'Gestion des Chambres')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <h1>Gestion des Chambres</h1>
            <div>
                <a href="{{ route('hotel-manager.rooms.create', $hotel) }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Ajouter une chambre
                </a>
                <a href="{{ route('hotel-manager.hotels.show', $hotel) }}" class="btn btn-outline-secondary ms-2">
                    <i class="fas fa-arrow-left me-2"></i>Retour à l'hôtel
                </a>
            </div>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('hotel-manager.dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hotel-manager.hotels.index') }}">Mes Hôtels</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hotel-manager.hotels.show', $hotel) }}">{{ $hotel->name }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Chambres</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="mb-4">
                <h5 class="card-title">Hôtel : {{ $hotel->name }}</h5>
                <p class="text-muted mb-0">Gérez les chambres de votre établissement. Vous avez {{ $rooms->total() }} chambre(s) enregistrée(s).</p>
            </div>

            @if($rooms->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Chambre</th>
                                <th>Type</th>
                                <th>Capacité</th>
                                <th>Prix/nuit</th>
                                <th>Quantité</th>
                                <th>Statut</th>
                                <th>Équipements</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rooms as $room)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($room->photos->isNotEmpty())
                                            <img src="{{ Storage::url($room->photos->first()->path) }}" alt="{{ $room->name }}" class="rounded me-3" width="50" height="50" style="object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                                <i class="fas fa-door-open text-muted"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <h6 class="mb-0">{{ $room->name }}</h6>
                                            <small class="text-muted">Créée le {{ $room->created_at->format('d/m/Y') }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $room->roomType->name }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        <i class="fas fa-user me-1"></i>{{ $room->capacity }}
                                    </span>
                                </td>
                                <td>
                                    <strong>{{ number_format($room->price_per_night, 2, ',', ' ') }} €</strong>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $room->quantity }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $room->is_available ? 'success' : 'danger' }}">
                                        {{ $room->is_available ? 'Disponible' : 'Indisponible' }}
                                    </span>
                                </td>
                                <td>
                                    @if($room->amenities->isNotEmpty())
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($room->amenities->take(2) as $amenity)
                                                <span class="badge bg-light text-dark border">{{ $amenity->name }}</span>
                                            @endforeach
                                            @if($room->amenities->count() > 2)
                                                <span class="badge bg-light text-dark border">+{{ $room->amenities->count() - 2 }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted">Aucun</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('hotel-manager.rooms.show', ['hotel' => $hotel, 'room' => $room]) }}" class="btn btn-sm btn-info text-white" title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('hotel-manager.rooms.edit', ['hotel' => $hotel, 'room' => $room]) }}" class="btn btn-sm btn-outline-primary" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $room->id }}" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteModal{{ $room->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Confirmer la suppression</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Êtes-vous sûr de vouloir supprimer la chambre <strong>{{ $room->name }}</strong> ?
                                                    Cette action est irréversible.
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <form action="{{ route('hotel-manager.rooms.destroy', ['hotel' => $hotel, 'room' => $room]) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Supprimer</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $rooms->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-door-open fa-4x text-muted"></i>
                    </div>
                    <h4 class="mb-3">Aucune chambre enregistrée</h4>
                    <p class="text-muted mb-4">Commencez par ajouter votre première chambre pour gérer votre établissement.</p>
                    <a href="{{ route('hotel-manager.rooms.create', $hotel) }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Ajouter une chambre
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table th {
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #5a5c69;
        border-top: none;
    }
    
    .table td {
        vertical-align: middle;
    }
    
    .btn-group .btn {
        border-radius: 0.25rem !important;
    }
    
    .btn-group .btn:first-child {
        border-top-right-radius: 0 !important;
        border-bottom-right-radius: 0 !important;
    }
    
    .btn-group .btn:last-child {
        border-top-left-radius: 0 !important;
        border-bottom-left-radius: 0 !important;
    }
    
    .btn-group .btn:not(:first-child) {
        margin-left: -1px;
    }
    
    .page-item.active .page-link {
        background-color: #4e73df;
        border-color: #4e73df;
    }
    
    .page-link {
        color: #4e73df;
    }
    
    .badge {
        font-size: 0.75em;
    }
</style>
@endpush