@extends('layouts.site-manager')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Gestion des réservations</h1>
        <p class="mb-0">Consultez et gérez les réservations de vos sites touristiques</p>
    </div>
    <div>
        <a href="{{ route('site-manager.bookings.export') }}" class="btn btn-outline-secondary me-2">
            <i class="bi bi-download me-1"></i> Exporter
        </a>
        <a href="{{ route('site-manager.calendar') }}" class="btn btn-outline-primary">
            <i class="bi bi-calendar3 me-1"></i> Vue calendrier
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('site-manager.bookings.index') }}" method="GET" class="row g-3">
            <div class="col-md-5">
                <label for="search" class="form-label">Recherche</label>
                <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Rechercher par ID, nom, email...">
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label">Statut</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Tous les statuts</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmée</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Annulée</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Terminée</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="date_from" class="form-label">Du</label>
                <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label for="date_to" class="form-label">Au</label>
                <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
            </div>
            <div class="col-12 text-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bi bi-funnel me-1"></i> Filtrer
                </button>
                <a href="{{ route('site-manager.bookings.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Réinitialiser
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($bookings->isEmpty())
            <div class="text-center p-5">
                <div class="mb-3">
                    <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                </div>
                <h5 class="mb-2">Aucune réservation trouvée</h5>
                <p class="text-muted mb-4">Aucune réservation ne correspond à vos critères de recherche.</p>
                <a href="{{ route('site-manager.bookings.index') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Réinitialiser les filtres
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Site</th>
                            <th>Visiteur</th>
                            <th>Date de visite</th>
                            <th class="text-center">Visiteurs</th>
                            <th class="text-end">Montant</th>
                            <th class="text-center">Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $booking)
                            <tr>
                                <td>#{{ $booking->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($booking->site->photo_url)
                                            <img src="{{ Storage::url($booking->site->photo_url) }}" alt="{{ $booking->site->name }}" class="me-2" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                        @endif
                                        <div>
                                            <div class="fw-medium">{{ Str::limit($booking->site->name, 25) }}</div>
                                            <small class="text-muted">{{ $booking->site->city }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-medium">{{ $booking->user->name }}</div>
                                    <small class="text-muted">{{ $booking->user->email }}</small>
                                </td>
                                <td>
                                    <div class="fw-medium">{{ $booking->visit_date->format('d/m/Y') }}</div>
                                    <small class="text-muted">{{ $booking->time_slot }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary">{{ $booking->visitor_count }}</span>
                                </td>
                                <td class="text-end fw-medium">
                                    {{ number_format($booking->total_amount, 0, ',', ' ') }} FCFA
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ 
                                        $booking->status == 'confirmed' ? 'success' : 
                                        ($booking->status == 'cancelled' ? 'danger' : 'warning')
                                    }}">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton{{ $booking->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton{{ $booking->id }}">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('site-manager.bookings.show', $booking) }}">
                                                    <i class="bi bi-eye me-2"></i> Voir les détails
                                                </a>
                                            </li>
                                            @if($booking->status != 'cancelled' && $booking->status != 'completed')
                                                <li><hr class="dropdown-divider"></li>
                                                @if($booking->status == 'pending')
                                                    <li>
                                                        <form action="{{ route('site-manager.bookings.update-status', $booking) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="status" value="confirmed">
                                                            <button type="submit" class="dropdown-item text-success">
                                                                <i class="bi bi-check-circle me-2"></i> Confirmer
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                                <li>
                                                    <form action="{{ route('site-manager.bookings.update-status', $booking) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="status" value="cancelled">
                                                        <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Êtes-vous sûr de vouloir annuler cette réservation ?')">
                                                            <i class="bi bi-x-circle me-2"></i> Annuler
                                                        </button>
                                                    </form>
                                                </li>
                                                @if($booking->status == 'confirmed' && $booking->visit_date <= now())
                                                    <li>
                                                        <form action="{{ route('site-manager.bookings.update-status', $booking) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="status" value="completed">
                                                            <button type="submit" class="dropdown-item text-primary">
                                                                <i class="bi bi-check2-all me-2"></i> Marquer comme terminée
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                            @endif
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#sendMessageModal{{ $booking->id }}">
                                                    <i class="bi bi-envelope me-2"></i> Envoyer un message
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    
                                    <!-- Modal d'envoi de message -->
                                    <div class="modal fade" id="sendMessageModal{{ $booking->id }}" tabindex="-1" aria-labelledby="sendMessageModalLabel{{ $booking->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('site-manager.bookings.send-message', $booking) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="sendMessageModalLabel{{ $booking->id }}">Envoyer un message</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="subject" class="form-label">Objet</label>
                                                            <input type="text" class="form-control" id="subject" name="subject" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="message" class="form-label">Message</label>
                                                            <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                        <button type="submit" class="btn btn-primary">
                                                            <i class="bi bi-send me-1"></i> Envoyer
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($bookings->hasPages())
                <div class="card-footer">
                    {{ $bookings->appends(request()->query())->links() }}
                </div>
            @endif
        @endif
    </div>
</div>

<!-- Statistiques -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card bg-primary bg-opacity-10 border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase text-muted mb-1">Total</h6>
                        <h3 class="mb-0">{{ $stats['total'] }}</h3>
                    </div>
                    <div class="bg-primary bg-opacity-25 p-3 rounded">
                        <i class="bi bi-calendar-check text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning bg-opacity-10 border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase text-muted mb-1">En attente</h6>
                        <h3 class="mb-0">{{ $stats['pending'] }}</h3>
                    </div>
                    <div class="bg-warning bg-opacity-25 p-3 rounded">
                        <i class="bi bi-hourglass-split text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success bg-opacity-10 border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase text-muted mb-1">Confirmées</h6>
                        <h3 class="mb-0">{{ $stats['confirmed'] }}</h3>
                    </div>
                    <div class="bg-success bg-opacity-25 p-3 rounded">
                        <i class="bi bi-check-circle text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger bg-opacity-10 border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase text-muted mb-1">Annulées</h6>
                        <h3 class="mb-0">{{ $stats['cancelled'] }}</h3>
                    </div>
                    <div class="bg-danger bg-opacity-25 p-3 rounded">
                        <i class="bi bi-x-circle text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialisation des tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush
