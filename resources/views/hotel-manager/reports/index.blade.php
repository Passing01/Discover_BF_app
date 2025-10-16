@extends('layouts.hotel')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="me-3 mb-2">
                    <h2 class="h3 mb-1">Rapports et statistiques</h2>
                    <p class="text-muted mb-0">
                        <i class="fas fa-hotel me-1"></i> {{ $hotel->name }}
                    </p>
                </div>
                <div class="mb-2">
                    <a href="{{ route('hotel-manager.hotels.bookings.index', $hotel) }}" 
                       class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>
                        Retour aux réservations
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('hotels.reports', $hotel) }}" class="row g-3">
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Date de début</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" 
                           value="{{ $startDate->format('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">Date de fin</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" 
                           value="{{ $endDate->format('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label for="report_type" class="form-label">Type de rapport</label>
                    <select class="form-select" id="report_type" name="report_type">
                        <option value="monthly" {{ $reportType === 'monthly' ? 'selected' : '' }}>Mensuel</option>
                        <option value="room_type" {{ $reportType === 'room_type' ? 'selected' : '' }}>Par type de chambre</option>
                        <option value="source" {{ $reportType === 'source' ? 'selected' : '' }}>Par source de réservation</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-1"></i>Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Réservations</h6>
                            <h3 class="mb-0">{{ $stats['total_bookings'] }}</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="fas fa-calendar-check text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Chiffre d'affaires</h6>
                            <h3 class="mb-0">{{ number_format($stats['total_revenue'], 0, ',', ' ') }} FCFA</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="fas fa-money-bill-wave text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Séjour moyen</h6>
                            <h3 class="mb-0">{{ number_format($stats['average_stay'], 1, ',', ' ') }} jours</h3>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="fas fa-moon text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Taux d'occupation</h6>
                            <h3 class="mb-0">{{ number_format($stats['occupancy_rate'], 1, ',', ' ') }}%</h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="fas fa-chart-line text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphique -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title mb-4">
                @if($reportType === 'monthly')
                    Réservations et revenus mensuels
                @elseif($reportType === 'room_type')
                    Réservations par type de chambre
                @else
                    Réservations par source
                @endif
            </h5>
            <div class="chart-container" style="position: relative; height: 400px;">
                <canvas id="reportChart"></canvas>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Dernières réservations -->
        <div class="col-lg-8 mb-4">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Dernières réservations</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Référence</th>
                                    <th>Client</th>
                                    <th>Chambre</th>
                                    <th>Dates</th>
                                    <th>Montant</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentBookings as $booking)
                                    <tr>
                                        <td>
                                            <a href="{{ route('hotel-manager.hotels.bookings.show', [$hotel, $booking]) }}">
                                                {{ $booking->reference }}
                                            </a>
                                        </td>
                                        <td>{{ $booking->user->name ?? 'N/A' }}</td>
                                        <td>{{ $booking->room->name ?? 'N/A' }}</td>
                                        <td>
                                            {{ $booking->start_date->format('d/m/Y') }} - 
                                            {{ $booking->end_date->format('d/m/Y') }}
                                        </td>
                                        <td>{{ number_format($booking->total_price, 0, ',', ' ') }} FCFA</td>
                                        <td>
                                            @php
                                                $statusClass = [
                                                    'pending' => 'warning',
                                                    'confirmed' => 'primary',
                                                    'checked_in' => 'success',
                                                    'completed' => 'info',
                                                    'cancelled' => 'danger'
                                                ][$booking->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $statusClass }}">
                                                {{ ucfirst($booking->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            Aucune réservation récente
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chambres populaires -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Chambres populaires</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($popularRooms as $room)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $room->name }}</h6>
                                        <small class="text-muted">
                                            {{ $room->bookings_count }} réservation(s)
                                        </small>
                                    </div>
                                    <span class="badge bg-primary rounded-pill">
                                        {{ number_format($room->price_per_night, 0, ',', ' ') }} FCFA/nuit
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item text-center py-4">
                                Aucune donnée disponible
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('reportChart').getContext('2d');
    const chartData = @json($chartData);
    
    let labels = chartData.labels || [];
    let datasets = [];
    
    if (chartData.bookings) {
        datasets.push({
            label: 'Nombre de réservations',
            data: chartData.bookings,
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1,
            yAxisID: 'y'
        });
    }
    
    if (chartData.revenue) {
        datasets.push({
            label: 'Revenu (FCFA)',
            data: chartData.revenue,
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1,
            type: 'line',
            yAxisID: 'y1'
        });
    }
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Nombre de réservations'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: {
                        drawOnChartArea: false
                    },
                    title: {
                        display: true,
                        text: 'Revenu (FCFA)'
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection
