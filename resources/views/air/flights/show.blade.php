@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <div class="row g-4">
    <div class="col-12 col-lg-7 mb-4 mb-lg-0">
      @php($photo = optional($flight->destination)->photo_url)
      @if($photo)
        <img src="{{ $photo }}" class="img-fluid w-100 rounded-4 shadow-sm mb-3" alt="{{ $flight->destination->city }}">
      @endif
      <div class="card shadow-sm rounded-4 mb-3">
        <div class="card-body">
          <h2 class="h4 mb-3 fw-bold text-primary">
            <i class="bi bi-airplane me-1"></i>{{ $flight->origin->city }} → {{ $flight->destination->city }}
          </h2>
          <div class="mb-3">
            <span class="badge bg-primary-subtle text-primary me-2">{{ $flight->airline ?? 'Compagnie inconnue' }}</span>
            @if($flight->flight_number)
              <span class="badge bg-info-subtle text-info me-2">Vol {{ $flight->flight_number }}</span>
            @endif
            @if(optional($flight->origin)->iata_code)
              <span class="badge bg-secondary-subtle text-secondary me-2">Départ: {{ $flight->origin->iata_code }}</span>
            @endif
            @if(optional($flight->destination)->iata_code)
              <span class="badge bg-secondary-subtle text-secondary me-2">Arrivée: {{ $flight->destination->iata_code }}</span>
            @endif
            <span class="badge bg-light text-dark me-2">Places: {{ $flight->seats_available }} / {{ $flight->seats_total }}</span>
          </div>
          <div class="mb-3">
            <div class="fw-semibold">Départ : {{ \Illuminate\Support\Carbon::parse($flight->departure_time)->format('d M Y, H:i') }} - {{ $flight->origin->city }} @if(optional($flight->origin)->iata_code) ({{ $flight->origin->iata_code }}) @endif</div>
            <div class="fw-semibold">Arrivée : {{ \Illuminate\Support\Carbon::parse($flight->arrival_time)->format('d M Y, H:i') }} - {{ $flight->destination->city }} @if(optional($flight->destination)->iata_code) ({{ $flight->destination->iata_code }}) @endif</div>
          </div>
          <div class="d-flex justify-content-between align-items-center">
            <div class="h5 mb-0">À partir de <span class="text-primary">{{ number_format($flight->base_price, 0, ',', ' ') }} XOF</span></div>
            <a class="btn btn-primary" href="{{ route('air.flights.book', $flight) }}">Réserver</a>
          </div>
        </div>
      </div>

      @if($flight->origin->latitude && $flight->origin->longitude && $flight->destination->latitude && $flight->destination->longitude)
        <div id="map" style="height: 320px;" class="mt-3 rounded-20 overflow-hidden border"></div>
      @endif
    </div>
    <div class="col-12 col-lg-5">
      <div class="card shadow-sm rounded-4 position-sticky" style="top: 100px;">
        <div class="card-body">
          <h5 class="mb-2 fw-bold">Réservation rapide</h5>
          <span class="badge bg-success-subtle text-success mb-2">{{ $flight->seats_available }} places</span>
          <div class="small text-muted mb-3">Meilleur tarif à partir de {{ number_format($flight->base_price, 0, ',', ' ') }} XOF</div>
          <a class="btn btn-primary w-100 mb-2" href="{{ route('air.flights.book', $flight) }}">Réserver ce vol</a>
          <div class="small">En réservant, vous acceptez nos <a href="#" class="text-decoration-none">conditions</a>.</div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/flight-show.css') }}">
  <link rel="stylesheet" href="{{ asset('css/flight-show-touch.css') }}">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
@endpush

@section('quick_actions')
  <a href="{{ route('air.flights.book', $flight) }}" class="btn btn-orange btn-sm"><i class="bi bi-bag-plus me-1"></i>Réserver ce vol</a>
  <a href="{{ route('air.flights.index') }}" class="btn btn-cream btn-sm"><i class="bi bi-search me-1"></i>Voir d'autres vols</a>
@endsection

@push('scripts')
  @if($flight->origin->latitude && $flight->origin->longitude && $flight->destination->latitude && $flight->destination->longitude)
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
      document.addEventListener('DOMContentLoaded', function(){
        var map = L.map('map');
        var origin = [{{ $flight->origin->latitude }}, {{ $flight->origin->longitude }}];
        var dest = [{{ $flight->destination->latitude }}, {{ $flight->destination->longitude }}];
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 18 }).addTo(map);
        var o = L.marker(origin).addTo(map).bindPopup("Origine: {{ $flight->origin->city }}");
        var d = L.marker(dest).addTo(map).bindPopup("Destination: {{ $flight->destination->city }}");
        L.polyline([origin, dest], {color: 'blue', weight: 3, opacity: 0.7}).addTo(map);
        var group = L.featureGroup([o, d]).addTo(map);
        map.fitBounds(group.getBounds().pad(0.3));
      });
    </script>
  @endif
@endpush
