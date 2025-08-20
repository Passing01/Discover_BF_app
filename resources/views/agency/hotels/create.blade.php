@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <h1 class="mb-3 text-orange">Créer un hôtel</h1>

  <form method="POST" action="{{ route('agency.hotels.store') }}" enctype="multipart/form-data" class="panel-cream rounded-20 p-3 p-md-4">
    @csrf
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Nom</label>
        <input type="text" name="name" class="form-control" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Ville</label>
        <input type="text" name="city" class="form-control" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Pays</label>
        <input type="text" name="country" class="form-control" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Adresse</label>
        <input type="text" name="address" class="form-control">
      </div>
      <div class="col-md-6">
        <label class="form-label">Téléphone</label>
        <input type="text" name="phone" class="form-control">
      </div>
      <div class="col-md-6">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control">
      </div>
      <div class="col-md-6">
        <label class="form-label">Étoiles</label>
        <input type="number" min="1" max="5" name="stars" class="form-control">
      </div>
      <div class="col-md-6">
        <label class="form-label">Photo (optionnel)</label>
        <input type="file" name="photo" accept="image/*" class="form-control">
      </div>
      <div class="col-md-6">
        <label class="form-label">Latitude <span class="text-muted small">(optionnel)</span></label>
        <input type="number" step="0.00000001" min="-90" max="90" name="latitude" class="form-control" placeholder="Auto-remplie depuis l'adresse si vide">
      </div>
      <div class="col-md-6">
        <label class="form-label">Longitude <span class="text-muted small">(optionnel)</span></label>
        <input type="number" step="0.00000001" min="-180" max="180" name="longitude" class="form-control" placeholder="Auto-remplie depuis l'adresse si vide">
      </div>
      <div class="col-12">
        <label class="form-label">Position sur la carte (cliquez pour définir)</label>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
        <div id="hotel-map-picker" style="width:100%; height: 320px; border-radius:8px; overflow:hidden;"></div>
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script>
          (function(){
            var map = L.map('hotel-map-picker');
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' }).addTo(map);
            var latInput = document.querySelector('input[name="latitude"]');
            var lngInput = document.querySelector('input[name="longitude"]');
            var marker = null;
            function setMarker(lat, lng){
              if (marker) { marker.setLatLng([lat,lng]); } else { marker = L.marker([lat,lng]).addTo(map); }
            }
            // Default view (Ouagadougou approx) or existing values
            var initLat = parseFloat(latInput.value) || 12.3686;
            var initLng = parseFloat(lngInput.value) || -1.5271;
            map.setView([initLat, initLng], 12);
            if (latInput.value && lngInput.value) setMarker(initLat, initLng);
            map.on('click', function(e){
              var lat = +e.latlng.lat.toFixed(6);
              var lng = +e.latlng.lng.toFixed(6);
              latInput.value = lat; lngInput.value = lng; setMarker(lat, lng);
            });
          })();
        </script>
      </div>
      <div class="col-md-6">
        <label class="form-label">Équipements</label>
        <div class="border rounded p-2" style="max-height:180px; overflow:auto;">
          @foreach(($amenities ?? []) as $amenity)
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="amenities[]" value="{{ $amenity->id }}" id="am-{{ $amenity->id }}">
              <label class="form-check-label" for="am-{{ $amenity->id }}">{{ $amenity->name }}</label>
            </div>
          @endforeach
          @if(empty($amenities) || count($amenities)===0)
            <div class="text-muted small">Aucun équipement défini.</div>
          @endif
        </div>
      </div>
      <div class="col-md-6">
        <label class="form-label">Règles du séjour</label>
        <div class="border rounded p-2" style="max-height:180px; overflow:auto;">
          @foreach(($rules ?? []) as $rule)
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="rules[]" value="{{ $rule->id }}" id="rl-{{ $rule->id }}">
              <label class="form-check-label" for="rl-{{ $rule->id }}">{{ $rule->name }}</label>
            </div>
          @endforeach
          @if(empty($rules) || count($rules)===0)
            <div class="text-muted small">Aucune règle définie.</div>
          @endif
        </div>
      </div>
      <div class="col-12">
        <label class="form-label">Galerie photos (plusieurs)</label>
        <input type="file" name="gallery[]" accept="image/*" class="form-control" multiple>
      </div>
      <div class="col-12">
        <label class="form-label">Description</label>
        <textarea name="description" rows="5" class="form-control"></textarea>
      </div>
      <div class="col-12">
        <button class="btn btn-orange">Enregistrer</button>
        <a href="{{ route('agency.hotels.index') }}" class="btn btn-cream">Annuler</a>
      </div>
    </div>
  </form>
</div>
@endsection
