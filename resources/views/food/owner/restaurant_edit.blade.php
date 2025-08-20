@extends('layouts.tourist')

@section('title', 'Mon Restaurant')

@section('content')
<div class="container py-4">
  <h2 class="mb-3"><i class="bi bi-shop me-2"></i>Gérer mon restaurant</h2>

  @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <div class="row g-4">
    <div class="col-lg-8">
      <div class="card shadow-sm">
        <div class="card-body">
          <form action="{{ route('food.owner.restaurant.update') }}" method="post" enctype="multipart/form-data">
            @csrf

            <div class="row g-3">
              <div class="col-md-8">
                <label class="form-label">Nom</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $restaurant->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-4">
                <label class="form-label">Prix moyen (CFA)</label>
                <input type="number" step="0.01" name="avg_price" class="form-control @error('avg_price') is-invalid @enderror" value="{{ old('avg_price', $restaurant->avg_price) }}">
                @error('avg_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-8">
                <label class="form-label">Adresse</label>
                <input type="text" name="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address', $restaurant->address) }}">
                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-4">
                <label class="form-label">Ville</label>
                <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city', $restaurant->city) }}">
                @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-3">
                <label class="form-label">Latitude</label>
                <input type="text" name="latitude" class="form-control @error('latitude') is-invalid @enderror" value="{{ old('latitude', $restaurant->latitude) }}">
                @error('latitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-3">
                <label class="form-label">Longitude</label>
                <input type="text" name="longitude" class="form-control @error('longitude') is-invalid @enderror" value="{{ old('longitude', $restaurant->longitude) }}">
                @error('longitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label class="form-label">Lien Google Maps (optionnel)</label>
                <input type="url" name="map_url" class="form-control @error('map_url') is-invalid @enderror" value="{{ old('map_url', $restaurant->map_url) }}">
                @error('map_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="col-12">
                <label class="form-label">Description</label>
                <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description', $restaurant->description) }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="col-md-6">
                <label class="form-label">Image de couverture</label>
                <input type="file" name="cover_image" class="form-control @error('cover_image') is-invalid @enderror" accept="image/*">
                @error('cover_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                @if($restaurant->cover_image)
                  <div class="mt-2">
                    <img src="{{ \Illuminate\Support\Str::startsWith($restaurant->cover_image, ['http://','https://','/']) ? $restaurant->cover_image : asset('storage/'.$restaurant->cover_image) }}" style="height:90px;object-fit:cover;border-radius:8px;">
                  </div>
                @endif
              </div>

              <div class="col-md-6">
                <label class="form-label">Galerie (plusieurs images)</label>
                <input type="file" name="gallery[]" class="form-control @error('gallery.*') is-invalid @enderror" accept="image/*" multiple>
                @error('gallery.*')<div class="invalid-feedback">{{ $message }}</div>@enderror
                @if(!empty($restaurant->gallery))
                  <div class="d-flex gap-2 flex-wrap mt-2">
                    @foreach($restaurant->gallery as $g)
                      <img src="{{ \Illuminate\Support\Str::startsWith($g, ['http://','https://','/']) ? $g : asset('storage/'.$g) }}" style="width:70px;height:70px;object-fit:cover;border-radius:6px;">
                    @endforeach
                  </div>
                @endif
              </div>

              <div class="col-12">
                <label class="form-label">Vidéos (une URL par ligne)</label>
                <textarea name="video_urls" rows="3" class="form-control @error('video_urls') is-invalid @enderror">{{ old('video_urls', isset($restaurant->video_urls) ? implode("\n", $restaurant->video_urls) : '') }}</textarea>
                @error('video_urls')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>

            <div class="mt-3 d-flex gap-2">
              <button class="btn btn-primary" type="submit"><i class="bi bi-save me-1"></i>Enregistrer</button>
              <a href="{{ route('food.owner.dishes.index') }}" class="btn btn-outline-secondary">Gérer les plats</a>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Aperçu localisation</h5>
          <div id="map" style="height: 280px; border-radius: 10px; overflow:hidden;"></div>
          <div class="small text-muted mt-2">Cliquez sur la carte ou déplacez le marqueur pour définir la position. Les champs latitude/longitude seront mis à jour.</div>
          <div class="mt-2">
            @if($restaurant->map_url)
              <a class="btn btn-outline-primary btn-sm" target="_blank" href="{{ $restaurant->map_url }}"><i class="bi bi-geo-alt"></i> Ouvrir la carte</a>
            @elseif($restaurant->latitude && $restaurant->longitude)
              <a class="btn btn-outline-primary btn-sm" target="_blank" href="https://maps.google.com/?q={{ $restaurant->latitude }},{{ $restaurant->longitude }}"><i class="bi bi-geo-alt"></i> Ouvrir la carte</a>
            @else
              <div class="text-muted small">Renseignez la latitude/longitude ou un lien Google Maps.</div>
            @endif
          </div>
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
      const latInput = document.querySelector('input[name="latitude"]');
      const lngInput = document.querySelector('input[name="longitude"]');
      const startLat = parseFloat(latInput.value) || 12.3686; // Ouagadougou par défaut
      const startLng = parseFloat(lngInput.value) || -1.5275;
      const map = L.map('map').setView([startLat, startLng], (latInput.value && lngInput.value) ? 14 : 12);

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
      }).addTo(map);

      const marker = L.marker([startLat, startLng], {draggable: true}).addTo(map);

      function updateInputs(latlng){
        latInput.value = latlng.lat.toFixed(6);
        lngInput.value = latlng.lng.toFixed(6);
      }

      marker.on('dragend', function(e){
        updateInputs(e.target.getLatLng());
      });

      map.on('click', function(e){
        marker.setLatLng(e.latlng);
        updateInputs(e.latlng);
      });
    })();
  </script>
@endpush
