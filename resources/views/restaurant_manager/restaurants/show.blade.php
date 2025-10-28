@extends('layouts.restau')

@php
    // Normalize cover image to a string path if it's accidentally stored as an array
    $coverImageValue = $restaurant->cover_image;
    if (is_array($coverImageValue)) {
        $coverImageValue = $coverImageValue['path'] ?? ($coverImageValue[0] ?? null);
    }
@endphp

@section('title', $restaurant->name)

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <h1>{{ $restaurant->name }}</h1>
            <div>
                <a href="{{ route('restaurant-manager.restaurants.edit', $restaurant) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-2"></i>Modifier
                </a>
                <a href="{{ route('restaurant-manager.restaurants.index') }}" class="btn btn-outline-secondary ms-2">
                    <i class="fas fa-arrow-left me-2"></i>Retour
                </a>
            </div>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('restaurant-manager.dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item"><a href="{{ route('restaurant-manager.restaurants.index') }}">Mes Restaurants</a></li>
                <li class="breadcrumb-item active" aria-current="page">Détails</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Informations</h5>
                    <dl class="row mb-0">
                        <dt class="col-sm-3">Adresse</dt>
                        <dd class="col-sm-9">{{ $restaurant->address }}, {{ $restaurant->city }}</dd>
                        <dt class="col-sm-3">Téléphone</dt>
                        <dd class="col-sm-9">{{ $restaurant->phone }}</dd>
                        <dt class="col-sm-3">Email</dt>
                        <dd class="col-sm-9">{{ $restaurant->email }}</dd>
                        <dt class="col-sm-3">Prix moyen</dt>
                        <dd class="col-sm-9">{{ number_format($restaurant->avg_price, 2, ',', ' ') }} FCFA</dd>
                        <dt class="col-sm-3">Statut</dt>
                        <dd class="col-sm-9">{!! $restaurant->is_active ? '<span class="badge bg-success">Actif</span>' : '<span class="badge bg-secondary">Inactif</span>' !!}</dd>
                    </dl>
                    <div class="mt-3">
                        <h6>Description</h6>
                        <p class="mb-0">{{ $restaurant->description }}</p>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Galerie</h5>
                    @if(!empty($restaurant->gallery))
                        <div class="row g-2">
                            @foreach($restaurant->gallery as $image)
                                @php
                                    $imagePath = is_array($image) ? ($image['path'] ?? ($image[0] ?? null)) : $image;
                                @endphp
                                <div class="col-6 col-md-4">
                                    @if(is_string($imagePath) && $imagePath !== '')
                                        <img src="{{ Storage::url($imagePath) }}" class="img-fluid rounded border" alt="Image">
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-muted">Aucune image dans la galerie.</div>
                    @endif
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Vidéos</h5>
                    @if(!empty($restaurant->video_urls))
                        <ul class="mb-0">
                            @foreach($restaurant->video_urls as $url)
                                <li><a href="{{ $url }}" target="_blank" rel="noopener">{{ $url }}</a></li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-muted">Aucune vidéo.</div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    @php($finalCoverUrl = $restaurant->cover_image_url ?? (is_string($coverImageValue) ? Storage::url($coverImageValue) : null))
                    @if($finalCoverUrl)
                        <img src="{{ $finalCoverUrl }}" class="img-fluid rounded" alt="Couverture">
                    @else
                        <div class="bg-light p-5 rounded d-flex align-items-center justify-content-center" style="height: 200px;">
                            <div class="text-center">
                                <i class="fas fa-image fa-3x text-muted mb-2"></i>
                                <p class="mb-0 text-muted">Aucune image</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            @if($restaurant->map_url)
                <div class="card">
                    <div class="card-body">
                        <h6 class="mb-2">Localisation</h6>
                        <a href="{{ $restaurant->map_url }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary">
                            Ouvrir dans Google Maps
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection


