@extends('layouts.restau')

@section('title', 'Gestion des plats - ' . $restaurant->name)

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-md-5 col-8 align-self-center">
            <h3 class="text-themecolor">Gestion des plats</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('restaurant-manager.dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item"><a href="{{ route('restaurant-manager.restaurants.index') }}">Mes restaurants</a></li>
                <li class="breadcrumb-item active">Plats - {{ $restaurant->name }}</li>
            </ol>
        </div>
        <div class="col-md-7 col-4 align-self-center">
            <a href="{{ route('restaurant-manager.restaurants.dishes.create', $restaurant) }}" class="btn btn-success pull-right">
                <i class="fa fa-plus"></i> Ajouter un plat
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Nom</th>
                                    <th>Catégorie</th>
                                    <th>Prix</th>
                                    <th>Disponible</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dishes as $dish)
                                    <tr>
                                        <td>
                                            @if($dish->image_path)
                                                <img src="{{ asset('storage/' . $dish->image_path) }}" alt="{{ $dish->name }}" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <span class="text-muted">Aucune image</span>
                                            @endif
                                        </td>
                                        <td>{{ $dish->name }}</td>
                                        <td>{{ $dish->category }}</td>
                                        <td>{{ number_format($dish->price, 2, ',', ' ') }} €</td>
                                        <td>
                                            @if($dish->is_available)
                                                <span class="badge badge-success">Disponible</span>
                                            @else
                                                <span class="badge badge-danger">Indisponible</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('restaurant-manager.restaurants.dishes.edit', [$restaurant, $dish]) }}" class="btn btn-sm btn-primary">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form action="{{ route('restaurant-manager.restaurants.dishes.destroy', [$restaurant, $dish]) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce plat ?')">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Aucun plat trouvé.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        
                        <div class="d-flex justify-content-center">
                            {{ $dishes->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
