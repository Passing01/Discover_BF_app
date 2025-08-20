@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <nav aria-label="breadcrumb" class="mb-2">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('/') }}">Accueil</a></li>
      <li class="breadcrumb-item"><a href="{{ route('events.index') }}">Agenda culturel</a></li>
      <li class="breadcrumb-item active" aria-current="page">{{ $event->name }}</li>
    </ol>
  </nav>
  <div class="row g-3">
    <div class="col-lg-8 vstack gap-3">
      <div class="panel-cream rounded-20 overflow-hidden">
        @if(!empty($event->image_path))
          <img src="{{ asset('storage/'.$event->image_path) }}" class="w-100" style="max-height:340px; object-fit:cover;" alt="{{ $event->name }}">
        @endif
        <div class="p-3">
          <h1 class="mb-1">{{ $event->name }}</h1>
          <div class="text-muted mb-2"><i class="bi bi-geo-alt me-1"></i>{{ $event->location }} • <i class="bi bi-calendar3 mx-1"></i>{{ $event->start_date }} → {{ $event->end_date }}</div>
          <div class="d-flex flex-wrap gap-2 mb-2">
            <span class="badge text-bg-light border"><i class="bi bi-ticket-perforated me-1"></i>Billets</span>
            @if(($event->category ?? null))
              <span class="badge text-bg-light border"><i class="bi bi-tags me-1"></i>{{ $event->category }}</span>
            @endif
          </div>
          <div class="d-flex flex-wrap gap-2 mb-3">
            <a href="#" class="btn btn-cream btn-sm"><i class="bi bi-share me-1"></i>Partager</a>
            <a href="#" class="btn btn-cream btn-sm"><i class="bi bi-bookmark me-1"></i>Sauver</a>
            @if(auth()->check() && auth()->id() === ($event->organizer_id ?? null))
              <button class="btn btn-secondary btn-sm" type="button" disabled><i class="bi bi-lock me-1"></i>Réserver</button>
              <a href="{{ route('organizer.events.edit', $event) }}" class="btn btn-orange btn-sm"><i class="bi bi-pencil me-1"></i>Modifier</a>
              <form action="{{ route('organizer.events.destroy', $event) }}" method="post" onsubmit="return confirm('Supprimer cet évènement ? Cette action est irréversible.');" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash me-1"></i>Supprimer</button>
              </form>
            @else
              <a class="btn btn-primary btn-sm" href="{{ route('bookings.create', $event) }}"><i class="bi bi-cart"></i> Réserver</a>
            @endif
          </div>
          <p class="mb-0">{{ $event->description }}</p>
        </div>
      </div>

      @if($event->ticketTypes && $event->ticketTypes->count())
        <div class="panel-cream rounded-20">
          <div class="px-3 py-2 fw-semibold">Billets disponibles</div>
          <div class="p-3 vstack gap-2">
            @foreach($event->ticketTypes as $tt)
              <div class="d-flex justify-content-between align-items-center border rounded p-2 bg-white">
                <div>
                  <div class="fw-semibold">{{ $tt->name }}</div>
                  @if(!empty($tt->description))<div class="small text-muted">{{ $tt->description }}</div>@endif
                </div>
                <div class="text-end">
                  <div class="fw-semibold">{{ number_format($tt->price, 0, ',', ' ') }} CFA</div>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      @endif

      @if(!is_null($event->latitude ?? null) && !is_null($event->longitude ?? null))
        <div class="panel-cream rounded-20 overflow-hidden">
          <div class="px-3 py-2 fw-semibold">Emplacement</div>
          <div class="p-0">
            <div id="event-map" style="width:100%; height: 300px;"></div>
            <div class="p-3">
              <a class="small" target="_blank" rel="noopener" href="https://www.openstreetmap.org/?mlat={{ $event->latitude }}&mlon={{ $event->longitude }}#map=14/{{ $event->latitude }}/{{ $event->longitude }}">Voir sur OpenStreetMap</a>
            </div>
          </div>
        </div>
      @endif
    </div>

    <div class="col-lg-4">
      <div class="panel-cream rounded-20 overflow-hidden position-sticky" style="top:100px;">
        <div class="px-3 py-2 fw-semibold">Réserver maintenant</div>
        <div class="p-3">
          <p class="small text-muted">Assurez votre place pour cet évènement.</p>
          @if(auth()->check() && auth()->id() === ($event->organizer_id ?? null))
            <div class="alert alert-info mb-0">Vous êtes l'organisateur de cet évènement. La réservation est désactivée.</div>
          @else
            <a class="btn btn-primary w-100" href="{{ route('bookings.create', $event) }}">Continuer</a>
          @endif
          @if(session('status'))
            <div class="alert alert-success mt-3 mb-0">{{ session('status') }}</div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
  @if(!is_null($event->latitude ?? null) && !is_null($event->longitude ?? null))
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
  @endif
@endpush

@push('scripts')
  @if(!is_null($event->latitude ?? null) && !is_null($event->longitude ?? null))
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
      document.addEventListener('DOMContentLoaded', function(){
        var el = document.getElementById('event-map');
        if(!el) return;
        var lat = {{ $event->latitude }};
        var lng = {{ $event->longitude }};
        var map = L.map('event-map');
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap contributors' }).addTo(map);
        var m = L.marker([lat, lng]).addTo(map).bindPopup(`{{ addslashes($event->name) }}<br>{{ addslashes($event->location) }}`);
        m.openPopup();
        map.setView([lat, lng], 14);
      });
    </script>
  @endif
@endpush
