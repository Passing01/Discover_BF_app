@extends('layouts.hotel-manager')

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
                <h2 class="h4 font-weight-bold">Gestion des réservations - {{ $hotel->name }}</h2>
                
                <div class="d-flex gap-2 mt-3 mt-md-0">
                    <a href="{{ route('hotels.bookings.export', $hotel) }}" 
                       class="btn btn-success d-flex align-items-center">
                        <svg class="me-2" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                        Exporter en CSV
                    </a>
                    
                    <a href="{{ route('hotels.bookings.create', $hotel) }}" 
                       class="btn btn-primary d-flex align-items-center">
                        <svg class="me-2" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Nouvelle réservation
                    </a>
                </div>
            </div>
                
                <!-- Filtres -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form action="{{ route('hotels.bookings.index', $hotel) }}" method="GET" class="row g-3 align-items-end">
                            <div class="col-md-5">
                                <label for="status" class="form-label">Statut</label>
                                <select id="status" name="status" class="form-select">
                                    <option value="">Tous les statuts</option>
                                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>En attente</option>
                                    <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmée</option>
                                    <option value="checked_in" {{ request('status') === 'checked_in' ? 'selected' : '' }}>En cours</option>
                                    <option value="checked_out" {{ request('status') === 'checked_out' ? 'selected' : '' }}>Terminée</option>
                                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Annulée</option>
                                </select>
                            </div>
                            
                            <div class="col-md-5">
                                <label for="date_range" class="form-label">Période</label>
                                <input type="text" id="date_range" name="date_range" 
                                       class="form-control" 
                                       value="{{ request('date_range') }}" 
                                       placeholder="Sélectionner une période">
                            </div>
                            
                            <div class="col-md-2 d-grid">
                                <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center">
                                    <svg class="me-2" width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 11-2 0V4H5v12h4a1 1 0 110 2H4a1 1 0 01-1-1V3zm12.95 6.95a1 1 0 011.414 0l2.121 2.12a1 1 0 010 1.415l-2.12 2.121a1 1 0 11-1.415-1.414l.707-.707H10a1 1 0 110-2h6.586l-.707-.707a1 1 0 01-.22-1.095z" clip-rule="evenodd" />
                                    </svg>
                                    Filtrer
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="table-light">
                            <tr>
                                <th class="text-nowrap">
                                    Référence
                                </th>
                                <th>Chambre</th>
                                <th>Client</th>
                                <th>Dates</th>
                                <th>Montant</th>
                                <th>Statut</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookings as $booking)
                            <tr>
                                <td>
                                    <div class="fw-bold text-primary">{{ $booking->booking_reference }}</div>
                                    <div class="small text-muted">{{ $booking->created_at->format('d/m/Y H:i') }}</div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($booking->room->photos->isNotEmpty())
                                            <div class="flex-shrink-0 me-3">
                                                <img class="rounded" 
                                                     src="{{ $booking->room->main_photo_url }}" 
                                                     alt="{{ $booking->room->name }}"
                                                     style="width: 40px; height: 40px; object-fit: cover;">
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-medium">{{ $booking->room->name }}</div>
                                            <div class="small text-muted">{{ $booking->room->roomType->name ?? 'Non spécifié' }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-medium">{{ $booking->user->name }}</div>
                                    <div class="small text-muted">{{ $booking->user->email }}</div>
                                </td>
                                <td>
                                    <div class="small">
                                        <div>Du {{ $booking->start_date->format('d/m/Y') }}</div>
                                        <div>Au {{ $booking->end_date->format('d/m/Y') }}</div>
                                        <div class="text-muted">
                                            {{ $booking->start_date->diffInDays($booking->end_date) }} nuit(s)
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-medium">{{ number_format($booking->total_amount, 0, ',', ' ') }} FCFA</div>
                                    <div class="small text-muted">
                                        @if($booking->payment)
                                            {{ ucfirst($booking->payment->status) }}
                                            @if($booking->payment->paid_at)
                                                - {{ $booking->payment->paid_at->format('d/m/Y') }}
                                            @endif
                                        @else
                                            Non payé
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $statusClasses = [
                                            'pending' => 'bg-warning text-dark',
                                            'confirmed' => 'bg-primary text-white',
                                            'checked_in' => 'bg-success text-white',
                                            'checked_out' => 'bg-secondary text-white',
                                            'cancelled' => 'bg-danger text-white',
                                            'no_show' => 'bg-dark text-white',
                                        ][$booking->status] ?? 'bg-light text-dark';
                                    @endphp
                                    <span class="badge {{ $statusClasses }}">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('hotels.bookings.show', [$hotel, $booking]) }}" 
                                           class="btn btn-sm btn-outline-primary"
                                           title="Voir les détails">
                                            <i class="far fa-eye"></i>
                                        </a>
                                    
                                    @if($booking->status === 'pending' || $booking->status === 'confirmed')
                                        <button type="button" 
                                                onclick="if(confirm('Êtes-vous sûr de vouloir annuler cette réservation ?')) document.getElementById('cancel-booking-{{ $booking->id }}').submit()"
                                                class="btn btn-sm btn-outline-danger"
                                                title="Annuler la réservation">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <form id="cancel-booking-{{ $booking->id }}" 
                                              action="{{ route('hotels.bookings.destroy', [$hotel, $booking]) }}" 
                                              method="POST" 
                                              class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    @endif
                                    
                                    @if($booking->status === 'confirmed' && $booking->start_date->isToday())
                                        <form action="{{ route('hotel-manager.hotels.bookings.update-status', [$hotel, $booking]) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="checked_in">
                                            <button type="submit" 
                                                    class="btn btn-sm btn-outline-success"
                                                    title="Enregistrer l'arrivée">
                                                <i class="fas fa-sign-in-alt"></i>
                                            </button>
                                        </form>
                                    @endif
                                    
                                    @if($booking->status === 'checked_in')
                                        <form action="{{ route('hotel-manager.hotels.bookings.update-status', [$hotel, $booking]) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="checked_out">
                                            <button type="submit" 
                                                    class="btn btn-sm btn-outline-primary"
                                                    title="Enregistrer le départ">
                                                <i class="fas fa-sign-out-alt"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center p-5">
                                    <div class="py-4">
                                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                        <h3 class="h5">Aucune réservation</h3>
                                        <p class="text-muted mb-4">
                                            Commencez par créer une nouvelle réservation.
                                        </p>
                                        <a href="{{ route('hotels.bookings.create', $hotel) }}" 
                                           class="btn btn-primary">
                                            <i class="fas fa-plus me-2"></i>
                                            Nouvelle réservation
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4 d-flex justify-content-center">
                    {{ $bookings->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/fr.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialisation du sélecteur de plage de dates
        flatpickr("#date_range", {
            mode: "range",
            dateFormat: "Y-m-d",
            locale: "fr",
            minDate: "today",
            showMonths: 2,
            static: true,
            disable: [
                function(date) {
                    // Désactiver les dates passées
                    return date < new Date().fp_incr(-1);
                }
            ]
        });
        
        // Gestion de la confirmation de suppression
        document.querySelectorAll('[data-confirm]').forEach(button => {
            button.addEventListener('click', (e) => {
                if (!confirm(button.getAttribute('data-confirm'))) {
                    e.preventDefault();
                }
            });
        });
        
        // Mise à jour de l'URL avec les paramètres de filtre
        const updateUrl = (param, value) => {
            const url = new URL(window.location.href);
            if (value) {
                url.searchParams.set(param, value);
            } else {
                url.searchParams.delete(param);
            }
            window.history.pushState({}, '', url);
        };
        
        // Gestion des changements de filtre
        document.querySelectorAll('select[name="status"]').forEach(select => {
            select.addEventListener('change', (e) => {
                updateUrl('status', e.target.value);
            });
        });
        
        document.querySelectorAll('input[name="date_range"]').forEach(input => {
            input.addEventListener('change', (e) => {
                updateUrl('date_range', e.target.value);
            });
        });
    });
</script>
@endpush

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .flatpickr-calendar {
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        border-radius: 0.5rem;
    }
    .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange, .flatpickr-day.selected.inRange, .flatpickr-day.startRange.inRange, .flatpickr-day.endRange.inRange, .flatpickr-day.selected:focus, .flatpickr-day.startRange:focus, .flatpickr-day.endRange:focus, .flatpickr-day.selected:hover, .flatpickr-day.startRange:hover, .flatpickr-day.endRange:hover, .flatpickr-day.selected.prevMonthDay, .flatpickr-day.startRange.prevMonthDay, .flatpickr-day.endRange.prevMonthDay, .flatpickr-day.selected.nextMonthDay, .flatpickr-day.startRange.nextMonthDay, .flatpickr-day.endRange.nextMonthDay {
        background: #4f46e5;
        border-color: #4f46e5;
    }
    .flatpickr-day.inRange {
        background: #eef2ff;
        border-color: #e0e7ff;
    }
</style>
@endpush

@endsection
