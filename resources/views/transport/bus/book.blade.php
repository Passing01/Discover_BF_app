@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <nav aria-label="breadcrumb" class="mb-2">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('tourist.dashboard') }}">Tableau de bord</a></li>
      <li class="breadcrumb-item"><a href="{{ route('transport.bus.index') }}">Bus</a></li>
      <li class="breadcrumb-item active" aria-current="page">Réserver</li>
    </ol>
  </nav>

  <div class="d-flex align-items-center justify-content-between mb-2">
    <h1 class="h4 mb-0">Bus — Réservation</h1>
    <div class="text-muted small">Prix de base: {{ number_format($trip->price, 0, ',', ' ') }} FCFA / place</div>
  </div>

  <form method="post" action="{{ route('transport.bus.book.store', $trip) }}" id="bus-book-form" novalidate>
    @csrf
    <div class="row g-3">
      <div class="col-lg-4">
        <div class="panel-cream rounded-20 p-3 h-100">
          <div class="fw-semibold mb-2">Détails du trajet</div>
          <div class="small text-muted mb-2">{{ $trip->origin }} → {{ $trip->destination }}</div>
          <div class="small mb-3">
            <span class="text-muted">Départ:</span> {{ $trip->departure_time }}
          </div>

          <div class="vstack gap-2">
            <div>
              <label class="form-label small text-muted">Nombre de places</label>
              <input type="number" min="1" max="{{ $trip->seats_available }}" name="seats" id="seats" class="form-control" value="{{ old('seats', 1) }}" required>
              <div class="form-text">Places disponibles: {{ $trip->seats_available }}</div>
              @error('seats')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div>
              <label class="form-label small text-muted">Notes</label>
              <input type="text" class="form-control" placeholder="Optionnel (ex: proche d'une fenêtre)">
            </div>
          </div>

          <hr class="my-3">
          <div class="fw-semibold mb-2">Préférences</div>
          <div class="d-flex flex-wrap gap-2">
            <div class="btn btn-outline-secondary btn-sm" data-pref="seat-window">Siège • Fenêtre</div>
            <div class="btn btn-outline-secondary btn-sm" data-pref="seat-front">Siège • Avant</div>
            <div class="btn btn-outline-secondary btn-sm" data-pref="ac">Clim • Requise</div>
          </div>
        </div>
      </div>

      <div class="col-lg-8">
        <div class="panel-cream rounded-20 p-3 mb-3">
          <div class="fw-semibold mb-2 d-flex justify-content-between align-items-center">
            <span>Classes & options</span>
            <span class="small text-muted">Sélectionnez une classe</span>
          </div>
          <div class="vstack gap-2" id="bus-options">
            <label class="border rounded p-2 d-flex justify-content-between align-items-center cursor-pointer">
              <div class="d-flex align-items-center gap-2">
                <input type="radio" name="class_option" value="standard" class="form-check-input" checked>
                <div>
                  <div class="fw-semibold">Standard</div>
                  <div class="small text-muted">Siège classique</div>
                </div>
              </div>
              <div class="text-end">
                <div class="fw-semibold" data-price="standard">—</div>
              </div>
            </label>

            <label class="border rounded p-2 d-flex justify-content-between align-items-center cursor-pointer">
              <div class="d-flex align-items-center gap-2">
                <input type="radio" name="class_option" value="comfort" class="form-check-input">
                <div>
                  <div class="fw-semibold">Confort</div>
                  <div class="small text-muted">Plus d'espace pour les jambes</div>
                </div>
              </div>
              <div class="text-end">
                <div class="fw-semibold" data-price="comfort">—</div>
              </div>
            </label>

            <label class="border rounded p-2 d-flex justify-content-between align-items-center cursor-pointer">
              <div class="d-flex align-items-center gap-2">
                <input type="radio" name="class_option" value="vip" class="form-check-input">
                <div>
                  <div class="fw-semibold">VIP</div>
                  <div class="small text-muted">Inclut rafraîchissements</div>
                </div>
              </div>
              <div class="text-end">
                <div class="fw-semibold" data-price="vip">—</div>
              </div>
            </label>
          </div>
        </div>

        <div class="panel-cream rounded-20 p-3 mb-3">
          <div class="fw-semibold mb-2">Aperçu de la carte</div>
          <div class="ratio ratio-21x9 bg-light rounded" id="busMap"></div>
          <div class="small text-muted mt-2" id="busDistanceInfo">Aperçu basique. La distance estimée sera affichée ici.</div>
        </div>

        <div class="panel-cream rounded-20 p-3 position-sticky" style="bottom: 12px;">
          <div class="fw-semibold mb-2">Votre sélection</div>
          <div class="row g-2 small">
            <div class="col-md-3"><div class="text-muted">Classe</div><div id="sel-class">Standard</div></div>
            <div class="col-md-3"><div class="text-muted">Trajet</div><div>{{ $trip->origin }} → {{ $trip->destination }}</div></div>
            <div class="col-md-3"><div class="text-muted">Places</div><div id="sel-seats">1</div></div>
            <div class="col-md-3 text-md-end"><div class="text-muted">Total estimé</div><div class="fw-bold" id="sel-total">—</div></div>
          </div>
          <div class="mt-3 d-flex gap-2 flex-wrap">
            <a href="{{ route('transport.bus.show', $trip) }}" class="btn btn-outline-secondary">Détails</a>
            <button class="btn btn-primary" type="submit" id="bus-submit-btn">
              <span class="spinner-border spinner-border-sm me-1 d-none" role="status" aria-hidden="true" id="bus-submit-spinner"></span>
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
    const base = {{ (float) $trip->price }};
    const seatEl = document.getElementById('seats');
    const form = document.getElementById('bus-book-form');
    const maxSeats = {{ (int) $trip->seats_available }};
    const submitBtn = document.getElementById('bus-submit-btn');
    const submitSp = document.getElementById('bus-submit-spinner');
    const priceEls = {
      standard: document.querySelector('[data-price="standard"]'),
      comfort: document.querySelector('[data-price="comfort"]'),
      vip: document.querySelector('[data-price="vip"]'),
    };
    const multipliers = { standard: 1, comfort: 1.2, vip: 1.5 };
    const selClass = document.getElementById('sel-class');
    const selSeats = document.getElementById('sel-seats');
    const selTotal = document.getElementById('sel-total');

    function formatCFA(val){
      return new Intl.NumberFormat('fr-FR', { maximumFractionDigits: 0 }).format(Math.round(val)) + ' FCFA';
    }

    function recalc(){
      const n = Math.max(1, parseInt(seatEl.value || '1', 10));
      Object.keys(priceEls).forEach(k => {
        priceEls[k].textContent = formatCFA(base * multipliers[k]);
      });
      const selected = document.querySelector('input[name="class_option"]:checked').value;
      selClass.textContent = selected.toUpperCase();
      selSeats.textContent = n;
      selTotal.textContent = formatCFA(base * multipliers[selected] * n);
    }

    function clampSeats(){
      let v = parseInt(seatEl.value || '1', 10);
      if (isNaN(v) || v < 1) v = 1;
      if (v > maxSeats) v = maxSeats;
      seatEl.value = v;
    }

    ['input','change','blur'].forEach(evt => {
      seatEl.addEventListener(evt, recalc);
      seatEl.addEventListener(evt, clampSeats);
      document.getElementById('bus-options').addEventListener(evt, (e)=>{
        if(e.target && e.target.name === 'class_option'){ recalc(); }
      });
    });

    form.addEventListener('submit', function(e){
      clampSeats();
      if(!form.checkValidity()){
        e.preventDefault();
        form.reportValidity();
        return;
      }
      submitBtn.setAttribute('disabled', 'disabled');
      submitSp.classList.remove('d-none');
    });

    recalc();
  })();

  // Leaflet Map init + geocoding + distance estimate
  (function(){
    const el = document.getElementById('busMap');
    if(!el) return;
    // Default center: Ouagadougou, Burkina Faso
    const defaultCenter = [12.3713, -1.5197];
    const map = L.map('busMap', { zoomControl: true }).setView(defaultCenter, 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    const originText = @json($trip->origin);
    const destText = @json($trip->destination);
    const infoEl = document.getElementById('busDistanceInfo');
    let markers = { o: null, d: null };
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

    function place(kind, p, label){
      if(markers[kind]){ map.removeLayer(markers[kind]); }
      markers[kind] = L.marker([p.lat, p.lng]).addTo(map).bindPopup(label).openPopup();
    }

    function redraw(){
      if(line){ map.removeLayer(line); line = null; }
      if(markers.o && markers.d){
        const a = markers.o.getLatLng();
        const b = markers.d.getLatLng();
        line = L.polyline([a, b], { color: '#3f8efc' }).addTo(map);
        map.fitBounds(L.featureGroup([markers.o, markers.d, line]).getBounds().pad(0.2));
        const d = haversineKm({lat:a.lat,lng:a.lng}, {lat:b.lat,lng:b.lng});
        if(infoEl){
          infoEl.textContent = `Distance estimée (ligne droite): ${ (Math.round(d*10)/10).toFixed(1) } km`;
        }
      }
    }

    (async function init(){
      try {
        const [a, b] = await Promise.all([ geocode(originText), geocode(destText) ]);
        if(a){ place('o', a, 'Départ: ' + originText); }
        if(b){ place('d', b, 'Arrivée: ' + destText); }
        if(a || b){ redraw(); } else {
          L.marker(defaultCenter).addTo(map).bindPopup('Zone de prévisualisation');
          if(infoEl){ infoEl.textContent = 'Aperçu basique. Impossible d\'estimer la distance.'; }
        }
      } catch(e){
        L.marker(defaultCenter).addTo(map).bindPopup('Zone de prévisualisation');
        if(infoEl){ infoEl.textContent = 'Aperçu basique. Erreur lors du géocodage.'; }
      }
    })();
  })();
</script>
@endpush
@endsection
