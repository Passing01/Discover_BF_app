@extends('layouts.tourist')

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('site.events.index') }}">Événements</a></li>
            <li class="breadcrumb-item"><a href="{{ route('site.events.show', $event) }}">{{ $event->title }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Inscriptions</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Gestion des inscriptions</h1>
        <div>
            <a href="{{ route('site.events.export-registrations', $event) }}" class="btn btn-outline-primary me-2">
                <i class="fas fa-file-export me-1"></i> Exporter
            </a>
            <a href="{{ route('site.events.show', $event) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Retour
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-users fa-lg"></i>
                        </div>
                        <div class="ms-3">
                            <div class="text-muted small">Inscriptions</div>
                            <div class="h4 mb-0">{{ $event->registrations_count }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-check-circle fa-lg"></i>
                        </div>
                        <div class="ms-3">
                            <div class="text-muted small">Confirmées</div>
                            <div class="h4 mb-0">{{ $event->confirmed_registrations_count }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-warning text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-user-clock fa-lg"></i>
                        </div>
                        <div class="ms-3">
                            <div class="text-muted small">En attente</div>
                            <div class="h4 mb-0">{{ $event->pending_registrations_count }}</div>
                        </div>
                    </div>
                </div>
            </div>
            
            @if($event->max_participants)
                <div class="mt-4">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Capacité d'accueil</span>
                        <span>{{ $event->registrations_count }} / {{ $event->max_participants }} ({{ $event->registration_progress }}%)</span>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-{{ $event->registration_progress >= 100 ? 'danger' : 'success' }}" 
                             role="progressbar" 
                             style="width: {{ min($event->registration_progress, 100) }}%" 
                             aria-valuenow="{{ $event->registration_progress }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Liste des inscrits</h5>
            <div class="input-group input-group-sm" style="width: 250px;">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text" id="searchInput" class="form-control" placeholder="Rechercher...">
            </div>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($registrations as $registration)
                            <tr data-search="{{ strtolower($registration->name . ' ' . $registration->email . ' ' . $registration->phone) }}">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $registration->name }}</td>
                                <td>{{ $registration->email }}</td>
                                <td>{{ $registration->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <span class="badge bg-{{ $registration->status_color }}">
                                        {{ $registration->status_label }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light rounded-circle" type="button" 
                                                data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <button type="button" class="dropdown-item" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#viewRegistrationModal"
                                                        data-registration-id="{{ $registration->id }}">
                                                    <i class="far fa-eye me-2"></i> Détails
                                                </button>
                                            </li>
                                            @if($registration->status === 'pending')
                                                <li>
                                                    <form action="{{ route('site.events.update-registration-status', [$event, $registration]) }}" 
                                                          method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="confirmed">
                                                        <button type="submit" class="dropdown-item text-success">
                                                            <i class="fas fa-check-circle me-2"></i> Confirmer
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                            <li>
                                                <form action="{{ route('site.events.destroy-registration', [$event, $registration]) }}" 
                                                      method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger" 
                                                            onclick="return confirm('Supprimer cette inscription ?')">
                                                        <i class="far fa-trash-alt me-2"></i> Supprimer
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3"></i>
                                        <p class="mb-0">Aucune inscription</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($registrations->hasPages())
                <div class="card-footer bg-white">
                    {{ $registrations->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Détails de l'inscription -->
<div class="modal fade" id="viewRegistrationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Détails de l'inscription</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="registrationDetails">
                <!-- Détails chargés en AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Recherche dans le tableau
document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchText = this.value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const text = row.getAttribute('data-search');
        if (text.includes(searchText)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

// Chargement des détails de l'inscription
const viewModal = document.getElementById('viewRegistrationModal');
viewModal.addEventListener('show.bs.modal', function(event) {
    const button = event.relatedTarget;
    const registrationId = button.getAttribute('data-registration-id');
    const modalBody = viewModal.querySelector('#registrationDetails');
    
    // Afficher un indicateur de chargement
    modalBody.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Chargement...</span>
            </div>
        </div>`;
    
    // Charger les détails via AJAX
    fetch(`/api/events/{{ $event->id }}/registrations/${registrationId}`)
        .then(response => response.json())
        .then(data => {
            // Formater et afficher les détails
            let html = `
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Nom :</strong> ${data.name}</p>
                        <p class="mb-1"><strong>Email :</strong> ${data.email}</p>
                        ${data.phone ? `<p class="mb-1"><strong>Téléphone :</strong> ${data.phone}</p>` : ''}
                        ${data.company ? `<p class="mb-1"><strong>Entreprise :</strong> ${data.company}</p>` : ''}
                        ${data.position ? `<p class="mb-1"><strong>Poste :</strong> ${data.position}</p>` : ''}
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Date d'inscription :</strong> ${new Date(data.created_at).toLocaleString()}</p>
                        <p class="mb-1"><strong>Statut :</strong> 
                            <span class="badge bg-${data.status === 'confirmed' ? 'success' : 'warning'}">
                                ${data.status === 'confirmed' ? 'Confirmée' : 'En attente'}
                            </span>
                        </p>
                    </div>
                </div>`;
                
            // Afficher les réponses personnalisées
            if (data.custom_answers && Object.keys(data.custom_answers).length > 0) {
                html += `<hr><h6 class="mb-3">Réponses personnalisées</h6><div class="row">`;
                
                for (const [question, answer] of Object.entries(data.custom_answers)) {
                    html += `
                        <div class="col-md-6 mb-2">
                            <p class="mb-0"><strong>${question}</strong></p>
                            <p class="text-muted">${Array.isArray(answer) ? answer.join(', ') : answer}</p>
                        </div>`;
                }
                
                html += `</div>`;
            }
            
            modalBody.innerHTML = html;
        })
        .catch(error => {
            console.error('Erreur:', error);
            modalBody.innerHTML = `
                <div class="alert alert-danger">
                    Une erreur est survenue lors du chargement des détails.
                </div>`;
        });
});
</script>
@endpush
@endsection
