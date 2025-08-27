@extends('layouts.tourist')

@php
    $titles = [
        'restaurant' => 'Réservation de restaurant',
        'hotel' => 'Réservation d\'hôtel',
        'event' => 'Réservation d\'événement',
        'flight' => 'Réservation de vol',
        'bus' => 'Réservation de bus',
        'taxi' => 'Réservation de taxi',
        'tourist_site' => 'Réservation de site touristique',
    ];
    
    $title = $titles[$type] ?? 'Réservation';
    
    // Définir les détails spécifiques selon le type de réservation
    $details = [];
    
    switch ($type) {
        case 'restaurant':
            $details = [
                ['label' => 'Restaurant', 'value' => $reservation->restaurant->name],
                ['label' => 'Date', 'value' => $reservation->reservation_at->format('d/m/Y H:i')],
                ['label' => 'Nombre de personnes', 'value' => $reservation->party_size],
            ];
            break;
            
        case 'hotel':
            $details = [
                ['label' => 'Hôtel', 'value' => $reservation->hotel->name],
                ['label' => 'Chambre', 'value' => $reservation->room->name],
                ['label' => 'Dates', 'value' => $reservation->check_in->format('d/m/Y') . ' - ' . $reservation->check_out->format('d/m/Y')],
                ['label' => 'Nuits', 'value' => $reservation->nights],
            ];
            break;
            
        case 'event':
            $details = [
                ['label' => 'Événement', 'value' => $reservation->event->name],
                ['label' => 'Date', 'value' => $reservation->event->start_date->format('d/m/Y H:i')],
                ['label' => 'Lieu', 'value' => $reservation->event->location],
                ['label' => 'Billets', 'value' => $reservation->ticket_quantity],
            ];
            break;
            
        case 'flight':
            $details = [
                ['label' => 'Compagnie', 'value' => $reservation->flight->airline],
                ['label' => 'Vol', 'value' => $reservation->flight->flight_number],
                ['label' => 'Départ', 'value' => $reservation->flight->departure_airport . ' - ' . $reservation->flight->departure_time->format('d/m/Y H:i')],
                ['label' => 'Arrivée', 'value' => $reservation->flight->arrival_airport . ' - ' . $reservation->flight->arrival_time->format('d/m/Y H:i')],
                ['label' => 'Passagers', 'value' => $reservation->passengers_count],
            ];
            break;
            
        case 'bus':
            $details = [
                ['label' => 'Ligne', 'value' => $reservation->bus->route_name],
                ['label' => 'Départ', 'value' => $reservation->departure_station . ' - ' . $reservation->departure_time->format('d/m/Y H:i')],
                ['label' => 'Arrivée', 'value' => $reservation->arrival_station . ' - ' . $reservation->arrival_time->format('d/m/Y H:i')],
                ['label' => 'Sièges', 'value' => $reservation->seat_count],
            ];
            break;
            
        case 'taxi':
            $details = [
                ['label' => 'Trajet', 'value' => $reservation->pickup_location . ' → ' . $reservation->dropoff_location],
                ['label' => 'Date', 'value' => $reservation->pickup_time->format('d/m/Y H:i')],
                ['label' => 'Passagers', 'value' => $reservation->passenger_count],
                ['label' => 'Type de véhicule', 'value' => $reservation->vehicle_type],
            ];
            break;
            
        case 'tourist_site':
            $details = [
                ['label' => 'Site touristique', 'value' => $reservation->site->name],
                ['label' => 'Date de visite', 'value' => $reservation->visit_date->format('d/m/Y')],
                ['label' => 'Visiteurs', 'value' => $reservation->visitor_count],
                ['label' => 'Type de visite', 'value' => $reservation->visit_type],
            ];
            break;
    }
    
    // Récupérer le montant payé (peut varier selon le modèle)
    $amount = isset($reservation->amount_paid) ? $reservation->amount_paid : 
             (isset($reservation->total_amount) ? $reservation->total_amount : 0);
    
    // Déterminer la route pour les réservations de l'utilisateur
    $reservationsRoute = 'profile.reservations';
    if (in_array($type, ['hotel', 'flight', 'bus', 'taxi', 'tourist_site'])) {
        $reservationsRoute = 'profile.' . $type . '.index';
    }
