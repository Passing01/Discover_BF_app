@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <nav aria-label="breadcrumb" class="mb-2">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('/') }}">Accueil</a></li>
      <li class="breadcrumb-item"><a href="{{ route('food.restaurants.index') }}">Restaurants</a></li>
      <li class="breadcrumb-item"><a href="{{ route('food.restaurants.show', $order->restaurant) }}">{{ $order->restaurant->name }}</a></li>
      <li class="breadcrumb-item active" aria-current="page">Commande #{{ $order->id }}</li>
    </ol>
  </nav>

  <div class="row g-3">
    <div class="col-lg-8">
      <div class="panel-cream rounded-20 p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h1 class="h4 mb-0">Votre commande</h1>
          <span class="badge bg-primary text-uppercase">{{ $order->status }}</span>
        </div>
        <ul class="list-unstyled mb-0">
          <li><strong>Plat:</strong> {{ $order->dish->name }}</li>
          <li><strong>Restaurant:</strong> {{ $order->restaurant->name }}</li>
          <li><strong>Quantité:</strong> {{ $order->quantity }}</li>
          <li><strong>Montant total:</strong> {{ number_format($order->total_price, 0, ',', ' ') }} CFA</li>
          <li><strong>Adresse de livraison:</strong> {{ $order->delivery_address }}</li>
          @if($order->delivery_time)
            <li><strong>Heure demandée:</strong> {{ $order->delivery_time->format('d/m/Y H:i') }}</li>
          @endif
          @if($order->notes)
            <li><strong>Notes:</strong> {{ $order->notes }}</li>
          @endif
        </ul>
        <div class="mt-3">
          <a href="{{ route('food.dishes.show', $order->dish) }}" class="btn btn-outline-secondary">Voir le plat</a>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card shadow-sm">
        <div class="card-body">
          <h6 class="card-title mb-2">Localisation du restaurant</h6>
          @if($order->restaurant->latitude && $order->restaurant->longitude)
            <div id="map-order" style="height:220px;border-radius:10px;overflow:hidden;"></div>
            <a href="https://maps.google.com/?q={{ $order->restaurant->latitude }},{{ $order->restaurant->longitude }}" target="_blank" class="btn btn-outline-primary btn-sm mt-2"><i class="bi bi-geo-alt"></i> Ouvrir dans Google Maps</a>
          @elseif($order->restaurant->map_url)
            <a href="{{ $order->restaurant->map_url }}" target="_blank" class="btn btn-outline-primary btn-sm"><i class="bi bi-geo-alt"></i> Ouvrir la carte</a>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
@endpush

@push('scripts')
  @if($order->restaurant->latitude && $order->restaurant->longitude)
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
      (function(){
        const rlat = {{ $order->restaurant->latitude ?? 'null' }};
        const rlng = {{ $order->restaurant->longitude ?? 'null' }};
        const dlat = {{ $order->delivery_lat ?? 'null' }};
        const dlng = {{ $order->delivery_lng ?? 'null' }};
        if (rlat && rlng) {
          const map = L.map('map-order').setView([rlat, rlng], 14);
          L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' }).addTo(map);
          const restMarker = L.marker([rlat, rlng]).addTo(map).bindPopup(@json($order->restaurant->name));
          let bounds = L.latLngBounds([rlat, rlng]);
          if (dlat && dlng) {
            const delMarker = L.marker([dlat, dlng], { opacity: 0.9 }).addTo(map).bindPopup('Adresse de livraison');
            bounds.extend([dlat, dlng]);
            map.fitBounds(bounds.pad(0.2));
            delMarker.openPopup();
          } else {
            restMarker.openPopup();
          }
        }
      })();
    </script>
  @endif
@endpush
