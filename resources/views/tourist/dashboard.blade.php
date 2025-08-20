@extends('layouts.tourist')

@push('styles')
<style>
  /* Leaflet CSS is linked below */
  .dash-hero { background: linear-gradient(90deg, #ff7e5f 0%, #feb47b 100%); border: 0; color: #fff; }
  .dash-hero .subtitle { opacity: .9; }
  .dash-hero .btn-outline-light { border-color: rgba(255,255,255,.8); color: #fff; }
  .dash-hero .btn-outline-light:hover { background: rgba(255,255,255,.15); }
  .dash-card { border: 0; box-shadow: 0 8px 24px rgba(0,0,0,.06); }
  .dash-section-title { font-weight: 700; }
  .dash-badge { background: #fff; color: #212529; border: 0; }
  .thumb-cover { background-size: cover; background-position: center; }
  .muted-link { color: #6c757d; text-decoration: none; }
  .muted-link:hover { color: #343a40; }
  #miniMap { width: 180px; height: 120px; }
  @media (max-width: 767.98px){ #miniMap { width: 100%; height: 180px; } }
</style>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
@endpush

@section('content')
<div class="container py-4">
  <div class="row g-3">
    <!-- Left rail: Quick actions -->
    <!-- <aside class="col-lg-2 d-none d-lg-block">
      <div class="vstack gap-2">
        <div class="fw-semibold text-muted small">Actions rapides</div>
        <a class="btn btn-light text-start" href="{{ route('assistant.index') }}">Planifier un voyage</a>
        <a class="btn btn-light text-start" href="{{ route('air.flights.wizard') }}">Réserver un vol</a>
        <a class="btn btn-light text-start" href="{{ route('tourist.hotels.index') }}">Réserver un séjour</a>
        <a class="btn btn-light text-start" href="{{ route('events.index') }}">Explorer la carte</a>
        <div class="mt-2 fw-semibold text-muted small">Assistant</div>
        <a class="btn btn-outline-primary text-start" href="{{ route('assistant.index') }}">Assistant vocal</a>
        <a class="btn btn-light text-start" href="{{ route('tourist.community') }}">Communauté</a>
        <a class="btn btn-light text-start" href="{{ route('transport.taxi.index') }}">Réserver un taxi</a>
      </div>
    </aside> -->

    <!-- Main content -->
    <main class="col-12 col-lg-11 mx-auto">
      <!-- Hero header -->
      <div class="card dash-hero mb-3">
        <div class="card-body d-flex justify-content-between align-items-start flex-wrap gap-3">
          <div>
            <div class="h3 mb-1">Bienvenue, {{ $user?->name ?? 'Voyageur' }}</div>
            <div class="subtitle">Découvrez, planifiez et vivez le meilleur du Burkina Faso.</div>
            <div class="d-flex gap-2 mt-3">
              <a href="{{ route('assistant.index') }}" class="btn btn-outline-light"><i class="bi bi-robot me-1"></i> Assistant</a>
              <a href="{{ route('tourist.plan') }}" class="btn btn-light"><i class="bi bi-calendar3 me-1"></i> Planifier</a>
            </div>
          </div>
          <div class="ms-auto" style="min-width:220px;">
            <div class="rounded thumb-cover" style="width:100%;height:120px;background-image:url('{{ asset('assets/img/portfolio/portfolio-1.jpg') }}');"></div>
          </div>
        </div>
      </div>

      <!-- Two-column primary section -->
      <div class="row g-3">
        <div class="col-lg-8 vstack gap-3">
          <!-- Upcoming Events -->
          <div class="card dash-card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="dash-section-title">Évènements à venir</div>
                <a href="{{ route('events.index') }}" class="btn btn-sm btn-light">Voir tout</a>
              </div>
              <div class="row g-2">
                @forelse(($events ?? collect())->take(6) as $ev)
                  <div class="col-md-6">
                    <div class="border rounded p-2 h-100 d-flex justify-content-between align-items-start gap-2">
                      <div>
                        <div class="fw-semibold">{{ $ev->title ?? $ev->name ?? 'Évènement' }}</div>
                        <div class="small text-muted">{{ $ev->city ?? $ev->location ?? '—' }} · {{ $ev->starts_at ?? $ev->start_date ?? '' }}</div>
                      </div>
                      <div class="text-end">
                        <a href="{{ route('events.show', $ev) }}" class="btn btn-sm btn-outline-primary">Voir</a>
                      </div>
                    </div>
                  </div>
                @empty
                  <div class="col-12 text-muted small">Aucun évènement à venir.</div>
                @endforelse
              </div>
            </div>
          </div>
          <!-- Current Trip -->
          <div class="card dash-card">
            <div class="card-body d-flex justify-content-between gap-3 flex-wrap">
              <div>
                <div class="dash-section-title">Votre voyage</div>
                <div class="text-muted small">{{ $currentTrip['city'] ?? 'Burkina Faso' }}</div>
                <div class="mt-2 d-flex align-items-center gap-2">
                  @if(($currentTrip['start'] ?? null) && ($currentTrip['end'] ?? null))
                    <span class="badge dash-badge">{{ \Illuminate\Support\Carbon::parse($currentTrip['start'])->format('d M') }} – {{ \Illuminate\Support\Carbon::parse($currentTrip['end'])->format('d M') }}</span>
                  @else
                    <span class="badge dash-badge">Plan à définir</span>
                  @endif
                  <span class="badge dash-badge">2 voyageurs</span>
                </div>
                <div class="mt-3 d-flex gap-2">
                  <a href="{{ route('tourist.itinerary') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-share"></i> Partager</a>
                  <a href="{{ route('tourist.itinerary') }}" class="btn btn-sm btn-primary"><i class="bi bi-check2-circle"></i> Confirmer</a>
                </div>
              </div>
              <div class="ms-auto">
                <div id="miniMap" class="rounded overflow-hidden"></div>
              </div>
            </div>
          </div>

          <!-- Itinerary Today -->
          <div class="card dash-card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="dash-section-title">Aujourd'hui</div>
                <div class="btn-group btn-group-sm" role="group">
                  <button class="btn btn-light active" type="button">Day</button>
                  <button class="btn btn-light" type="button">3 Day</button>
                  <button class="btn btn-light" type="button">Week</button>
                </div>
              </div>
              <ul class="list-unstyled mb-0 vstack gap-2">
                @forelse(($todayItems ?? []) as $it)
                  <li class="d-flex justify-content-between align-items-center border rounded p-2">
                    <div>
                      <div class="fw-semibold">{{ $it['activity'] ?? 'Activity' }}</div>
                      <div class="small text-muted">{{ $it['date'] ?? $today }} · {{ $it['city'] ?? '' }}</div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                      <a href="{{ route('tourist.itinerary') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-right-circle"></i> Y aller</a>
                    </div>
                  </li>
                @empty
                  <li class="border rounded p-2 text-muted small">Rien pour aujourd'hui. <a href="{{ route('assistant.index') }}">Générer un itinéraire</a></li>
                @endforelse
              </ul>
            </div>
          </div>

          <!-- Explore Map & AR -->
          <div class="card dash-card">
            <div class="card-body">
              <div class="dash-section-title mb-2">Explorer la carte & AR</div>
              <div class="row g-3">
                <div class="col-md-7">
                  <div class="rounded thumb-cover" style="height:220px;background-image:url('{{ asset('assets/img/portfolio/portfolio-3.jpg') }}');"></div>
                </div>
                <div class="col-md-5">
                  <div class="rounded thumb-cover" style="height:220px;background-image:url('{{ asset('assets/img/portfolio/portfolio-4.jpg') }}');"></div>
                </div>
              </div>
              <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="d-flex gap-2">
                  <button class="btn btn-light btn-sm"><i class="bi bi-search"></i> Points d'intérêt</button>
                  <a class="btn btn-primary btn-sm" href="{{ route('tourist.itinerary') }}"><i class="bi bi-geo-alt"></i> Démarrer</a>
                </div>
                <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-headphones"></i> Audio guide</button>
              </div>
            </div>
          </div>

          <!-- Quick Bookings -->
          <div class="card dash-card">
            <div class="card-body">
              <div class="dash-section-title mb-2">Réservations rapides</div>
              <div class="row g-2">
                <div class="col-md-6">
                  <div class="p-2 border rounded mb-2 small text-muted"><i class="bi bi-airplane"></i> Vols</div>
                  @forelse($flights as $f)
                    <div class="border rounded p-2 d-flex justify-content-between align-items-center mb-2">
                      <div>
                        <div class="fw-semibold">{{ $f->airline ?? 'Compagnie aérienne' }} — {{ $f->flight_number ?? '' }}</div>
                        <div class="small text-muted">{{ $f->origin->city ?? '—' }} → {{ $f->destination->city ?? '—' }}</div>
                      </div>
                      <div class="text-end">
                        <div class="fw-semibold">{{ number_format($f->base_price ?? 0, 0) }} FCFA</div>
                        <a href="{{ route('air.flights.show', $f) }}" class="btn btn-sm btn-outline-primary mt-1">Voir</a>
                      </div>
                    </div>
                  @empty
                    <div class="text-muted small">Aucun vol trouvé.</div>
                  @endforelse
                </div>
                <div class="col-md-6">
                  <div class="p-2 border rounded mb-2 small text-muted"><i class="bi bi-calendar-event"></i> Évènements</div>
                  @forelse($events as $e)
                    <div class="border rounded p-2 d-flex justify-content-between align-items-center mb-2">
                      <div>
                        <div class="fw-semibold">{{ $e->title ?? 'Event' }}</div>
                        <div class="small text-muted">{{ $e->city ?? '—' }} · {{ $e->starts_at }}</div>
                      </div>
                      <div class="text-end">
                        <a href="{{ route('events.show', $e) }}" class="btn btn-sm btn-outline-primary">Voir</a>
                      </div>
                    </div>
                  @empty
                    <div class="text-muted small">No upcoming events.</div>
                  @endforelse
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-4 vstack gap-3">
          <!-- Notifications Widget -->
          @include('components.notifications-widget')

          <!-- Ads Sidebar Placement -->
          <x-ad-banner placement="dashboard_sidebar" />
          <!-- Recommendations -->
          <div class="card dash-card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="dash-section-title">Recommandations</div>
                <div class="btn-group btn-group-sm">
                  <button class="btn btn-light active">Activities</button>
                  <button class="btn btn-light">Food</button>
                  <button class="btn btn-light">Culture</button>
                </div>
              </div>
              <div class="vstack gap-2">
                @foreach($events->take(3) as $r)
                  <a href="{{ route('events.show', $r) }}" class="text-decoration-none">
                    <div class="border rounded p-2">
                      <div class="fw-semibold">{{ $r->title }}</div>
                      <div class="small text-muted">{{ $r->city ?? '—' }} · {{ $r->starts_at }}</div>
                    </div>
                  </a>
                @endforeach
              </div>
            </div>
          </div>

          <!-- Real-time Assistant -->
          <div class="card dash-card">
            <div class="card-body">
              <div class="dash-section-title mb-2">Assistant en temps réel</div>
              <div class="d-grid gap-2">
                <a href="{{ route('assistant.index') }}" class="btn btn-outline-primary">Parler à l'assistant</a>
                <a href="{{ route('assistant.index') }}" class="btn btn-light">Traduire</a>
              </div>
            </div>
          </div>

          <!-- Achievements -->
          <div class="card dash-card">
            <div class="card-body">
              <div class="dash-section-title mb-2">Succès & progression</div>
              <div class="small text-muted">Keep exploring to unlock badges!</div>
              <div class="d-flex gap-2 mt-2 flex-wrap">
                <span class="badge text-bg-light">City Explorer</span>
                <span class="badge text-bg-light">Cultural Enthusiast</span>
                <span class="badge text-bg-light">Foodie</span>
              </div>
            </div>
          </div>

          <!-- Community & Reviews -->
          <div class="card dash-card">
            <div class="card-body">
              <div class="dash-section-title mb-2">Communauté & avis</div>
              <div class="vstack gap-2 small">
                <div class="border rounded p-2">Fatou — "Le meilleur maquis pour le tô ?" <a href="{{ route('events.index') }}" class="ms-1">Répondre</a></div>
                <div class="border rounded p-2">Issa — "Prix du taxi depuis OUA ?" <a href="{{ route('transport.taxi.index') }}" class="ms-1">Répondre</a></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Bottom nav mimic -->
      <div class="position-sticky bottom-0 mt-3">
        <div class="card dash-card">
          <div class="card-body d-flex justify-content-around">
            <a href="{{ route('tourist.dashboard') }}" class="muted-link"><i class="bi bi-house"></i> Accueil</a>
            <a href="{{ route('tourist.itinerary') }}" class="muted-link"><i class="bi bi-map"></i> Itinéraire</a>
            <a href="{{ route('events.index') }}" class="muted-link"><i class="bi bi-bag"></i> Réservations</a>
            <a href="{{ route('events.index') }}" class="muted-link"><i class="bi bi-compass"></i> Explorer</a>
            <a href="{{ route('profile.edit') }}" class="muted-link"><i class="bi bi-person"></i> Profil</a>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    var el = document.getElementById('miniMap');
    if (!el) return;
    // Default center: Ouagadougou
    var center = [12.3713, -1.5197];
    var city = @json($currentTrip['city'] ?? null);
    var LUT = {
      'Ouagadougou': [12.3713, -1.5197],
      'Bobo-Dioulasso': [11.1771, -4.2979],
      'Banfora': [10.6333, -4.7667],
      'Koudougou': [12.2526, -2.3627],
      'Nazinga': [11.25, -1.6667]
    };
    if (city && LUT[city]) center = LUT[city];

    var map = L.map('miniMap', { zoomControl: false, attributionControl: false }).setView(center, 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);
    L.marker(center).addTo(map);
  });
</script>
@endpush
