@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <nav aria-label="breadcrumb" class="mb-2">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('/') }}">Accueil</a></li>
      <li class="breadcrumb-item"><a href="{{ route('food.restaurants.index') }}">Restaurants</a></li>
      <li class="breadcrumb-item"><a href="{{ route('food.restaurants.show', $dish->restaurant) }}">{{ $dish->restaurant->name }}</a></li>
      <li class="breadcrumb-item active" aria-current="page">{{ $dish->name }}</li>
    </ol>
  </nav>

  <div class="row g-3">
    <div class="col-lg-8">
      <div class="panel-cream rounded-20 p-3">
        <div class="d-flex align-items-start gap-3">
          @php $thumb = $dish->image_path ?? ($dish->gallery[0] ?? null); @endphp
          @if($thumb)
            <img src="{{ \Illuminate\Support\Str::startsWith($thumb, ['http://','https://','/']) ? $thumb : asset('storage/'.$thumb) }}" alt="{{ $dish->name }}" style="width:120px;height:120px;object-fit:cover;border-radius:12px;">
          @endif
          <div>
            <h1 class="h4 mb-1">{{ $dish->name }}</h1>
            <div class="text-muted small">Catégorie: {{ $dish->category ?? '—' }}</div>
            <div class="mt-2 fw-semibold">{{ number_format($dish->price, 0, ',', ' ') }} CFA</div>
            <div class="mt-3">
              <a href="{{ route('food.dishes.orders.create', $dish) }}" class="btn btn-orange btn-sm">Commander en livraison</a>
            </div>
          </div>
        </div>

        @if($dish->description)
          <hr>
          <p class="mb-0">{{ $dish->description }}</p>
        @endif

        @if(!empty($dish->gallery))
          <hr>
          <h6>Photos</h6>
          <div class="row g-2">
            @foreach($dish->gallery as $img)
              <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ $img }}" target="_blank"><img src="{{ $img }}" class="img-fluid rounded-3 img-elevate" alt="Photo"></a>
              </div>
            @endforeach
          </div>
        @endif

        @if(!empty($dish->video_urls))
          <hr>
          <h6>Vidéos</h6>
          <div class="row g-3">
            @foreach($dish->video_urls as $vid)
              <div class="col-12 col-md-6">
                @php
                  $isYoutube = \Illuminate\Support\Str::contains($vid, ['youtube.com/watch', 'youtu.be/']);
                  $embed = $vid;
                  if ($isYoutube) {
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
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card shadow-sm">
        <div class="card-body">
          <h6 class="card-title mb-2">Restaurant</h6>
          <div class="fw-semibold">{{ $dish->restaurant->name }}</div>
          <div class="small text-muted">{{ $dish->restaurant->address }}, {{ $dish->restaurant->city }}</div>
          @if($dish->restaurant->latitude && $dish->restaurant->longitude)
            <div id="map-dish" class="mt-2" style="height:220px;border-radius:10px;overflow:hidden;"></div>
            <a href="https://maps.google.com/?q={{ $dish->restaurant->latitude }},{{ $dish->restaurant->longitude }}" target="_blank" class="btn btn-outline-primary btn-sm mt-2"><i class="bi bi-geo-alt"></i> Ouvrir dans Google Maps</a>
          @elseif($dish->restaurant->map_url)
            <a href="{{ $dish->restaurant->map_url }}" target="_blank" class="btn btn-outline-primary btn-sm mt-2"><i class="bi bi-geo-alt"></i> Ouvrir la carte</a>
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
  @if($dish->restaurant->latitude && $dish->restaurant->longitude)
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
      (function(){
        const lat = {{ $dish->restaurant->latitude ?? 'null' }};
        const lng = {{ $dish->restaurant->longitude ?? 'null' }};
        if (lat && lng) {
          const map = L.map('map-dish').setView([lat, lng], 15);
          L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' }).addTo(map);
          L.marker([lat, lng]).addTo(map).bindPopup(@json($dish->restaurant->name)).openPopup();
        }
      })();
    </script>
  @endif
@endpush
