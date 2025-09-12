@extends('layouts.site-manager')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Réservation #{{ $booking->id }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('site-manager.dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item"><a href="{{ route('site-manager.bookings.index') }}">Réservations</a></li>
                <li class="breadcrumb-item active" aria-current="page">Détails #{{ $booking->id }}</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="{{ route('site-manager.bookings.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Retour
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Détails de la réservation</h6>
                <span class="badge bg-{{ 
                    $booking->status == 'confirmed' ? 'success' : 
                    ($booking->status == 'cancelled' ? 'danger' : 'warning')
                }}">
                    {{ ucfirst($booking->status) }}
                </span>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Informations de réservation</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <span class="text-muted">ID de réservation :</span>
                                <span class="fw-medium">#{{ $booking->id }}</span>
                            </li>
                            <li class="mb-2">
                                <span class="text-muted">Date de réservation :</span>
                                <span class="fw-medium">{{ $booking->created_at->format('d/m/Y H:i') }}</span>
                            </li>
                            <li class="mb-2">
                                <span class="text-muted">Date de visite :</span>
                                <span class="fw-medium">{{ $booking->visit_date->format('d/m/Y') }}</span>
                            </li>
                            <li class="mb-2">
                                <span class="text-muted">Créneau horaire :</span>
                                <span class="fw-medium">{{ $booking->time_slot }}</span>
                            </li>
                            <li class="mb-2">
                                <span class="text-muted">Nombre de visiteurs :</span>
                                <span class="fw-medium">{{ $booking->visitor_count }} personne(s)</span>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Site touristique</h6>
                        <div class="d-flex align-items-start mb-3">
                            @if($booking->site->photo_url)
                                <img src="{{ Storage::url($booking->site->photo_url) }}" alt="{{ $booking->site->name }}" class="me-3 rounded" style="width: 80px; height: 60px; object-fit: cover;">
                            @endif
                            <div>
                                <h6 class="mb-1">{{ $booking->site->name }}</h6>
                                <p class="text-muted small mb-1">
                                    <i class="bi bi-geo-alt"></i> {{ $booking->site->address }}, {{ $booking->site->city }}
                                </p>
                                <a href="{{ route('site-manager.sites.show', $booking->site) }}" class="btn btn-sm btn-outline-primary">
                                    Voir le site
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <h6 class="text-muted mb-3">Visiteurs</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nom</th>
                                        <th>Type</th>
                                        <th class="text-end">Prix unitaire</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($booking->visitors as $visitor)
                                        <tr>
                                            <td>{{ $visitor->first_name }} {{ $visitor->last_name }}</td>
                                            <td>{{ ucfirst($visitor->type) }}</td>
                                            <td class="text-end">{{ number_format($visitor->price, 0, ',', ' ') }} FCFA</td>
                                            <td class="text-end">{{ number_format($visitor->price, 0, ',', ' ') }} FCFA</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">Sous-total :</td>
                                        <td class="text-end fw-bold">{{ number_format($booking->subtotal, 0, ',', ' ') }} FCFA</td>
                                    </tr>
                                    @if($booking->discount > 0)
                                        <tr>
                                            <td colspan="3" class="text-end">Remise :</td>
                                            <td class="text-end text-danger">-{{ number_format($booking->discount, 0, ',', ' ') }} FCFA</td>
                                        </tr>
                                    @endif
                                    <tr class="table-active">
                                        <td colspan="3" class="text-end fw-bold">Total TTC :</td>
                                        <td class="text-end fw-bold">{{ number_format($booking->total_amount, 0, ',', ' ') }} FCFA</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        @if($booking->special_requests)
                            <div class="mt-4">
                                <h6 class="text-muted mb-2">Demandes spéciales</h6>
                                <div class="alert alert-light">
                                    {{ $booking->special_requests }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="mb-0">Historique des statuts</h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    @forelse($booking->statusHistory as $history)
                        <div class="timeline-item">
                            <div class="timeline-badge bg-{{ 
                                $history->status == 'confirmed' ? 'success' : 
                                ($history->status == 'cancelled' ? 'danger' : 'warning')
                            }}">
                                <i class="bi {{
                                    $history->status == 'confirmed' ? 'bi-check-circle' : 
                                    ($history->status == 'cancelled' ? 'bi-x-circle' : 'bi-hourglass-split')
                                }}"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between">
                                    <h6 class="mb-1">
                                        {{ ucfirst($history->status) }}
                                    </h6>
                                    <small class="text-muted">{{ $history->created_at->diffForHumans() }}</small>
                                </div>
                                @if($history->notes)
                                    <p class="small mb-0">{{ $history->notes }}</p>
                                @endif
                                @if($history->changed_by)
                                    <small class="text-muted">Par {{ $history->changedBy->name }}</small>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-3">
                            Aucun historique disponible pour cette réservation.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">Client</h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="avatar avatar-xl mb-2">
                        @if($booking->user->profile_photo_path)
                            <img src="{{ Storage::url($booking->user->profile_photo_path) }}" alt="{{ $booking->user->name }}" class="rounded-circle">
                        @else
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2rem;">
                                {{ strtoupper(substr($booking->user->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <h5 class="mb-1">{{ $booking->user->name }}</h5>
                    <p class="text-muted mb-2">{{ $booking->user->email }}</p>
                    @if($booking->user->phone)
                        <p class="mb-0">
                            <i class="bi bi-telephone me-2"></i> {{ $booking->user->phone }}
                        </p>
                    @endif
                </div>
                
                <div class="d-grid gap-2 mt-3">
                    <a href="mailto:{{ $booking->user->email }}" class="btn btn-outline-primary">
                        <i class="bi bi-envelope me-1"></i> Envoyer un email
                    </a>
                    @if($booking->user->phone)
                        <a href="tel:{{ $booking->user->phone }}" class="btn btn-outline-secondary">
                            <i class="bi bi-telephone me-1"></i> Appeler
                        </a>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">Actions</h6>
            </div>
            <div class="card-body">
                @if($booking->status == 'pending')
                    <form action="{{ route('site-manager.bookings.update-status', $booking) }}" method="POST" class="mb-2">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="confirmed">
                        <button type="submit" class="btn btn-success w-100 mb-2">
                            <i class="bi bi-check-circle me-1"></i> Confirmer la réservation
                        </button>
                    </form>
                @endif
                
                @if($booking->status == 'confirmed' && $booking->visit_date <= now())
                    <form action="{{ route('site-manager.bookings.update-status', $booking) }}" method="POST" class="mb-2">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="completed">
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="bi bi-check2-all me-1"></i> Marquer comme terminée
                        </button>
                    </form>
                @endif
                
                @if($booking->status != 'cancelled' && $booking->status != 'completed')
                    <button type="button" class="btn btn-outline-danger w-100 mb-2" data-bs-toggle="modal" data-bs-target="#cancelBookingModal">
                        <i class="bi bi-x-circle me-1"></i> Annuler la réservation
                    </button>
                @endif
                
                <a href="#" class="btn btn-outline-secondary w-100 mb-2" data-bs-toggle="modal" data-bs-target="#sendMessageModal">
                    <i class="bi bi-envelope me-1"></i> Envoyer un message
                </a>
                
                <a href="{{ route('site-manager.bookings.export', ['id' => $booking->id]) }}" class="btn btn-outline-primary w-100">
                    <i class="bi bi-download me-1"></i> Télécharger le reçu
                </a>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="mb-0">Paiement</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Méthode :</span>
                        <span class="fw-medium">{{ ucfirst($booking->payment_method) }}</span>
                    </li>
                    <li class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Statut :</span>
                        <span class="badge bg-{{ $booking->payment_status == 'paid' ? 'success' : 'warning' }}">
                            {{ ucfirst($booking->payment_status) }}
                        </span>
                    </li>
                    @if($booking->payment_date)
                        <li class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Date de paiement :</span>
                            <span class="fw-medium">{{ $booking->payment_date->format('d/m/Y H:i') }}</span>
                        </li>
                    @endif
                    @if($booking->transaction_id)
                        <li class="d-flex justify-content-between mb-2">
                            <span class="text-muted">ID de transaction :</span>
                            <span class="fw-medium">{{ $booking->transaction_id }}</span>
                        </li>
                    @endif
                </ul>
                
                @if($booking->payment_status == 'pending' && $booking->payment_method == 'on_site')
                    <form action="{{ route('site-manager.bookings.mark-as-paid', $booking) }}" method="POST" class="mt-3">
                        @csrf
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-credit-card me-1"></i> Marquer comme payé
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal d'annulation de réservation -->
<div class="modal fade" id="cancelBookingModal" tabindex="-1" aria-labelledby="cancelBookingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('site-manager.bookings.update-status', $booking) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="cancelled">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelBookingModalLabel">Annuler la réservation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir annuler cette réservation ? Cette action est irréversible.</p>
                    <div class="mb-3">
                        <label for="cancellation_reason" class="form-label">Raison de l'annulation (optionnel) :</label>
                        <textarea class="form-control" id="cancellation_reason" name="notes" rows="3"></textarea>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="refund" name="refund" value="1">
                        <label class="form-check-label" for="refund">
                            Rembourser le client
                        </label>
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

<!-- Modal d'envoi de message -->
<div class="modal fade" id="sendMessageModal" tabindex="-1" aria-labelledby="sendMessageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('site-manager.bookings.send-message', $booking) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="sendMessageModalLabel">Envoyer un message</h5>
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
@endsection

@push('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 1.5rem;
    }
    
    .timeline::before {
        content: '';
        position: absolute;
        left: 0.5rem;
        top: 0;
        bottom: 0;
        width: 2px;
        background-color: #e9ecef;
    }
    
    .timeline-item {
        position: relative;
        padding-bottom: 1.5rem;
    }
    
    .timeline-badge {
        position: absolute;
        left: -1.5rem;
        width: 2rem;
        height: 2rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        z-index: 1;
    }
    
    .timeline-content {
        margin-left: 1.5rem;
        padding: 0.5rem 1rem;
        background-color: #f8f9fa;
        border-radius: 0.25rem;
    }
    
    .avatar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 80px;
        height: 80px;
        margin: 0 auto 1rem;
        overflow: hidden;
    }
    
    .avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
</style>
@endpush
