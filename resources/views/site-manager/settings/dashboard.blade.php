@extends('layouts.site-manager')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Paramètres du tableau de bord</h1>
        <p class="text-muted mb-0">Personnalisez l'apparence et le comportement de votre tableau de bord</p>
    </div>
    <div>
        <a href="{{ route('site-manager.dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Retour au tableau de bord
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">Apparence</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('site-manager.settings.dashboard.update') }}" method="POST" id="dashboardSettingsForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="theme" class="form-label">Thème</label>
                            <select class="form-select" id="theme" name="theme">
                                <option value="light" {{ ($dashboardSettings['theme'] ?? 'light') === 'light' ? 'selected' : '' }}>Clair</option>
                                <option value="dark" {{ ($dashboardSettings['theme'] ?? 'light') === 'dark' ? 'selected' : '' }}>Sombre</option>
                                <option value="system" {{ ($dashboardSettings['theme'] ?? 'light') === 'system' ? 'selected' : '' }}>Système</option>
                            </select>
                            <div class="form-text">Choisissez entre un thème clair, sombre ou suivez les paramètres de votre système</div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="timezone" class="form-label">Fuseau horaire</label>
                            <select class="form-select" id="timezone" name="timezone">
                                @foreach($timezoneOptions as $timezone => $label)
                                    <option value="{{ $timezone }}" {{ ($dashboardSettings['timezone'] ?? config('app.timezone')) === $timezone ? 'selected' : '' }}>
                                        {{ $timezone }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Définissez votre fuseau horaire pour un affichage correct des dates et heures</div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="date_format" class="form-label">Format de date</label>
                            <select class="form-select" id="date_format" name="date_format">
                                <option value="d/m/Y" {{ ($dashboardSettings['date_format'] ?? 'd/m/Y') === 'd/m/Y' ? 'selected' : '' }}>JJ/MM/AAAA ({{ now()->format('d/m/Y') }})</option>
                                <option value="m/d/Y" {{ ($dashboardSettings['date_format'] ?? 'd/m/Y') === 'm/d/Y' ? 'selected' : '' }}>MM/JJ/AAAA ({{ now()->format('m/d/Y') }})</option>
                                <option value="Y-m-d" {{ ($dashboardSettings['date_format'] ?? 'd/m/Y') === 'Y-m-d' ? 'selected' : '' }}>AAAA-MM-JJ ({{ now()->format('Y-m-d') }})</option>
                                <option value="d M Y" {{ ($dashboardSettings['date_format'] ?? 'd/m/Y') === 'd M Y' ? 'selected' : '' }}>DD MMM AAAA ({{ now()->format('d M Y') }})</option>
                                <option value="F j, Y" {{ ($dashboardSettings['date_format'] ?? 'd/m/Y') === 'F j, Y' ? 'selected' : '' }}>Mois JJ, AAAA ({{ now()->format('F j, Y') }})</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="time_format" class="form-label">Format d'heure</label>
                            <select class="form-select" id="time_format" name="time_format">
                                <option value="H:i" {{ ($dashboardSettings['time_format'] ?? 'H:i') === 'H:i' ? 'selected' : '' }}>24 heures ({{ now()->format('H:i') }})</option>
                                <option value="h:i A" {{ ($dashboardSettings['time_format'] ?? 'H:i') === 'h:i A' ? 'selected' : '' }}>12 heures ({{ now()->format('h:i A') }})</option>
                            </select>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <h6 class="mb-3">Éléments à afficher</h6>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="show_recent_bookings" 
                                       name="show_recent_bookings" value="1" 
                                       {{ $dashboardSettings['show_recent_bookings'] ?? true ? 'checked' : '' }}>
                                <label class="form-check-label" for="show_recent_bookings">Afficher les réservations récentes</label>
                                <div class="form-text">Affiche la section des dernières réservations sur le tableau de bord</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="show_recent_sites" 
                                       name="show_recent_sites" value="1" 
                                       {{ $dashboardSettings['show_recent_sites'] ?? true ? 'checked' : '' }}>
                                <label class="form-check-label" for="show_recent_sites">Afficher les sites récents</label>
                                <div class="form-text">Affiche la section des derniers sites ajoutés</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="show_statistics" 
                                       name="show_statistics" value="1" 
                                       {{ $dashboardSettings['show_statistics'] ?? true ? 'checked' : '' }}>
                                <label class="form-check-label" for="show_statistics">Afficher les statistiques</label>
                                <div class="form-text">Affiche les cartes de statistiques en haut du tableau de bord</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="show_calendar" 
                                       name="show_calendar" value="1" 
                                       {{ $dashboardSettings['show_calendar'] ?? true ? 'checked' : '' }}>
                                <label class="form-check-label" for="show_calendar">Afficher le calendrier</label>
                                <div class="form-text">Affiche le calendrier des réservations sur le tableau de bord</div>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <h6 class="mb-3">Préférences d'affichage</h6>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="default_view" class="form-label">Vue par défaut</label>
                            <select class="form-select" id="default_view" name="default_view">
                                @foreach($views as $value => $label)
                                    <option value="{{ $value }}" {{ ($dashboardSettings['default_view'] ?? 'overview') === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">La vue qui s'affiche lorsque vous accédez au tableau de bord</div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="items_per_page" class="form-label">Éléments par page</label>
                            <input type="number" class="form-control" id="items_per_page" name="items_per_page" 
                                   min="5" max="100" value="{{ $dashboardSettings['items_per_page'] ?? 10 }}">
                            <div class="form-text">Nombre d'éléments à afficher par page dans les listes</div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="refresh_interval" class="form-label">Intervalle de rafraîchissement (secondes)</label>
                            <input type="number" class="form-control" id="refresh_interval" name="refresh_interval" 
                                   min="30" max="3600" step="30" value="{{ $dashboardSettings['refresh_interval'] ?? 300 }}">
                            <div class="form-text">Fréquence de rafraîchissement automatique du tableau de bord</div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between mt-5">
                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#resetSettingsModal">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Réinitialiser
                        </button>
                        
                        <div>
                            <a href="{{ route('site-manager.dashboard') }}" class="btn btn-outline-secondary me-2">
                                <i class="bi bi-x-lg me-1"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> Enregistrer les modifications
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">Aperçu des paramètres</h6>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h6>Thème sélectionné :</h6>
                    <div class="d-flex gap-2">
                        <div class="theme-preview theme-preview-light {{ ($dashboardSettings['theme'] ?? 'light') === 'light' ? 'active' : '' }}" data-theme="light">
                            <div class="theme-preview-header"></div>
                            <div class="theme-preview-sidebar"></div>
                            <div class="theme-preview-content"></div>
                            <div class="theme-preview-label">Clair</div>
                        </div>
                        <div class="theme-preview theme-preview-dark {{ ($dashboardSettings['theme'] ?? 'light') === 'dark' ? 'active' : '' }}" data-theme="dark">
                            <div class="theme-preview-header"></div>
                            <div class="theme-preview-sidebar"></div>
                            <div class="theme-preview-content"></div>
                            <div class="theme-preview-label">Sombre</div>
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <h6>Format de date et d'heure :</h6>
                    <div class="p-3 bg-light rounded">
                        <div class="mb-2">
                            <strong>Date :</strong> 
                            <span id="datePreview">{{ now()->format($dashboardSettings['date_format'] ?? 'd/m/Y') }}</span>
                        </div>
                        <div>
                            <strong>Heure :</strong> 
                            <span id="timePreview">{{ now()->format(($dashboardSettings['time_format'] ?? 'H:i') === 'H:i' ? 'H:i' : 'h:i A') }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <h6>Fuseau horaire :</h6>
                    <div class="p-3 bg-light rounded">
                        <div class="mb-2">
                            <strong>Sélectionné :</strong> 
                            <span id="timezonePreview">{{ $dashboardSettings['timezone'] ?? config('app.timezone') }}</span>
                        </div>
                        <div class="small text-muted">
                            Heure actuelle : 
                            <span id="currentTime">
                                {{ now()->setTimezone($dashboardSettings['timezone'] ?? config('app.timezone'))->format('H:i:s') }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <h6>Statistiques actuelles :</h6>
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>Sites actifs</span>
                            <span class="badge bg-primary rounded-pill">{{ $stats['active_sites'] }} / {{ $stats['total_sites'] }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>Réservations ce mois-ci</span>
                            <span class="badge bg-success rounded-pill">{{ $stats['total_bookings'] }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>Réservations en attente</span>
                            <span class="badge bg-warning text-dark rounded-pill">{{ $stats['pending_bookings'] }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>Chiffre d'affaires ({{ now()->format('M Y') }})</span>
                            <span class="badge bg-info rounded-pill">{{ number_format($stats['monthly_revenue'], 2, ',', ' ') }} €</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de réinitialisation -->
<div class="modal fade" id="resetSettingsModal" tabindex="-1" aria-labelledby="resetSettingsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resetSettingsModalLabel">Confirmer la réinitialisation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir réinitialiser tous les paramètres du tableau de bord aux valeurs par défaut ?</p>
                <p class="text-danger">Cette action est irréversible et supprimera toutes vos personnalisations.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <a href="{{ route('site-manager.settings.dashboard.reset') }}" class="btn btn-danger">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Réinitialiser
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .theme-preview {
        position: relative;
        width: 100%;
        height: 80px;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        overflow: hidden;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .theme-preview:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    .theme-preview.active {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    
    .theme-preview-light .theme-preview-header {
        height: 15px;
        background-color: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
    }
    
    .theme-preview-light .theme-preview-sidebar {
        position: absolute;
        top: 16px;
        left: 0;
        bottom: 20px;
        width: 30%;
        background-color: #f1f3f5;
    }
    
    .theme-preview-light .theme-preview-content {
        position: absolute;
        top: 16px;
        left: 30%;
        right: 0;
        bottom: 20px;
        background-color: #fff;
    }
    
    .theme-preview-dark .theme-preview-header {
        height: 15px;
        background-color: #212529;
        border-bottom: 1px solid #343a40;
    }
    
    .theme-preview-dark .theme-preview-sidebar {
        position: absolute;
        top: 16px;
        left: 0;
        bottom: 20px;
        width: 30%;
        background-color: #2c3034;
    }
    
    .theme-preview-dark .theme-preview-content {
        position: absolute;
        top: 16px;
        left: 30%;
        right: 0;
        bottom: 20px;
        background-color: #212529;
    }
    
    .theme-preview-label {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        text-align: center;
        font-size: 0.75rem;
        padding: 2px 0;
        background-color: rgba(0, 0, 0, 0.05);
    }
    
    .form-switch .form-check-input {
        width: 2.5em;
        margin-left: -2.7em;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='rgba%280, 0, 0, 0.25%29'/%3e%3c/svg%3e");
        background-position: left center;
        border-radius: 2em;
        transition: background-position 0.15s ease-in-out;
    }
    
    .form-switch .form-check-input:checked {
        background-position: right center;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e");
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mise à jour de l'aperçu du thème
        const themeSelect = document.getElementById('theme');
        const themePreviews = document.querySelectorAll('.theme-preview');
        
        themeSelect?.addEventListener('change', function() {
            const selectedTheme = this.value;
            themePreviews.forEach(preview => {
                if (preview.dataset.theme === selectedTheme) {
                    preview.classList.add('active');
                } else {
                    preview.classList.remove('active');
                }
            });
        });
        
        // Mise à jour de l'aperçu du format de date
        const dateFormatSelect = document.getElementById('date_format');
        const datePreview = document.getElementById('datePreview');
        
        dateFormatSelect?.addEventListener('change', function() {
            const now = new Date();
            const format = this.value;
            const formattedDate = formatDate(now, format);
            datePreview.textContent = formattedDate;
        });
        
        // Mise à jour de l'aperçu du format d'heure
        const timeFormatSelect = document.getElementById('time_format');
        const timePreview = document.getElementById('timePreview');
        
        timeFormatSelect?.addEventListener('change', function() {
            const now = new Date();
            const format = this.value;
            const formattedTime = formatTime(now, format);
            timePreview.textContent = formattedTime;
        });
        
        // Mise à jour de l'heure actuelle en temps réel
        function updateCurrentTime() {
            const timezone = document.getElementById('timezone')?.value || '{{ config('app.timezone') }}';
            const now = new Date();
            const options = { 
                timeZone: timezone,
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            };
            
            const timeString = now.toLocaleTimeString('fr-FR', options);
            const timezonePreview = document.getElementById('timezonePreview');
            const currentTimeElement = document.getElementById('currentTime');
            
            if (timezonePreview) {
                timezonePreview.textContent = timezone;
            }
            
            if (currentTimeElement) {
                currentTimeElement.textContent = timeString;
            }
        }
        
        // Mettre à jour l'heure toutes les secondes
        setInterval(updateCurrentTime, 1000);
        
        // Fonction utilitaire pour formater la date
        function formatDate(date, format) {
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            const monthNames = ["janvier", "février", "mars", "avril", "mai", "juin", "juillet", "août", "septembre", "octobre", "novembre", "décembre"];
            
            switch(format) {
                case 'd/m/Y':
                    return `${day}/${month}/${year}`;
                case 'm/d/Y':
                    return `${month}/${day}/${year}`;
                case 'Y-m-d':
                    return `${year}-${month}-${day}`;
                case 'd M Y':
                    return `${day} ${monthNames[date.getMonth()].substring(0, 3)} ${year}`;
                case 'F j, Y':
                    return `${monthNames[date.getMonth()]} ${parseInt(day)}, ${year}`;
                default:
                    return date.toLocaleDateString();
            }
        }
        
        // Fonction utilitaire pour formater l'heure
        function formatTime(date, format) {
            let hours = date.getHours();
            const minutes = String(date.getMinutes()).padStart(2, '0');
            
            if (format === 'h:i A') {
                const ampm = hours >= 12 ? 'PM' : 'AM';
                hours = hours % 12;
                hours = hours ? hours : 12; // Convertir 0 à 12 pour 12h format
                return `${hours}:${minutes} ${ampm}`;
            } else {
                // Format 24h
                return `${String(hours).padStart(2, '0')}:${minutes}`;
            }
        }
        
        // Gestion de la soumission du formulaire
        const form = document.getElementById('dashboardSettingsForm');
        form?.addEventListener('submit', function(e) {
            // Désactiver le bouton de soumission pour éviter les doubles soumissions
            const submitButton = this.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Enregistrement...';
            }
        });
    });
</script>
@endpush
