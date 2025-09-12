@section('title', 'Tableau de bord')

@extends('layouts.hotel-manager')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <!-- Statistiques -->
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3 text-primary">
                        <i class="bi bi-building fs-2"></i>
                    </div>
                    <div>
                        <h6 class="text-muted">Hôtels</h6>
                        <h4>{{ $stats['total_hotels'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3 text-info">
                        <i class="bi bi-door-open fs-2"></i>
                    </div>
                    <div>
                        <h6 class="text-muted">Chambres</h6>
                        <h4>{{ $stats['total_rooms'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3 text-success">
                        <i class="bi bi-calendar-check fs-2"></i>
                    </div>
                    <div>
                        <h6 class="text-muted">Réservations actives</h6>
                        <h4>{{ $stats['active_bookings'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3 text-warning">
                        <i class="bi bi-currency-euro fs-2"></i>
                    </div>
                    <div>
                        <h6 class="text-muted">Revenu mensuel</h6>
                        <h4>{{ number_format($stats['monthly_revenue'], 2, ',', ' ') }} €</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Dernières réservations -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Dernières réservations</h5>
                    <a href="{{ route('hotel-manager.bookings.index') }}" class="btn btn-sm btn-outline-primary">Voir tout</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>N° Réservation</th>
                                    <th>Hôtel & Chambre</th>
                                    <th>Dates</th>
                                    <th>Statut</th>
                                    <th class="text-end">Montant</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentBookings as $booking)
                                    <tr>
                                        <td>#{{ $booking->id }}</td>
                                        <td>
                                            <strong>{{ $booking->room->hotel->name ?? 'Hôtel inconnu' }}</strong><br>
                                            <small>{{ $booking->room->name ?? 'N/A' }}</small>
                                        </td>
                                        <td>
                                            {{ $booking->start_date->format('d/m/Y') }} - {{ $booking->end_date->format('d/m/Y') }}<br>
                                            <small>{{ $booking->end_date->diffInDays($booking->start_date) }} nuit(s)</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $booking->status === 'confirmed' ? 'success' : ($booking->status === 'cancelled' ? 'danger' : 'secondary') }}">
                                                {{ ucfirst($booking->status) }}
                                            </span>
                                        </td>
                                        <td class="text-end">{{ number_format($booking->total_amount / 100, 2, ',', ' ') }} €</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Aucune réservation récente</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mes hôtels -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Mes hôtels</h5>
                    <a href="{{ route('hotel-manager.hotels.index') }}" class="btn btn-sm btn-outline-primary">Voir tout</a>
                </div>
                <div class="card-body">
                    @forelse($hotels as $hotel)
                        <div class="mb-3">
                            <div class="card">
                                @if($hotel->photos->isNotEmpty())
                                    <img src="{{ Storage::url($hotel->photos->first()->path) }}" class="card-img-top" alt="{{ $hotel->name }}">
                                @else
                                    <div class="card-img-top bg-light text-center py-5">
                                        <span class="text-muted">Aucune photo</span>
                                    </div>
                                @endif
                                <div class="card-body">
                                    <h6 class="card-title">{{ $hotel->name }}</h6>
                                    <p class="card-text text-muted">{{ $hotel->city }}, {{ $hotel->country }}</p>
                                    <a href="{{ route('hotel-manager.hotels.edit', $hotel) }}" class="btn btn-sm btn-outline-secondary">Gérer</a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-muted">Aucun hôtel</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
