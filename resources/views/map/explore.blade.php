@extends('layouts.tourist')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
      crossorigin=""/>
<style>
    #map {
        height: calc(100vh - 60px);
        width: 100%;
    }
    .map-container {
        position: relative;
    }
    .map-controls {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 1000;
        background: white;
        padding: 10px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
</style>
@endpush

@section('content')
<div class="map-container">
    <div id="map"></div>
    <div class="map-controls">
        <div class="btn-group-vertical">
            <button class="btn btn-sm btn-light mb-2" id="locate-me">
                <i class="bi bi-geo-alt"></i> Me localiser
            </button>
            <button class="btn btn-sm btn-light" id="filter-toggle">
                <i class="bi bi-funnel"></i> Filtres
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize map centered on Burkina Faso
    const map = L.map('map').setView([12.3714, -1.5197], 7);
    
    // Add OpenStreetMap tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Sample points of interest
    const pois = [
        {lat: 12.3714, lng: -1.5197, title: 'Ouagadougou', type: 'city'},
        {lat: 11.1771, lng: -4.2979, title: 'Bobo-Dioulasso', type: 'city'},
        {lat: 10.6333, lng: -4.7667, title: 'Banfora', type: 'city'},
        {lat: 12.2526, lng: -2.3627, title: 'Koudougou', type: 'city'},
        {lat: 11.1771, lng: -4.2979, title: 'Musée de la Musique', type: 'attraction'},
        {lat: 12.3814, lng: -1.5197, title: 'Marché de Ouaga', type: 'market'},
        {lat: 12.3614, lng: -1.5297, title: 'Parc Bangr-Weoogo', type: 'park'}
    ];

    // Add markers for each POI
    pois.forEach(poi => {
        const marker = L.marker([poi.lat, poi.lng])
            .addTo(map)
            .bindPopup(`<b>${poi.title}</b><br>${poi.type === 'city' ? 'Ville' : 'Point d\'intérêt'}`);
    });

    // Locate me button
    document.getElementById('locate-me').addEventListener('click', function() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const { latitude, longitude } = position.coords;
                    map.setView([latitude, longitude], 15);
                    L.marker([latitude, longitude])
                        .addTo(map)
                        .bindPopup('Votre position actuelle')
                        .openPopup();
                },
                (error) => {
                    alert('Impossible de récupérer votre position : ' + error.message);
                }
            );
        } else {
            alert('La géolocalisation n\'est pas supportée par votre navigateur');
        }
    });

    // Filter toggle
    document.getElementById('filter-toggle').addEventListener('click', function() {
        alert('Fonctionnalité de filtrage à venir !');
    });
});
</script>
@endpush
