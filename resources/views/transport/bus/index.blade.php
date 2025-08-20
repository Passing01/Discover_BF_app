@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <nav aria-label="breadcrumb" class="mb-2">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('tourist.dashboard') }}">Tableau de bord</a></li>
      <li class="breadcrumb-item active" aria-current="page">Trajets en bus</li>
    </ol>
  </nav>

  <div class="d-flex align-items-center justify-content-between mb-2">
    <h1 class="h4 mb-0">Trajets en bus</h1>
    <div class="text-muted small">Sélectionnez un trajet pour réserver</div>
  </div>

  <div class="row g-3">
    @forelse($trips as $trip)
      <div class="col-md-4">
        <div class="panel-cream rounded-20 h-100 p-3 d-flex flex-column">
          <div class="flex-grow-1">
            <div class="fw-semibold mb-1">{{ $trip->origin }} → {{ $trip->destination }}</div>
            <div class="small mb-1"><span class="text-muted">Départ:</span> {{ $trip->departure_time }}</div>
            <div class="small mb-1"><span class="text-muted">Prix:</span> {{ number_format($trip->price, 0, ',', ' ') }} FCFA</div>
            <div class="small text-muted">Places: {{ $trip->seats_available }} / {{ $trip->seats_total }}</div>
          </div>
          <div class="pt-2 d-flex gap-2">
            <a class="btn btn-light border flex-fill" href="{{ route('transport.bus.show', $trip) }}">Détails</a>
            <a class="btn btn-orange flex-fill" href="{{ route('transport.bus.book', $trip) }}">Réserver</a>
          </div>
        </div>
      </div>
    @empty
      <p class="text-muted">Aucun trajet disponible pour le moment.</p>
    @endforelse
  </div>
  <div class="mt-3">{{ $trips->links() }}</div>
</div>
@endsection
