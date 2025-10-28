@php
    $isModal = $isModal ?? false;
    $showActions = $showActions ?? true;
    $booking = $booking ?? null;
    $hotel = $hotel ?? null;
    $user = $booking->user ?? null;
    $payment = $booking->payment ?? null;
    $room = $booking->room ?? null;
    
    // Définir les classes de statut (Bootstrap)
    $statusClasses = [
        'pending' => 'badge bg-warning text-dark',
        'confirmed' => 'badge bg-primary',
        'checked_in' => 'badge bg-success',
        'checked_out' => 'badge bg-secondary',
        'cancelled' => 'badge bg-danger',
    ];
    
    $statusLabels = [
        'pending' => 'En attente',
        'confirmed' => 'Confirmée',
        'checked_in' => 'En cours',
        'checked_out' => 'Terminée',
        'cancelled' => 'Annulée',
    ];
    
    $paymentStatusClasses = [
        'pending' => 'badge bg-warning text-dark',
        'paid' => 'badge bg-success',
        'failed' => 'badge bg-danger',
        'refunded' => 'badge bg-info text-dark',
    ];
    
    $paymentStatusLabels = [
        'pending' => 'En attente',
        'paid' => 'Payé',
        'failed' => 'Échoué',
        'refunded' => 'Remboursé',
    ];
    
    $paymentMethodIcons = [
        'cash' => 'bi bi-cash-coin',
        'credit_card' => 'bi bi-credit-card',
        'bank_transfer' => 'bi bi-bank',
        'mobile_money' => 'bi bi-phone',
    ];
    
    $paymentMethodLabels = [
        'cash' => 'Espèces',
        'credit_card' => 'Carte de crédit',
        'bank_transfer' => 'Virement bancaire',
        'mobile_money' => 'Mobile Money',
    ];
@endphp

