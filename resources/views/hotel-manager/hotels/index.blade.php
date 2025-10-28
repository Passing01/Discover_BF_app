@extends('layouts.hotel')

@section('title', 'Tous les Hôtels')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <h1>Tous les Hôtels</h1>
            <div>
                <a href="{{ route('hotel-manager.hotels.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Ajouter un hôtel
                </a>
                <a href="{{ route('hotel-manager.dashboard') }}" class="btn btn-outline-secondary ms-2">
                    <i class="fas fa-home me-2"></i>Tableau de bord
                </a>
            </div>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('hotel-manager.dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item active" aria-current="page">Mes Hôtels</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <div class="card-body">
            @if($hotels->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Ville</th>
                                <th>Pays</th>
                                <th>Étoiles</th>
                                <th>Statut</th>
                                <th>Chambres</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($hotels as $hotel)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($hotel->photos->isNotEmpty())
                                            @php($thumb = $hotel->photos->first()->path)
                                            <img src="{{ \Illuminate\Support\Str::startsWith($thumb, ['http://','https://','/']) ? $thumb : Storage::url($thumb) }}" alt="{{ $hotel->name }}" class="rounded me-3" width="50" height="50" style="object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                                <i class="fas fa-hotel text-muted"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <h6 class="mb-0">{{ $hotel->name }}</h6>
                                            <small class="text-muted">{{ $hotel->address }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $hotel->city }}</td>
                                <td>{{ $hotel->country }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= $hotel->stars ? 'text-warning' : 'text-muted' }}"></i>
                                            @endfor
                                        </div>
                                        <span class="text-muted">({{ $hotel->stars ?? '0' }})</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $hotel->is_active ? 'success' : 'danger' }}">
                                        {{ $hotel->is_active ? 'Actif' : 'Inactif' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ $hotel->rooms_count ?? $hotel->rooms->count() }} chambres
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('hotel-manager.hotels.rooms.index', $hotel) }}" class="btn btn-sm btn-info text-white" title="Gérer les chambres">
                                            <i class="fas fa-door-open"></i>
                                        </a>
                                        <a href="{{ route('hotel-manager.hotels.show', $hotel) }}" class="btn btn-sm btn-outline-primary" title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('hotel-manager.hotels.edit', $hotel) }}" class="btn btn-sm btn-outline-warning" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $hotel->id }}" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteModal{{ $hotel->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Confirmer la suppression</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Êtes-vous sûr de vouloir supprimer l'hôtel <strong>{{ $hotel->name }}</strong> ?
                                                    Cette action est irréversible.
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <form action="{{ route('hotel-manager.hotels.destroy', $hotel) }}" method="POST">
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
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $hotels->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-hotel fa-4x text-muted"></i>
                    </div>
                    <h4 class="mb-3">Aucun hôtel enregistré</h4>
                    <p class="text-muted mb-4">Commencez par ajouter votre premier hôtel pour gérer votre établissement.</p>
                    <a href="{{ route('hotel-manager.hotels.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Ajouter un hôtel
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table th {
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #5a5c69;
        border-top: none;
    }
    
    .table td {
        vertical-align: middle;
    }
    
    .btn-group .btn {
        border-radius: 0.25rem !important;
    }
    
    .btn-group .btn:first-child {
        border-top-right-radius: 0 !important;
        border-bottom-right-radius: 0 !important;
    }
    
    .btn-group .btn:last-child {
        border-top-left-radius: 0 !important;
        border-bottom-left-radius: 0 !important;
    }
    
    .btn-group .btn:not(:first-child) {
        margin-left: -1px;
    }
    
    .page-item.active .page-link {
        background-color: #4e73df;
        border-color: #4e73df;
    }
    
    .page-link {
        color: #4e73df;
    }
</style>
@endpush