@php
    $isEdit = isset($booking) && $booking->exists;
    $route = $isEdit 
        ? route('hotel-manager.hotels.bookings.update', [$hotel, $booking])
        : route('hotels.bookings.store', $hotel);
    $method = $isEdit ? 'PUT' : 'POST';
    
    // Valeurs par défaut pour l'édition
    $defaults = [
        'user_id' => $booking->user_id ?? old('user_id'),
        'guest_name' => $booking->guest_name ?? old('guest_name'),
        'guest_email' => $booking->guest_email ?? old('guest_email'),
        'guest_phone' => $booking->guest_phone ?? old('guest_phone'),
        'room_id' => $booking->room_id ?? old('room_id'),
        'room_type_id' => $booking->room ? $booking->room->room_type_id : old('room_type_id'),
        'check_in' => $isEdit ? $booking->check_in->format('Y-m-d') : (old('check_in') ?? now()->format('Y-m-d')),
        'check_out' => $isEdit ? $booking->check_out->format('Y-m-d') : (old('check_out') ?? now()->addDay()->format('Y-m-d')),
        'adults' => $booking->adults ?? old('adults', 2),
        'children' => $booking->children ?? old('children', 0),
        'special_requests' => $booking->special_requests ?? old('special_requests'),
        'total_amount' => (float)($booking->total_amount ?? old('total_amount', 0)),
        'payment_method' => $booking->payment->payment_method ?? old('payment_method', 'cash'),
        'payment_status' => $booking->payment->status ?? old('payment_status', 'paid'),
        'status' => $booking->status ?? old('status', 'confirmed'),
        'notes' => $booking->notes ?? old('notes')
    ];
@endphp

