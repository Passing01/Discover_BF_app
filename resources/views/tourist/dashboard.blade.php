@extends('layouts.tourist')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/dashboard_Tourist-styles.css') }}">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" 
      crossorigin=""/>
<style>
  .nav-anim {
    transition: transform 0.2s, box-shadow 0.2s, color 0.2s;
    will-change: transform, color;
  }
  .nav-anim:hover, .nav-anim:focus {
    transform: scale(1.12) !important;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    color: #0d6efd !important;
  }
  .nav-anim.active {
    color: #0d6efd !important;
    font-weight: bold;
    transform: scale(1.15) !important;
  }
  /* Use normal document flow for dashboard container to avoid forced empty space */
  .dashboard-container {
    min-height: auto;
    display: block;
    /* default desktop padding to avoid overlap with any sticky footer */
    padding-bottom: 24px;
    background: transparent;
  }

  /* Ensure html/body fill the viewport and set the page background gradient so it always extends to bottom */
  html, body {
    height: 100%;
  }
  body {
    background: linear-gradient(180deg, #f6f9fb 0%, #f3f7fb 40%, #f8fafc 100%);
    background-attachment: scroll;
  }
  /* Larger bottom padding on smaller screens to account for the fixed bottom nav */
  @media (max-width: 991.98px) {
    .dashboard-container {
      padding-bottom: 88px;
    }
  }
  /* Let the primary inner container expand to fill available space */
  .dashboard-container > .container {
    /* normal flow — do not expand to fill viewport */
    flex: 0 0 auto;
    display: block;
  }
  /* Make the last major section grow so the background reaches bottom */
  .dashboard-container .card.dash-card:last-of-type {
    margin-bottom: 0;
  }
</style>
@endpush

@section('content')
<!-- DEBUT CONTENEUR PRINCIPAL -->
<div class="dashboard-container container-fluid px-0">
  <!-- Hero Section -->
  <div class="dashboard-header py-4 px-3 px-md-5">
    <div class="row align-items-center gx-0">
      <div class="col-12 col-md-8 mb-4 mb-md-0">
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
      <div class="col-12 col-md-4 text-center">
        <img src="{{ asset('assets/img/dashboard-hero.svg') }}" alt="Voyage" class="img-fluid" style="max-height: 200px;">
      </div>
    </div>
  </div>


    <div class="container mb-4">
      <div class="dashboard-card p-4">
        <style>
          .dashboard-hover-card {
            transition: box-shadow 0.2s, transform 0.2s;
          }
          .dashboard-hover-card:hover {
            box-shadow: 0 4px 24px rgba(59,91,253,0.12), 0 1.5px 8px rgba(0,0,0,0.08);
            transform: translateY(-4px) scale(1.04);
            border-color: #3b5bfd;
          }
        </style>

  <div class="container-fluid px-0">

    <div class="row g-3 mb-4">
      <div class="col-12 col-md-6">
        <!-- Bloc voyage -->
        <div class="dashboard-card h-100 border rounded-3 shadow-sm bg-white dashboard-hover-card">
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
   
      <div class="col-12 col-lg-6">
          <!-- Météo -->
          <div class="dashboard-card shadow-lg border-0 rounded-4 overflow-hidden weather-card animate__animated animate__fadeInUp h-100">
            <div class="card-header bg-gradient bg-info text-white d-flex align-items-center py-3">
              <i class="bi bi-cloud-sun fs-3 me-2"></i>
              <span class="fw-bold fs-5">Météo actuelle</span>
              <span class="badge bg-primary ms-auto">En direct</span>
            </div>
            <div class="card-body bg-light bg-opacity-50">
              <div class="d-flex flex-column flex-md-row align-items-center justify-content-between mb-3 gap-3">
                <div class="d-flex align-items-center gap-3">
                  <span class="weather-icon bg-white rounded-circle shadow-sm d-flex align-items-center justify-content-center me-2"><i class="bi bi-sun text-warning" style="font-size:2.5rem;"></i></span>
                  <div>
                    <div class="h1 mb-0 fw-bold text-dark">32°C</div>
                    <div class="text-muted fs-5">Ensoleillé</div>
                    <div class="text-info fw-semibold">Ouagadougou</div>
                  </div>
                </div>
                <div class="d-flex gap-4 weather-forecast">
                  <div class="text-center">
                    <div class="small text-muted mb-1">Demain</div>
                    <span class="weather-icon-sm bg-white rounded-circle shadow-sm mb-1"><i class="bi bi-cloud-sun-fill text-warning" style="font-size:1.5rem;"></i></span>
                    <div class="fw-bold text-dark">31°C</div>
                  </div>
                  <div class="text-center">
                    <div class="small text-muted mb-1">Après-demain</div>
                    <span class="weather-icon-sm bg-white rounded-circle shadow-sm mb-1"><i class="bi bi-cloud-lightning-rain-fill text-primary" style="font-size:1.5rem;"></i></span>
                    <div class="fw-bold text-dark">28°C</div>
                  </div>
                  <div class="text-center">
                    <div class="small text-muted mb-1">Ven.</div>
                    <span class="weather-icon-sm bg-white rounded-circle shadow-sm mb-1"><i class="bi bi-cloud-sun-fill text-warning" style="font-size:1.5rem;"></i></span>
                    <div class="fw-bold text-dark">30°C</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div> 
      <style>
        .dashboard-hover-card {
          transition: box-shadow 0.2s, transform 0.2s;
        }
        .dashboard-hover-card:hover {
          box-shadow: 0 4px 24px rgba(59,91,253,0.12), 0 1.5px 8px rgba(0,0,0,0.08);
          transform: translateY(-4px) scale(1.04);
          border-color: #3b5bfd;
        }
        .stat-card {
          display: inline-block;
          min-width: 90px;
          margin: 0 8px;
          padding: 12px 0;
          border-radius: 12px;
          background: #f8f9fc;
          box-shadow: 0 1px 4px rgba(59,91,253,0.07);
        }
        .stat-card .icon {
          font-size: 1.7rem;
          margin-bottom: 4px;
          display: block;
        }
      </style>

      <div class="row g-3 mb-4">
        <div class="col-12 col-md-6">
          <!-- Événements à venir -->
          <div class="dashboard-card h-100 border rounded-3 shadow-sm bg-white dashboard-hover-card">
            <div class="card-header d-flex justify-content-between align-items-center">
              <span><i class="bi bi-calendar-event me-2"></i>Événements à venir</span>
              <a href="{{ route('events.index') }}" class="btn btn-sm btn-outline-primary">Voir tout</a>
            </div>
            <div class="card-body p-0">
              <div class="list-group list-group-flush">
                @php
                  $now = \Carbon\Carbon::now();
                  $upcomingEvents = ($events ?? collect())
                    ->filter(function($event) use ($now) {
                      $date = \Carbon\Carbon::parse($event->starts_at ?? $event->start_date);
                      return $date->gte($now);
                    })
                    ->sortBy(function($event) {
                      return \Carbon\Carbon::parse($event->starts_at ?? $event->start_date);
                    })
                    ->take(2);
                @endphp
                @forelse($upcomingEvents as $event)
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
        </div>
        <div class="col-12 col-md-6">
          <!-- Statistiques utilisateur -->
          <div class="dashboard-card h-100 border rounded-3 shadow-sm bg-white dashboard-hover-card">
            <div class="card-header">
              <i class="bi bi-graph-up me-2"></i>Vos statistiques
            </div>
            <div class="card-body">
              <div class="text-center p-3">
                <div class="d-flex justify-content-center mb-4 flex-wrap gap-2">
                  <div class="stat-card">
                    <span class="icon"><i class="bi bi-airplane text-primary"></i></span>
                    <div class="h4 mb-1">12</div>
                    <div class="small text-muted">Voyages</div>
                  </div>
                  <div class="stat-card">
                    <span class="icon"><i class="bi bi-geo-alt text-success"></i></span>
                    <div class="h4 mb-1">8</div>
                    <div class="small text-muted">Villes</div>
                  </div>
                  <div class="stat-card">
                    <span class="icon"><i class="bi bi-star text-warning"></i></span>
                    <div class="h4 mb-1">24</div>
                    <div class="small text-muted">Activités</div>
                  </div>
                </div>
                <a href="#" class="btn btn-sm btn-outline-primary">Voir plus de stats</a>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Sidebar -->

      <div class="row g-4 align-items-stretch mb-4">
      <div class="col-12 col-md-6">
        <!-- Bloc publicité -->
          <div class="dashboard-card h-100 border rounded-3 shadow-sm bg-white dashboard-hover-card">
            <div class="card-header">
              <i class="bi bi-megaphone me-2"></i>Offre spéciale
            </div>
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
          </div>
        <div class="col-12 col-lg-6">
          <!-- Assistant + Succès -->
          <div class="row g-3 h-100">
            <div class="col-12">
              <div class="card dash-card h-100">
                <div class="card-body">
                  <div class="dash-section-title mb-2">Assistant en temps réel</div>
                  <div class="d-grid gap-2">
                    <a href="{{ route('assistant.index') }}" class="btn btn-outline-primary">Parler à l'assistant</a>
                    <a href="{{ route('assistant.index') }}" class="btn btn-light">Traduire</a>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-12">
              <div class="card dash-card h-100">
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
            </div>
          </div>
        </div>
      </div>
      <style>
        .weather-card {
          background: linear-gradient(135deg, #eaf6fb 0%, #f8f9fc 100%);
        }
        .weather-icon {
          width: 64px;
          height: 64px;
          font-size: 2.5rem;
        }
        .weather-icon-sm {
          width: 36px;
          height: 36px;
          display: inline-flex;
          align-items: center;
          justify-content: center;
        }
      </style>

      <!-- Carte interactive -->

      <div class="dashboard-card shadow-lg border-0 rounded-4 overflow-hidden map-card animate__animated animate__fadeInUp">
        <div class="card-header bg-gradient bg-success text-white d-flex align-items-center py-3 position-relative">
          <span class="d-flex align-items-center gap-2">
            <span class="map-icon-anim bg-white rounded-circle shadow-sm d-flex align-items-center justify-content-center"><i class="bi bi-map text-success" style="font-size:2rem;"></i></span>
            <span class="fw-bold fs-5">Explorez les alentours</span>
          </span>
          <a href="{{ route('explore.map') }}" class="btn btn-sm btn-light fw-bold px-3 ms-auto">Agrandir <i class="bi bi-arrows-fullscreen ms-1"></i></a>
          <span class="map-overlay position-absolute top-0 start-0 w-100 h-100"></span>
        </div>
        <div class="card-body p-0 position-relative" style="height: 220px; background: linear-gradient(135deg, #eaf6fb 0%, #f8f9fc 100%);">
          <div id="miniMap" style="height: 100%; width: 100%; border-radius: 0 0 2rem 2rem; box-shadow: 0 2px 16px rgba(59,91,253,0.08);"></div>
          <a href="{{ route('explore.map') }}" class="btn btn-success btn-lg rounded-pill shadow position-absolute bottom-3 end-3 d-flex align-items-center gap-2 map-zoom-btn">
            <i class="bi bi-arrows-fullscreen fs-5"></i>
            <span class="fw-bold">Agrandir</span>
          </a>
          <div class="position-absolute bottom-2 start-2 p-2">
            <span class="badge bg-success bg-opacity-75 shadow">Carte interactive</span>
          </div>
        </div>
      </div>
      <style>
        .map-card {
          background: linear-gradient(135deg, #eaf6fb 0%, #f8f9fc 100%);
        }
        .map-icon-anim {
          width: 48px;
          height: 48px;
          animation: mapPulse 1.8s infinite;
        }
        @keyframes mapPulse {
          0% { box-shadow: 0 0 0 0 rgba(44, 40, 167, 0.2); }
          70% { box-shadow: 0 0 0 12px rgba(40,167,69,0.0); }
          100% { box-shadow: 0 0 0 0 rgba(40, 57, 167, 0); }
        }
        .map-overlay {
          pointer-events: none;
          background: linear-gradient(90deg, rgba(40, 61, 167, 0.08) 0%, rgba(30, 30, 206, 0) 100%);
          z-index: 1;
        }
        .map-zoom-btn {
          transition: box-shadow 0.2s, transform 0.2s;
          font-size: 1.1rem;
        }
        .map-zoom-btn:hover {
          box-shadow: 0 8px 32px rgba(40,167,69,0.18);
          transform: scale(1.07);
          background: linear-gradient(90deg,#4354e9 0%,#3852f9 100%);
          color: #fff;
        }
      </style>

      <!-- Actions rapides -->
      <div class="dashboard-card shadow-lg border-0 rounded-4 overflow-hidden animate__animated animate__fadeInUp">
        <div class="card-header bg-gradient bg-primary text-white d-flex align-items-center py-3">
          <i class="bi bi-lightning-charge me-2 fs-4"></i>
          <span class="fw-bold fs-5">Actions rapides</span>
        </div>
        <div class="card-body p-0">
          <div class="row g-0">
            <div class="col-12 col-md-6 col-lg-4">
              <a href="#" class="action-card list-group-item-action d-flex flex-column align-items-center justify-content-center py-4 h-100 text-decoration-none">
                <span class="icon-circle mb-2 bg-primary bg-opacity-10"><i class="bi bi-plus-circle text-primary fs-2"></i></span>
                <span class="fw-semibold text-dark">Créer un itinéraire</span>
              </a>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
              <a href="{{ route('air.flights.wizard') }}" class="action-card list-group-item-action d-flex flex-column align-items-center justify-content-center py-4 h-100 text-decoration-none">
                <span class="icon-circle mb-2 bg-info bg-opacity-10"><i class="bi bi-airplane text-info fs-2"></i></span>
                <span class="fw-semibold text-dark">Réserver un vol</span>
                <small class="text-muted">Trouvez les meilleurs vols</small>
              </a>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
              <a href="{{ route('tourist.hotels.index') }}" class="action-card list-group-item-action d-flex flex-column align-items-center justify-content-center py-4 h-100 text-decoration-none">
                <span class="icon-circle mb-2 bg-success bg-opacity-10"><i class="bi bi-building text-success fs-2"></i></span>
                <span class="fw-semibold text-dark">Trouver un hôtel</span>
                <small class="text-muted">Réservez votre hébergement</small>
              </a>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
              <a href="{{ route('transport.taxi.index') }}" class="action-card list-group-item-action d-flex flex-column align-items-center justify-content-center py-4 h-100 text-decoration-none">
                <span class="icon-circle mb-2 bg-warning bg-opacity-10"><i class="bi bi-taxi-front text-warning fs-2"></i></span>
                <span class="fw-semibold text-dark">Réserver un taxi</span>
                <small class="text-muted">Déplacez-vous facilement</small>
              </a>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
              <a href="{{ route('tourist.community') }}" class="action-card list-group-item-action d-flex flex-column align-items-center justify-content-center py-4 h-100 text-decoration-none">
                <span class="icon-circle mb-2 bg-info bg-opacity-10"><i class="bi bi-people text-info fs-2"></i></span>
                <span class="fw-semibold text-dark">Communauté</span>
                <small class="text-muted">Rencontrez d'autres voyageurs</small>
              </a>
            </div>
          </div>
        </div>
      </div>
      <style>
        .action-card {
          transition: box-shadow 0.2s, transform 0.2s;
          border-radius: 1.25rem;
          background: #f8f9fc;
          box-shadow: 0 1px 4px rgba(59,91,253,0.07);
        }
        .action-card:hover {
          box-shadow: 0 4px 24px rgba(59,91,253,0.12), 0 1.5px 8px rgba(0,0,0,0.08);
          transform: translateY(-4px) scale(1.04);
          background: #eef2fb;
        }
        .icon-circle {
          display: inline-flex;
          align-items: center;
          justify-content: center;
          width: 56px;
          height: 56px;
          border-radius: 50%;
        }
      </style>

      <!-- Conseils locaux -->
    </div>
  </div>
</div>

<!-- Barre de navigation inférieure -->
<nav class="fixed-bottom bg-white shadow-lg d-lg-none">
  <div class="nav-bottom d-flex justify-content-around py-2">
    <a href="{{ route('tourist.dashboard') }}" class="nav-item flex-fill text-center nav-anim {{ request()->routeIs('tourist.dashboard') ? 'active' : '' }}">
      <i class="bi bi-house"></i>
      <small>Accueil</small>
    </a>
    <a href="{{ route('tourist.itinerary') }}" class="nav-item flex-fill text-center nav-anim {{ request()->routeIs('tourist.itinerary') ? 'active' : '' }}">
      <i class="bi bi-map"></i>
      <small>Itinéraire</small>
    </a>
    <a href="#" class="nav-item flex-fill text-center nav-anim">
      <i class="bi bi-heart"></i>
      <small>Favoris</small>
    </a>
    <a href="{{ route('explore.map') }}" class="nav-item flex-fill text-center nav-anim {{ request()->routeIs('explore.map') ? 'active' : '' }}">
      <i class="bi bi-compass"></i>
      <small>Explorer</small>
    </a>
    <a href="{{ route('profile.edit') }}" class="nav-item flex-fill text-center nav-anim {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
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
      

      // Design des icônes selon le type
      function getPoiIcon(type) {
        let color = '#3b5bfd', icon = 'bi-geo-alt-fill';
        if (type === 'city') { color = '#3b5bfd'; icon = 'bi-buildings'; }
        if (type === 'park') { color = '#43e97b'; icon = 'bi-tree-fill'; }
        if (type === 'sight') { color = '#f7b731'; icon = 'bi-star-fill'; }
        return L.divIcon({
          html: `<div class='poi-marker' style='background:rgba(255,255,255,0.95);border-radius:50%;box-shadow:0 2px 8px rgba(59,91,253,0.12);padding:6px;display:flex;align-items:center;justify-content:center;'><i class='bi ${icon}' style='font-size:1.5rem;color:${color};'></i></div>`,
          iconSize: [36, 36],
          iconAnchor: [18, 36],
          className: 'poi-icon'
        });
      }

      // Points d'intérêt
      const pois = [
        {lat: 12.3714, lng: -1.5197, title: 'Ouagadougou', type: 'city'},
        {lat: 12.5916, lng: -12.3386, title: 'Parc du W', type: 'park'},
        {lat: 13.0833, lng: -1.0833, title: 'Laongo', type: 'sight'}
      ];

      // Ajout des POIs sur la carte avec design
      pois.forEach(poi => {
        L.marker([poi.lat, poi.lng], {icon: getPoiIcon(poi.type)})
          .addTo(map)
          .bindPopup(`<div style='min-width:120px;text-align:center;'>
            <span class='badge' style='background:${poi.type==='city'?'#3b5bfd':poi.type==='park'?'#43e97b':'#f7b731'};color:#fff;font-size:0.9rem;border-radius:1rem;padding:0.3em 0.8em;'>${poi.type==='city'?'Ville':poi.type==='park'?'Parc':'Site'}</span><br>
            <span style='font-weight:bold;font-size:1.1rem;'>${poi.title}</span>
          </div>`);
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
@endpush

                  
          <!-- Quick Bookings -->
          <div class="card dash-card">
            <div class="card-body">
              <div class="dash-section-title mb-2">Réservations rapides</div>
              <div class="row g-2">
                <div class="col-md-6">
                  <div class="mb-2">
                    <span class="fs-5 fw-bold text-primary"><i class="bi bi-airplane me-1"></i> Vols</span>
                  </div>
                  <div class="row g-3">
                    @forelse($flights as $f)
                      <div class="col-12">
                        <div class="flight-card d-flex flex-column flex-md-row align-items-center justify-content-between p-3 rounded-4 shadow-sm bg-white position-relative h-100" style="background: linear-gradient(90deg,#eaf6fb 0%,#f8f9fc 100%); border: none;">
                          <div class="d-flex align-items-center gap-3 flex-grow-1">
                            <span class="flight-icon bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2" style="width:56px;height:56px;">
                              <i class="bi bi-airplane text-primary fs-2"></i>
                            </span>
                            <div>
                              <div class="fw-bold fs-5 mb-1 text-dark">{{ $f->airline ?? 'Compagnie aérienne' }}</div>
                              <div class="small text-muted mb-1"><i class="bi bi-geo-alt-fill me-1"></i>{{ $f->origin->city ?? '—' }} <span class="mx-1">→</span> {{ $f->destination->city ?? '—' }}</div>
                              <span class="badge bg-light text-primary fw-semibold px-2 py-1 me-2">{{ $f->flight_number ?? '' }}</span>
                            </div>
                          </div>
                          <div class="d-flex flex-column align-items-end gap-2 ms-md-3">
                            <span class="badge bg-gradient bg-success text-white fs-6 px-3 py-2 shadow-sm">{{ number_format($f->base_price ?? 0, 0) }} FCFA</span>
                            <a href="{{ route('air.flights.show', $f) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">Voir <i class="bi bi-arrow-right ms-1"></i></a>
                          </div>
                        </div>
                      </div>
                    @empty
                      <div class="col-12">
                        <div class="text-muted small">Aucun vol trouvé.</div>
                      </div>
                    @endforelse
                  </div>
                  <style>
                    .flight-card {
                      transition: box-shadow 0.2s, transform 0.2s;
                    }
                    .flight-card:hover {
                      box-shadow: 0 8px 32px rgba(59,91,253,0.12), 0 2px 12px rgba(40,167,69,0.10);
                      transform: translateY(-4px) scale(1.03);
                      background: linear-gradient(90deg,#eaf6fb 0%,#d6e6fa 100%);
                    }
                  </style>
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
      </div>

      <div class="row g-3">
          <div class="col-12 col-md-6 col-lg-3">
            <div class="h-100 border rounded-3 shadow-sm bg-white d-flex flex-column justify-content-center align-items-center p-0 dashboard-hover-card">
              <div id="carouselCommunity" class="carousel slide w-100" data-bs-ride="carousel" data-bs-interval="4000">
                <div class="carousel-inner">
                  <div class="carousel-item active">
                    <div class="d-flex flex-column align-items-center justify-content-center p-3">
                      <span class="d-inline-block bg-white rounded-circle shadow-sm p-3 mb-2">
                        <i class="bi bi-people" style="font-size:2.5rem;color:#3b5bfd;"></i>
                      </span>
                      <h5 class="fw-bold mb-1" style="color:#3b5bfd;">Communauté & avis</h5>
                      <p class="mb-2 small">Partagez vos expériences, lisez les avis d'autres voyageurs et échangez avec la communauté Discover BF.</p>
                      <a href="{{ route('tourist.community') }}" class="btn btn-primary rounded-pill px-3 py-1 fw-bold">Accéder</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-6 col-lg-3">
            <div class="h-100 border rounded-3 shadow-sm bg-white d-flex flex-column justify-content-center align-items-center p-0 dashboard-hover-card">
              <div id="carouselLocalTips" class="carousel slide w-100" data-bs-ride="carousel" data-bs-interval="4000">
                <div class="carousel-inner">
                  <div class="carousel-item active">
                    <div class="d-flex flex-column align-items-center justify-content-center p-3">
                      <span class="d-inline-block bg-white rounded-circle shadow-sm p-3 mb-2">
                        <i class="bi bi-lightbulb" style="font-size:2.5rem;color:#ffc107;"></i>
                      </span>
                      <h5 class="dashboard-title mb-1">Conseils locaux</h5>
                      <p class="mb-2 small">Découvrez les astuces et recommandations des habitants pour profiter pleinement de votre séjour au Burkina Faso.</p>
                      <a href="{{ route('tourist.local-tips') }}" class="btn btn-primary rounded-pill px-3 py-1">Accéder</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-6 col-lg-3">
            <div class="h-100 border rounded-3 shadow-sm bg-white d-flex flex-column justify-content-center align-items-center p-0 dashboard-hover-card">
              <div id="carouselNotifications" class="carousel slide w-100" data-bs-ride="carousel" data-bs-interval="4000">
                <div class="carousel-inner">
                  <div class="carousel-item active">
                    <div class="d-flex flex-column align-items-center justify-content-center p-3">
                      <span class="d-inline-block bg-white rounded-circle shadow-sm p-3 mb-2">
                        <i class="bi bi-bell" style="font-size:2.5rem;color:#0d6efd;"></i>
                      </span>
                      <h5 class="dashboard-title mb-1">Notifications</h5>
                      <p class="mb-2 small">Recevez toutes les alertes importantes, mises à jour et messages personnalisés pour votre voyage.</p>
                      <a href="{{ route('user.notifications.index') }}" class="btn btn-primary rounded-pill px-3 py-1">Accéder</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-6 col-lg-3">
            <div class="h-100 border rounded-3 shadow-sm bg-white d-flex flex-column justify-content-center align-items-center p-0 dashboard-hover-card">
              <div id="carouselRecommendations" class="carousel slide w-100" data-bs-ride="carousel" data-bs-interval="4000">
                <div class="carousel-inner">
                  <div class="carousel-item active">
                    <div class="d-flex flex-column align-items-center justify-content-center p-3">
                      <span class="d-inline-block bg-white rounded-circle shadow-sm p-3 mb-2">
                        <i class="bi bi-star" style="font-size:2.5rem;color:#f7b731;"></i>
                      </span>
                      <h5 class="dashboard-title mb-1">Recommandations</h5>
                      <p class="mb-2 small">Explorez nos suggestions personnalisées pour des activités, sites et restaurants à ne pas manquer.</p>
                      <a href="{{ route('tourist.recommendations') }}" class="btn btn-primary rounded-pill px-3 py-1">Accéder</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
      <!-- Bottom nav mimic -->
      <div class="position-sticky bottom-0">
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
  // --- Script pour la carte miniMap ---
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

  // --- Script pour la barre de navigation inférieure animée ---
  // La barre est cachée en haut de la page, visible uniquement si on descend
  document.addEventListener('DOMContentLoaded', function() {
    let lastScrollY = window.scrollY;
    const navbar = document.querySelector('.fixed-bottom');
    if (!navbar) {
      console.log('Barre inférieure non trouvée');
      return;
    }
    // Au chargement, cacher la barre si on est tout en haut
    if (window.scrollY === 0) {
      navbar.classList.add('navbar-hidden');
    }
    window.addEventListener('scroll', function() {
      // Si on est tout en haut, on replie la barre
      if (window.scrollY === 0) {
        navbar.classList.add('navbar-hidden');
        console.log('Barre cachée (haut de page)');
        lastScrollY = 0;
        return;
      }
      // Si on scrolle vers le bas, on montre la barre
      if (window.scrollY > lastScrollY) {
        navbar.classList.remove('navbar-hidden');
        console.log('Barre affichée (scroll vers le bas)');
      } else {
        // Si on remonte, on replie la barre
        navbar.classList.add('navbar-hidden');
        console.log('Barre cachée (scroll vers le haut)');
      }
      lastScrollY = window.scrollY;
    });
  });
</script>
@endpush
