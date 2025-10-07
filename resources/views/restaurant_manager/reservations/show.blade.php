@extends('layouts.restau')

@section('title', 'Détails de la réservation #' . $reservation->id)

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <h1>Détails de la réservation #{{ $reservation->id }}</h1>
            <div>
                <a href="{{ route('restaurant-manager.reservations.index') }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-arrow-left me-1"></i> Retour
                </a>
                @if($reservation->status == 'pending')
                <form action="{{ route('restaurant-manager.reservations.update-status', $reservation) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="confirmed">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-1"></i> Confirmer
                    </button>
                </form>
                @endif
            </div>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('restaurant-manager.dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item"><a href="{{ route('restaurant-manager.reservations.index') }}">Réservations</a></li>
                <li class="breadcrumb-item active" aria-current="page">Détails #{{ $reservation->id }}</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Détails de la commande</h5>
                    <span class="badge bg-{{ 
                        $reservation->status == 'confirmed' ? 'success' : 
                        ($reservation->status == 'cancelled' ? 'danger' : 
                        ($reservation->status == 'completed' ? 'secondary' : 'warning'))
                    }}">
                        {{ ucfirst($reservation->status) }}
                    </span>
                </div>
                <div class="card-body">
                    @if(!empty($reservation->order_items) && count($reservation->order_items) > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Plat</th>
                                        <th class="text-end">Quantité</th>
                                        <th class="text-end">Prix unitaire</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $subtotal = 0;
                                    @endphp
                                    
                                    @foreach($reservation->order_items as $item)
                                        @php
                                            $itemTotal = $item['price'] * $item['quantity'];
                                            $subtotal += $itemTotal;
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if(!empty($item['image']))
                                                        <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="rounded me-3" width="60" height="60" style="object-fit: cover;">
                                                    @endif
                                                    <div>
                                                        <h6 class="mb-0">{{ $item['name'] }}</h6>
                                                        @if(!empty($item['options']))
                                                            <small class="text-muted">
                                                                @foreach($item['options'] as $option)
                                                                    {{ $option['name'] }}: {{ $option['value'] }}
                                                                    @if(!$loop->last), @endif
                                                                @endforeach
                                                            </small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end">{{ $item['quantity'] }}</td>
                                            <td class="text-end">{{ number_format($item['price'], 0, ',', ' ') }} FCFA</td>
                                            <td class="text-end">{{ number_format($itemTotal, 0, ',', ' ') }} FCFA</td>
                                        </tr>
                                    @endforeach
                                    
                                    <!-- Sous-total -->
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">Sous-total</td>
                                        <td class="text-end fw-bold">{{ number_format($subtotal, 0, ',', ' ') }} FCFA</td>
                                    </tr>
                                    
                                    <!-- Frais de livraison -->
                                    @if($reservation->delivery_fee > 0)
                                    <tr>
                                        <td colspan="3" class="text-end">Frais de livraison</td>
                                        <td class="text-end">{{ number_format($reservation->delivery_fee, 0, ',', ' ') }} FCFA</td>
                                    </tr>
                                    @endif
                                    
                                    <!-- Total -->
                                    <tr class="table-active">
                                        <td colspan="3" class="text-end fw-bold">Total</td>
                                        <td class="text-end fw-bold">{{ number_format($subtotal + $reservation->delivery_fee, 0, ',', ' ') }} FCFA</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Instructions spéciales -->
                        @if(!empty($reservation->special_requests))
                        <div class="mt-4">
                            <h6>Instructions spéciales :</h6>
                            <div class="alert alert-light">
                                {{ $reservation->special_requests }}
                            </div>
                        </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-utensils fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">Aucun plat commandé avec cette réservation.</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Informations de livraison -->
            @if($reservation->delivery_address || $reservation->delivery_coords)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Informations de livraison</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Adresse de livraison :</h6>
                            <p class="mb-0">{{ $reservation->delivery_address ?? 'Non spécifiée' }}</p>
                            
                            @if($reservation->delivery_coords)
                                <div class="mt-3">
                                    <a href="https://www.google.com/maps?q={{ $reservation->delivery_coords }}" 
                                       target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-map-marker-alt me-1"></i> Voir sur la carte
                                    </a>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6>Coordonnées du client :</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-user me-2 text-muted"></i> {{ $reservation->user->name }}</li>
                                <li><i class="fas fa-phone me-2 text-muted"></i> 
                                    <a href="tel:{{ $reservation->user->phone ?? '' }}">
                                        {{ $reservation->user->phone ?? 'Non renseigné' }}
                                    </a>
                                </li>
                                <li><i class="fas fa-envelope me-2 text-muted"></i> 
                                    <a href="mailto:{{ $reservation->user->email }}">
                                        {{ $reservation->user->email }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        
        <div class="col-md-4">
            <!-- Informations de la réservation -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Informations de la réservation</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="fas fa-store me-2 text-muted"></i> Restaurant</span>
                            <span class="text-end">{{ $reservation->restaurant->name }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="far fa-calendar-alt me-2 text-muted"></i> Date/Heure</span>
                            <span class="text-end">{{ $reservation->reservation_at->format('d/m/Y H:i') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="fas fa-users me-2 text-muted"></i> Nombre de personnes</span>
                            <span class="text-end">{{ $reservation->party_size }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="fas fa-tag me-2 text-muted"></i> Type</span>
                            <span class="text-end">
                                <span class="badge bg-{{ $reservation->delivery_address ? 'info' : 'primary' }}">
                                    {{ $reservation->delivery_address ? 'Livraison' : 'Sur place' }}
                                </span>
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="fas fa-money-bill-wave me-2 text-muted"></i> Paiement</span>
                            <span class="text-end">
                                @if($reservation->payment_status == 'paid')
                                    <span class="badge bg-success">Payé</span>
                                @elseif($reservation->payment_status == 'pending')
                                    <span class="badge bg-warning">En attente</span>
                                @else
                                    <span class="badge bg-secondary">Non payé</span>
                                @endif
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="far fa-clock me-2 text-muted"></i> Créée le</span>
                            <span class="text-end">{{ $reservation->created_at->format('d/m/Y H:i') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($reservation->status != 'confirmed' && $reservation->status != 'cancelled' && $reservation->status != 'completed')
                        <form action="{{ route('restaurant-manager.reservations.update-status', $reservation) }}" method="POST" class="d-grid">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="confirmed">
                            <button type="submit" class="btn btn-success mb-2">
                                <i class="fas fa-check me-1"></i> Confirmer la réservation
                            </button>
                        </form>
                        @endif
                        
                        @if($reservation->status != 'cancelled' && $reservation->status != 'completed')
                        <button type="button" class="btn btn-outline-danger mb-2" data-bs-toggle="modal" data-bs-target="#cancelModal">
                            <i class="fas fa-times me-1"></i> Annuler la réservation
                        </button>
                        @endif
                        
                        @if($reservation->status == 'confirmed')
                        <form action="{{ route('restaurant-manager.reservations.update-status', $reservation) }}" method="POST" class="d-grid">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="completed">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-flag-checkered me-1"></i> Marquer comme terminée
                            </button>
                        </form>
                        @endif
                        
                        <a href="tel:{{ $reservation->user->phone ?? '' }}" class="btn btn-outline-primary">
                            <i class="fas fa-phone me-1"></i> Appeler le client
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal d'annulation -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('restaurant-manager.reservations.update-status', $reservation) }}" method="POST">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="cancelled">
                
                <div class="modal-header">
                    <h5 class="modal-title">Annuler la réservation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir annuler cette réservation ? Cette action est irréversible.</p>
                    <div class="mb-3">
                        <label for="cancellationReason" class="form-label">Raison de l'annulation (optionnel)</label>
                        <textarea class="form-control" id="cancellationReason" name="cancellation_reason" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-danger">Confirmer l'annulation</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .list-group-item {
        border-left: none;
        border-right: none;
    }
    
    .list-group-item:first-child {
        border-top: none;
        padding-top: 0;
    }
    
    .list-group-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    
    .table th {
        font-weight: 500;
        font-size: 0.8rem;
        text-transform: uppercase;
        color: #6c757d;
        border-top: none;
    }
    
    .table td {
        vertical-align: middle;
    }
    
    .badge {
        font-weight: 500;
        padding: 0.4em 0.8em;
    }
</style>
@endpush
