@extends('layouts.restau')

@section('title', 'Gestion des réservations')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <h1>Gestion des réservations</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('restaurant-manager.dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item active" aria-current="page">Réservations</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Liste des réservations</h5>
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-filter me-1"></i>Filtrer
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => '']) }}">Toutes</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item {{ request('status') == 'pending' ? 'active' : '' }}" 
                               href="{{ request()->fullUrlWithQuery(['status' => 'pending']) }}">En attente</a></li>
                        <li><a class="dropdown-item {{ request('status') == 'confirmed' ? 'active' : '' }}" 
                               href="{{ request()->fullUrlWithQuery(['status' => 'confirmed']) }}">Confirmées</a></li>
                        <li><a class="dropdown-item {{ request('status') == 'cancelled' ? 'active' : '' }}" 
                               href="{{ request()->fullUrlWithQuery(['status' => 'cancelled']) }}">Annulées</a></li>
                        <li><a class="dropdown-item {{ request('status') == 'completed' ? 'active' : '' }}" 
                               href="{{ request()->fullUrlWithQuery(['status' => 'completed']) }}">Terminées</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if($reservations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Restaurant</th>
                                <th>Client</th>
                                <th>Date/Heure</th>
                                <th>Personnes</th>
                                <th>Commande</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reservations as $reservation)
                            <tr>
                                <td>#{{ $reservation->id }}</td>
                                <td>{{ $reservation->restaurant->name }}</td>
                                <td>{{ $reservation->user->name }}</td>
                                <td>{{ $reservation->reservation_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $reservation->party_size }}</td>
                                <td>
                                    @if(!empty($reservation->order_items) && count($reservation->order_items) > 0)
                                        <span class="badge bg-info">{{ count($reservation->order_items) }} plats</span>
                                    @else
                                        <span class="text-muted">Aucun plat</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statusClass = [
                                            'pending' => 'warning',
                                            'confirmed' => 'success',
                                            'cancelled' => 'danger',
                                            'completed' => 'secondary'
                                        ][$reservation->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $statusClass }}">
                                        {{ ucfirst($reservation->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('restaurant-manager.reservations.show', $reservation) }}" 
                                           class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-cog"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                @if($reservation->status != 'confirmed')
                                                <li>
                                                    <form action="{{ route('restaurant-manager.reservations.update-status', $reservation) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="confirmed">
                                                        <button type="submit" class="dropdown-item text-success">
                                                            <i class="fas fa-check me-2"></i>Confirmer
                                                        </button>
                                                    </form>
                                                </li>
                                                @endif
                                                
                                                @if($reservation->status != 'cancelled')
                                                <li>
                                                    <form action="{{ route('restaurant-manager.reservations.update-status', $reservation) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="cancelled">
                                                        <button type="submit" class="dropdown-item text-danger" 
                                                                onclick="return confirm('Êtes-vous sûr de vouloir annuler cette réservation ?')">
                                                            <i class="fas fa-times me-2"></i>Annuler
                                                        </button>
                                                    </form>
                                                </li>
                                                @endif
                                                
                                                @if($reservation->status != 'completed' && $reservation->status != 'cancelled')
                                                <li>
                                                    <form action="{{ route('restaurant-manager.reservations.update-status', $reservation) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="completed">
                                                        <button type="submit" class="dropdown-item text-primary">
                                                            <i class="fas fa-flag-checkered me-2"></i>Marquer comme terminée
                                                        </button>
                                                    </form>
                                                </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $reservations->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-calendar-times fa-4x text-muted"></i>
                    </div>
                    <h4 class="mb-3">Aucune réservation trouvée</h4>
                    <p class="text-muted">Aucune réservation ne correspond à vos critères de recherche.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Activer les tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush
