@extends('layouts.tourist')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/dashboard-styles.css') }}">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" 
      crossorigin=""/>
<style>
  .dashboard-container {
    padding: 1.5rem;
    max-width: 1400px;
    margin: 0 auto;
  }

  .dashboard-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 1.5rem;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    will-change: transform, box-shadow;
  }

  .dashboard-card.hover-scale {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

  .dashboard-card.hover-scale:hover {
    transform: scale(1.02);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
  }

  .dashboard-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
  }

  .ad-banner {
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
  }

  .ad-banner:hover {
    transform: scale(1.02);
  }

  .card-header {
    padding: 1rem 1.25rem;
    background: #f8f9fa;
    border-bottom: 1px solid #eee;
    font-weight: 600;
    display: flex;
    align-items: center;
  }

  .card-body {
    padding: 1.25rem;
  }

  .dashboard-header {
    background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
    color: white;
    padding: 2rem;
    border-radius: 12px;
    margin-bottom: 1.5rem;
  }

  .trip-card {
    position: relative;
    color: white;
    padding: 1.5rem;
    border-radius: 8px;
    background-size: cover;
    background-position: center;
    margin-bottom: 1rem;
  }

  .trip-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    border-radius: 8px;
    z-index: 1;
  }

  .trip-card > * {
    position: relative;
    z-index: 2;
  }

  .btn-action {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.2s;
  }

  .btn-action i {
    font-size: 1.1em;
  }

  .fixed-bottom {
    position: fixed;
    right: 0;
    bottom: 0;
    left: 0;
    z-index: 1030;
    background: white;
    box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
    padding: 0.5rem 0;
  }

  .nav-bottom {
    display: flex;
    justify-content: space-around;
    padding: 0.5rem 0;
  }

  .nav-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    color: #6c757d;
    text-decoration: none;
    padding: 0.5rem;
    border-radius: 8px;
    transition: all 0.2s;
  }

  .nav-item.active, .nav-item:hover {
    color: #4361ee;
    background: rgba(67, 97, 238, 0.1);
  }

  .nav-item i {
    font-size: 1.25rem;
    margin-bottom: 0.25rem;
  }

  .nav-item small {
    font-size: 0.7rem;
  }

  @media (max-width: 767.98px) {
    .dashboard-container {
      padding-bottom: 70px;
    }
  }
</style>
@endpush

