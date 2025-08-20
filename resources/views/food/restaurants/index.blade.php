@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <nav aria-label="breadcrumb" class="mb-2">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('/') }}">Accueil</a></li>
      <li class="breadcrumb-item active" aria-current="page">Restaurants</li>
    </ol>
  </nav>

  <div class="d-flex align-items-end flex-wrap gap-2 mb-3">
    <div>
      <h1 class="mb-0">Restaurants</h1>
      <div class="text-muted">Découvrez et réservez une table.</div>
    </div>
    <div class="ms-auto d-flex align-items-center gap-2">
      <span class="small text-muted">{{ $restaurants->total() ?? $restaurants->count() }} résultat(s)</span>
    </div>
  </div>

  <x-ad-banner placement="restaurants_top" />

  <div class="row g-3 mt-1">
    @forelse($restaurants as $r)
      <div class="col-md-4">
        <div class="panel-cream rounded-20 h-100 overflow-hidden d-flex flex-column">
          @if(!empty($r->cover_image))
            <img src="{{ \Illuminate\Support\Str::startsWith($r->cover_image, ['http://','https://','/']) ? $r->cover_image : asset('storage/'.$r->cover_image) }}" class="w-100" style="height:160px; object-fit:cover;" alt="{{ $r->name }}">
          @endif
          <div class="p-3 d-flex flex-column h-100">
            <div class="fw-semibold mb-1">{{ $r->name }}</div>
            <div class="text-muted small mb-1"><i class="bi bi-geo-alt me-1"></i>{{ $r->address }}, {{ $r->city }}</div>
            <div class="text-muted small mb-2"><i class="bi bi-star-fill text-warning me-1"></i>{{ number_format($r->rating ?? 0, 1) }}</div>
            <p class="mb-2">{{ \Illuminate\Support\Str::limit($r->description, 120) }}</p>
            <div class="mt-auto d-flex gap-2">
              <a class="btn btn-outline-primary btn-sm" href="{{ route('food.restaurants.show', $r) }}">Voir</a>
              <a class="btn btn-primary btn-sm" href="{{ route('food.restaurants.reserve', $r) }}">Réserver</a>
            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12"><div class="alert alert-info">Aucun restaurant.</div></div>
    @endforelse
  </div>

  <div class="d-flex justify-content-center mt-3">{!! $restaurants->onEachSide(1)->links() !!}</div>
</div>
@endsection