<form action="{{ $route }}" method="POST" class="needs-validation" novalidate id="bookingForm">
    @csrf
    @method($method)
    
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row g-3">
                <!-- Informations client -->
                <div class="col-12">
                    <h4 class="h5 text-dark mb-4">Client</h4>
                </div>

                <div class="col-md-6">
                    <label for="user_id" class="form-label">Client existant</label>
                    <select id="user_id" name="user_id" class="form-select" type="button">
                        <option value="">Sélectionner un client existant</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ $defaults['user_id'] == $user->id ? 'selected' : '' }} data-email="{{ $user->email }}" data-phone="{{ $user->phone }}">
                                {{ $user->full_name ?? $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    <p class="form-text mt-2">
                        Ou <a href="{{ route('hotels.clients.create', $hotel->id) }}" class="text-primary text-decoration-none">ajouter un nouveau client</a>
                    </p>
                </div>

                <!-- Champs pour les informations du client -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Informations du client</h5>
                            <div id="client-info">
                                @if(isset($selectedUser) && $selectedUser)
                                    <div id="selected-user-data" data-user-id="{{ $selectedUser->id }}">
                                        <p><strong>Nom :</strong> {{ $selectedUser->full_name ?? $selectedUser->name }}</p>
                                        <p><strong>Email :</strong> {{ $selectedUser->email }}</p>
                                        <p><strong>Téléphone :</strong> {{ $selectedUser->phone ?? 'Non renseigné' }}</p>
                                        @if($selectedUser->profile)
                                            @if($selectedUser->profile->address)
                                                <p><strong>Adresse :</strong> {{ $selectedUser->profile->address }}</p>
                                            @endif
                                            @if($selectedUser->profile->city || $selectedUser->profile->country)
                                                <p><strong>Ville/Pays :</strong> {{ $selectedUser->profile->city ?? '' }}{{ $selectedUser->profile->city && $selectedUser->profile->country ? ', ' : '' }}{{ $selectedUser->profile->country ?? '' }}</p>
                                            @endif
                                        @endif
                                    </div>
                                @else
                                    <p class="text-muted">Sélectionnez un client pour afficher ses informations</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                </div>

                <!-- Détails du séjour -->
                <div class="col-span-6 mt-6">
                    <h4 class="text-md font-medium text-gray-900 mb-4">Détails du séjour</h4>
                </div>

                <div class="col-md-6">
                    <label for="room_type_id" class="form-label">Type de chambre</label>
                    <select id="room_type_id" name="room_type_id" class="form-select" required>
                        <option value="">Sélectionnez un type de chambre</option>
                        @foreach($roomTypes as $type)
                            <option value="{{ $type->id }}" {{ $defaults['room_type_id'] == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('room_type_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="room_id" class="form-label">Chambre <span class="text-danger">*</span></label>
                    <select id="room_id" name="room_id" class="form-select @error('room_id') is-invalid @enderror" required>
                        <option value="">Sélectionnez d'abord un type de chambre</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}" 
                                    data-price="{{ $room->price_per_night }}"
                                    data-room-type-id="{{ $room->room_type_id }}"
                                    {{ $defaults['room_id'] == $room->id ? 'selected' : '' }}
                                    style="display: none;">
                                {{ $room->name }} - {{ number_format($room->price_per_night, 0, ',', ' ') }} FCFA/nuit
                                ({{ $room->roomType ? $room->roomType->name : 'Sans type' }})
                            </option>
                        @endforeach
                    </select>
                    @error('room_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @else
                        <div class="form-text">Sélectionnez d'abord un type de chambre pour voir les chambres disponibles</div>
                    @enderror
                    <div id="no-rooms-message" class="text-warning mt-2" style="display: none;">
                        Aucune chambre disponible pour ce type. Veuillez sélectionner un autre type de chambre.
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="check_in" class="form-label">Date d'arrivée</label>
                    <input type="date" name="check_in" id="check_in" value="{{ $defaults['check_in'] }}" min="{{ now()->format('Y-m-d') }}" class="form-control">
                    @error('check_in')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="check_out" class="form-label">Date de départ</label>
                    <input type="date" name="check_out" id="check_out" value="{{ $defaults['check_out'] }}" min="{{ now()->addDay()->format('Y-m-d') }}" class="form-control">
                    @error('check_out')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="adults" class="form-label">Adultes</label>
                    <select id="adults" name="adults" class="form-select">
                        @for($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}" {{ $defaults['adults'] == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="children" class="form-label">Enfants</label>
                    <select id="children" name="children" class="form-select">
                        @for($i = 0; $i <= 5; $i++)
                            <option value="{{ $i }}" {{ $defaults['children'] == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="total_nights" class="form-label">Nuits</label>
                    <input type="text" id="total_nights" value="1" readonly class="form-control bg-light">
                </div>

                <div class="col-12">
                    <label for="special_requests" class="form-label">Demandes spéciales</label>
                    <textarea id="special_requests" name="special_requests" rows="3" class="form-control">{{ $defaults['special_requests'] }}</textarea>
                </div>

                <!-- Détails du paiement -->
                <div class="col-12 mt-4">
                    <h4 class="h5 text-dark mb-4">Paiement</h4>
                </div>

                <div class="col-md-6">
                    <label for="total_amount" class="form-label">Montant total</label>
                    <div class="input-group">
                        <span class="input-group-text">FCFA</span>
                        <input type="number" 
                               name="total_amount" 
                               id="total_amount" 
                               value="{{ $defaults['total_amount'] }}" 
                               class="form-control"
                               min="0"
                               step="0.01">
                    </div>
                    @error('total_amount')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="payment_method" class="form-label">Méthode de paiement</label>
                    <select id="payment_method" name="payment_method" class="form-select">
                        <option value="cash" {{ $defaults['payment_method'] == 'cash' ? 'selected' : '' }}>Espèces</option>
                        <option value="credit_card" {{ $defaults['payment_method'] == 'credit_card' ? 'selected' : '' }}>Carte de crédit</option>
                        <option value="bank_transfer" {{ $defaults['payment_method'] == 'bank_transfer' ? 'selected' : '' }}>Virement bancaire</option>
                        <option value="mobile_money" {{ $defaults['payment_method'] == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="payment_status" class="form-label">Statut du paiement</label>
                    <select id="payment_status" name="payment_status" class="form-select">
                        <option value="pending" {{ $defaults['payment_status'] == 'pending' ? 'selected' : '' }}>En attente</option>
                        <option value="paid" {{ $defaults['payment_status'] == 'paid' ? 'selected' : '' }}>Payé</option>
                        <option value="failed" {{ $defaults['payment_status'] == 'failed' ? 'selected' : '' }}>Échoué</option>
                        <option value="refunded" {{ $defaults['payment_status'] == 'refunded' ? 'selected' : '' }}>Remboursé</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="status" class="form-label">Statut de la réservation</label>
                    <select id="status" name="status" class="form-select">
                        <option value="pending" {{ $defaults['status'] == 'pending' ? 'selected' : '' }}>En attente</option>
                        <option value="confirmed" {{ $defaults['status'] == 'confirmed' ? 'selected' : '' }}>Confirmée</option>
                        <option value="checked_in" {{ $defaults['status'] == 'checked_in' ? 'selected' : '' }}>En cours</option>
                        <option value="checked_out" {{ $defaults['status'] == 'checked_out' ? 'selected' : '' }}>Terminée</option>
                        <option value="cancelled" {{ $defaults['status'] == 'cancelled' ? 'selected' : '' }}>Annulée</option>
                    </select>
                </div>

                <div class="col-12">
                    <label for="notes" class="form-label">Notes internes</label>
                    <textarea id="notes" name="notes" rows="2" class="form-control">{{ $defaults['notes'] }}</textarea>
                    <div class="form-text">Ces notes ne seront pas visibles par le client.</div>
                </div>
            </div>
        </div>
        
        <div class="card-footer bg-light d-flex justify-content-between align-items-center">
            <a href="{{ $isEdit ? route('hotel-manager.hotels.bookings.show', [$hotel, $booking]) : route('hotel-manager.hotels.bookings.index', $hotel) }}" 
               class="btn btn-outline-secondary">
                Annuler
            </a>
            <div class="d-flex">
                <button type="submit" class="btn btn-primary">
                    {{ $isEdit ? 'Mettre à jour' : 'Enregistrer' }} la réservation
                </button>
                
                @if($isEdit)
                    <button type="button" 
                            onclick="if(confirm('Êtes-vous sûr de vouloir supprimer cette réservation ?')) { document.getElementById('delete-form').submit(); }"
                            class="btn btn-danger ms-2">
                        <i class="fas fa-trash me-1"></i>
                        Supprimer
                    </button>
                @endif
            </div>
        </div>
    </div>
</form>

@if(isset($booking) && $booking->exists)
    <form id="delete-form" action="{{ route('hotel-manager.hotels.bookings.destroy', [$hotel, $booking]) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
@endif

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialisation des champs de date avec Flatpickr
        const dateInputs = document.querySelectorAll('input[type="date"]');
        if (dateInputs.length > 0 && typeof flatpickr !== 'undefined') {
            flatpickr(dateInputs, {
                dateFormat: 'Y-m-d',
                allowInput: true,
                clickOpens: true,
                disableMobile: true,
                locale: 'fr'
            });
        }
        
        // Gestion du chargement des informations du client
        const userIdSelect = document.getElementById('user_id');
        const clientInfoDiv = document.getElementById('client-info');
        
        // Désactiver le comportement par défaut du formulaire pour le sélecteur de client
        if (userIdSelect) {
            // Empêcher tout comportement par défaut sur le changement de sélection
            userIdSelect.addEventListener('change', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                return false;
            }, true);
            
            // Gérer la sélection d'un client
            $(userIdSelect).on('change', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const userId = this.value;
                if (!userId) {
                    clientInfoDiv.innerHTML = '<p class="text-muted">Sélectionnez un client pour afficher ses informations</p>';
                    return;
                }
                
                // Afficher un indicateur de chargement
                clientInfoDiv.innerHTML = '<p class="text-muted">Chargement des informations du client...</p>';
                
                // Récupérer les informations du client via AJAX
                $.ajax({
                    url: `/users/${userId}/details`,
                    method: 'GET',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(user) {
                        // Construire le HTML avec les informations du client
                        let html = `
                            <p><strong>Nom :</strong> ${user.full_name || user.name}</p>
                            <p><strong>Email :</strong> ${user.email}</p>
                            <p><strong>Téléphone :</strong> ${user.phone || 'Non renseigné'}</p>
                        `;
                        
                        if (user.profile) {
                            if (user.profile.address) {
                                html += `<p><strong>Adresse :</strong> ${user.profile.address}</p>`;
                            }
                            
                            const location = [];
                            if (user.profile.city) location.push(user.profile.city);
                            if (user.profile.country) location.push(user.profile.country);
                            
                            if (location.length > 0) {
                                html += `<p><strong>Ville/Pays :</strong> ${location.join(', ')}</p>`;
                            }
                        }
                        
                        clientInfoDiv.innerHTML = html;
                    },
                    error: function(xhr, status, error) {
                        console.error('Erreur lors du chargement des informations du client:', error);
                        
                        // Afficher les informations de base disponibles dans le DOM
                        const selectedOption = userIdSelect.options[userIdSelect.selectedIndex];
                        if (selectedOption) {
                            const email = selectedOption.getAttribute('data-email') || 'Non disponible';
                            const phone = selectedOption.getAttribute('data-phone') || 'Non disponible';
                            const name = selectedOption.textContent.split(' (')[0];
                            
                            clientInfoDiv.innerHTML = `
                                <p><strong>Nom :</strong> ${name}</p>
                                <p><strong>Email :</strong> ${email}</p>
                                <p><strong>Téléphone :</strong> ${phone}</p>
                                <p class="text-warning"><small>Impossible de charger les informations complètes du client</small></p>
                            `;
                        } else {
                            clientInfoDiv.innerHTML = '<p class="text-danger">Erreur lors du chargement des informations du client</p>';
                        }
                    }
                });
                
                return false;
            });
        }
        
        // Gestion du calcul du nombre de nuits et du montant total
        const checkInInput = document.getElementById('check_in');
        const checkOutInput = document.getElementById('check_out');
        const roomSelect = document.getElementById('room_id');
        const totalNightsSpan = document.getElementById('total_nights');
        const totalAmountInput = document.getElementById('total_amount');
        const roomTypeSelect = document.getElementById('room_type_id');
        
        // Fonction pour formater un nombre avec séparateurs de milliers
        function formatNumber(number) {
            return new Intl.NumberFormat('fr-FR').format(number);
        }
        
        // Fonction pour calculer le nombre de nuits
        function calculateNights() {
            if (checkInInput.value && checkOutInput.value) {
                const checkIn = new Date(checkInInput.value);
                const checkOut = new Date(checkOutInput.value);
                
                // Vérifier que la date de départ est après la date d'arrivée
                if (checkOut <= checkIn) {
                    checkOutInput.value = '';
                    alert('La date de départ doit être postérieure à la date d\'arrivée');
                    return 0;
                }
                
                // Calculer la différence en millisecondes
                const diffTime = Math.abs(checkOut - checkIn);
                // Convertir en jours
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                
                return diffDays;
            }
            return 0;
        }
        
        // Fonction pour mettre à jour le montant total
        function updateTotalAmount() {
            const nights = calculateNights();
            const selectedOption = roomSelect.options[roomSelect.selectedIndex];
            const pricePerNight = selectedOption ? parseFloat(selectedOption.getAttribute('data-price') || 0) : 0;
            const total = nights * pricePerNight;
            
            totalNightsSpan.value = nights;
            totalAmountInput.value = formatNumber(total);
        }
        
        // Fonction pour filtrer les chambres par type
        function filterRoomsByType() {
            const selectedTypeId = roomTypeSelect ? roomTypeSelect.value : '';
            const roomOptions = roomSelect.getElementsByTagName('option');
            let hasAvailableRooms = false;
            
            // Masquer toutes les options sauf la première
            for (let i = 1; i < roomOptions.length; i++) {
                roomOptions[i].style.display = 'none';
            }
            
            // Afficher uniquement les chambres du type sélectionné
            if (selectedTypeId) {
                for (let i = 1; i < roomOptions.length; i++) {
                    const roomOption = roomOptions[i];
                    const roomTypeId = roomOption.getAttribute('data-room-type-id');
                    
                    if (roomTypeId === selectedTypeId) {
                        roomOption.style.display = '';
                        hasAvailableRooms = true;
                    }
                }
                
                // Mettre à jour le message d'erreur si aucune chambre n'est disponible
                const noRoomsMessage = document.getElementById('no-rooms-message');
                if (noRoomsMessage) {
                    noRoomsMessage.style.display = hasAvailableRooms ? 'none' : 'block';
                }
                
                // Si aucune chambre n'est disponible, réinitialiser la sélection
                if (!hasAvailableRooms) {
                    roomSelect.selectedIndex = 0;
                }
            } else {
                // Si aucun type n'est sélectionné, masquer le message d'erreur
                const noRoomsMessage = document.getElementById('no-rooms-message');
                if (noRoomsMessage) {
                    noRoomsMessage.style.display = 'none';
                }
            }
            
            updateTotalAmount();
        }
        
        // Écouteurs d'événements
        if (checkInInput) {
            checkInInput.addEventListener('change', function() {
                if (checkInInput.value) {
                    const minCheckOut = new Date(checkInInput.value);
                    minCheckOut.setDate(minCheckOut.getDate() + 1);
                    
                    if (checkOutInput) {
                        checkOutInput.min = minCheckOut.toISOString().split('T')[0];
                        
                        if (checkOutInput.value && new Date(checkOutInput.value) <= new Date(checkInInput.value)) {
                            checkOutInput.value = '';
                        }
                    }
                }
                updateTotalAmount();
            });
        }
        
        if (checkOutInput) {
            checkOutInput.addEventListener('change', updateTotalAmount);
        }
        
        if (roomSelect) {
            roomSelect.addEventListener('change', updateTotalAmount);
        }
        
        if (roomTypeSelect) {
            roomTypeSelect.addEventListener('change', function() {
                // Réinitialiser la sélection de la chambre lors du changement de type
                if (roomSelect) {
                    roomSelect.selectedIndex = 0;
                }
                filterRoomsByType();
            });
            
            // Déclencher le filtrage initial si un type est déjà sélectionné
            if (roomTypeSelect.value) {
                filterRoomsByType();
            }
        }
        
        // Initialiser les valeurs
        if (checkInInput && checkOutInput) {
            updateTotalAmount();
        }
        
        // Si un type de chambre est sélectionné, filtrer les chambres
        if (roomTypeSelect && roomTypeSelect.value) {
            filterRoomsByType();
        }
        
        // Validation du formulaire avant soumission
        const bookingForm = document.getElementById('bookingForm');
        if (bookingForm) {
            bookingForm.addEventListener('submit', function(event) {
                // Vérifier si une chambre est sélectionnée
                if (roomSelect && roomSelect.value === '') {
                    event.preventDefault();
                    alert('Veuillez sélectionner une chambre valide.');
                    roomSelect.focus();
                    return false;
                }
                
                // Vérifier les dates
                if (checkInInput && checkOutInput) {
                    const checkIn = new Date(checkInInput.value);
                    const checkOut = new Date(checkOutInput.value);
                    
                    if (checkOut <= checkIn) {
                        event.preventDefault();
                        alert('La date de départ doit être postérieure à la date d\'arrivée.');
                        checkOutInput.focus();
                        return false;
                    }
                }
                
                return true;
            });
        }
    });
</script>
@endpush
