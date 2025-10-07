@extends('layouts.hotel-manager')

@section('title', 'Gestion des clients - ' . $hotel->name)

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gestion des clients</h1>
        <a href="{{ route('hotels.clients.create', $hotel->id) }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nouveau client
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Liste des clients</h6>
            <div class="d-flex">
                <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Rechercher un client...">
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="clientsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nom complet</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Réservations</th>
                            <th>Dernière réservation</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clients as $client)
                            <tr>
                                <td>{{ $client->full_name }}</td>
                                <td>{{ $client->email }}</td>
                                <td>{{ $client->phone ?? 'Non renseigné' }}</td>
                                <td class="text-center">
                                    <span class="badge badge-primary">{{ $client->hotel_bookings_count }}</span>
                                </td>
                                <td>
                                    @php
                                        $lastBooking = $client->hotelBookings()
                                            ->whereHas('room', function($q) use ($hotel) {
                                                $q->where('hotel_id', $hotel->id);
                                            })
                                            ->latest()
                                            ->first();
                                    @endphp
                                    {{ $lastBooking ? $lastBooking->created_at->format('d/m/Y') : 'N/A' }}
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('hotels.clients.show', [$hotel->id, $client->id]) }}" 
                                           class="btn btn-sm btn-info" 
                                           title="Voir les détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('hotels.clients.edit', [$hotel->id, $client->id]) }}" 
                                           class="btn btn-sm btn-warning" 
                                           title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Aucun client trouvé</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                
                @if($clients->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $clients->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteClientModal" tabindex="-1" role="dialog" aria-labelledby="deleteClientModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteClientModalLabel">Confirmer la suppression</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer ce client ? Cette action est irréversible.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <form id="deleteClientForm" method="POST" action="">
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
<script>
    $(document).ready(function() {
        // Initialisation de la recherche
        $('#searchInput').on('keyup', function() {
            const value = $(this).val().toLowerCase();
            $('#clientsTable tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });

        // Gestion de la suppression
        $('.delete-client').on('click', function() {
            const url = $(this).data('url');
            $('#deleteClientForm').attr('action', url);
            $('#deleteClientModal').modal('show');
        });
    });
</script>
@endpush
