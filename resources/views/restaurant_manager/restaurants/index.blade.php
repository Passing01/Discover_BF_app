@extends('layouts.restau')

@section('title', 'Tous les Restaurants')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <h1>Tous les Restaurants</h1>
            <div>
                <a href="{{ route('restaurant-manager.restaurants.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Ajouter un restaurant
                </a>
                <a href="{{ route('restaurant-manager.dashboard') }}" class="btn btn-outline-secondary ms-2">
                    <i class="fas fa-home me-2"></i>Tableau de bord
                </a>
            </div>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('restaurant-manager.dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item active" aria-current="page">Mes Restaurants</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <div class="card-body">
            @if($restaurants->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Ville</th>
                                <th>Prix moyen</th>
                                <th>Note</th>
                                <th>Type</th>
                                <th>Plats</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($restaurants as $restaurant)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($restaurant->cover_image)
                                            <img src="{{ Storage::url($restaurant->cover_image) }}" alt="{{ $restaurant->name }}" class="rounded me-3" width="50" height="50" style="object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                                <i class="fas fa-utensils text-muted"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <h6 class="mb-0">{{ $restaurant->name }}</h6>
                                            <small class="text-muted">{{ $restaurant->address }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $restaurant->city }}</td>
                                <td>{{ number_format($restaurant->avg_price, 2, ',', ' ') }} FCFA</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= $restaurant->rating ? 'text-warning' : 'text-muted' }}"></i>
                                            @endfor
                                        </div>
                                        <span class="text-muted">({{ $restaurant->rating ?? '0.0' }})</span>
                                    </div>
                                </td>
                                <td>
                                    @if($restaurant->owner_id === Auth::id())
                                        <span class="badge bg-primary">
                                            <i class="fas fa-user-shield me-1"></i> Votre restaurant
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-store me-1"></i> Partenaire
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ $restaurant->dishes_count ?? $restaurant->dishes->count() }} plats
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('restaurant-manager.restaurants.dishes.index', $restaurant) }}" class="btn btn-sm btn-info text-white" title="Gérer les plats">
                                            <i class="fas fa-utensils"></i>
                                        </a>
                                        <a href="{{ route('restaurant-manager.restaurants.edit', $restaurant) }}" class="btn btn-sm btn-outline-primary" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $restaurant->id }}" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteModal{{ $restaurant->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Confirmer la suppression</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Êtes-vous sûr de vouloir supprimer le restaurant <strong>{{ $restaurant->name }}</strong> ?
                                                    Cette action est irréversible.
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <form action="{{ route('restaurant-manager.restaurants.destroy', $restaurant) }}" method="POST">
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
                    {{ $restaurants->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-utensils fa-4x text-muted"></i>
                    </div>
                    <h4 class="mb-3">Aucun restaurant enregistré</h4>
                    <p class="text-muted mb-4">Commencez par ajouter votre premier restaurant pour gérer votre établissement.</p>
                    <a href="{{ route('restaurant-manager.restaurants.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Ajouter un restaurant
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
