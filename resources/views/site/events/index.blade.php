@extends('layouts.tourist')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gestion des Événements</h1>
        <a href="{{ route('site.events.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Nouvel événement
        </a>
    </div>

    <div class="card">
        <div class="card-header bg-light">
            <form action="{{ route('site.events.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">Tous les statuts</option>
                        @foreach(['draft' => 'Brouillon', 'published' => 'Publié', 'ongoing' => 'En cours', 'completed' => 'Terminé', 'cancelled' => 'Annulé'] as $value => $label)
                            <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" name="date" class="form-control" value="{{ request('date') }}" onchange="this.form.submit()">
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Rechercher..." value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('site.events.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-sync-alt me-1"></i> Réinitialiser
                    </a>
                </div>
            </form>
        </div>
        
        <div class="card-body p-0">
            @if($events->isEmpty())
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-calendar-alt fa-3x text-muted"></i>
                    </div>
                    <h5 class="text-muted">Aucun événement trouvé</h5>
                    <p class="text-muted">Commencez par créer votre premier événement</p>
                    <a href="{{ route('site.events.create') }}" class="btn btn-primary mt-2">
                        <i class="fas fa-plus me-1"></i> Créer un événement
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Événement</th>
                                <th>Date et lieu</th>
                                <th>Inscriptions</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($events as $event)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-3">
                                                <img src="{{ $event->featured_image_url }}" alt="{{ $event->title }}" class="rounded" style="width: 60px; height: 40px; object-fit: cover;">
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">{{ $event->title }}</h6>
                                                <small class="text-muted">
                                                    {{ $event->category ? $event->category->name : 'Non catégorisé' }}
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span><i class="far fa-calendar-alt me-2 text-primary"></i> {{ $event->formatted_date }}</span>
                                            @if($event->formatted_time)
                                                <small class="text-muted">
                                                    <i class="far fa-clock me-1"></i> {{ $event->formatted_time }}
                                                </small>
                                            @endif
                                            @if($event->location)
                                                <small class="text-muted">
                                                    <i class="fas fa-map-marker-alt me-1"></i> {{ Str::limit($event->location, 30) }}
                                                </small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($event->max_participants)
                                            <div class="progress" style="height: 6px;">
                                                @php
                                                    $percentage = min(100, ($event->registration_count / $event->max_participants) * 100);
                                                    $color = $percentage >= 90 ? 'bg-danger' : ($percentage >= 70 ? 'bg-warning' : 'bg-success');
                                                @endphp
                                                <div class="progress-bar {{ $color }}" role="progressbar" 
                                                     style="width: {{ $percentage }}%" 
                                                     aria-valuenow="{{ $percentage }}" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                </div>
                                            </div>
                                            <small class="text-muted">
                                                {{ $event->registration_count }} / {{ $event->max_participants }} places
                                            </small>
                                        @else
                                            <span class="badge bg-light text-dark">
                                                {{ $event->registration_count }} inscrits
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $statusClass = [
                                                'draft' => 'bg-secondary',
                                                'published' => 'bg-primary',
                                                'ongoing' => 'bg-info',
                                                'completed' => 'bg-success',
                                                'cancelled' => 'bg-danger',
                                                'postponed' => 'bg-warning',
                                            ][$event->status] ?? 'bg-secondary';
                                            
                                            $statusLabels = [
                                                'draft' => 'Brouillon',
                                                'published' => 'Publié',
                                                'ongoing' => 'En cours',
                                                'completed' => 'Terminé',
                                                'cancelled' => 'Annulé',
                                                'postponed' => 'Reporté',
                                            ];
                                        @endphp
                                        <span class="badge {{ $statusClass }}">
                                            {{ $statusLabels[$event->status] ?? $event->status }}
                                        </span>
                                        
                                        @if($event->is_featured)
                                            <span class="badge bg-warning text-dark mt-1 d-block">À la une</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" 
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                Actions
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('site.events.show', $event) }}">
                                                        <i class="far fa-eye me-2"></i>Voir
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('site.events.edit', $event) }}">
                                                        <i class="far fa-edit me-2"></i>Modifier
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('site.events.registrations', $event) }}">
                                                        <i class="fas fa-users me-2"></i>Inscriptions ({{ $event->registration_count }})
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                @if($event->status === 'draft')
                                                    <li>
                                                        <form action="{{ route('site.events.update-status', $event) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status" value="published">
                                                            <button type="submit" class="dropdown-item">
                                                                <i class="fas fa-check-circle me-2"></i>Publier
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                                @if(in_array($event->status, ['published', 'ongoing']))
                                                    <li>
                                                        <form action="{{ route('site.events.update-status', $event) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status" value="cancelled">
                                                            <button type="submit" class="dropdown-item text-danger" 
                                                                    onclick="return confirm('Êtes-vous sûr de vouloir annuler cet événement ?')">
                                                                <i class="fas fa-times-circle me-2"></i>Annuler
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('site.events.destroy', $event) }}" method="POST" 
                                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet événement ?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="far fa-trash-alt me-2"></i>Supprimer
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if($events->hasPages())
                    <div class="card-footer">
                        {{ $events->withQueryString()->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .progress {
    background-color: #e9ecef;
    border-radius: 0.25rem;
    height: 0.5rem;
    margin-bottom: 0.25rem;
}

.progress-bar {
    transition: width 0.6s ease;
}

.table th, .table td {
    vertical-align: middle;
}

.badge {
    font-weight: 500;
    padding: 0.35em 0.5em;
}

.dropdown-menu {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    border: none;
    border-radius: 0.5rem;
}

.dropdown-item {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
}

.dropdown-item i {
    width: 1.25rem;
    text-align: center;
    margin-right: 0.5rem;
}

.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    padding: 1rem 1.25rem;
}

.form-select, .form-control {
    border-radius: 0.375rem;
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}

.btn {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
    border-radius: 0.375rem;
}

.pagination {
    margin-bottom: 0;
}

.pagination .page-item .page-link {
    border: none;
    color: #6c757d;
    margin: 0 0.25rem;
    border-radius: 0.25rem;
}

.pagination .page-item.active .page-link {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.pagination .page-item.disabled .page-link {
    color: #dee2e6;
}
</style>
@endpush

@push('scripts')
<script>
    // Script pour gérer les interactions de la page
    document.addEventListener('DOMContentLoaded', function() {
        // Initialiser les tooltips Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Gestion des confirmations de suppression
        document.querySelectorAll('.delete-event').forEach(button => {
            button.addEventListener('click', function(e) {
                if (!confirm('Êtes-vous sûr de vouloir supprimer cet événement ? Cette action est irréversible.')) {
                    e.preventDefault();
                }
            });
        });
    });
</script>
@endpush
