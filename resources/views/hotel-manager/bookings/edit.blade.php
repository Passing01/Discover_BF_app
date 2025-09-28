@extends('layouts.hotel-manager')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    Modifier la réservation #{{ $booking->booking_reference }}
                </h2>
                <div class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-6">
                    <div class="mt-2 flex items-center text-sm text-gray-500">
                        <span>Hôtel: {{ $hotel->name }}</span>
                        <span class="mx-2">•</span>
                        <span>Créée le {{ $booking->created_at->format('d/m/Y à H:i') }}</span>
                        @if($booking->user)
                            <span class="mx-2">•</span>
                            <span>Client: {{ $booking->user->name }}</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="mt-4 flex space-x-3 md:mt-0">
                <a href="{{ route('hotel-manager.hotels.bookings.show', [$hotel, $booking]) }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                    </svg>
                    Voir les détails
                </a>
                <a href="{{ route('hotel-manager.hotels.bookings.index', $hotel) }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    Retour à la liste
                </a>
            </div>
        </div>

        <div class="mt-8">
            @include('hotel-manager.bookings._form', [
                'booking' => $booking,
                'users' => $users,
                'rooms' => $rooms,
                'roomTypes' => $roomTypes,
                'hotel' => $hotel
            ])
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Styles spécifiques à la page d'édition */
    .booking-status {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    .status-pending {
        background-color: #FEF3C7;
        color: #92400E;
    }
    
    .status-confirmed {
        background-color: #DBEAFE;
        color: #1E40AF;
    }
    
    .status-checked_in {
        background-color: #D1FAE5;
        color: #065F46;
    }
    
    .status-checked_out {
        background-color: #E5E7EB;
        color: #1F2937;
    }
    
    .status-cancelled {
        background-color: #FEE2E2;
        color: #991B1B;
    }
</style>
@endpush

@endsection
