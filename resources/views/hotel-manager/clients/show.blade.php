@extends('layouts.hotel')

@section('title', 'Détails du client - ' . $client->full_name)

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Détails du client</h1>
        <div>
            <a href="{{ route('hotels.clients.edit', [$hotel->id, $client->id]) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Modifier
            </a>
            <a href="{{ route('hotels.clients.index', $hotel->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Informations personnelles -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informations personnelles</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <img class="img-profile rounded-circle" 
                             src="{{ $client->profile->profile_picture ?? 'https://ui-avatars.com/api/?name=' . urlencode($client->full_name) }}"
                             style="width: 150px; height: 150px; object-fit: cover;">
                    </div>
                    
                    <div class="mb-3">
                        <h5 class="font-weight-bold">{{ $client->full_name }}</h5>
                        <p class="text-muted">
                            <i class="fas fa-envelope mr-2"></i> {{ $client->email }}
                        </p>
                        @if($client->phone)
                        <p class="text-muted">
                            <i class="fas fa-phone mr-2"></i> {{ $client->phone }}
                        </p>
                        @endif
                        @if($client->profile && $client->profile->date_of_birth)
                        <p class="text-muted">
                            <i class="fas fa-birthday-cake mr-2"></i> 
                            {{ $client->profile->date_of_birth->format('d/m/Y') }} 
                            ({{ now()->diffInYears($client->profile->date_of_birth) }} ans)
                        </p>
                        @endif
                    </div>

                    @if($client->profile && ($client->profile->address || $client->profile->city || $client->profile->country))
                    <hr>
                    <h6 class="font-weight-bold">Adresse</h6>
                    <p class="text-muted">
                        @if($client->profile->address){{ $client->profile->address }}<br>@endif
                        @if($client->profile->postal_code || $client->profile->city)
                            {{ $client->profile->postal_code ?? '' }} {{ $client->profile->city }}<br>
                        @endif
                        @if($client->profile->country){{ $client->profile->country }}@endif
                    </p>
                    @endif

                    @if($client->profile && $client->profile->notes)
                    <hr>
                    <h6 class="font-weight-bold">Notes</h6>
                    <p class="text-muted">{{ $client->profile->notes }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Historique des réservations -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Historique des réservations</h6>
                </div>
                <div class="card-body">
                    @if($bookings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Référence</th>
                                        <th>Chambre</th>
                                        <th>Dates</th>
                                        <th>Statut</th>
                                        <th>Montant</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bookings as $booking)
                                        <tr>
                                            <td>#{{ $booking->reference }}</td>
                                            <td>{{ $booking->room->name }} ({{ $booking->room->type ?? 'Non spécifié' }})</td>
                                            <td>
                                                {{ $booking->start_date->format('d/m/Y') }} - 
                                                {{ $booking->end_date->format('d/m/Y') }}
                                                ({{ $booking->start_date->diffInDays($booking->end_date) }} nuits)
                                            </td>
                                            <td>
                                                @php
                                                    $statusClass = [
                                                        'confirmed' => 'success',
                                                        'pending' => 'warning',
                                                        'cancelled' => 'danger',
                                                        'completed' => 'info',
                                                    ][$booking->status] ?? 'secondary';
                                                @endphp
                                                <span class="badge badge-{{ $statusClass }}">
                                                    {{ ucfirst($booking->status) }}
                                                </span>
                                            </td>
                                            <td>{{ number_format($booking->total_price, 2, ',', ' ') }} FCFA</td>
                                            <td>
                                                <a href="{{ route('hotels.bookings.show', [$hotel, $booking]) }}" 
                                                   class="btn btn-sm btn-info" 
                                                   title="Voir les détails">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        @if($bookings->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $bookings->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-hotel fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted">Aucune réservation trouvée pour ce client.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
