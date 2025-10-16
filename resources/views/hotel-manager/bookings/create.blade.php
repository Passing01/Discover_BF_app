@extends('layouts.hotel')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="me-3 mb-2">
                    <h2 class="h3 mb-1">Nouvelle réservation</h2>
                    <p class="text-muted mb-0">
                        <i class="fas fa-hotel me-1"></i> Hôtel: {{ $hotel->name }}
                    </p>
                </div>
                <div class="mb-2">
                    <a href="{{ route('hotel-manager.hotels.bookings.index', $hotel) }}" 
                       class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>
                        Retour à la liste
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            @include('hotel-manager.bookings._form', [
                'booking' => new \App\Models\HotelBooking(),
                'users' => $users,
                'rooms' => $rooms,
                'roomTypes' => $roomTypes,
                'hotel' => $hotel
            ])
        </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Scripts JavaScript ici
        const checkInInput = document.getElementById('check_in');
        const checkOutInput = document.getElementById('check_out');
        const roomSelect = document.getElementById('room_id');
        const totalNightsSpan = document.getElementById('total_nights');
        const totalAmountInput = document.getElementById('total_amount');

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
            totalAmountInput.value = total.toLocaleString('fr-FR');
        }
        
        // Écouteurs d'événements
        checkInInput.addEventListener('change', function() {
            if (checkInInput.value) {
                const minCheckOut = new Date(checkInInput.value);
                minCheckOut.setDate(minCheckOut.getDate() + 1);
                checkOutInput.min = minCheckOut.toISOString().split('T')[0];
                
                if (checkOutInput.value && new Date(checkOutInput.value) <= new Date(checkInInput.value)) {
                    checkOutInput.value = '';
                }
            }
            updateTotalAmount();
        });
        
        checkOutInput.addEventListener('change', updateTotalAmount);
        roomSelect.addEventListener('change', updateTotalAmount);
        
        // Initialiser les valeurs
        if (checkInInput.value && checkOutInput.value) {
            updateTotalAmount();
        }
    });
    
    // Gestion de la sélection du type de chambre pour filtrer les chambres
    document.addEventListener('DOMContentLoaded', function() {
        const roomTypeSelect = document.getElementById('room_type_id');
        const roomSelect = document.getElementById('room_id');
        
        roomTypeSelect.addEventListener('change', function() {
            const selectedTypeId = this.value;
            const roomOptions = roomSelect.getElementsByTagName('option');
            
            // Afficher toutes les options
            for (let i = 0; i < roomOptions.length; i++) {
                const roomOption = roomOptions[i];
                if (roomOption.value === '') continue; // Ne pas cacher l'option vide
                
                const roomTypeId = roomOption.getAttribute('data-room-type-id');
                if (selectedTypeId === '' || roomTypeId === selectedTypeId) {
                    roomOption.style.display = '';
                } else {
                    roomOption.style.display = 'none';
                    // Désélectionner si l'option était sélectionnée
                    if (roomOption.selected) {
                        roomSelect.selectedIndex = 0;
                    }
                }
            }
        });
    });
</script>
@endpush

@push('styles')
<style>
    /* Styles personnalisés pour améliorer l'expérience utilisateur */
    [x-cloak] { display: none !important; }
    
    /* Style pour les champs en lecture seule */
    input[readonly] {
        background-color: #f3f4f6;
        cursor: not-allowed;
    }
    
    /* Style pour les sections du formulaire */
    .form-section {
        @apply border-t border-gray-200 pt-6 mt-6;
    }
    
    /* Style pour les titres de section */
    .section-title {
        @apply text-lg font-medium text-gray-900 mb-4;
    }
    
    /* Style pour les messages d'erreur */
    .error-message {
        @apply mt-2 text-sm text-red-600;
    }
</style>
@endpush

@endsection