@section('content')
<div class="dashboard-container">
  <!-- Hero Section -->
  <div class="dashboard-header">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-8">
          <h1 class="display-5 fw-bold mb-3">Bonjour, {{ $user?->name ?? 'Voyageur' }} 👋</h1>
          <p class="lead mb-4">Découvrez les merveilles du Burkina Faso et vivez une expérience inoubliable</p>
          <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('assistant.index') }}" class="btn btn-light btn-action">
              <i class="bi bi-robot"></i> Assistant de voyage
            </a>
            <a href="{{ route('tourist.plan') }}" class="btn btn-outline-light btn-action">
              <i class="bi bi-calendar3"></i> Planifier un voyage
            </a>
            <a href="{{ route('explore.map') }}" class="btn btn-outline-light btn-action">
              <i class="bi bi-geo-alt"></i> Explorer la carte
            </a>
          </div>
        </div>
        <div class="col-lg-4 d-none d-lg-block">
          <div class="text-center">
            <img src="{{ asset('assets/img/dashboard-hero.svg') }}" alt="Voyage" class="img-fluid" style="max-height: 200px;">
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="container">
    <div class="row g-4">
      <!-- Colonne principale -->
      <div class="col-12 col-lg-8">
        <!-- Carte de voyage en cours -->
        <div class="dashboard-card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-geo-alt-fill me-2"></i>Votre voyage en cours</span>
            <a href="{{ route('tourist.plan') }}" class="btn btn-sm btn-outline-primary">
              <i class="bi bi-pencil-square"></i> Modifier
            </a>
          </div>
          <div class="card-body p-0">
            @if(($currentTrip['start'] ?? null) && ($currentTrip['end'] ?? null))
              <div class="trip-card" style="background-image: url('{{ asset('assets/img/destinations/ouagadougou.jpg') }}')">
                <h3 class="h4 mb-2">{{ $currentTrip['city'] ?? 'Burkina Faso' }}</h3>
                <div class="d-flex align-items-center mb-3">
                  <i class="bi bi-calendar3 me-2"></i>
                  <span>{{ \Illuminate\Support\Carbon::parse($currentTrip['start'])->format('d M') }} - {{ \Illuminate\Support\Carbon::parse($currentTrip['end'])->format('d M Y') }}</span>
                </div>
                <div class="d-flex flex-wrap gap-2">
                  <span class="badge bg-light text-dark"><i class="bi bi-people-fill me-1"></i> 2 voyageurs</span>
                </div>
              </div>
            @else
              <div class="text-center p-5">
                <i class="bi bi-calendar-plus display-4 text-muted mb-3"></i>
                <h4>Planifiez votre prochain voyage</h4>
                <p class="text-muted mb-4">Créez un itinéraire personnalisé et commencez votre aventure</p>
                <a href="{{ route('tourist.plan') }}" class="btn btn-primary">Commencer la planification</a>
              </div>
            @endif
          </div>
        </div>
        </div>
      </div>

      <!-- Événements à venir -->
      <div class="dashboard-card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <span><i class="bi bi-calendar-event me-2"></i>Événements à venir</span>
          <a href="{{ route('events.index') }}" class="btn btn-sm btn-outline-primary">Voir tout</a>
        </div>
        <div class="card-body p-0">
          <div class="list-group list-group-flush">
            @forelse(($events ?? collect())->take(5) as $event)
              <a href="{{ route('events.show', $event) }}" class="list-group-item list-group-item-action p-3 event-card">
                <div class="d-flex justify-content-between align-items-center">
                  <div class="d-flex align-items-center">
                    <div class="me-3 text-center" style="min-width: 50px;">
                      <div class="fw-bold text-primary">{{ \Carbon\Carbon::parse($event->starts_at ?? $event->start_date)->format('d') }}</div>
                      <div class="small text-muted">{{ \Carbon\Carbon::parse($event->starts_at ?? $event->start_date)->locale('fr')->shortMonthName }}</div>
                    </div>
                    <div>
                      <h6 class="mb-0">{{ $event->title ?? $event->name ?? 'Événement' }}</h6>
                      <div class="text-muted small">
                        <i class="bi bi-geo-alt-fill me-1"></i>{{ $event->city ?? $event->location ?? 'Lieu non spécifié' }}
                      </div>
                    </div>
                  </div>
                  <div class="badge bg-light text-dark">
                    {{ \Carbon\Carbon::parse($event->starts_at ?? $event->start_date)->format('H:i') }}
                  </div>
                </div>
              </a>
            @empty
              <div class="text-center p-4">
                <i class="bi bi-calendar-x text-muted display-6 mb-3"></i>
                <p class="text-muted mb-0">Aucun événement à venir pour le moment</p>
              </div>
            @endforelse
          </div>
        </div>
      </div>
      
        <!-- Deuxième rangée de cartes -->
        <div class="row g-4">
          <div class="col-md-6 col-lg-12">
            <!-- Carte de statistiques -->
            <div class="dashboard-card h-100">
              <div class="card-header">
                <i class="bi bi-graph-up me-2"></i>Vos statistiques
              </div>
              <div class="card-body">
                <div class="text-center p-3">
                  <div class="d-flex justify-content-around mb-4">
                    <div class="text-center">
                      <div class="h3 mb-1">12</div>
                      <div class="small text-muted">Voyages</div>
                    </div>
                    <div class="text-center">
                      <div class="h3 mb-1">8</div>
                      <div class="small text-muted">Villes</div>
                    </div>
                    <div class="text-center">
                      <div class="h3 mb-1">24</div>
                      <div class="small text-muted">Activités</div>
                    </div>
                  </div>
                  <a href="#" class="btn btn-sm btn-outline-primary">Voir plus de stats</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="col-12 col-lg-4">
      <!-- Publicité -->
      <div class="dashboard-card hover-scale h-100">
        <div class="card-body p-0 overflow-hidden d-flex flex-column" style="border-radius: 12px; height: 100%;">
          <div class="ad-banner d-flex flex-column justify-content-center" style="background: linear-gradient(135deg, #4e54c8 0%, #8f94fb 100%); color: white; padding: 1.5rem; position: relative; overflow: hidden; min-height: 200px;">
            <div class="position-absolute" style="top: -20px; right: -20px; width: 100px; height: 100px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
            <div class="position-absolute" style="bottom: -30px; left: -30px; width: 120px; height: 120px; background: rgba(255,255,255,0.08); border-radius: 50%;"></div>
            <div class="position-relative z-2">
              <h5 class="fw-bold mb-2">Découvrez nos offres exclusives !</h5>
              <p class="small mb-3">Réservez votre hôtel dès maintenant et profitez de -20%</p>
              <a href="#" class="btn btn-sm btn-light text-primary fw-bold px-3">Voir l'offre <i class="bi bi-arrow-right ms-1"></i></a>
            </div>
          </div>
        </div>
      </div>

      <!-- Météo -->
      <div class="dashboard-card hover-scale h-100">
        <div class="card-header">
          <i class="bi bi-cloud-sun me-2"></i>Météo actuelle
        </div>
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">Ouagadougou</h6>
            <span class="badge bg-primary">En direct</span>
          </div>
          <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center">
              <i class="bi bi-sun fs-1 text-warning me-3"></i>
              <div>
                <div class="h2 mb-0 fw-bold">32°C</div>
                <div class="text-muted">Ensoleillé</div>
              </div>
            </div>
          </div>
          <div class="weather-forecast">
            <div class="d-flex justify-content-between text-center">
              <div class="px-2">
                <div class="small text-muted">Demain</div>
                <i class="bi bi-cloud-sun-fill text-warning"></i>
                <div class="small fw-medium">31°C</div>
              </div>
              <div class="px-2">
                <div class="small text-muted">Après-demain</div>
                <i class="bi bi-cloud-lightning-rain-fill text-primary"></i>
                <div class="small fw-medium">28°C</div>
              </div>
              <div class="px-2">
                <div class="small text-muted">Ven.</div>
                <i class="bi bi-cloud-sun-fill text-warning"></i>
                <div class="small fw-medium">30°C</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Carte interactive -->
      <div class="dashboard-card hover-scale h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
          <span><i class="bi bi-map me-2"></i>Explorez les alentours</span>
          <a href="{{ route('explore.map') }}" class="btn btn-sm btn-outline-primary">Agrandir</a>
        </div>
        <div class="card-body p-0" style="height: 200px;">
          <div id="miniMap" style="height: 100%; width: 100%; border-radius: 0 0 12px 12px;"></div>
        </div>
      </div>

      <!-- Actions rapides -->
      <div class="dashboard-card">
        <div class="card-header">
          <i class="bi bi-lightning-charge me-2"></i>Actions rapides
        </div>
        <div class="card-body p-0">
          <div class="list-group list-group-flush">
            <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
              <i class="bi bi-plus-circle me-2 text-primary"></i>
              <span>Créer un itinéraire</span>
            </a>
            <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
            <a href="{{ route('air.flights.wizard') }}" class="list-group-item list-group-item-action">
              <div class="d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 p-2 rounded me-3">
                  <i class="bi bi-airplane text-primary"></i>
                </div>
                <div>
                  <h6 class="mb-0">Réserver un vol</h6>
                  <small class="text-muted">Trouvez les meilleurs vols</small>
                </div>
              </div>
            </a>
            <a href="{{ route('tourist.hotels.index') }}" class="list-group-item list-group-item-action">
              <div class="d-flex align-items-center">
                <div class="bg-success bg-opacity-10 p-2 rounded me-3">
                  <i class="bi bi-building text-success"></i>
                </div>
                <div>
                  <h6 class="mb-0">Trouver un hôtel</h6>
                  <small class="text-muted">Réservez votre hébergement</small>
                </div>
              </div>
            </a>
            <a href="{{ route('transport.taxi.index') }}" class="list-group-item list-group-item-action">
              <div class="d-flex align-items-center">
                <div class="bg-warning bg-opacity-10 p-2 rounded me-3">
                  <i class="bi bi-taxi-front text-warning"></i>
                </div>
                <div>
                  <h6 class="mb-0">Réserver un taxi</h6>
                  <small class="text-muted">Déplacez-vous facilement</small>
                </div>
              </div>
            </a>
            <a href="{{ route('tourist.community') }}" class="list-group-item list-group-item-action">
              <div class="d-flex align-items-center">
                <div class="bg-info bg-opacity-10 p-2 rounded me-3">
                  <i class="bi bi-people text-info"></i>
                </div>
                <div>
                  <h6 class="mb-0">Communauté</h6>
                  <small class="text-muted">Rencontrez d'autres voyageurs</small>
                </div>
              </div>
            </a>
          </div>
        </div>
      </div>

      <!-- Conseils locaux -->
      <div class="dashboard-card">
        <div class="card-header">
          <i class="bi bi-lightbulb me-2"></i>Conseils locaux
        </div>
        <div class="card-body">
          <div class="alert alert-info d-flex align-items-start">
            <i class="bi bi-info-circle-fill me-2 mt-1"></i>
            <div>
              <strong>Bonne adresse</strong>
              <p class="mb-0">Essayez le restaurant "La Paisible" pour une délicieuse cuisine locale.</p>
            </div>
          </div>
          <div class="alert alert-warning d-flex align-items-start">
            <i class="bi bi-exclamation-triangle-fill me-2 mt-1"></i>
            <div>
              <strong>À éviter</strong>
              <p class="mb-0">Évitez la circulation aux heures de pointe (7h-9h et 17h-19h).</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Barre de navigation inférieure -->