@endphp

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success text-white py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <h4 class="mb-0">Paiement réussi !</h4>
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <div class="text-center mb-5">
                        <div class="bg-soft-success rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="fas fa-check text-success" style="font-size: 2.5rem;"></i>
                        </div>
                        <h2 class="mt-4 mb-3">Merci pour votre réservation !</h2>
                        <p class="lead text-muted">Votre paiement a été traité avec succès.</p>
                    </div>
                    
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-light py-3">
                            <h5 class="mb-0">Détails de votre réservation</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-uppercase text-muted mb-3">Réservation #{{ $reservation->id }}</h6>
                                    @foreach($details as $detail)
                                        <p class="mb-2">
                                            <strong>{{ $detail['label'] }} :</strong> 
                                            <span class="float-end">{{ $detail['value'] }}</span>
                                        </p>
                                        @if(!$loop->last)
                                            <hr class="my-2">
                                        @endif
                                    @endforeach
                                </div>
                                <div class="col-md-6 border-start">
                                    <h6 class="text-uppercase text-muted mb-3">Récapitulatif du paiement</h6>
                                    <p class="mb-2">
                                        <strong>Montant total :</strong>
                                        <span class="float-end fw-bold">{{ number_format($amount, 0, ',', ' ') }} FCFA</span>
                                    </p>
                                    <p class="mb-2">
                                        <strong>Statut :</strong>
                                        <span class="float-end">
                                            <span class="badge bg-success">Payé</span>
                                        </span>
                                    </p>
                                    <p class="mb-2">
                                        <strong>Date du paiement :</strong>
                                        <span class="float-end">{{ now()->format('d/m/Y à H:i') }}</span>
                                    </p>
                                    <p class="mb-0">
                                        <strong>Méthode :</strong>
                                        <span class="float-end">Carte bancaire</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info border-0">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle me-3 fa-2x"></i>
                            <div>
                                <h5 class="alert-heading">Important</h5>
                                <p class="mb-0">
                                    Un email de confirmation a été envoyé à <strong>{{ auth()->user()->email }}</strong>.
                                    Conservez bien ce numéro de réservation pour toute réclamation.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-3 d-md-flex justify-content-md-center mt-5">
                        <a href="{{ route('home') }}" class="btn btn-primary btn-lg px-4">
                            <i class="fas fa-home me-2"></i> Retour à l'accueil
                        </a>
                        <a href="{{ route($reservationsRoute) }}" class="btn btn-outline-secondary btn-lg px-4">
                            <i class="fas fa-calendar-alt me-2"></i> Voir mes réservations
                        </a>
                        <button class="btn btn-outline-primary btn-lg px-4" onclick="window.print()">
                            <i class="fas fa-print me-2"></i> Imprimer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Style spécifique à la page de confirmation -->
<style>
    body {
        background-color: #f8f9fa;
    }
    
    .card {
        border-radius: 12px;
        overflow: hidden;
        border: none;
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.08);
    }
    
    .card-header {
        border-bottom: none;
    }
    
    .bg-soft-success {
        background-color: rgba(40, 167, 69, 0.1) !important;
    }
    
    .btn-lg {
        padding: 0.75rem 1.5rem;
        font-size: 1.1rem;
        border-radius: 8px;
    }
    
    @media print {
        .no-print {
            display: none !important;
        }
        
        .card {
            box-shadow: none !important;
            border: 1px solid #dee2e6 !important;
        }
        
        .btn {
            display: none !important;
        }
    }
</style>
@endsection
