@php
    $isModal = $isModal ?? false;
    $showActions = $showActions ?? true;
    $booking = $booking ?? null;
    $hotel = $hotel ?? null;
    $user = $booking->user ?? null;
    $payment = $booking->payment ?? null;
    $room = $booking->room ?? null;
    $roomType = $room->roomType ?? null;
    
    // Définir les classes de statut
    $statusClasses = [
        'pending' => 'bg-yellow-100 text-yellow-800',
        'confirmed' => 'bg-blue-100 text-blue-800',
        'checked_in' => 'bg-green-100 text-green-800',
        'checked_out' => 'bg-gray-100 text-gray-800',
        'cancelled' => 'bg-red-100 text-red-800',
    ];
    
    $statusLabels = [
        'pending' => 'En attente',
        'confirmed' => 'Confirmée',
        'checked_in' => 'En cours',
        'checked_out' => 'Terminée',
        'cancelled' => 'Annulée',
    ];
    
    $paymentStatusClasses = [
        'pending' => 'bg-yellow-100 text-yellow-800',
        'paid' => 'bg-green-100 text-green-800',
        'failed' => 'bg-red-100 text-red-800',
        'refunded' => 'bg-blue-100 text-blue-800',
    ];
    
    $paymentStatusLabels = [
        'pending' => 'En attente',
        'paid' => 'Payé',
        'failed' => 'Échoué',
        'refunded' => 'Remboursé',
    ];
    
    $paymentMethodIcons = [
        'cash' => '💵',
        'credit_card' => '💳',
        'bank_transfer' => '🏦',
        'mobile_money' => '📱',
    ];
    
    $paymentMethodLabels = [
        'cash' => 'Espèces',
        'credit_card' => 'Carte de crédit',
        'bank_transfer' => 'Virement bancaire',
        'mobile_money' => 'Mobile Money',
    ];
@endphp

