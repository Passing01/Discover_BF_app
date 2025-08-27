@extends('layouts.tourist')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Gestion des Réservations</h2>
        <div>
            <a href="{{ route('restaurant.bookings.calendar') }}" class="btn btn-outline-primary me-2">
                <i class="far fa-calendar-alt me-1"></i> Voir le calendrier
            </a>
            <a href="{{ route('restaurant.tables.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-chair me-1"></i> Gérer les tables
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('restaurant.bookings.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="status" class="form-label">Statut</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>En attente</option>
                        <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmée</option>
                        <option value="seated" {{ request('status') === 'seated' ? 'selected' : '' }}>À table</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Terminée</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Annulée</option>
                        <option value="no_show" {{ request('status') === 'no_show' ? 'selected' : '' }}>No Show</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" class="form-control" id="date" name="date" 
                           value="{{ request('date', now()->format('Y-m-d')) }}">
                </div>
                
                <div class="col-md-3">
                    <label for="search" class="form-label">Recherche</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="search" name="search" 
                               placeholder="Nom, email ou téléphone" value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter me-1"></i> Filtrer
                    </button>
                    <a href="{{ route('restaurant.bookings.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Client</th>
                        <th>Date/Heure</th>
                        <th>Couverts</th>
                        <th>Table</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                        <tr>
                            <td>#{{ $booking->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-light rounded-circle me-2">
                                        <div class="avatar-title text-primary">
                                            {{ substr($booking->customer_name, 0, 1) }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fw-medium">{{ $booking->customer_name }}</div>
                                        <small class="text-muted">{{ $booking->customer_phone }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>{{ $booking->booking_date->format('d/m/Y') }}</div>
                                <small class="text-muted">{{ $booking->booking_time->format('H:i') }}</small>
                            </td>
                            <td>{{ $booking->people }} pers.</td>
                            <td>
                                @if($booking->table)
                                    <span class="badge bg-light text-dark">
                                        {{ $booking->table->name }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusClasses = [
                                        'pending' => 'bg-warning',
                                        'confirmed' => 'bg-primary',
                                        'seated' => 'bg-info',
                                        'completed' => 'bg-success',
                                        'cancelled' => 'bg-danger',
                                        'no_show' => 'bg-secondary',
                                    ];
                                    
                                    $statusLabels = [
                                        'pending' => 'En attente',
                                        'confirmed' => 'Confirmée',
                                        'seated' => 'À table',
                                        'completed' => 'Terminée',
                                        'cancelled' => 'Annulée',
                                        'no_show' => 'No Show',
                                    ];
                                @endphp
                                <span class="badge {{ $statusClasses[$booking->status] ?? 'bg-secondary' }}">
                                    {{ $statusLabels[$booking->status] ?? $booking->status }}
                                </span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" 
                                            id="bookingActions{{ $booking->id }}" data-bs-toggle="dropdown" 
                                            aria-expanded="false">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="bookingActions{{ $booking->id }}">
                                        <li>
                                            <a class="dropdown-item" href="#" data-bs-toggle="modal" 
                                               data-bs-target="#viewBookingModal{{ $booking->id }}">
                                                <i class="far fa-eye me-2"></i>Voir les détails
                                            </a>
                                        </li>
                                        
                                        @if($booking->canBeConfirmed())
                                            <li>
                                                <form action="{{ route('restaurant.bookings.update-status', $booking) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="confirmed">
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="far fa-check-circle me-2"></i>Confirmer
                                                    </button>
                                                </form>
                                            </li>
                                        @endif
                                        
                                        @if($booking->canBeSeated())
                                            <li>
                                                <form action="{{ route('restaurant.bookings.update-status', $booking) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="seated">
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="fas fa-chair me-2"></i>Marquer comme à table
                                                    </button>
                                                </form>
                                            </li>
                                        @endif
                                        
                                        @if($booking->canBeCompleted())
                                            <li>
                                                <form action="{{ route('restaurant.bookings.update-status', $booking) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="completed">
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="far fa-check-square me-2"></i>Terminer
                                                    </button>
                                                </form>
                                            </li>
                                        @endif
                                        
                                        @if($booking->canBeCancelled())
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" 
                                                   data-bs-target="#cancelBookingModal{{ $booking->id }}">
                                                    <i class="far fa-times-circle me-2"></i>Annuler
                                                </a>
                                            </li>
                                        @endif
                                        
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item text-primary" href="#" data-bs-toggle="modal" 
                                               data-bs-target="#editBookingModal{{ $booking->id }}">
                                                <i class="far fa-edit me-2"></i>Modifier
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                
                                <!-- View Booking Modal -->
                                @include('restaurant.bookings.partials.view_modal', ['booking' => $booking])
                                
                                <!-- Edit Booking Modal -->
                                @include('restaurant.bookings.partials.edit_modal', ['booking' => $booking])
                                
                                <!-- Cancel Booking Modal -->
                                @include('restaurant.bookings.partials.cancel_modal', ['booking' => $booking])
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="far fa-calendar-times fa-2x mb-2"></i>
                                    <p class="mb-0">Aucune réservation trouvée</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($bookings->hasPages())
            <div class="card-footer">
                {{ $bookings->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Create Booking Modal -->
<div class="modal fade" id="createBookingModal" tabindex="-1" aria-labelledby="createBookingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createBookingModalLabel">Nouvelle réservation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('restaurant.bookings.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="customer_name" class="form-label">Nom du client *</label>
                                <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="customer_phone" class="form-label">Téléphone *</label>
                                <input type="tel" class="form-control" id="customer_phone" name="customer_phone" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="customer_email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="customer_email" name="customer_email">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="people" class="form-label">Nombre de personnes *</label>
                                <select class="form-select" id="people" name="people" required>
                                    @for($i = 1; $i <= $maxCapacity; $i++)
                                        <option value="{{ $i }}">{{ $i }} {{ $i === 1 ? 'personne' : 'personnes' }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="booking_date" class="form-label">Date *</label>
                                <input type="date" class="form-control" id="booking_date" name="booking_date" 
                                       min="{{ now()->format('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="booking_time" class="form-label">Heure *</label>
                                <select class="form-select" id="booking_time" name="booking_time" required>
                                    @foreach($timeSlots as $time => $label)
                                        <option value="{{ $time }}" {{ $time === '19:00' ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="special_requests" class="form-label">Demandes spéciales</label>
                                <textarea class="form-control" id="special_requests" name="special_requests" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer la réservation</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Initialize date picker with min date
    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('booking_date').min = today;
    });
    
    // Handle form submission for status updates
    document.querySelectorAll('.update-booking-status').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('Êtes-vous sûr de vouloir modifier le statut de cette réservation ?')) {
                e.preventDefault();
            }
        });
    });
</script>
@endpush
@endsection
