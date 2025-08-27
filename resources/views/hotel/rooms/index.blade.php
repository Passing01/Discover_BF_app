@extends('layouts.tourist')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Gestion des Chambres</h2>
        <a href="{{ route('hotel.rooms.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Ajouter une chambre
        </a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Numéro</th>
                        <th>Type</th>
                        <th>Prix/Nuit</th>
                        <th>Capacité</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rooms as $room)
                        <tr>
                            <td>{{ $room->room_number }}</td>
                            <td>{{ $room->type->name }}</td>
                            <td>{{ number_format($room->price_per_night, 0, ',', ' ') }} FCFA</td>
                            <td>{{ $room->capacity }} personne(s)</td>
                            <td>
                                <span class="badge bg-{{ $room->is_available ? 'success' : 'secondary' }}">
                                    {{ $room->is_available ? 'Disponible' : 'Occupée' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('hotel.rooms.edit', $room) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('hotel.rooms.destroy', $room) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette chambre ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Aucune chambre enregistrée pour le moment.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($rooms->hasPages())
            <div class="card-footer">
                {{ $rooms->links() }}
            </div>
        @endif
    </div>
</div>

@include('hotel.partials.availability_modal')
@endsection
