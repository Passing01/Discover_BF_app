@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <nav aria-label="breadcrumb" class="mb-2">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('tourist.dashboard') }}">Tableau de bord</a></li>
      <li class="breadcrumb-item"><a href="{{ route('transport.taxi.index') }}">Taxis</a></li>
      <li class="breadcrumb-item active" aria-current="page">Détails de la course</li>
    </ol>
  </nav>

  <h1 class="h4 mb-3">Course #{{ $ride->id }}</h1>
  <div class="panel-cream rounded-20 p-3">
    <div class="row g-3">
      <div class="col-md-3">
        <div class="text-muted small">Date</div>
        <div>{{ $ride->ride_date }}</div>
      </div>
      <div class="col-md-3">
        <div class="text-muted small">Départ</div>
        <div>{{ $ride->pickup_location }}</div>
      </div>
      <div class="col-md-3">
        <div class="text-muted small">Destination</div>
        <div>{{ $ride->dropoff_location }}</div>
      </div>
      <div class="col-md-3 text-md-end">
        <div class="text-muted small">Total</div>
        <div class="fw-bold">{{ number_format($ride->price, 0, ',', ' ') }} FCFA</div>
      </div>
    </div>
    <hr>
    <div class="row g-3 small">
      <div class="col-md-3"><span class="text-muted">Distance:</span> {{ number_format($ride->distance_km, 1) }} km</div>
      <div class="col-md-3"><span class="text-muted">Taxi:</span> {{ optional($ride->taxi)->model }} • {{ optional($ride->taxi)->color }}</div>
      <div class="col-md-3"><span class="text-muted">Immat:</span> {{ optional($ride->taxi)->license_plate }}</div>
      <div class="col-md-3"><span class="badge badge-status {{ $ride->status }}">{{ ucfirst(str_replace('_', ' ', $ride->status)) }}</span></div>
    </div>
  </div>

  <div class="mt-3 d-flex gap-2">
    <a href="{{ route('transport.taxi.index') }}" class="btn btn-light border">Retour</a>
  </div>
</div>
@endsection
