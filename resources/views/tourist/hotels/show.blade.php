@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <nav aria-label="breadcrumb" class="mb-2">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('/') }}">Accueil</a></li>
      <li class="breadcrumb-item"><a href="{{ route('tourist.hotels.index') }}">Hébergements</a></li>
      <li class="breadcrumb-item active" aria-current="page">{{ $hotel->name }}</li>
    </ol>
  </nav>
  @php $gallery = $hotel->photos ?? collect(); @endphp
  @if(($gallery->count() ?? 0) > 0)
    <div id="hotelCarousel" class="carousel slide mb-3" data-bs-ride="carousel">
      <div class="carousel-inner rounded" style="max-height:360px; overflow:hidden;">
        @foreach($gallery as $idx => $p)
          <div class="carousel-item @if($idx===0) active @endif">
            <img src="{{ asset($p->path) }}" class="d-block w-100" style="object-fit:cover; height:360px;" alt="photo {{ $idx+1 }}">
          </div>
        @endforeach
      </div>
      <button class="carousel-control-prev" type="button" data-bs-target="#hotelCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Précédent</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#hotelCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Suivant</span>
      </button>
    </div>
  @elseif($hotel->photo)
    <div class="mb-3">
      <img src="{{ asset($hotel->photo) }}" alt="{{ $hotel->name }}" class="img-fluid rounded w-100" style="max-height:360px; object-fit:cover;">
    </div>
  @endif
  <h1 class="mb-1">{{ $hotel->name }}</h1>
  <div class="text-muted mb-1">{{ $hotel->address }}, {{ $hotel->city }}, {{ $hotel->country }}</div>
  <div class="d-flex flex-wrap gap-2 mb-2">
    <span class="badge text-bg-light border"><i class="bi bi-geo-alt me-1"></i>{{ $hotel->city }}</span>
    <span class="badge text-bg-light border"><i class="bi bi-star-half me-1"></i>{{ number_format($hotel->rating ?? 4.4, 1) }}/5</span>
    <span class="badge text-bg-light border"><i class="bi bi-egg-fried me-1"></i>Petit-déj inclus</span>
    @if(!is_null($hotel->rooms_min_price_per_night) || ($hotel->rooms && $hotel->rooms->count()))
      @php $minRate = $hotel->rooms_min_price_per_night ?? $hotel->rooms->min('price_per_night'); @endphp
      <span class="badge text-bg-light border">Dès {{ number_format($minRate, 0, ',', ' ') }} XOF / nuit</span>
    @endif
  </div>
  <div class="d-flex flex-wrap gap-2 mb-3">
    <a href="#" class="btn btn-cream btn-sm"><i class="bi bi-share me-1"></i>Partager</a>
    <a href="#" class="btn btn-cream btn-sm"><i class="bi bi-bookmark me-1"></i>Sauver dans l'itinéraire</a>
    <a href="#" class="btn btn-cream btn-sm"><i class="bi bi-map me-1"></i>Prévisualiser dans le plan</a>
    <a href="#" class="btn btn-cream btn-sm"><i class="bi bi-badge-vr me-1"></i>Démarrer AR</a>
    <a href="#" class="btn btn-cream btn-sm"><i class="bi bi-tree me-1"></i>Nature à proximité</a>
  </div>
  <p class="mb-4">{{ $hotel->description }}</p>

  <div class="row g-3 mb-3">
    <div class="col-lg-8">
      <div class="panel-cream rounded-20 overflow-hidden">
        <div class="px-3 py-2 fw-semibold">Emplacement</div>
        <div class="p-0">
          <div id="hotel-map" style="width:100%; height: 340px;"></div>
          <div class="p-3">
            <a class="small" target="_blank" rel="noopener" href="https://www.openstreetmap.org/?mlat={{ $hotel->latitude }}&mlon={{ $hotel->longitude }}#map=14/{{ $hotel->latitude }}/{{ $hotel->longitude }}">Voir sur OpenStreetMap</a>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="panel-cream rounded-20 overflow-hidden recap-card">
        <div class="px-3 py-2 fw-semibold">Récapitulatif du séjour</div>
        <div class="p-3">
          <div class="d-flex justify-content-between small mb-2"><span>Nuit(s)</span><span id="recapNights">1</span></div>
          <div class="d-flex justify-content-between small mb-2"><span>Chambre</span><span><strong>À choisir</strong></span></div>
          <div class="d-flex justify-content-between small mb-3"><span>Petit-déj</span><span><strong>Inclus</strong></span></div>
          @if(isset($minRate))
            <div class="border rounded-3 p-2 mb-3 bg-white">
              <div class="d-flex justify-content-between"><span>Tarif/nuit</span><strong>{{ number_format($minRate, 0, ',', ' ') }} XOF</strong></div>
              <div class="d-flex justify-content-between small text-muted"><span>Taxes & frais</span><span>—</span></div>
              <div class="d-flex justify-content-between mt-1"><span>Total</span><strong>{{ number_format($minRate, 0, ',', ' ') }} XOF</strong></div>
            </div>
          @endif
          <div class="mb-3">
            <div class="fw-semibold mb-1">Avantages</div>
            <ul class="list-unstyled small mb-0">
              <li class="d-flex align-items-center gap-2"><i class="bi bi-shield-check"></i>Annulation gratuite</li>
              <li class="d-flex align-items-center gap-2"><i class="bi bi-taxi-front"></i>Transfert aéroport (option)</li>
              <li class="d-flex align-items-center gap-2"><i class="bi bi-alarm"></i>Late checkout (selon dispo)</li>
            </ul>
          </div>
          <div class="d-flex gap-2">
            <a href="#" class="btn btn-cream btn-sm w-50">Ajouter à l'itinéraire</a>
            <a href="#rooms" class="btn btn-orange btn-sm w-50">Procéder à la réservation</a>
          </div>
        </div>
        <div class="px-3 pb-3 small text-muted">Règles: Check-in 15:00 · Check-out 11:00 · Non fumeur</div>
      </div>
      <div class="mt-3 panel-cream rounded-20">
        <div class="px-3 py-2 fw-semibold">Contact</div>
        <div class="p-3">
          <div class="mb-1"><i class="bi bi-telephone me-1"></i> {{ $hotel->phone ?? '—' }}</div>
          <div class="mb-1"><i class="bi bi-envelope me-1"></i> {{ $hotel->email ?? '—' }}</div>
          <div class="mb-1"><i class="bi bi-star-fill text-warning me-1"></i> {{ $hotel->stars }} étoile(s)</div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-3">
    <div class="col-md-6">
      <div class="panel-cream rounded-20 h-100">
        <div class="px-3 py-2 fw-semibold">Équipements</div>
        <div class="p-3">
          @if(($hotel->amenities->count() ?? 0) > 0)
            <ul class="mb-0 list-unstyled d-flex flex-wrap gap-2">
              @foreach($hotel->amenities as $am)
                <li class="badge text-bg-light border">{{ $am->name }}</li>
              @endforeach
            </ul>
          @else
            <div class="text-muted small">Aucun équipement renseigné.</div>
          @endif
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="panel-cream rounded-20 h-100">
        <div class="px-3 py-2 fw-semibold">Règles du séjour</div>
        <div class="p-3">
          @if(($hotel->rules->count() ?? 0) > 0)
            <ul class="mb-0 list-unstyled d-flex flex-wrap gap-2">
              @foreach($hotel->rules as $rule)
                <li class="badge text-bg-light border">{{ $rule->name }}</li>
              @endforeach
            </ul>
          @else
            <div class="text-muted small">Aucune règle renseignée.</div>
          @endif
        </div>
      </div>
    </div>
  </div>

  <h4 class="mb-2" id="rooms">Chambres</h4>
  <div class="panel-cream rounded-20 p-3 mb-2">
    <div class="row g-2 align-items-end">
      <div class="col-md-3">
        <label class="form-label small mb-1">Date d'arrivée</label>
        <input type="date" id="filterStart" class="form-control">
      </div>
      <div class="col-md-3">
        <label class="form-label small mb-1">Date de départ</label>
        <input type="date" id="filterEnd" class="form-control">
      </div>
      <div class="col-md-3">
        <label class="form-label small mb-1">Rechercher</label>
        <input type="text" id="filterSearch" class="form-control" placeholder="Nom ou type">
      </div>
      <div class="col-md-3">
        @php $types = ($hotel->rooms->pluck('type')->filter()->unique()->values() ?? collect()); @endphp
        <label class="form-label small mb-1">Type</label>
        <select id="filterType" class="form-select">
          <option value="">Tous</option>
          @foreach($types as $t)
            <option value="{{ $t }}">{{ $t }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label small mb-1">Trier</label>
        <select id="filterSort" class="form-select">
          <option value="price_asc">Prix ↑</option>
          <option value="price_desc">Prix ↓</option>
          <option value="capacity_desc">Capacité ↓</option>
          <option value="capacity_asc">Capacité ↑</option>
        </select>
      </div>
      <div class="col-md-3">
        <button type="button" id="applyFilters" class="btn btn-orange">Appliquer</button>
        <button type="button" id="resetFilters" class="btn btn-cream ms-2">Réinitialiser</button>
      </div>
      <div class="col-md-6 text-end small text-muted">
        <span id="roomsCount"></span>
      </div>
    </div>
  </div>
  <div class="row g-3" id="roomsList">
    @forelse($hotel->rooms as $room)
      @php
        $upcoming = ($room->bookings ?? collect())
          ->where('status', '!=', 'cancelled')
          ->map(fn($b) => [\Carbon\Carbon::parse($b->start_date)->toDateString(), \Carbon\Carbon::parse($b->end_date)->toDateString()])
          ->values();
      @endphp
      <div class="col-md-6 room-item" data-room-id="{{ $room->id }}" data-price="{{ (int)$room->price_per_night }}" data-capacity="{{ (int)($room->capacity ?? 0) }}" data-type="{{ $room->type }}">
        <div class="panel-cream rounded-20 h-100">
          @php $rgal = $room->photos ?? collect(); @endphp
          @if(($rgal->count() ?? 0) > 0)
            <div id="roomCarousel-{{ $room->id }}" class="carousel slide" data-bs-ride="carousel">
              <div class="carousel-inner" style="height:160px; overflow:hidden;">
                @foreach($rgal as $idx => $p)
                  <div class="carousel-item @if($idx===0) active @endif">
                    <img src="{{ asset($p->path) }}" class="d-block w-100" style="object-fit:cover; height:160px;" alt="photo {{ $idx+1 }}">
                  </div>
                @endforeach
              </div>
              <button class="carousel-control-prev" type="button" data-bs-target="#roomCarousel-{{ $room->id }}" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Précédent</span>
              </button>
              <button class="carousel-control-next" type="button" data-bs-target="#roomCarousel-{{ $room->id }}" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Suivant</span>
              </button>
            </div>
          @elseif($room->photo)
            <img src="{{ asset($room->photo) }}" alt="{{ $room->name }}" class="card-img-top" style="object-fit:cover; height:160px;">
          @else
            <div class="d-flex align-items-center justify-content-center bg-light" style="height:160px;">
              <div class="text-muted small"><i class="bi bi-image me-1"></i>Pas d'image</div>
            </div>
          @endif
          <div class="p-3">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <h5 class="card-title mb-1">{{ $room->name }} <span class="badge text-bg-light border ms-1">{{ $room->type }}</span></h5>
                <div class="text-muted small">Capacité: {{ $room->capacity ?? '—' }}</div>
              </div>
              <div class="fw-semibold">{{ number_format($room->price_per_night, 0, ',', ' ') }} F CFA <span class="text-muted small">/ nuit</span></div>
            </div>
            <p class="mt-2">{{ $room->description }}</p>

            @php $upcomingSmall = ($room->bookings ?? collect())->where('status','!=','cancelled')->sortBy('start_date')->take(5); @endphp
            @if(($upcomingSmall->count() ?? 0) > 0)
              <div class="small text-muted mb-2">
                <strong>Indisponible:</strong>
                @foreach($upcomingSmall as $b)
                  <span class="badge text-bg-light border">{{ \Carbon\Carbon::parse($b->start_date)->format('d/m/Y') }} → {{ \Carbon\Carbon::parse($b->end_date)->format('d/m/Y') }}</span>
                @endforeach
              </div>
            @endif
            @if($errors->any())
              <div class="alert alert-danger small">
                <strong>Veuillez corriger les erreurs suivantes :</strong>
                <ul class="mb-0 mt-1">
                  @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif
            <form method="POST" action="{{ route('tourist.rooms.book', $room) }}" class="row g-2 room-booking-form" data-room-id="{{ $room->id }}">
              @csrf
              <div class="col-sm-5">
                <input type="date" name="start_date" value="{{ old('start_date') }}" class="form-control @error('start_date') is-invalid @enderror room-start" required>
                @error('start_date')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="col-sm-5">
                <input type="date" name="end_date" value="{{ old('end_date') }}" class="form-control @error('end_date') is-invalid @enderror room-end" required>
                @error('end_date')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="col-sm-2 d-grid">
                <button class="btn btn-primary btn-sm">Réserver</button>
              </div>
            </form>
            <script>
              window.__roomBookings = window.__roomBookings || {};
              window.__roomBookings[{{ $room->id }}] = @json($upcoming);
            </script>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12"><div class="alert alert-info">Aucune chambre listée.</div></div>
    @endforelse
  </div>
</div>
@endsection

@push('styles')
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
  <style>
    /* Recap sticky only on >= lg to avoid overlap on small screens */
    .recap-card{ position: static; }
    @media (min-width: 992px){ .recap-card{ position: sticky; top: 100px; } }
  </style>
@endpush

@push('scripts')
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
  <script>
    document.addEventListener('DOMContentLoaded', function(){
      var el = document.getElementById('hotel-map');
      if(!el) return;
      var lat = {{ $hotel->latitude ?? 'null' }};
      var lng = {{ $hotel->longitude ?? 'null' }};
      if(lat===null || lng===null){ return; }
      var map = L.map('hotel-map');
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap contributors' }).addTo(map);
      var marker = L.marker([lat, lng]).addTo(map).bindPopup(`{{ addslashes($hotel->name) }}<br>{{ addslashes($hotel->city) }}, {{ addslashes($hotel->country) }}`);
      marker.openPopup();
      map.setView([lat, lng], 14);
    });
  </script>
  <script>
    (function(){
      function ready(fn){ if(document.readyState!=='loading') fn(); else document.addEventListener('DOMContentLoaded', fn); }
      function fmtDate(d){ if(!d) return null; var x=new Date(d); if(isNaN(x)) return null; return x; }
      function daysBetween(a,b){ if(!a||!b) return 0; return Math.max(1, Math.round((b-a)/(1000*60*60*24))); }
      function overlaps(startA, endA, startB, endB){ return (startA < endB) && (endA > startB); }
      ready(function(){
        var startEl = document.getElementById('filterStart');
        var endEl   = document.getElementById('filterEnd');
        var searchEl= document.getElementById('filterSearch');
        var typeEl  = document.getElementById('filterType');
        var sortEl  = document.getElementById('filterSort');
        var applyBtn= document.getElementById('applyFilters');
        var resetBtn= document.getElementById('resetFilters');
        var list    = document.getElementById('roomsList');
        var countEl = document.getElementById('roomsCount');
        var recapN  = document.getElementById('recapNights');

        function syncForms(){
          var sVal = startEl && startEl.value; var eVal = endEl && endEl.value;
          document.querySelectorAll('.room-booking-form').forEach(function(f){
            if(sVal) { var i=f.querySelector('.room-start'); if(i) i.value=sVal; }
            if(eVal) { var i=f.querySelector('.room-end'); if(i) i.value=eVal; }
          });
          if(recapN){
            var sd = fmtDate(sVal), ed = fmtDate(eVal);
            recapN.textContent = (sd && ed) ? daysBetween(sd,ed) : '1';
          }
        }

        function apply(){
          var q = (searchEl && searchEl.value || '').toLowerCase();
          var ty= (typeEl && typeEl.value) || '';
          var sd= fmtDate(startEl && startEl.value);
          var ed= fmtDate(endEl && endEl.value);
          var items = Array.from(list.querySelectorAll('.room-item'));
          var visible = 0;
          items.forEach(function(it){
            var name = (it.querySelector('.card-title')?.textContent || '').toLowerCase();
            var rtype= (it.getAttribute('data-type') || '').toLowerCase();
            var ok = true;
            if(q && !(name.includes(q) || rtype.includes(q))) ok = false;
            if(ok && ty && rtype !== ty.toLowerCase()) ok = false;
            if(ok && sd && ed){
              var rb = (window.__roomBookings || {})[it.getAttribute('data-room-id')] || [];
              // any overlap between [sd,ed] and [s,e]
              for(var i=0;i<rb.length;i++){
                var s = fmtDate(rb[i][0]);
                var e = fmtDate(rb[i][1]);
                if(s && e && overlaps(sd, ed, s, e)) { ok = false; break; }
              }
            }
            it.style.display = ok ? '' : 'none';
            if(ok) visible++;
          });
          if(countEl){ countEl.textContent = visible + ' chambre(s) disponible(s)'; }

          // sort visible ones
          var v = (sortEl && sortEl.value) || 'price_asc';
          var vis = items.filter(function(it){ return it.style.display !== 'none'; });
          vis.sort(function(a,b){
            var pa = parseInt(a.getAttribute('data-price')||'0',10);
            var pb = parseInt(b.getAttribute('data-price')||'0',10);
            var ca = parseInt(a.getAttribute('data-capacity')||'0',10);
            var cb = parseInt(b.getAttribute('data-capacity')||'0',10);
            switch(v){
              case 'price_desc': return pb - pa;
              case 'capacity_desc': return cb - ca;
              case 'capacity_asc': return ca - cb;
              case 'price_asc': default: return pa - pb;
            }
          });
          vis.forEach(function(el){ list.appendChild(el); });
          syncForms();
        }

        applyBtn && applyBtn.addEventListener('click', apply);
        resetBtn && resetBtn.addEventListener('click', function(){
          if(startEl) startEl.value=''; if(endEl) endEl.value=''; if(searchEl) searchEl.value=''; if(typeEl) typeEl.value=''; if(sortEl) sortEl.value='price_asc';
          apply();
        });
        [startEl,endEl,searchEl,typeEl,sortEl].forEach(function(el){ el && el.addEventListener('change', apply); });
        apply();
      });
    })();
  </script>
@endpush
