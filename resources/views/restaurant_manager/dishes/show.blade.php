@extends('layouts.restau')

@section('title', 'Détail du plat - ' . $dish->name)

@section('content')
<div class="container-fluid">
  <div class="row page-titles">
    <div class="col-md-5 col-8 align-self-center">
      <h3 class="text-themecolor">Détail du plat</h3>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('restaurant-manager.dashboard') }}">Tableau de bord</a></li>
        <li class="breadcrumb-item"><a href="{{ route('restaurant-manager.restaurants.index') }}">Mes restaurants</a></li>
        <li class="breadcrumb-item"><a href="{{ route('restaurant-manager.restaurants.dishes.index', $restaurant) }}">Plats - {{ $restaurant->name }}</a></li>
        <li class="breadcrumb-item active">{{ $dish->name }}</li>
      </ol>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-5">
      <div class="card">
        <div class="card-body">
          @if($dish->image_path)
            <img src="{{ asset('storage/' . $dish->image_path) }}" alt="{{ $dish->name }}" class="img-fluid rounded">
          @else
            <div class="text-muted">Aucune image</div>
          @endif
        </div>
      </div>
    </div>
    <div class="col-lg-7">
      <div class="card">
        <div class="card-body">
          <h3 class="mb-3">{{ $dish->name }}</h3>
          <p class="text-muted mb-2">Catégorie: <strong>{{ $dish->category }}</strong></p>
          <p class="mb-3">{{ $dish->description }}</p>
          <p class="h5">Prix: {{ number_format($dish->price, 2, ',', ' ') }} €</p>
          <p class="mb-3">Disponibilité: {!! $dish->is_available ? '<span class="badge bg-success">Disponible</span>' : '<span class="badge bg-secondary">Indisponible</span>' !!}</p>
          <a href="{{ route('restaurant-manager.restaurants.dishes.edit', [$restaurant, $dish]) }}" class="btn btn-primary"><i class="fa fa-edit"></i> Modifier</a>
          <a href="{{ route('restaurant-manager.restaurants.dishes.index', $restaurant) }}" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Retour</a>
        </div>
      </div>

      @if($dish->gallery && count($dish->gallery))
      <div class="card mt-3">
        <div class="card-body">
          <h5 class="mb-3">Galerie</h5>
          <div class="row g-3">
            @foreach($dish->gallery as $g)
              <div class="col-6 col-md-4">
                <img src="{{ asset('storage/' . $g) }}" class="img-fluid rounded" alt="{{ $dish->name }}">
              </div>
            @endforeach
          </div>
        </div>
      </div>
      @endif

    </div>
  </div>
</div>
@endsection
