@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <nav aria-label="breadcrumb" class="mb-2">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('/') }}">Accueil</a></li>
      <li class="breadcrumb-item"><a href="{{ route('food.restaurants.index') }}">Restaurants</a></li>
      <li class="breadcrumb-item"><a href="{{ route('food.restaurants.show', $restaurant) }}">{{ $restaurant->name }}</a></li>
      <li class="breadcrumb-item active" aria-current="page">Réserver</li>
    </ol>
  </nav>

  <h1 class="mb-3">Réserver une table</h1>

  <div class="row g-3">
    <div class="col-lg-7">
      <div class="panel-cream rounded-20 p-3">
        <form method="post" action="{{ route('food.restaurants.reserve.store', $restaurant) }}">
          @csrf
          <div class="mb-3">
            <label class="form-label">Date & heure</label>
            <input type="datetime-local" name="reservation_at" class="form-control @error('reservation_at') is-invalid @enderror" value="{{ old('reservation_at') }}" required>
            @error('reservation_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Nombre de personnes</label>
            <input type="number" min="1" max="20" name="party_size" class="form-control @error('party_size') is-invalid @enderror" value="{{ old('party_size', 2) }}" required>
            @error('party_size')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          @if($restaurant->dishes->count())
          <div class="mb-3">
            <label class="form-label">Pré-commander des plats (optionnel)</label>
            <div class="row g-2">
              @foreach($restaurant->dishes as $dish)
                <div class="col-12">
                  <div class="d-flex align-items-center gap-3 border rounded-3 p-2">
                    @php $thumb = $dish->image_path ?? ($dish->gallery[0] ?? null); @endphp
                    @if($thumb)
                      <img src="{{ \Illuminate\Support\Str::startsWith($thumb, ['http://','https://','/']) ? $thumb : asset('storage/'.$thumb) }}" alt="{{ $dish->name }}" style="width:56px;height:56px;object-fit:cover;border-radius:8px;">
                    @endif
                    <div class="flex-grow-1">
                      <div class="fw-semibold">{{ $dish->name }}</div>
                      <div class="small text-muted">{{ number_format($dish->price, 0, ',', ' ') }} CFA</div>
                    </div>
                    <div style="width:110px;">
                      <input type="number" min="0" max="50" name="items[{{ $dish->id }}]" class="form-control form-control-sm" value="{{ old('items.'.$dish->id, 0) }}" placeholder="Qté">
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
          @endif
          <div class="mb-3">
            <label class="form-label">Demandes spéciales (optionnel)</label>
            <textarea name="special_requests" class="form-control @error('special_requests') is-invalid @enderror" rows="3" placeholder="Allergies, préférences, occasion spéciale...">{{ old('special_requests') }}</textarea>
            @error('special_requests')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="d-flex gap-2">
            <a href="{{ route('food.restaurants.show', $restaurant) }}" class="btn btn-cream">Annuler</a>
            <button class="btn btn-orange" type="submit"><i class="bi bi-check2-circle me-1"></i> Valider</button>
          </div>
        </form>
      </div>
    </div>
    <div class="col-lg-5">
      <div class="border rounded-3 p-3 h-100">
        <div class="d-flex align-items-start gap-3">
          @if($restaurant->cover_image)
            <img src="{{ \Illuminate\Support\Str::startsWith($restaurant->cover_image, ['http://','https://','/']) ? $restaurant->cover_image : asset('storage/'.$restaurant->cover_image) }}" alt="{{ $restaurant->name }}" style="width:120px;height:120px;object-fit:cover;border-radius:10px;">
          @endif
          <div class="flex-grow-1">
            <div class="fw-semibold">{{ $restaurant->name }}</div>
            <div class="small text-muted">{{ $restaurant->address }}, {{ $restaurant->city }}</div>
            <div class="small text-muted mt-1"><i class="bi bi-star-fill text-warning me-1"></i>{{ number_format($restaurant->rating ?? 0, 1) }} • Prix moyen: {{ $restaurant->avg_price ? number_format($restaurant->avg_price, 0, ',', ' ') . ' CFA' : '—' }}</div>
          </div>
        </div>
        <div class="mt-3">
          <x-ad-banner placement="restaurant_reserve_sidebar" />
        </div>
        @if($restaurant->latitude && $restaurant->longitude)
          <div class="mt-3">
            <h6 class="mb-2">Localisation</h6>
            <div id="map-reserve" style="height:220px; border-radius:10px; overflow:hidden;"></div>
            <a href="https://maps.google.com/?q={{ $restaurant->latitude }},{{ $restaurant->longitude }}" target="_blank" class="btn btn-outline-primary btn-sm mt-2"><i class="bi bi-geo-alt"></i> Ouvrir dans Google Maps</a>
          </div>
        @elseif($restaurant->map_url)
          <div class="mt-3">
            <h6 class="mb-2">Localisation</h6>
            <a href="{{ $restaurant->map_url }}" target="_blank" class="btn btn-outline-primary btn-sm"><i class="bi bi-geo-alt"></i> Ouvrir la carte</a>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
@endpush

@push('scripts')
  @if($restaurant->latitude && $restaurant->longitude)
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
      (function(){
        const lat = {{ $restaurant->latitude ?? 'null' }};
        const lng = {{ $restaurant->longitude ?? 'null' }};
        if (lat && lng) {
          const map = L.map('map-reserve').setView([lat, lng], 15);
          L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' }).addTo(map);
          L.marker([lat, lng]).addTo(map).bindPopup(@json($restaurant->name)).openPopup();
        }
      })();
    </script>
  @endif
@endpush
