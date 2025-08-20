@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <nav aria-label="breadcrumb" class="mb-2">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('tourist.dashboard') }}">Tableau de bord</a></li>
      <li class="breadcrumb-item"><a href="{{ route('transport.taxi.index') }}">Taxis</a></li>
      <li class="breadcrumb-item active" aria-current="page">Commander une course</li>
    </ol>
  </nav>

  <div class="d-flex align-items-center justify-content-between mb-2">
    <h1 class="h4 mb-0">Taxi — Commande</h1>
    <div class="text-muted small">Tarif de base: {{ number_format($taxi->price_per_km, 0, ',', ' ') }} FCFA/km</div>
  </div>

  <form method="post" action="{{ route('transport.taxi.ride.store', $taxi) }}" id="ride-form" novalidate>
    @csrf
    <div class="row g-3">
      <div class="col-lg-4">
        <div class="panel-cream rounded-20 p-3 h-100">
          <div class="fw-semibold mb-2">Détails de la course</div>
          <div class="vstack gap-2">
            <div>
              <label class="form-label small text-muted">Départ</label>
              <input type="text" name="pickup_location" class="form-control" placeholder="Ex: Hôtel Silmandé" value="{{ old('pickup_location') }}" minlength="3" required autocomplete="off">
              @error('pickup_location')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div>
              <label class="form-label small text-muted">Destination</label>
              <input type="text" name="dropoff_location" class="form-control" placeholder="Ex: Aéroport international de Ouagadougou" value="{{ old('dropoff_location') }}" minlength="3" required autocomplete="off">
              @error('dropoff_location')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="row g-2">
              <div class="col-6">
                <label class="form-label small text-muted">Date</label>
                <input type="date" class="form-control" id="ride-date-only" required>
              </div>
              <div class="col-6">
                <label class="form-label small text-muted">Heure</label>
                <input type="time" class="form-control" id="ride-time-only" required>
              </div>
            </div>
            <input type="hidden" name="ride_date" id="ride-datetime" value="{{ old('ride_date') }}">
            @error('ride_date')<div class="text-danger small">{{ $message }}</div>@enderror
            <div>
              <label class="form-label small text-muted">Distance estimée (km)</label>
              <input type="number" min="0.1" step="0.1" name="distance_km" id="distance-km" class="form-control" placeholder="Ex: 15.5" value="{{ old('distance_km') }}">
              <div class="form-text">Utilisé pour l'estimation — le prix final est calculé lors de la confirmation.</div>
              @error('distance_km')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
          </div>

          <hr class="my-3">
          <div class="fw-semibold mb-2">Préférences</div>
          <div class="d-flex flex-wrap gap-2">
            <div class="btn btn-outline-secondary btn-sm" data-pref="vehicle-standard">Véhicule • Standard</div>
            <div class="btn btn-outline-secondary btn-sm" data-pref="ac-required">Clim • Requise</div>
            <div class="btn btn-outline-secondary btn-sm" data-pref="verified-only">Sécurité • Chauffeur vérifié</div>
            <div class="btn btn-outline-secondary btn-sm" data-pref="notes">Notes • Optionnel</div>
          </div>
          <div class="mt-2 d-flex align-items-center gap-2">
            <button class="btn btn-light border btn-sm" type="button" id="btn-refresh">Actualiser</button>
            <span class="small text-muted">Estimation auto — Prix en FCFA</span>
          </div>
        </div>
      </div>

      <div class="col-lg-8">
        <div class="panel-cream rounded-20 p-3 mb-3">
          <div class="fw-semibold mb-2 d-flex justify-content-between align-items-center">
            <span>Propositions</span>
            <span class="small text-muted">Sélectionnez un type de course</span>
          </div>
          <div class="vstack gap-2" id="ride-options">
            <label class="border rounded p-2 d-flex justify-content-between align-items-center cursor-pointer">
              <div class="d-flex align-items-center gap-2">
                <input type="radio" name="ride_option" value="standard" class="form-check-input" checked>
                <div>
                  <div class="fw-semibold">Standard</div>
                  <div class="small text-muted">Berline • 2-3 pax • 2 bagages</div>
                </div>
              </div>
              <div class="text-end">
                <div class="fw-semibold" data-price="standard">—</div>
                <div class="small text-muted">ETA 12–15 min</div>
              </div>
            </label>

            <label class="border rounded p-2 d-flex justify-content-between align-items-center cursor-pointer">
              <div class="d-flex align-items-center gap-2">
                <input type="radio" name="ride_option" value="comfort" class="form-check-input">
                <div>
                  <div class="fw-semibold">Confort</div>
                  <div class="small text-muted">SUV • 3-4 pax • Espace supplémentaire</div>
                </div>
              </div>
              <div class="text-end">
                <div class="fw-semibold" data-price="comfort">—</div>
                <div class="small text-muted">ETA 10–12 min</div>
              </div>
            </label>

            <label class="border rounded p-2 d-flex justify-content-between align-items-center cursor-pointer">
              <div class="d-flex align-items-center gap-2">
                <input type="radio" name="ride_option" value="verified" class="form-check-input">
                <div>
                  <div class="fw-semibold">Chauffeur vérifié</div>
                  <div class="small text-muted">Meilleure note • Siège enfant dispo</div>
                </div>
              </div>
              <div class="text-end">
                <div class="fw-semibold" data-price="verified">—</div>
                <div class="small text-muted">ETA 15–18 min</div>
              </div>
            </label>
          </div>
        </div>

        <div class="panel-cream rounded-20 p-3 mb-3">
          <div class="fw-semibold mb-2">Aperçu de la carte</div>
          <div class="ratio ratio-21x9 bg-light rounded" id="taxiMap"></div>
          <div class="small text-muted mt-2">Entrez Départ et Destination, la distance sera estimée automatiquement.</div>
        </div>

        <div class="panel-cream rounded-20 p-3 position-sticky" style="bottom: 12px;">
          <div class="fw-semibold mb-2">Votre sélection</div>
          <div class="row g-2 small">
            <div class="col-md-3"><div class="text-muted">Type</div><div id="sel-type">Standard</div></div>
            <div class="col-md-3"><div class="text-muted">Départ</div><div id="sel-pickup">—</div></div>
            <div class="col-md-3"><div class="text-muted">Destination</div><div id="sel-dropoff">—</div></div>
            <div class="col-md-3 text-md-end"><div class="text-muted">Total estimé</div><div class="fw-bold" id="sel-total">—</div></div>
          </div>
          <div class="mt-3 d-flex gap-2 flex-wrap">
            <a href="#" class="btn btn-outline-secondary">Politiques</a>
            <button class="btn btn-primary" type="submit" id="ride-submit-btn">
              <span class="spinner-border spinner-border-sm me-1 d-none" role="status" aria-hidden="true" id="ride-submit-spinner"></span>
              Confirmer
            </button>
            <a href="{{ route('tourist.itinerary') }}" class="btn btn-light border">Enregistrer dans l'itinéraire</a>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>

