@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <nav aria-label="breadcrumb" class="mb-2">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('/') }}">Accueil</a></li>
      <li class="breadcrumb-item"><a href="{{ route('food.restaurants.index') }}">Restaurants</a></li>
      <li class="breadcrumb-item"><a href="{{ route('food.restaurants.show', $dish->restaurant) }}">{{ $dish->restaurant->name }}</a></li>
      <li class="breadcrumb-item"><a href="{{ route('food.dishes.show', $dish) }}">{{ $dish->name }}</a></li>
      <li class="breadcrumb-item active" aria-current="page">Commander en livraison</li>
    </ol>
  </nav>

  <div class="row g-3">
    <div class="col-lg-7">
      <div class="panel-cream rounded-20 p-3">
        <h1 class="h4 mb-3">Commander: {{ $dish->name }}</h1>
        <form method="post" action="{{ route('food.dishes.orders.store', $dish) }}">
          @csrf

          <div class="row g-3">
            <div class="col-sm-4">
              <label class="form-label">Quantité</label>
              <input type="number" class="form-control @error('quantity') is-invalid @enderror" name="quantity" min="1" max="50" value="{{ old('quantity', 1) }}" required>
              @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-sm-8">
              <label class="form-label">Heure de livraison (optionnel)</label>
              <input type="datetime-local" class="form-control @error('delivery_time') is-invalid @enderror" name="delivery_time" value="{{ old('delivery_time') }}">
              @error('delivery_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
              <label class="form-label">Adresse de livraison</label>
              <textarea id="deliveryAddress" class="form-control @error('delivery_address') is-invalid @enderror" name="delivery_address" rows="3" placeholder="Saisissez l'adresse puis ajustez sur la carte" required>{{ old('delivery_address') }}</textarea>
              @error('delivery_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
              <div class="form-text">Astuce: commencez à taper l'adresse; la carte se centre automatiquement. Vous pouvez aussi cliquer sur la carte ou déplacer le marqueur.</div>
            </div>
            <div class="col-12">
              <label class="form-label">Notes au livreur (optionnel)</label>
              <textarea class="form-control @error('notes') is-invalid @enderror" name="notes" rows="2" placeholder="Code porte, repère, etc.">{{ old('notes') }}</textarea>
              @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          <input type="hidden" name="delivery_lat" id="deliveryLat" value="{{ old('delivery_lat') }}">
          <input type="hidden" name="delivery_lng" id="deliveryLng" value="{{ old('delivery_lng') }}">

          <div class="d-flex align-items-center mt-3 gap-3 flex-wrap">
            <div class="fw-semibold">Total: <span id="totalPrice">{{ number_format($dish->price, 0, ',', ' ') }} CFA</span></div>
            <button type="button" id="useMyLocation" class="btn btn-outline-secondary">
              Utiliser ma position
            </button>
            <button type="submit" class="btn btn-orange ms-auto">Valider la commande</button>
          </div>
        </form>
      </div>
    </div>
    <div class="col-lg-5">
      <div class="border rounded-3 p-3 h-100">
        <div class="d-flex align-items-start gap-3">
          @php $thumb = $dish->image_path ?? ($dish->gallery[0] ?? null); @endphp
          @if($thumb)
            <img src="{{ \Illuminate\Support\Str::startsWith($thumb, ['http://','https://','/']) ? $thumb : asset('storage/'.$thumb) }}" alt="{{ $dish->name }}" style="width:80px;height:80px;object-fit:cover;border-radius:8px;">
          @endif
          <div class="flex-grow-1">
            <div class="fw-semibold">{{ $dish->name }}</div>
            <div class="small text-muted">{{ $dish->restaurant->name }}</div>
            <div class="mt-1">{{ number_format($dish->price, 0, ',', ' ') }} CFA</div>
          </div>
        </div>
        <div class="mt-3">
          <h6 class="mb-2">Carte de livraison</h6>
          <div id="map-dish-order" style="height:320px;border-radius:10px;overflow:hidden;"></div>
          @if($dish->restaurant->latitude && $dish->restaurant->longitude)
            <a href="https://maps.google.com/?q={{ $dish->restaurant->latitude }},{{ $dish->restaurant->longitude }}" target="_blank" class="btn btn-outline-primary btn-sm mt-2"><i class="bi bi-geo-alt"></i> Voir le restaurant sur Google Maps</a>
          @elseif($dish->restaurant->map_url)
            <a href="{{ $dish->restaurant->map_url }}" target="_blank" class="btn btn-outline-primary btn-sm mt-2"><i class="bi bi-geo-alt"></i> Ouvrir la carte du restaurant</a>
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
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
  <script>
    (function(){
      // Total dynamique
      const price = {{ (float)$dish->price }};
      const qtyEl = document.querySelector('input[name="quantity"]');
      const totalEl = document.getElementById('totalPrice');
      function refresh(){
        const q = Math.max(1, Math.min(50, parseInt(qtyEl.value || '1', 10)));
        const total = Math.round(price * q);
        totalEl.textContent = new Intl.NumberFormat('fr-FR').format(total) + ' CFA';
      }
      qtyEl.addEventListener('input', refresh);
      refresh();

      // Carte et géocodage
      const addrEl = document.getElementById('deliveryAddress');
      const restLat = {{ $dish->restaurant->latitude ?? 'null' }};
      const restLng = {{ $dish->restaurant->longitude ?? 'null' }};
      const hasRest = !!(restLat && restLng);
      const map = L.map('map-dish-order').setView(hasRest ? [restLat, restLng] : [12.3686, -1.5275], hasRest ? 14 : 13);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' }).addTo(map);

      let deliveryMarker = L.marker(map.getCenter(), { draggable: true }).addTo(map);
      const latInput = document.getElementById('deliveryLat');
      const lngInput = document.getElementById('deliveryLng');
      let restaurantMarker = null;
      if (hasRest) {
        restaurantMarker = L.marker([restLat, restLng], { draggable: false, opacity: 0.9 }).addTo(map).bindPopup({!! json_encode($dish->restaurant->name) !!});
      }

      // Debounce helper
      function debounce(fn, ms){ let t; return function(){ clearTimeout(t); t = setTimeout(()=>fn.apply(this, arguments), ms); }; }

      // Forward geocoding: address -> lat/lng
      function setDelivery(lat, lon, center = true) {
        deliveryMarker.setLatLng([lat, lon]);
        latInput.value = lat.toFixed(7);
        lngInput.value = lon.toFixed(7);
        if (center) map.setView([lat, lon], 15);
      }

      const geocode = debounce(async function(query){
        if (!query || query.trim().length < 4) return; // éviter les requêtes trop courtes
        try {
          const url = 'https://nominatim.openstreetmap.org/search?format=json&addressdetails=1&limit=1&q=' + encodeURIComponent(query + ', Burkina Faso');
          const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
          const data = await res.json();
          if (Array.isArray(data) && data.length) {
            const lat = parseFloat(data[0].lat); const lon = parseFloat(data[0].lon);
            setDelivery(lat, lon, true);
          }
        } catch(e) { /* ignore network errors */ }
      }, 500);

      // Reverse geocoding: lat/lng -> address
      async function reverseGeocode(latlng){
        try {
          const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${latlng.lat}&lon=${latlng.lng}`;
          const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
          const data = await res.json();
          if (data && data.display_name) {
            addrEl.value = data.display_name;
          }
        } catch(e) { /* ignore */ }
      }

      // Listeners
      addrEl.addEventListener('input', function(){ geocode(addrEl.value); });

      deliveryMarker.on('dragend', function(e){
        const p = e.target.getLatLng();
        setDelivery(p.lat, p.lng, false);
        reverseGeocode(p);
      });

      map.on('click', function(e){ setDelivery(e.latlng.lat, e.latlng.lng, false); reverseGeocode(e.latlng); });

      // Use My Location
      const useBtn = document.getElementById('useMyLocation');
      if (useBtn && 'geolocation' in navigator) {
        useBtn.addEventListener('click', function(){
          useBtn.disabled = true; useBtn.textContent = 'Localisation...';
          navigator.geolocation.getCurrentPosition(function(pos){
            const { latitude, longitude } = pos.coords;
            setDelivery(latitude, longitude, true);
            reverseGeocode({ lat: latitude, lng: longitude });
            useBtn.disabled = false; useBtn.textContent = 'Utiliser ma position';
          }, function(){
            useBtn.disabled = false; useBtn.textContent = 'Utiliser ma position';
            alert("Impossible de récupérer votre position.");
          }, { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 });
        });
      }
    })();
  </script>
@endpush
