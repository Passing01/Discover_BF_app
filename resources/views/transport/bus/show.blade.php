@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <nav aria-label="breadcrumb" class="mb-2">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('tourist.dashboard') }}">Tableau de bord</a></li>
      <li class="breadcrumb-item"><a href="{{ route('transport.bus.index') }}">Bus</a></li>
      <li class="breadcrumb-item active" aria-current="page">Trajet</li>
    </ol>
  </nav>

  <h1 class="h4 mb-3">{{ $trip->origin }} → {{ $trip->destination }}</h1>
  <div class="panel-cream rounded-20 p-3">
    <div class="row g-3">
      <div class="col-md-3">
        <div class="text-muted small">Départ</div>
        <div>{{ $trip->departure_time }}</div>
      </div>
      <div class="col-md-3">
        <div class="text-muted small">Arrivée</div>
        <div>{{ $trip->arrival_time ?? '—' }}</div>
      </div>
      <div class="col-md-3">
        <div class="text-muted small">Places</div>
        <div>{{ $trip->seats_available }} / {{ $trip->seats_total }}</div>
      </div>
      <div class="col-md-3 text-md-end">
        <div class="text-muted small">Prix</div>
        <div class="fw-bold">{{ number_format($trip->price, 0, ',', ' ') }} FCFA</div>
      </div>
    </div>
  </div>

  <div class="mt-3 d-flex gap-2">
    <a href="{{ route('transport.bus.index') }}" class="btn btn-light border">Retour</a>
    <a href="{{ route('transport.bus.book', $trip) }}" class="btn btn-orange">Réserver</a>
  </div>
</div>
@endsection
