@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <nav aria-label="breadcrumb" class="mb-2">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('/') }}">Accueil</a></li>
      <li class="breadcrumb-item"><a href="{{ route('food.restaurants.index') }}">Restaurants</a></li>
      <li class="breadcrumb-item active" aria-current="page">{{ $restaurant->name }}</li>
    </ol>
  </nav>

  <div class="d-flex align-items-end flex-wrap gap-2 mb-3">
    <div>
      <h1 class="mb-0">{{ $restaurant->name }}</h1>
      <div class="text-muted">{{ $restaurant->address }}, {{ $restaurant->city }}</div>
    </div>
    <a class="btn btn-orange ms-auto" href="{{ route('food.restaurants.reserve', $restaurant) }}"><i class="bi bi-calendar-check me-1"></i> Réserver une table</a>
  </div>

  <x-ad-banner placement="restaurant_show_top" />

  <div class="panel-cream rounded-20 overflow-hidden">
    @if($restaurant->cover_image)
      <img src="{{ \Illuminate\Support\Str::startsWith($restaurant->cover_image, ['http://','https://','/']) ? $restaurant->cover_image : asset('storage/'.$restaurant->cover_image) }}" class="w-100" style="height:260px; object-fit:cover;" alt="{{ $restaurant->name }}">
    @endif
    <div class="p-3">
      <p class="mb-2">{{ $restaurant->description }}</p>
      <div class="small text-muted mb-3"><i class="bi bi-star-fill text-warning me-1"></i>{{ number_format($restaurant->rating ?? 0, 1) }} • Prix moyen: {{ $restaurant->avg_price ? number_format($restaurant->avg_price, 0, ',', ' ') . ' CFA' : '—' }}</div>

      @if(!empty($restaurant->gallery))
        <h5 class="mt-3">Galerie</h5>
        <div class="row g-2">
          @foreach($restaurant->gallery as $img)
            <div class="col-6 col-md-4 col-lg-3">
              <a href="{{ $img }}" target="_blank" class="d-block">
                <img src="{{ $img }}" class="img-fluid rounded-3 img-elevate" alt="Photo du cadre">
              </a>
            </div>
          @endforeach
        </div>
      @endif

      @if(!empty($restaurant->video_urls))
        <h5 class="mt-4">Vidéos</h5>
        <div class="row g-3">
          @foreach($restaurant->video_urls as $vid)
            <div class="col-12 col-md-6">
              @php
                $isYoutube = \Illuminate\Support\Str::contains($vid, ['youtube.com/watch', 'youtu.be/']);
                $embed = $vid;
                if ($isYoutube) {
                  // Convert to embed URL
                  if (\Illuminate\Support\Str::contains($vid, 'watch?v=')) {
                    $embed = 'https://www.youtube.com/embed/'.\Illuminate\Support\Str::after($vid, 'watch?v=');
                  } elseif (\Illuminate\Support\Str::contains($vid, 'youtu.be/')) {
                    $embed = 'https://www.youtube.com/embed/'.\Illuminate\Support\Str::after($vid, 'youtu.be/');
                  }
                }
              @endphp
              @if($isYoutube)
                <div class="ratio ratio-16x9">
                  <iframe src="{{ $embed }}" title="Vidéo" allowfullscreen loading="lazy"></iframe>
                </div>
              @else
                <video src="{{ $vid }}" class="w-100 rounded-3" controls></video>
              @endif
            </div>
          @endforeach
        </div>
      @endif

      @if($restaurant->latitude && $restaurant->longitude)
        <h5 class="mt-4">Localisation</h5>
        <div id="map-restaurant" class="w-100" style="height:260px; border-radius:12px; overflow:hidden;"></div>
        <div class="d-flex align-items-center gap-2 mt-2">
          <a href="https://maps.google.com/?q={{ $restaurant->latitude }},{{ $restaurant->longitude }}" target="_blank" class="btn btn-outline-primary btn-sm"><i class="bi bi-geo-alt"></i> Ouvrir dans Google Maps</a>
          <span class="text-muted small">{{ $restaurant->latitude }}, {{ $restaurant->longitude }}</span>
        </div>
      @elseif($restaurant->map_url)
        <h5 class="mt-4">Localisation</h5>
        <a href="{{ $restaurant->map_url }}" target="_blank" class="btn btn-outline-primary btn-sm"><i class="bi bi-geo-alt"></i> Ouvrir la carte</a>
      @endif

      <h5 class="mt-3">Plats</h5>
      <div class="row g-3">
        @forelse($restaurant->dishes as $dish)
          <div class="col-md-6">
            <div class="border rounded-3 p-3 h-100">
              <div class="d-flex align-items-start gap-3">
                @php
                  $thumb = $dish->image_path ?? (($dish->gallery[0] ?? null));
                @endphp
                @if($thumb)
                  <img src="{{ \Illuminate\Support\Str::startsWith($thumb, ['http://','https://','/']) ? $thumb : asset('storage/'.$thumb) }}" alt="{{ $dish->name }}" style="width:80px;height:80px;object-fit:cover;border-radius:8px;">
                @endif
                <div class="flex-grow-1">
                  <div class="fw-semibold">{{ $dish->name }}</div>
                  <div class="small text-muted">{{ $dish->category ?? '' }}</div>
                  <div class="mt-2">{{ number_format($dish->price, 0, ',', ' ') }} CFA</div>
                  @if(!empty($dish->gallery))
                    <div class="d-flex gap-2 mt-2 flex-wrap">
                      @foreach(array_slice($dish->gallery, 0, 4) as $g)
                        <a href="{{ $g }}" target="_blank">
                          <img src="{{ $g }}" alt="Photo plat" style="width:46px;height:46px;object-fit:cover;border-radius:6px;">
                        </a>
                      @endforeach
                    </div>
                  @endif
                  @if(!empty($dish->video_urls))
                    <div class="mt-2 small">
                      @foreach($dish->video_urls as $v)
                        <a href="{{ $v }}" target="_blank" class="me-2"><i class="bi bi-play-btn"></i> Vidéo</a>
                      @endforeach
                    </div>
                  @endif
                  <div class="mt-3 d-flex gap-2">
                    <a href="{{ route('food.dishes.show', $dish) }}" class="btn btn-sm btn-outline-secondary">Voir le plat</a>
                    <a href="{{ route('food.dishes.orders.create', $dish) }}" class="btn btn-sm btn-orange">Commander en livraison</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        @empty
          <div class="col-12"><div class="alert alert-info">Aucun plat enregistré.</div></div>
        @endforelse
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
          const map = L.map('map-restaurant').setView([lat, lng], 15);
          L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' }).addTo(map);
          L.marker([lat, lng]).addTo(map).bindPopup(@json($restaurant->name)).openPopup();
        }
      })();
    </script>
  @endif
@endpush
