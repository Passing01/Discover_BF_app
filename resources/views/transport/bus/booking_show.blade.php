@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <nav aria-label="breadcrumb" class="mb-2">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('tourist.dashboard') }}">Tableau de bord</a></li>
      <li class="breadcrumb-item"><a href="{{ route('transport.bus.index') }}">Bus</a></li>
      <li class="breadcrumb-item active" aria-current="page">Réservation</li>
    </ol>
  </nav>

  <h1 class="h4 mb-3">Réservation #{{ $booking->id }}</h1>
  <div class="panel-cream rounded-20 p-3">
    <div class="row g-3">
      <div class="col-md-3">
        <div class="text-muted small">Trajet</div>
        <div>{{ $booking->trip->origin }} → {{ $booking->trip->destination }}</div>
      </div>
      <div class="col-md-3">
        <div class="text-muted small">Départ</div>
        <div>{{ $booking->trip->departure_time }}</div>
      </div>
      <div class="col-md-3">
        <div class="text-muted small">Places</div>
        <div>{{ $booking->seats }}</div>
      </div>
      <div class="col-md-3 text-md-end">
        <div class="text-muted small">Total</div>
        <div class="fw-bold">{{ number_format($booking->total_price, 0, ',', ' ') }} FCFA</div>
      </div>
    </div>
    <hr>
    <div class="small"><span class="text-muted">Statut:</span> <span class="badge badge-status {{ $booking->status }}">{{ ucfirst($booking->status) }}</span></div>
  </div>

  <div class="mt-3 d-flex gap-2">
    <a href="{{ route('transport.bus.index') }}" class="btn btn-light border">Retour</a>
  </div>
</div>
@endsection
