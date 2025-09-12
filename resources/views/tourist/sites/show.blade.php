@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <nav aria-label="breadcrumb" class="mb-2">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('/') }}">Accueil</a></li>
      <li class="breadcrumb-item"><a href="{{ route('sites.index') }}">Sites touristiques</a></li>
      <li class="breadcrumb-item active" aria-current="page">{{ $site->name }}</li>
    </ol>
  </nav>

  @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <div class="row g-3">
    <div class="col-12 col-lg-7">
      @if($site->photo_url)
        <div class="mb-3">
          <img src="{{ $site->photo_url }}" class="img-fluid rounded w-100" style="max-height:360px; object-fit:cover;" alt="{{ $site->name }}">
        </div>
      @endif
      <h1 class="mb-1">{{ $site->name }}</h1>
      <div class="text-muted mb-2">{{ $site->city }} @if($site->category) · {{ $site->category }} @endif</div>
      <div class="d-flex flex-wrap gap-2 mb-2">
        @if($site->city)
          <span class="badge text-bg-light border"><i class="bi bi-geo-alt me-1"></i>{{ $site->city }}</span>
        @endif
        @if($site->category)
          <span class="badge text-bg-light border"><i class="bi bi-tag me-1"></i>{{ $site->category }}</span>
        @endif
      </div>
      <div class="d-flex flex-wrap gap-2 mb-3">
        <a href="#" class="btn btn-cream btn-sm"><i class="bi bi-share me-1"></i>Partager</a>
        <a href="#" class="btn btn-cream btn-sm"><i class="bi bi-bookmark me-1"></i>Sauver dans l'itinéraire</a>
        @if($site->latitude && $site->longitude)
          <a href="#site-map" class="btn btn-cream btn-sm"><i class="bi bi-map me-1"></i>Voir la carte</a>
        @endif
      </div>
      @if($site->description)
        <p class="mb-3">{{ $site->description }}</p>
      @endif
      @if($site->latitude && $site->longitude)
        <div class="panel-cream rounded-20 overflow-hidden mb-3">
          <div class="px-3 py-2 fw-semibold">Emplacement</div>
          <div class="p-0">
            <div id="site-map" style="width:100%; height: 320px;"></div>
            <div class="p-3">
              <a class="small" target="_blank" rel="noopener" href="https://www.openstreetmap.org/?mlat={{ $site->latitude }}&mlon={{ $site->longitude }}#map=14/{{ $site->latitude }}/{{ $site->longitude }}">Voir sur OpenStreetMap</a>
            </div>
          </div>
        </div>
      @endif
    </div>

    <div class="col-12 col-lg-5">
      <div class="panel-cream rounded-20">
        <div class="px-3 py-2 fw-semibold">Contacter un guide</div>
        <div class="p-3">
          @php
            $isGuideAffiliated = false;
            if (auth()->check() && auth()->user()->role === 'guide') {
                $isGuideAffiliated = \App\Models\GuideContact::where('site_id', $site->id)
                    ->where('guide_id', auth()->id())
                    ->exists();
            }
          @endphp

          @if($guide)
            <div class="d-flex align-items-center mb-3">
              <div class="me-2">
                <span class="badge text-bg-light border">Guide</span>
              </div>
              <div>
                <strong>{{ $guide->first_name }} {{ $guide->last_name }}</strong>
                <div class="small text-muted">{{ $guide->email }}</div>
              </div>
            </div>
            @if($isGuideAffiliated)
              <div class="alert alert-warning mb-3">
                <i class="bi bi-exclamation-triangle me-1"></i> 
                @if(auth()->check() && $guide && auth()->id() === $guide->id)
                  <strong>Action non autorisée :</strong> Vous ne pouvez pas vous envoyer de message à vous-même.
                @else
                  Vous êtes déjà affilié à ce site touristique.
                @endif
              </div>
            @endif
          @else
            <div class="alert alert-warning">Aucun guide n'est disponible pour le moment. Vous pouvez quand même envoyer votre demande.</div>
          @endif

          <form method="post" action="{{ route('sites.contact', $site->id) }}">
            @csrf
            <div class="mb-3">
              <label class="form-label">Votre nom</label>
              <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', auth()->user()->name ?? '') }}" required>
              @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', auth()->user()->email ?? '') }}" required>
              @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
              <label class="form-label">Téléphone (optionnel)</label>
              <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
              @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
              <label class="form-label">Message</label>
              <textarea name="message" rows="4" class="form-control @error('message') is-invalid @enderror" placeholder="Dates souhaitées, nombre de personnes, préférences..." required>{{ old('message') }}</textarea>
              @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="btn btn-orange w-100" @if($isGuideAffiliated) disabled @endif>
              @if($isGuideAffiliated)
                <i class="bi bi-check-circle me-1"></i> Déjà affilié
              @else
                Envoyer la demande
              @endif
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
  @if($site->latitude && $site->longitude)
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
  @endif
@endpush

@push('scripts')
  @if($site->latitude && $site->longitude)
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
      document.addEventListener('DOMContentLoaded', function(){
        var el = document.getElementById('site-map');
        if(!el) return;
        var lat = {{ $site->latitude ?? 'null' }};
        var lng = {{ $site->longitude ?? 'null' }};
        if(lat===null || lng===null){ return; }
        var map = L.map('site-map');
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap contributors' }).addTo(map);
        var marker = L.marker([lat, lng]).addTo(map).bindPopup(`{{ addslashes($site->name) }}<br>{{ addslashes($site->city) }}`);
        marker.openPopup();
        map.setView([lat, lng], 14);
      });
    </script>
  @endif
@endpush
