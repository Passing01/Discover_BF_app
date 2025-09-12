@extends('layouts.site-manager')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Mes sites touristiques</h1>
        <p class="mb-0">Gérez vos sites touristiques et leurs réservations</p>
    </div>
    <a href="{{ route('site-manager.sites.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Ajouter un site
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card">
    <div class="card-body p-0">
        @if($sites->isEmpty())
            <div class="text-center p-5">
                <div class="mb-3">
                    <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                </div>
                <h5 class="mb-2">Aucun site enregistré</h5>
                <p class="text-muted mb-4">Commencez par ajouter votre premier site touristique</p>
                <a href="{{ route('site-manager.sites.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Ajouter un site
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nom</th>
                            <th>Ville</th>
                            <th>Catégorie</th>
                            <th class="text-center">Statut</th>
                            <th class="text-center">Réservations</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sites as $site)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($site->photo_url)
                                            <img src="{{ Storage::url($site->photo_url) }}" alt="{{ $site->name }}" class="site-photo me-3">
                                        @else
                                            <div class="bg-light d-flex align-items-center justify-content-center rounded me-3" style="width: 50px; height: 50px;">
                                                <i class="bi bi-image text-muted"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <h6 class="mb-0">{{ $site->name }}</h6>
                                            <small class="text-muted">Ajouté le {{ $site->created_at->format('d/m/Y') }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $site->city }}</td>
                                <td>{{ $site->category }}</td>
                                <td class="text-center">
                                    <form action="{{ route('site-manager.sites.toggle-status', $site) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm {{ $site->is_active ? 'btn-success' : 'btn-secondary' }} btn-sm">
                                            {{ $site->is_active ? 'Actif' : 'Inactif' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary">{{ $site->bookings_count }}</span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <a href="{{ route('site-manager.sites.show', $site) }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Voir les détails">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('site-manager.sites.edit', $site) }}" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteSiteModal{{ $site->id }}" title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                    
                                    <!-- Modal de confirmation de suppression -->
                                    <div class="modal fade" id="deleteSiteModal{{ $site->id }}" tabindex="-1" aria-labelledby="deleteSiteModalLabel{{ $site->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteSiteModalLabel{{ $site->id }}">Confirmer la suppression</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Êtes-vous sûr de vouloir supprimer le site "{{ $site->name }}" ? Cette action est irréversible.
                                                    @if($site->bookings_count > 0)
                                                        <div class="alert alert-warning mt-2">
                                                            <i class="bi bi-exclamation-triangle me-1"></i>
                                                            Ce site a {{ $site->bookings_count }} réservation(s) associée(s). La suppression du site supprimera également toutes les réservations associées.
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <form action="{{ route('site-manager.sites.destroy', $site) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Supprimer</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($sites->hasPages())
                <div class="card-footer">
                    {{ $sites->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialisation des tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush
