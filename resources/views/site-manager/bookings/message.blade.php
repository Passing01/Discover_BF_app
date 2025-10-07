@extends('layouts.site-manager')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Envoyer un message</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('site-manager.dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item"><a href="{{ route('site-manager.bookings.index') }}">Réservations</a></li>
                <li class="breadcrumb-item"><a href="{{ route('site-manager.bookings.show', $booking) }}">Réservation #{{ $booking->id }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Envoyer un message</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="{{ route('site-manager.bookings.show', $booking) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Retour
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="mb-0">Nouveau message</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('site-manager.bookings.send-message', $booking) }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="subject" class="form-label">Objet <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('subject') is-invalid @enderror" 
                               id="subject" name="subject" value="{{ old('subject') }}" required>
                        @error('subject')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('message') is-invalid @enderror" 
                                  id="message" name="message" rows="10" required>{{ old('message') }}</textarea>
                        @error('message')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Vous pouvez utiliser les balises suivantes qui seront automatiquement remplacées :
                            <code>{client_name}</code>, <code>{site_name}</code>, <code>{booking_id}</code>,
                            <code>{visit_date}</code>, <code>{time_slot}</code>
                        </div>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="send_copy" name="send_copy" value="1" {{ old('send_copy') ? 'checked' : '' }}>
                        <label class="form-check-label" for="send_copy">
                            M'envoyer une copie du message
                        </label>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <button type="button" class="btn btn-outline-secondary" onclick="history.back()">
                            <i class="bi bi-x-lg me-1"></i> Annuler
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send me-1"></i> Envoyer le message
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">Destinataire</h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    @if($booking->user->profile_photo_path)
                        <img src="{{ Storage::url($booking->user->profile_photo_path) }}" 
                             alt="{{ $booking->user->name }}" 
                             class="rounded-circle me-3" 
                             style="width: 50px; height: 50px; object-fit: cover;">
                    @else
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                             style="width: 50px; height: 50px; font-size: 1.25rem;">
                            {{ strtoupper(substr($booking->user->name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <h6 class="mb-0">{{ $booking->user->name }}</h6>
                        <small class="text-muted">{{ $booking->user->email }}</small>
                    </div>
                </div>
                
                <ul class="list-unstyled">
                    <li class="mb-1">
                        <i class="bi bi-envelope me-2 text-muted"></i>
                        <a href="mailto:{{ $booking->user->email }}" class="text-decoration-none">
                            {{ $booking->user->email }}
                        </a>
                    </li>
                    @if($booking->user->phone)
                        <li class="mb-1">
                            <i class="bi bi-telephone me-2 text-muted"></i>
                            <a href="tel:{{ $booking->user->phone }}" class="text-decoration-none">
                                {{ $booking->user->phone }}
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="mb-0">Détails de la réservation</h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    @if($booking->site->photo_url)
                        <img src="{{ Storage::url($booking->site->photo_url) }}" 
                             alt="{{ $booking->site->name }}" 
                             class="rounded me-3" 
                             style="width: 60px; height: 45px; object-fit: cover;">
                    @endif
                    <div>
                        <h6 class="mb-0">{{ $booking->site->name }}</h6>
                        <small class="text-muted">{{ $booking->site->city }}</small>
                    </div>
                </div>
                
                <ul class="list-unstyled">
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">ID Réservation :</span>
                        <span class="fw-medium">#{{ $booking->id }}</span>
                    </li>
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Date de visite :</span>
                        <span class="fw-medium">{{ $booking->visit_date->format('d/m/Y') }}</span>
                    </li>
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Créneau :</span>
                        <span class="fw-medium">{{ $booking->time_slot }}</span>
                    </li>
                    <li class="d-flex justify-content-between py-2">
                        <span class="text-muted">Visiteurs :</span>
                        <span class="fw-medium">{{ $booking->visitor_count }} personne(s)</span>
                    </li>
                </ul>
                
                <div class="alert alert-light mt-3 mb-0">
                    <h6 class="alert-heading">Modèles de messages</h6>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-sm btn-outline-primary text-start mb-2" onclick="useTemplate('confirmation')">
                            <i class="bi bi-check-circle me-1"></i> Confirmation de réservation
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary text-start mb-2" onclick="useTemplate('reminder')">
                            <i class="bi bi-bell me-1"></i> Rappel de visite
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary text-start" onclick="useTemplate('cancellation')">
                            <i class="bi bi-x-circle me-1"></i> Annulation de réservation
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Templates de messages
    const templates = {
        confirmation: {
            subject: 'Confirmation de votre réservation #{booking_id}',
            message: `Bonjour {client_name},

Nous vous confirmons votre réservation pour la visite de {site_name} le {visit_date} à {time_slot}.

Détails de votre réservation :
- Référence : #{booking_id}
- Date : {visit_date}
- Horaire : {time_slot}
- Lieu : {site_name}

Nous vous remercions de votre confiance et restons à votre disposition pour toute information complémentaire.

Cordialement,
L'équipe de {site_name}`
        },
        reminder: {
            subject: 'Rappel : Votre visite de {site_name} demain',
            message: `Bonjour {client_name},

Nous vous rappelons votre visite de {site_name} prévue demain {visit_date} à {time_slot}.

Nous vous conseillons d'arriver 15 minutes avant l'horaire prévu.

En cas d'empêchement, merci de nous prévenir dès que possible.

Au plaisir de vous accueillir,
L'équipe de {site_name}`
        },
        cancellation: {
            subject: 'Annulation de votre réservation #{booking_id}',
            message: `Bonjour {client_name},

Nous vous informons que votre réservation pour la visite de {site_name} prévue le {visit_date} à {time_slot} a été annulée.

Référence de la réservation : #{booking_id}

Si vous n'êtes pas à l'origine de cette annulation ou pour toute information complémentaire, n'hésitez pas à nous contacter.

Nous espérons vous accueillir prochainement.

Cordialement,
L'équipe de {site_name}`
        }
    };
    
    // Fonction pour utiliser un modèle de message
    function useTemplate(templateKey) {
        const template = templates[templateKey];
        if (!template) return;
        
        // Remplacer les balises par les valeurs de la réservation
        let subject = template.subject
            .replace('{client_name}', '{{ $booking->user->name }}')
            .replace('{site_name}', '{{ addslashes($booking->site->name) }}')
            .replace('{booking_id}', '{{ $booking->id }}')
            .replace('{visit_date}', '{{ $booking->visit_date->format("d/m/Y") }}')
            .replace('{time_slot}', '{{ $booking->time_slot }}');
            
        let message = template.message
            .replace(/{client_name}/g, '{{ $booking->user->name }}')
            .replace(/{site_name}/g, '{{ addslashes($booking->site->name) }}')
            .replace(/{booking_id}/g, '{{ $booking->id }}')
            .replace(/{visit_date}/g, '{{ $booking->visit_date->format("d/m/Y") }}')
            .replace(/{time_slot}/g, '{{ $booking->time_slot }}');
        
        // Mettre à jour les champs du formulaire
        document.getElementById('subject').value = subject;
        document.getElementById('message').value = message;
        
        // Faire défiler jusqu'au formulaire
        document.querySelector('form').scrollIntoView({ behavior: 'smooth' });
    }
    
    // Initialisation des tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush

@push('styles')
<style>
    /* Styles pour les boutons de modèle de message */
    .btn-template {
        transition: all 0.2s;
        text-align: left;
        white-space: normal;
    }
    
    .btn-template:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
    }
    
    /* Amélioration de l'éditeur de texte */
    #message {
        min-height: 200px;
        font-family: 'Courier New', monospace;
        white-space: pre-wrap;
    }
    
    /* Style pour les balises de remplacement */
    code {
        background-color: #f8f9fa;
        color: #d63384;
        padding: 0.2rem 0.4rem;
        border-radius: 0.25rem;
        font-size: 0.875em;
    }
    
    /* Style pour les champs du formulaire */
    .form-control:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1);
    }
    
    /* Style pour la zone de destinataire */
    .recipient-card {
        border-left: 3px solid #0d6efd;
    }
    
    /* Style pour la zone de détails de la réservation */
    .booking-details {
        border-left: 3px solid #198754;
    }
    
    /* Style pour les listes */
    .list-unstyled li {
        padding: 0.25rem 0;
    }
    
    /* Style pour les liens */
    a.text-decoration-none:hover {
        text-decoration: underline !important;
    }
    
    /* Style pour les icônes */
    .bi {
        width: 1.25em;
        text-align: center;
    }
</style>
@endpush
@endsection
