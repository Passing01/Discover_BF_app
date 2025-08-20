@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <nav aria-label="breadcrumb" class="mb-2">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('/') }}">Accueil</a></li>
      <li class="breadcrumb-item active" aria-current="page">Hébergements</li>
    </ol>
  </nav>
  <h1 class="mb-1">Hébergements</h1>
  <p class="text-muted mb-3">Parcourez les hôtels et réservez facilement.</p>
  <div id="hotels-map" class="mb-3 panel-cream" style="width:100%; height: 380px; border-radius: 16px; overflow: hidden;"></div>

  <form method="GET" class="panel-cream rounded-20 p-3 mb-3">
    <div class="row g-2 align-items-end">
      <div class="col-md-3">
        <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Rechercher (nom, description, ville, pays)" class="form-control">
      </div>
      <div class="col-md-3">
        <input type="text" name="city" value="{{ $filters['city'] ?? '' }}" placeholder="Ville" class="form-control">
      </div>
      <div class="col-md-3">
        <input type="text" name="country" value="{{ $filters['country'] ?? '' }}" placeholder="Pays" class="form-control">
      </div>
      <div class="col-md-2">
        <input type="number" name="capacity" value="{{ $filters['capacity'] ?? '' }}" min="1" placeholder="Capacité min" class="form-control">
      </div>
      <div class="col-md-2">
        <input type="number" name="min_price" value="{{ $filters['min_price'] ?? '' }}" min="0" step="1" placeholder="Prix min" class="form-control">
      </div>
      <div class="col-md-2">
        <input type="number" name="max_price" value="{{ $filters['max_price'] ?? '' }}" min="0" step="1" placeholder="Prix max" class="form-control">
      </div>
      <div class="col-md-3">
        <label class="form-label small mb-1">Trier</label>
        <select id="hotelSort" name="sort" class="form-select">
          <option value="recent" @selected(($filters['sort'] ?? '')==='recent')>Plus récents</option>
          <option value="rooms" @selected(($filters['sort'] ?? '')==='rooms')>Plus de chambres</option>
          <option value="price_asc" @selected(($filters['sort'] ?? '')==='price_asc')>Prix ↑</option>
          <option value="price_desc" @selected(($filters['sort'] ?? '')==='price_desc')>Prix ↓</option>
          <option value="stars" @selected(($filters['sort'] ?? '')==='stars')>Étoiles</option>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label small mb-1">Affichage</label>
        <div class="d-flex gap-2">
          <button type="button" id="viewGrid" class="btn btn-cream">Grille</button>
          <button type="button" id="viewList" class="btn btn-cream">Liste</button>
        </div>
      </div>
      <div class="col-md-3">
        <input type="date" name="start_date" value="{{ $filters['start_date'] ?? '' }}" class="form-control" placeholder="Date d'arrivée">
      </div>
      <div class="col-md-3">
        <input type="date" name="end_date" value="{{ $filters['end_date'] ?? '' }}" class="form-control" placeholder="Date de départ">
      </div>
      <div class="col-12">
        <button class="btn btn-link p-0" type="button" data-bs-toggle="collapse" data-bs-target="#advancedFilters" aria-expanded="false" aria-controls="advancedFilters">
          Filtres avancés (équipements, règles)
        </button>
        <div class="collapse mt-2" id="advancedFilters">
          <div class="row g-2">
            <div class="col-md-6">
              <div class="panel-cream rounded-20 p-2" style="max-height:180px; overflow:auto;">
                <div class="fw-semibold small mb-1">Équipements</div>
                @foreach(($amenities ?? []) as $amenity)
                  @php $sel = collect($filters['amenities'] ?? [])->contains($amenity->id); @endphp
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="amenities[]" value="{{ $amenity->id }}" id="am-{{ $amenity->id }}" @checked($sel)>
                    <label class="form-check-label" for="am-{{ $amenity->id }}">{{ $amenity->name }}</label>
                  </div>
                @endforeach
                @if(empty($amenities) || count($amenities)===0)
                  <div class="text-muted small">Aucun équipement disponible.</div>
                @endif
              </div>
            </div>
            <div class="col-md-6">
              <div class="panel-cream rounded-20 p-2" style="max-height:180px; overflow:auto;">
                <div class="fw-semibold small mb-1">Règles</div>
                @foreach(($rules ?? []) as $rule)
                  @php $sel = collect($filters['rules'] ?? [])->contains($rule->id); @endphp
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="rules[]" value="{{ $rule->id }}" id="rl-{{ $rule->id }}" @checked($sel)>
                    <label class="form-check-label" for="rl-{{ $rule->id }}">{{ $rule->name }}</label>
                  </div>
                @endforeach
                @if(empty($rules) || count($rules)===0)
                  <div class="text-muted small">Aucune règle disponible.</div>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-12 d-flex gap-2 mt-2 align-items-center">
        <button class="btn btn-orange">Filtrer</button>
        <a href="{{ route('tourist.hotels.index') }}" class="btn btn-cream">Réinitialiser</a>
        <span class="ms-auto small text-muted">Résultats: {{ $hotels->total() ?? count($hotels) }}</span>
      </div>
    </div>
  </form>

  @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <div class="row g-3" id="hotels-list">
    @forelse($hotels as $hotel)
      @php($minPrice = $hotel->rooms_min_price_per_night ?? 0)
      @php($roomsCount = $hotel->rooms_count ?? ($hotel->rooms->count() ?? null))
      <div class="col-md-4 hotel-item" data-price="{{ (int) $minPrice }}" data-rooms="{{ (int) ($roomsCount ?? 0) }}">
        <div class="panel-cream rounded-20 h-100 d-flex flex-column hotel-card" data-hotel-id="h{{ $hotel->id }}" @if(!is_null($hotel->latitude) && !is_null($hotel->longitude)) data-lat="{{ $hotel->latitude }}" data-lng="{{ $hotel->longitude }}" @endif>
          @if($hotel->photo)
            <img src="{{ asset($hotel->photo) }}" alt="{{ $hotel->name }}" class="w-100 rounded-top" style="object-fit:cover; height:160px;">
          @endif
          <div class="p-3 d-flex flex-column gap-2 flex-grow-1">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <h5 class="mb-1">{{ $hotel->name }}</h5>
                <div class="text-muted small">{{ $hotel->city }}, {{ $hotel->country }}</div>
              </div>
              @if(!is_null($hotel->rooms_min_price_per_night))
                <div class="text-end">
                  <div class="fw-semibold">{{ number_format($hotel->rooms_min_price_per_night, 0, ',', ' ') }} XOF</div>
                  <div class="small text-muted">/ nuit</div>
                </div>
              @endif
            </div>
            <p class="mb-2 small">{{ Str::limit($hotel->description, 120) }}</p>
            <div class="mt-auto d-flex gap-2">
              <a href="{{ route('tourist.hotels.show', $hotel) }}" class="btn btn-orange btn-sm flex-grow-1">Réserver</a>
              <a href="{{ route('tourist.hotels.show', $hotel) }}" class="btn btn-cream btn-sm">Détails</a>
              <button type="button" class="btn btn-cream btn-sm" onclick="window.focusHotelOnMap('h{{ $hotel->id }}')">Carte</button>
            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12">
        <div class="alert alert-info">Aucun hôtel disponible.</div>
      </div>
    @endforelse
  </div>

  <div class="mt-3">
    {{ $hotels->links() }}
  </div>