@if(!$isModal)
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Réservation #{{ $booking->booking_reference }}
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        Détails complets de la réservation
                    </p>
                </div>
                <div class="flex space-x-3
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusClasses[$booking->status] ?? 'bg-gray-100 text-gray-800' }}">
                    {{ $statusLabels[$booking->status] ?? ucfirst($booking->status) }}
                </span>
                @if($showActions)
                    <a href="{{ route('hotel-manager.hotels.bookings.edit', [$hotel, $booking]) }}" 
                       class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="-ml-1 mr-2 h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                        Modifier
                    </a>
                @endif
                </div>
            </div>
        </div>
        <div class="border-t border-gray-200">
            <dl>
                <!-- Informations sur le séjour -->
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Séjour</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <div class="flex items-start">
                            @if($room && $room->main_photo_url)
                                <div class="flex-shrink-0 h-20 w-20 mr-4">
                                    <img class="h-20 w-20 rounded-md object-cover" src="{{ $room->main_photo_url }}" alt="{{ $room->name }}">
                                </div>
                            @endif
                            <div>
                                <div class="font-medium">{{ $room->name ?? 'Chambre non spécifiée' }}</div>
                                @if($roomType)
                                    <div class="text-sm text-gray-500">{{ $roomType->name }}</div>
                                @endif
                                <div class="mt-1 text-sm text-gray-500">
                                    {{ $booking->adults }} {{ $booking->adults > 1 ? 'adultes' : 'adulte' }}
                                    @if($booking->children > 0)
                                        , {{ $booking->children }} {{ $booking->children > 1 ? 'enfants' : 'enfant' }}
                                    @endif
                                </div>
                                @if($booking->special_requests)
                                    <div class="mt-2">
                                        <span class="text-sm font-medium text-gray-500">Demandes spéciales :</span>
                                        <p class="text-sm text-gray-700">{{ $booking->special_requests }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="border rounded-lg p-4">
                                <div class="text-sm font-medium text-gray-500">Arrivée</div>
                                <div class="mt-1 text-lg font-semibold">{{ $booking->check_in->format('l d F Y') }}</div>
                                <div class="text-sm text-gray-500">À partir de 14h00</div>
                            </div>
                            <div class="border rounded-lg p-4">
                                <div class="text-sm font-medium text-gray-500">Départ</div>
                                <div class="mt-1 text-lg font-semibold">{{ $booking->check_out->format('l d F Y') }}</div>
                                <div class="text-sm text-gray-500">Avant 12h00</div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <div class="text-sm font-medium text-gray-500">Durée du séjour</div>
                            <div class="mt-1 text-sm text-gray-900">
                                {{ $booking->check_in->diffInDays($booking->check_out) }} nuits
                                (du {{ $booking->check_in->format('d/m/Y') }} au {{ $booking->check_out->format('d/m/Y') }})
                            </div>
                        </div>
                    </dd>
                </div>
                
                <!-- Informations client -->
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Client</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <div class="flex items-center">
                            <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center mr-3">
                                <span class="text-indigo-600 font-medium">{{ substr($booking->guest_name ?? ($user->name ?? '?'), 0, 1) }}</span>
                            </div>
                            <div>
                                <div class="font-medium">{{ $booking->guest_name ?? $user->name ?? 'Non spécifié' }}</div>
                                @if($booking->guest_email || $user?->email)
                                    <div class="text-sm text-gray-500">{{ $booking->guest_email ?? $user->email }}</div>
                                @endif
                                @if($booking->guest_phone || $user?->phone)
                                    <div class="text-sm text-gray-500">{{ $booking->guest_phone ?? $user->phone }}</div>
                                @endif
                            </div>
                        </div>
                        
                        @if(($user && ($user->address || $user->city || $user->country)) || $booking->guest_address)
                            <div class="mt-4">
                                <div class="text-sm font-medium text-gray-500">Adresse</div>
                                <div class="mt-1 text-sm text-gray-900">
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
                            <div class="mt-4 flex space-x-4">
                                <a href="{{ route('hotel-manager.users.show', $user) }}" 
                                   class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-900">
                                    <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                    </svg>
                                    Voir le profil
                                </a>
                                @if($user->email)
                                    <a href="mailto:{{ $user->email }}" 
                                       class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-900">
                                        <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                        </svg>
                                        Envoyer un email
                                    </a>
                                @endif
                                @if($user->phone)
                                    <a href="tel:{{ $user->phone }}" 
                                       class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-900">
                                        <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                                        </svg>
                                        Appeler
                                    </a>
                                @endif
                            </div>
                        @endif
                    </dd>
                </div>
                
                <!-- Paiement -->
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Paiement</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <div class="text-sm font-medium text-gray-500">Montant total</div>
                                <div class="mt-1 text-lg font-semibold">{{ number_format($booking->total_amount, 0, ',', ' ') }} FCFA</div>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-500">Statut du paiement</div>
                                <div class="mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $paymentStatusClasses[$payment->status ?? 'pending'] }}">
                                        {{ $paymentStatusLabels[$payment->status ?? 'pending'] }}
                                        @if($payment && $payment->paid_at)
                                            - {{ $payment->paid_at->format('d/m/Y') }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-500">Méthode de paiement</div>
                                <div class="mt-1 flex items-center">
                                    <span class="mr-2">{{ $paymentMethodIcons[$payment->payment_method ?? 'cash'] ?? '💳' }}</span>
                                    <span>{{ $paymentMethodLabels[$payment->payment_method ?? 'cash'] ?? 'Non spécifié' }}</span>
                                </div>
                            </div>
                            @if($payment && $payment->reference)
                                <div>
                                    <div class="text-sm font-medium text-gray-500">Référence</div>
                                    <div class="mt-1 font-mono text-sm">{{ $payment->reference }}</div>
                                </div>
                            @endif
                        </div>
                        
                        @if($payment && $payment->notes)
                            <div class="mt-4">
                                <div class="text-sm font-medium text-gray-500">Notes de paiement</div>
                                <div class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ $payment->notes }}</div>
                            </div>
                        @endif
                    </dd>
                </div>
                
                <!-- Historique et notes -->
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Historique et notes</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <div class="flow-root">
                            <ul class="-mb-8">
                                <li>
                                    <div class="relative pb-8">
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                    <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                <div>
                                                    <p class="text-sm text-gray-500">Réservation créée</p>
                                                </div>
                                                <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                    <time datetime="{{ $booking->created_at->toIso8601String() }}">{{ $booking->created_at->diffForHumans() }}</time>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                
                                @if($booking->cancelled_at)
                                    <li>
                                        <div class="relative pb-8">
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span class="h-8 w-8 rounded-full bg-red-500 flex items-center justify-center ring-8 ring-white">
                                                        <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                    <div>
                                                        <p class="text-sm text-gray-500">Réservation annulée</p>
                                                        @if($booking->cancellation_reason)
                                                            <p class="text-sm text-gray-400">{{ $booking->cancellation_reason }}</p>
                                                        @endif
                                                    </div>
                                                    <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                        <time datetime="{{ $booking->cancelled_at->toIso8601String() }}">{{ $booking->cancelled_at->diffForHumans() }}</time>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endif
                                
                                @if($booking->checked_in_at)
                                    <li>
                                        <div class="relative pb-8">
                                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                        <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                    <div>
                                                        <p class="text-sm text-gray-500">Arrivée enregistrée</p>
                                                    </div>
                                                    <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                        <time datetime="{{ $booking->checked_in_at->toIso8601String() }}">{{ $booking->checked_in_at->diffForHumans() }}</time>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endif
                                
                                @if($booking->checked_out_at)
                                    <li>
                                        <div class="relative pb-8">
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span class="h-8 w-8 rounded-full bg-gray-500 flex items-center justify-center ring-8 ring-white">
                                                        <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd" />
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                    <div>
                                                        <p class="text-sm text-gray-500">Départ enregistré</p>
                                                    </div>
                                                    <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                        <time datetime="{{ $booking->checked_out_at->toIso8601String() }}">{{ $booking->checked_out_at->diffForHumans() }}</time>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endif
                            </ul>
                        </div>
                        
                        @if($booking->notes)
                            <div class="mt-6">
                                <div class="text-sm font-medium text-gray-500 mb-2">Notes internes</div>
                                <div class="bg-white p-4 rounded-md border border-gray-200 text-sm text-gray-700 whitespace-pre-line">
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
    <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
        <div class="sm:flex sm:items-start">
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                    Réservation #{{ $booking->booking_reference }}
                </h3>
                <div class="mt-2">
                    <div class="flex items-center justify-between mb-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses[$booking->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $statusLabels[$booking->status] ?? ucfirst($booking->status) }}
                        </span>
                        <div class="text-sm text-gray-500">
                            {{ $booking->check_in->format('d/m/Y') }} - {{ $booking->check_out->format('d/m/Y') }}
                            ({{ $booking->check_in->diffInDays($booking->check_out) }} nuits)
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-md mb-4">
                        <div class="flex items-start">
                            @if($room && $room->main_photo_url)
                                <div class="flex-shrink-0 h-16 w-16 mr-3">
                                    <img class="h-16 w-16 rounded-md object-cover" src="{{ $room->main_photo_url }}" alt="{{ $room->name }}">
                                </div>
                            @endif
                            <div>
                                <div class="font-medium">{{ $room->name ?? 'Chambre non spécifiée' }}</div>
                                @if($roomType)
                                    <div class="text-sm text-gray-500">{{ $roomType->name }}</div>
                                @endif
                                <div class="mt-1 text-sm text-gray-500">
                                    {{ $booking->adults }} {{ $booking->adults > 1 ? 'adultes' : 'adulte' }}
                                    @if($booking->children > 0)
                                        , {{ $booking->children }} {{ $booking->children > 1 ? 'enfants' : 'enfant' }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <div class="text-sm font-medium text-gray-500">Client</div>
                            <div class="mt-1 text-sm text-gray-900">{{ $booking->guest_name ?? $user->name ?? 'Non spécifié' }}</div>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-500">Montant total</div>
                            <div class="mt-1 text-sm font-semibold text-gray-900">{{ number_format($booking->total_amount, 0, ',', ' ') }} FCFA</div>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-500">Statut du paiement</div>
                            <div class="mt-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $paymentStatusClasses[$payment->status ?? 'pending'] }}">
                                    {{ $paymentStatusLabels[$payment->status ?? 'pending'] }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-500">Méthode de paiement</div>
                            <div class="mt-1 text-sm text-gray-900">
                                {{ $paymentMethodLabels[$payment->payment_method ?? 'cash'] ?? 'Non spécifié' }}
                            </div>
                        </div>
                    </div>
                    
                    @if($booking->special_requests)
                        <div class="mt-4">
                            <div class="text-sm font-medium text-gray-500">Demandes spéciales</div>
                            <div class="mt-1 text-sm text-gray-700">{{ $booking->special_requests }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    @if($showActions)
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <a href="{{ route('hotel-manager.hotels.bookings.show', [$hotel, $booking]) }}" 
               class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                Voir les détails
            </a>
            <a href="{{ route('hotel-manager.hotels.bookings.edit', [$hotel, $booking]) }}" 
               class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                Modifier
            </a>
            <button type="button" 
                    @click="$dispatch('close')"
                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                Fermer
            </button>
        </div>
    @endif
@endif