@push('styles')
<link
  rel="stylesheet"
  href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
  integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
  crossorigin=""
/>
@endpush

@push('scripts')
<script
  src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
  integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
  crossorigin=""
></script>
<script>
  (function(){
    const basePerKm = {{ (float) $taxi->price_per_km }};
    const distanceEl = document.getElementById('distance-km');
    const priceEls = {
      standard: document.querySelector('[data-price="standard"]'),
      comfort: document.querySelector('[data-price="comfort"]'),
      verified: document.querySelector('[data-price="verified"]')
    };
    const multipliers = { standard: 1, comfort: 1.3, verified: 1.45 };
    const selType = document.getElementById('sel-type');
    const selPickup = document.getElementById('sel-pickup');
    const selDrop = document.getElementById('sel-dropoff');
    const selTotal = document.getElementById('sel-total');
    const pickupInput = document.querySelector('input[name="pickup_location"]');
    const dropInput = document.querySelector('input[name="dropoff_location"]');
    const dateOnly = document.getElementById('ride-date-only');
    const timeOnly = document.getElementById('ride-time-only');
    const hiddenDT = document.getElementById('ride-datetime');

    function formatCFA(val){
      return new Intl.NumberFormat('fr-FR', { maximumFractionDigits: 0 }).format(Math.round(val)) + ' FCFA';
    }

    function updateDatetime(){
      if(dateOnly.value && timeOnly.value){
        hiddenDT.value = dateOnly.value + 'T' + timeOnly.value;
      }
    }

    function recalc(){
      const d = parseFloat(distanceEl.value || '0');
      Object.keys(priceEls).forEach(k => {
        const est = d > 0 ? d * basePerKm * multipliers[k] : 0;
        priceEls[k].textContent = d > 0 ? formatCFA(est) : '—';
      });
      const selected = document.querySelector('input[name="ride_option"]:checked').value;
      selType.textContent = selected.charAt(0).toUpperCase() + selected.slice(1);
      const total = d > 0 ? d * basePerKm * multipliers[selected] : 0;
      selTotal.textContent = d > 0 ? formatCFA(total) : '—';
      selPickup.textContent = pickupInput.value || '—';
      selDrop.textContent = dropInput.value || '—';
    }

    ['input','change'].forEach(evt => {
      distanceEl.addEventListener(evt, recalc);
      pickupInput.addEventListener(evt, recalc);
      dropInput.addEventListener(evt, recalc);
      dateOnly.addEventListener(evt, updateDatetime);
      timeOnly.addEventListener(evt, updateDatetime);
      document.getElementById('ride-options').addEventListener(evt, (e)=>{
        if(e.target && e.target.name === 'ride_option'){ recalc(); }
      });
    });

    document.getElementById('btn-refresh').addEventListener('click', recalc);

    // Guard: ensure hidden ride_date is set on submit
    const form = document.getElementById('ride-form');
    const submitBtn = document.getElementById('ride-submit-btn');
    const submitSp = document.getElementById('ride-submit-spinner');
    form.addEventListener('submit', function(e){
      if(!hiddenDT.value){ updateDatetime(); }
      // Use HTML5 validation and provide feedback
      if(!form.checkValidity() || !hiddenDT.value){
        e.preventDefault();
        form.reportValidity();
        if(!hiddenDT.value){ alert('Veuillez saisir la date et l\'heure de la course.'); }
        return;
      }
      // Valid: lock UI
      submitBtn.setAttribute('disabled', 'disabled');
      submitSp.classList.remove('d-none');
    });
    recalc();
  })();

  // Leaflet + Geocoding + Auto distance
  (function(){
    const mapEl = document.getElementById('taxiMap');
    if(!mapEl) return;

    // Init map centered on Ouagadougou
    const defaultCenter = [12.3713, -1.5197];
    const map = L.map('taxiMap', { zoomControl: true }).setView(defaultCenter, 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    const pickupInput = document.querySelector('input[name="pickup_location"]');
    const dropInput = document.querySelector('input[name="dropoff_location"]');
    const distInput = document.getElementById('distance-km');

    let markers = { pickup: null, drop: null };
    let line = null;

    function haversineKm(a, b){
      const R = 6371; // km
      const dLat = (b.lat - a.lat) * Math.PI/180;
      const dLng = (b.lng - a.lng) * Math.PI/180;
      const la1 = a.lat * Math.PI/180;
      const la2 = b.lat * Math.PI/180;
      const x = Math.sin(dLat/2) ** 2 + Math.sin(dLng/2) ** 2 * Math.cos(la1) * Math.cos(la2);
      const d = 2 * Math.atan2(Math.sqrt(x), Math.sqrt(1-x));
      return R * d;
    }

    async function geocode(q){
      if(!q || q.trim().length < 3) return null;
      const url = 'https://nominatim.openstreetmap.org/search?format=json&limit=1&q=' + encodeURIComponent(q);
      const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
      if(!res.ok) return null;
      const data = await res.json();
      if(!Array.isArray(data) || data.length === 0) return null;
      const it = data[0];
      return { lat: parseFloat(it.lat), lng: parseFloat(it.lon), display_name: it.display_name };
    }

    function setMarker(kind, latlng, label){
      if(markers[kind]){ map.removeLayer(markers[kind]); }
      markers[kind] = L.marker([latlng.lat, latlng.lng]).addTo(map).bindPopup(label || kind).openPopup();
    }

    function drawLine(){
      if(line){ map.removeLayer(line); line = null; }
      if(markers.pickup && markers.drop){
        const a = markers.pickup.getLatLng();
        const b = markers.drop.getLatLng();
        line = L.polyline([a, b], { color: '#ff7e5f' }).addTo(map);
        map.fitBounds(L.featureGroup([markers.pickup, markers.drop, line]).getBounds().pad(0.2));
        const d = haversineKm({lat:a.lat,lng:a.lng}, {lat:b.lat,lng:b.lng});
        if(d > 0){
          distInput.value = (Math.round(d * 10) / 10).toFixed(1);
          // trigger input event to recalc price
          distInput.dispatchEvent(new Event('input'));
        }
      }
    }

    let debounce;
    async function update(kind){
      clearTimeout(debounce);
      debounce = setTimeout(async () => {
        const q = kind === 'pickup' ? pickupInput.value : dropInput.value;
        const p = await geocode(q);
        if(!p) return;
        setMarker(kind, p, q);
        drawLine();
      }, 400);
    }

    ['change','blur','input'].forEach(evt => {
      pickupInput.addEventListener(evt, () => update('pickup'));
      dropInput.addEventListener(evt, () => update('drop'));
    });
  })();
</script>
@endpush
@endsection