@if(!$isModal)
    <div class="card shadow-sm">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="h5 mb-1 text-dark">
                        Réservation #{{ $booking->booking_reference }}
                    </h3>
                    <p class="mb-0 small text-muted">
                        Détails complets de la réservation
                    </p>
                </div>
                <div>
                <span class="{{ $statusClasses[$booking->status] ?? 'badge bg-secondary' }}">
                    {{ $statusLabels[$booking->status] ?? ucfirst($booking->status) }}
                </span>
                @if($showActions)
                    <a href="{{ route('hotels.bookings.edit', [$hotel, $booking]) }}" 
                       class="btn btn-outline-secondary btn-sm ms-2 d-inline-flex align-items-center">
                        <i class="bi bi-pencil-square me-1"></i>
                        Modifier
                    </a>
                @endif
                </div>
            </div>
        </div>
        <div class="card-body">
            <dl class="row">
                <!-- Informations sur le séjour -->
                <div class="row g-3 mb-3">
                    <dt class="col-sm-3 text-muted fw-semibold">Séjour</dt>
                    <dd class="col-sm-9">
                        <div class="d-flex align-items-start">
                            @if($room && $room->main_photo_url)
                                <div class="flex-shrink-0 me-3">
                                    <img class="rounded" style="width:80px;height:80px;object-fit:cover;" src="{{ $room->main_photo_url }}" alt="{{ $room->name }}">
                                </div>
                            @endif
                            <div>
                                <div class="fw-semibold">{{ $room->name ?? 'Chambre non spécifiée' }}</div>
                                @if(!empty($room?->type))
                                    <div class="small text-muted">{{ $room->type }}</div>
                                @endif
                                <div class="mt-1 small text-muted">
                                    {{ $booking->adults }} {{ $booking->adults > 1 ? 'adultes' : 'adulte' }}
                                    @if($booking->children > 0)
                                        , {{ $booking->children }} {{ $booking->children > 1 ? 'enfants' : 'enfant' }}
                                    @endif
                                </div>
                                @if($booking->special_requests)
                                    <div class="mt-2">
                                        <span class="small text-muted fw-semibold">Demandes spéciales :</span>
                                        <p class="small text-dark mb-0">{{ $booking->special_requests }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="mt-3 row g-3">
                            <div class="col-md-6 border rounded p-3">
                                <div class="small text-muted fw-semibold">Arrivée</div>
                                <div class="mt-1 h6 mb-1">{{ $booking->start_date->format('l d F Y') }}</div>
                                <div class="small text-muted">À partir de 14h00</div>
                            </div>
                            <div class="col-md-6 border rounded p-3">
                                <div class="small text-muted fw-semibold">Départ</div>
                                <div class="mt-1 h6 mb-1">{{ $booking->end_date->format('l d F Y') }}</div>
                                <div class="small text-muted">Avant 12h00</div>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <div class="small text-muted fw-semibold">Durée du séjour</div>
                            <div class="mt-1 small text-dark">
                                {{ $booking->start_date->diffInDays($booking->end_date) }} nuits
                                (du {{ $booking->start_date->format('d/m/Y') }} au {{ $booking->end_date->format('d/m/Y') }})
                            </div>
                        </div>
                    </dd>
                </div>
                
                <!-- Informations client -->
                <div class="row g-3 mb-3">
                    <dt class="col-sm-3 text-muted fw-semibold">Client</dt>
                    <dd class="col-sm-9">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3" style="width:40px;height:40px;">
                                <span class="text-primary fw-semibold">{{ substr($booking->guest_name ?? ($user->name ?? '?'), 0, 1) }}</span>
                            </div>
                            <div>
                                <div class="fw-semibold">{{ $booking->guest_name ?? $user->name ?? 'Non spécifié' }}</div>
                                @if($booking->guest_email || $user?->email)
                                    <div class="small text-muted">{{ $booking->guest_email ?? $user->email }}</div>
                                @endif
                                @if($booking->guest_phone || $user?->phone)
                                    <div class="small text-muted">{{ $booking->guest_phone ?? $user->phone }}</div>
                                @endif
                            </div>
                        </div>
                        
                        @if(($user && ($user->address || $user->city || $user->country)) || $booking->guest_address)
                            <div class="mt-3">
                                <div class="small text-muted fw-semibold">Adresse</div>
                                <div class="mt-1 small text-dark">
                                    @if($user && ($user->address || $user->city || $user->country))
                                        {{ $user->address }}<br>
                                        @if($user->address2){{ $user->address2 }}<br>@endif
                                        {{ $user->postal_code }} {{ $user->city }}<br>
                                        {{ $user->country }}
                                    @else
                                        {{ $booking->guest_address }}
                                    @endif
                                </div>
                            </div>
                        @endif
                        
                        @if($user)
                            <div class="mt-3 d-flex gap-3">
                                <a href="{{ route('hotels.clients.show', [$hotel, $user]) }}" 
                                   class="d-inline-flex align-items-center small text-primary text-decoration-none">
                                    <i class="bi bi-person me-1"></i>
                                    Voir le profil
                                </a>
                                @if($user->email)
                                    <a href="mailto:{{ $user->email }}" 
                                       class="d-inline-flex align-items-center small text-primary text-decoration-none">
                                        <i class="bi bi-envelope me-1"></i>
                                        Envoyer un email
                                    </a>
                                @endif
                                @if($user->phone)
                                    <a href="tel:{{ $user->phone }}" 
                                       class="d-inline-flex align-items-center small text-primary text-decoration-none">
                                        <i class="bi bi-telephone me-1"></i>
                                        Appeler
                                    </a>
                                @endif
                            </div>
                        @endif
                    </dd>
                </div>
                
                <!-- Paiement -->
                <div class="row g-3 mb-3">
                    <dt class="col-sm-3 text-muted fw-semibold">Paiement</dt>
                    <dd class="col-sm-9">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="small text-muted fw-semibold">Montant total</div>
                                <div class="h6 mb-0 mt-1">{{ number_format($booking->total_amount, 0, ',', ' ') }} FCFA</div>
                            </div>
                            <div class="col-md-6">
                                <div class="small text-muted fw-semibold">Statut du paiement</div>
                                <div class="mt-1">
                                    <span class="{{ $paymentStatusClasses[$payment->status ?? 'pending'] }}">
                                        {{ $paymentStatusLabels[$payment->status ?? 'pending'] }}
                                        @if($payment && $payment->paid_at)
                                            <span class="ms-1 small">- {{ $payment->paid_at->format('d/m/Y') }}</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="small text-muted fw-semibold">Méthode de paiement</div>
                                <div class="mt-1 d-flex align-items-center">
                                    @php $pmIcon = $paymentMethodIcons[$payment->payment_method ?? 'cash'] ?? 'bi bi-credit-card'; @endphp
                                    <i class="me-2 {{ $pmIcon }}"></i>
                                    <span>{{ $paymentMethodLabels[$payment->payment_method ?? 'cash'] ?? 'Non spécifié' }}</span>
                                </div>
                            </div>
                            @if($payment && $payment->reference)
                                <div class="col-md-6">
                                    <div class="small text-muted fw-semibold">Référence</div>
                                    <div class="mt-1 small font-monospace">{{ $payment->reference }}</div>
                                </div>
                            @endif
                        </div>
                        @if($payment && $payment->notes)
                            <div class="mt-3">
                                <div class="small text-muted fw-semibold">Notes de paiement</div>
                                <div class="mt-1 small text-dark">{{ $payment->notes }}</div>
                            </div>
                        @endif
                    </dd>
                </div>
                
                <!-- Historique et notes -->
                <div class="row g-3 mb-3">
                    <dt class="col-sm-3 text-muted fw-semibold">Historique et notes</dt>
                    <dd class="col-sm-9">
                        <div>
                            <ul class="list-unstyled mb-0">
                                <li>
                                    <div class="d-flex align-items-start pb-3">
                                        <span class="badge bg-success me-2"><i class="bi bi-check-circle"></i></span>
                                        <div class="flex-grow-1 d-flex justify-content-between w-100 small text-muted">
                                            <span>Réservation créée</span>
                                            <time datetime="{{ $booking->created_at->toIso8601String() }}">{{ $booking->created_at->diffForHumans() }}</time>
                                        </div>
                                    </div>
                                </li>
                                
                                @if($booking->cancelled_at)
                                    <li>
                                        <div class="d-flex align-items-start pb-3">
                                            <span class="badge bg-danger me-2"><i class="bi bi-x-circle"></i></span>
                                            <div class="flex-grow-1 w-100">
                                                <div class="d-flex justify-content-between small text-muted">
                                                    <span>Réservation annulée</span>
                                                    <time datetime="{{ $booking->cancelled_at->toIso8601String() }}">{{ $booking->cancelled_at->diffForHumans() }}</time>
                                                </div>
                                                @if($booking->cancellation_reason)
                                                    <div class="small text-muted">{{ $booking->cancellation_reason }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </li>
                                @endif
                                
                                @if($booking->checked_in_at)
                                    <li>
                                        <div class="d-flex align-items-start pb-3">
                                            <span class="badge bg-primary me-2"><i class="bi bi-box-arrow-in-right"></i></span>
                                            <div class="flex-grow-1 d-flex justify-content-between w-100 small text-muted">
                                                <span>Arrivée enregistrée</span>
                                                <time datetime="{{ $booking->checked_in_at->toIso8601String() }}">{{ $booking->checked_in_at->diffForHumans() }}</time>
                                            </div>
                                        </div>
                                    </li>
                                @endif
                                
                                @if($booking->checked_out_at)
                                    <li>
                                        <div class="d-flex align-items-start pb-0">
                                            <span class="badge bg-secondary me-2"><i class="bi bi-box-arrow-right"></i></span>
                                            <div class="flex-grow-1 d-flex justify-content-between w-100 small text-muted">
                                                <span>Départ enregistré</span>
                                                <time datetime="{{ $booking->checked_out_at->toIso8601String() }}">{{ $booking->checked_out_at->diffForHumans() }}</time>
                                            </div>
                                        </div>
                                    </li>
                                @endif
                            </ul>
                        </div>
                        
                        @if($booking->notes)
                            <div class="mt-3">
                                <div class="small text-muted fw-semibold mb-1">Notes internes</div>
                                <div class="p-3 rounded border small text-dark">
                                    {{ $booking->notes }}
                                </div>
                            </div>
                        @endif
                    </dd>
                </div>
            </dl>
        </div>
    </div>
@else
    <!-- Version modale -->
    <div class="modal-body">
        <div class="w-100">
            <div class="mb-2">
                <h3 class="h6 mb-2" id="modal-title">
                    Réservation #{{ $booking->booking_reference }}
                </h3>
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="{{ $statusClasses[$booking->status] ?? 'badge bg-secondary' }}">
                            {{ $statusLabels[$booking->status] ?? ucfirst($booking->status) }}
                        </span>
                        <div class="small text-muted">
                            {{ $booking->start_date->format('d/m/Y') }} - {{ $booking->end_date->format('d/m/Y') }}
                            ({{ $booking->start_date->diffInDays($booking->end_date) }} nuits)
                        </div>
                    </div>
                    
                    <div class="bg-light p-3 rounded mb-3">
                        <div class="d-flex align-items-start">
                            @if($room && $room->main_photo_url)
                                <div class="flex-shrink-0 me-2">
                                    <img class="rounded" style="width:64px;height:64px;object-fit:cover;" src="{{ $room->main_photo_url }}" alt="{{ $room->name }}">
                                </div>
                            @endif
                            <div>
                                <div class="fw-semibold">{{ $room->name ?? 'Chambre non spécifiée' }}</div>
                                @if(!empty($room?->type))
                                    <div class="small text-muted">{{ $room->type }}</div>
                                @endif
                                <div class="mt-1 small text-muted">
                                    {{ $booking->adults }} {{ $booking->adults > 1 ? 'adultes' : 'adulte' }}
                                    @if($booking->children > 0)
                                        , {{ $booking->children }} {{ $booking->children > 1 ? 'enfants' : 'enfant' }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row g-3 mb-2">
                        <div class="col-6">
                            <div class="small text-muted fw-semibold">Client</div>
                            <div class="small">{{ $booking->guest_name ?? $user->name ?? 'Non spécifié' }}</div>
                        </div>
                        <div class="col-6">
                            <div class="small text-muted fw-semibold">Montant total</div>
                            <div class="small fw-semibold">{{ number_format($booking->total_amount, 0, ',', ' ') }} FCFA</div>
                        </div>
                        <div class="col-6">
                            <div class="small text-muted fw-semibold">Statut du paiement</div>
                            <div class="mt-1">
                                <span class="{{ $paymentStatusClasses[$payment->status ?? 'pending'] }}">
                                    {{ $paymentStatusLabels[$payment->status ?? 'pending'] }}
                                </span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="small text-muted fw-semibold">Méthode de paiement</div>
                            <div class="small d-flex align-items-center">
                                @php $pmIcon = $paymentMethodIcons[$payment->payment_method ?? 'cash'] ?? 'bi bi-credit-card'; @endphp
                                <i class="me-2 {{ $pmIcon }}"></i>
                                <span>{{ $paymentMethodLabels[$payment->payment_method ?? 'cash'] ?? 'Non spécifié' }}</span>
                            </div>
                        </div>
                    </div>
                    
                    @if($booking->special_requests)
                        <div class="mt-2">
                            <div class="small text-muted fw-semibold">Demandes spéciales</div>
                            <div class="small">{{ $booking->special_requests }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    
    @if($showActions)
        <div class="modal-footer d-flex justify-content-end gap-2">
            <a href="{{ route('hotels.bookings.show', [$hotel, $booking]) }}" 
               class="btn btn-primary btn-sm">
                Voir les détails
            </a>
            <a href="{{ route('hotels.bookings.edit', [$hotel, $booking]) }}" 
               class="btn btn-outline-secondary btn-sm">
                Modifier
            </a>
            <button type="button" 
                    @click="$dispatch('close')"
                    class="btn btn-light btn-sm">
                Fermer
            </button>
        </div>
    @endif
@endif