<nav class="fixed-bottom bg-white shadow-lg d-lg-none">
  <div class="nav-bottom">
    <a href="{{ route('tourist.dashboard') }}" class="nav-item {{ request()->routeIs('tourist.dashboard') ? 'active' : '' }}">
      <i class="bi bi-house"></i>
      <small>Accueil</small>
    </a>
    <a href="{{ route('tourist.itinerary') }}" class="nav-item {{ request()->routeIs('tourist.itinerary') ? 'active' : '' }}">
      <i class="bi bi-map"></i>
      <small>Itinéraire</small>
    </a>
    <a href="#" class="nav-item">
      <i class="bi bi-heart"></i>
      <small>Favoris</small>
    </a>
    <a href="{{ route('explore.map') }}" class="nav-item {{ request()->routeIs('explore.map') ? 'active' : '' }}">
      <i class="bi bi-compass"></i>
      <small>Explorer</small>
    </a>
    <a href="{{ route('profile.edit') }}" class="nav-item {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
      <i class="bi bi-person"></i>
      <small>Profil</small>
    </a>
  </div>
</nav>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
  // Initialize map when document is ready
  document.addEventListener('DOMContentLoaded', function() {
    // Initialize mini map
    if (document.getElementById('miniMap')) {
      const map = L.map('miniMap', {
        zoomControl: false,
        scrollWheelZoom: false,
        dragging: false,
        tap: false
      }).setView([12.3714, -1.5197], 13);
      
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        className: 'map-tiles'
      }).addTo(map);
      
      // Configuration de l'icône utilisateur
      const userIcon = L.divIcon({
        html: '<div class="user-location-marker"><i class="bi bi-geo-fill"></i></div>',
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32],
        className: 'user-location-icon'
      });
      
      // Marqueur de position utilisateur
      L.marker([12.3714, -1.5197], {icon: userIcon})
        .addTo(map)
        .bindPopup('Votre position actuelle')
        .openPopup();
      
      // Configuration des points d'intérêt
      const poiIcon = L.divIcon({
        html: '<div class="poi-marker"><i class="bi bi-geo-alt-fill"></i></div>',
        iconSize: [24, 24],
        iconAnchor: [12, 24],
        className: 'poi-icon'
      });
      
      // Points d'intérêt
      const pois = [
        {lat: 12.3714, lng: -1.5197, title: 'Ouagadougou', type: 'city'},
        {lat: 12.5916, lng: -12.3386, title: 'Parc du W', type: 'park'},
        {lat: 13.0833, lng: -1.0833, title: 'Laongo', type: 'sight'}
      ];
      
      // Ajout des POIs sur la carte
      pois.forEach(poi => {
        L.marker([poi.lat, poi.lng], {icon: poiIcon})
          .addTo(map)
          .bindPopup(`<b>${poi.title}</b><br>${poi.type === 'city' ? 'Ville' : 'Point d\'intérêt'}`);
      });
    
    // Add animation to cards on scroll
    const animateOnScroll = () => {
      const cards = document.querySelectorAll('.dashboard-card');
      cards.forEach((card, index) => {
        const cardTop = card.getBoundingClientRect().top;
        const windowHeight = window.innerHeight;
        
        if (cardTop < windowHeight - 100) {
          card.style.opacity = '1';
          card.style.transform = 'translateY(0)';
        }
      });
    };
    
    // Initial check
    animateOnScroll();
    
    // Check on scroll
    window.addEventListener('scroll', animateOnScroll);
    
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        e.preventDefault();
        
        const targetId = this.getAttribute('href');
        if (targetId === '#') return;
        
        const targetElement = document.querySelector(targetId);
        if (targetElement) {
          window.scrollTo({
            top: targetElement.offsetTop - 20,
            behavior: 'smooth'
          });
        }
      });
    });
  });
</script>
<style>
  /* Styles for the map */
  .user-location-marker {
    color: #4361ee;
    font-size: 24px;
    text-shadow: 0 0 8px rgba(255, 255, 255, 0.8);
  }
  
  .poi-marker {
    color: #e63946;
    font-size: 18px;
    text-shadow: 0 0 5px rgba(255, 255, 255, 0.8);
  }
  
  /* Bottom navigation styles */
  .fixed-bottom {
    z-index: 1030;
  }
  
  .fixed-bottom a {
    color: #6c757d;
    text-decoration: none;
    transition: all 0.2s ease;
  }
  
  .fixed-bottom a:hover, .fixed-bottom a.active {
    color: #4361ee;
  }
  
  .fixed-bottom a i {
    margin-bottom: 2px;
  }
  
  .fixed-bottom small {
    font-size: 0.7rem;
  }
  
  /* Animation for cards */
  .dashboard-card {
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 0.6s ease-out, transform 0.6s ease-out;
  }
  
  /* Responsive adjustments */
  @media (max-width: 767.98px) {
    .dashboard-container {
      padding-bottom: 70px;
    }
    
    .fixed-bottom {
      box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
    }
  }
</style>
@endpush
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