</div>
@endsection

@push('styles')
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
@endpush

@push('scripts')
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
  <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
  <script>
    (function(){
      var mapEl = document.getElementById('hotels-map');
      if (!mapEl) return;
      var map = L.map('hotels-map');
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap contributors' }).addTo(map);
      var cluster = L.markerClusterGroup();
      var markers = [];
      var markerById = {};
      @foreach($hotels as $h)
        @if(!is_null($h->latitude) && !is_null($h->longitude))
          (function(){
            var id = 'h{{ $h->id }}';
            var m = L.marker([{{ $h->latitude }}, {{ $h->longitude }}]);
            m.bindPopup(`<strong>{{ addslashes($h->name) }}</strong><br>{{ addslashes($h->city) }}, {{ addslashes($h->country) }}@if(!is_null($h->rooms_min_price_per_night))<br><span class='text-muted small'>Dès {{ number_format($h->rooms_min_price_per_night, 0, ',', ' ') }} XOF</span>@endif<br><a href='{{ route('tourist.hotels.show', $h) }}'>Voir</a>`);
            cluster.addLayer(m);
            markers.push(m);
            markerById[id] = m;
          })();
        @endif
      @endforeach
      map.addLayer(cluster);
      if (markers.length) { var group = L.featureGroup(markers); map.fitBounds(group.getBounds().pad(0.2)); }
      else { map.setView([12.3686, -1.5271], 12); }

      // expose for hover sync
      window.__hotelsMap = map;
      window.__hotelMarkerById = markerById;
    })();

    // Map <-> List sync: highlight markers/cards on hover/click
    (function(){
      function ready(fn){ if(document.readyState!=='loading') fn(); else document.addEventListener('DOMContentLoaded', fn); }
      ready(function(){
        var map = window.__hotelsMap;
        var markerById = window.__hotelMarkerById || {};
        if(!map) return;
        var cardById = {};
        document.querySelectorAll('.hotel-card[data-hotel-id]').forEach(function(card){
          var id = card.getAttribute('data-hotel-id');
          cardById[id] = card;
          card.addEventListener('mouseenter', function(){ var m = markerById[id]; if(m){ m.openPopup(); } });
          card.addEventListener('mouseleave', function(){ var m = markerById[id]; if(m){ m.closePopup(); } });
        });
        Object.keys(markerById).forEach(function(id){
          var m = markerById[id];
          m.on('click', function(){ var card = cardById[id]; if(card){ card.scrollIntoView({behavior:'smooth', block:'center'}); card.classList.add('shadow'); setTimeout(function(){ card.classList.remove('shadow'); }, 800); }});
        });
        window.focusHotelOnMap = function(id){
          var m = markerById[id];
          if(m){ m.openPopup(); map.panTo(m.getLatLng()); }
        }
      });
    })();

    // Client-side sort and view toggle
    (function(){
      function ready(fn){ if(document.readyState!=='loading') fn(); else document.addEventListener('DOMContentLoaded', fn); }
      ready(function(){
        var sortSel = document.getElementById('hotelSort');
        var list = document.getElementById('hotels-list');
        var btnGrid = document.getElementById('viewGrid');
        var btnList = document.getElementById('viewList');
        function sortItems(){
          var items = Array.from(list.querySelectorAll('.hotel-item'));
          var v = (sortSel && sortSel.value) || 'price_asc';
          items.sort(function(a,b){
            var pa = parseInt(a.getAttribute('data-price')||'0',10);
            var pb = parseInt(b.getAttribute('data-price')||'0',10);
            var ra = parseInt(a.getAttribute('data-rooms')||'0',10);
            var rb = parseInt(b.getAttribute('data-rooms')||'0',10);
            switch(v){
              case 'price_desc': return pb - pa;
              case 'rooms_desc': return rb - ra;
              case 'rooms_asc': return ra - rb;
              case 'price_asc': default: return pa - pb;
            }
          });
          items.forEach(function(el){ list.appendChild(el); });
        }
        function setList(){
          list.classList.remove('row');
          list.classList.add('d-flex','flex-column','gap-2');
          list.querySelectorAll('.hotel-item').forEach(function(col){ col.className = 'hotel-item'; });
        }
        function setGrid(){
          list.classList.add('row','g-3');
          list.classList.remove('d-flex','flex-column','gap-2');
          list.querySelectorAll('.hotel-item').forEach(function(col){ col.className = 'col-md-4 hotel-item'; });
        }
        sortSel && sortSel.addEventListener('change', sortItems);
        btnGrid && btnGrid.addEventListener('click', function(){ setGrid(); });
        btnList && btnList.addEventListener('click', function(){ setList(); });
        // init
        sortItems();
      });
    })();
  </script>
@endpush
