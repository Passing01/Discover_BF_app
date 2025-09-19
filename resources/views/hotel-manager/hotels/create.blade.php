@extends('layouts.hotel-manager')

@section('title', 'Ajouter un nouvel hôtel')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/m5Z5SaKGOGn+3qR4YQ="
          crossorigin=""/>
    <style>
        #map {
            height: 300px;
            width: 100%;
            border-radius: 0.5rem;
            margin-top: 1rem;
        }
        .amenity-item {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
        }
        .amenity-icon {
            margin-right: 0.5rem;
            color: #0d6efd;
        }
        .image-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 1rem;
        }
        .image-preview-item {
            position: relative;
            width: 150px;
            height: 100px;
            border-radius: 0.5rem;
            overflow: hidden;
            border: 1px solid #dee2e6;
        }
        .image-preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .remove-image {
            position: absolute;
            top: 0.25rem;
            right: 0.25rem;
            background-color: rgba(220, 53, 69, 0.8);
            color: white;
            border-radius: 50%;
            width: 1.5rem;
            height: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
    </style>
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0">Ajouter un nouvel hôtel</h2>
        <a href="{{ route('hotel-manager.hotels.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>
            Retour à la liste
        </a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('hotel-manager.hotels.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <!-- Informations de base -->
                <div class="mb-5">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-info-circle me-2 text-primary"></i>
                        Informations de base
                    </h5>
                    <p class="text-muted mb-4">Les informations essentielles pour votre établissement.</p>
                    
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label for="name" class="form-label">
                                Nom de l'hôtel <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4">
                            <label for="stars" class="form-label">
                                Nombre d'étoiles <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('stars') is-invalid @enderror" 
                                    id="stars" 
                                    name="stars" 
                                    required>
                                <option value="">Sélectionnez</option>
                                @for($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}" {{ old('stars') == $i ? 'selected' : '' }}>
                                        {{ $i }} étoile{{ $i > 1 ? 's' : '' }}
                                    </option>
                                @endfor
                            </select>
                            @error('stars')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12">
                            <label for="description" class="form-label">
                                Description <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="4" 
                                      required>{{ old('description') }}</textarea>
                            <div class="form-text">Décrivez votre établissement de manière attrayante.</div>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12">
                            <!-- Photo principale -->
                            <div class="mb-4">
                                <label class="form-label">
                                    Photo principale <span class="text-danger">*</span>
                                </label>
                                <input type="file" 
                                       id="main_photo" 
                                       name="main_photo" 
                                       class="form-control @error('main_photo') is-invalid @enderror" 
                                       accept="image/*" 
                                       required>
                                <small class="form-text text-muted">Cette photo sera affichée en tant qu'image principale de votre hôtel</small>
                                @error('main_photo')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Galerie de photos -->
                            <div class="mb-3">
                                <label class="form-label">
                                    Galerie de photos (optionnel)
                                </label>
                                <div class="border border-2 border-dashed rounded p-5 text-center">
                                    <i class="fas fa-images fa-3x text-muted mb-3"></i>
                                    <h6>Glissez et déposez vos images ici</h6>
                                    <p class="text-muted small mb-3">ou</p>
                                    <label for="photos" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-upload me-2"></i>
                                        Sélectionner des fichiers
                                        <input type="file" 
                                               id="photos" 
                                               name="photos[]" 
                                               class="d-none" 
                                               multiple 
                                               accept="image/*">
                                    </label>
                                    <p class="small text-muted mt-2 mb-0">
                                        Formats acceptés : JPG, PNG, JPEG (max 5 Mo par image)
                                    </p>
                                </div>
                                
                                <div id="image-preview" class="row g-2 mt-3">
                                    <!-- Les aperçus des images seront ajoutés ici -->
                                </div>
                                
                                @error('photos')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                @error('photos.*')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Adresse et contact -->
                <div class="mb-5 pt-4 border-top">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                        Adresse et contact
                    </h5>
                    <p class="text-muted mb-4">Où se trouve votre établissement et comment vous contacter ?</p>
                    
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label for="address" class="form-label">
                                Adresse <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('address') is-invalid @enderror" 
                                   id="address" 
                                   name="address" 
                                   value="{{ old('address') }}" 
                                   required>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4">
                            <label for="postal_code" class="form-label">
                                Code postal <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('postal_code') is-invalid @enderror" 
                                   id="postal_code" 
                                   name="postal_code" 
                                   value="{{ old('postal_code') }}" 
                                   required>
                            @error('postal_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="city" class="form-label">
                                Ville <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('city') is-invalid @enderror" 
                                   id="city" 
                                   name="city" 
                                   value="{{ old('city') }}" 
                                   required>
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="country" class="form-label">
                                Pays <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('country') is-invalid @enderror" 
                                    id="country" 
                                    name="country" 
                                    required>
                                <option value="">Sélectionnez un pays</option>
                                <option value="BF" {{ old('country') == 'BF' ? 'selected' : '' }}>Burkina Faso</option>
                                <option value="CI" {{ old('country') == 'CI' ? 'selected' : '' }}>Côte d'Ivoire</option>
                                <option value="ML" {{ old('country') == 'ML' ? 'selected' : '' }}>Mali</option>
                                <option value="NE" {{ old('country') == 'NE' ? 'selected' : '' }}>Niger</option>
                                <option value="SN" {{ old('country') == 'SN' ? 'selected' : '' }}>Sénégal</option>
                                <option value="TG" {{ old('country') == 'TG' ? 'selected' : '' }}>Togo</option>
                                <option value="BJ" {{ old('country') == 'BJ' ? 'selected' : '' }}>Bénin</option>
                                <option value="GN" {{ old('country') == 'GN' ? 'selected' : '' }}>Guinée</option>
                            </select>
                            @error('country')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">
                                Localisation sur la carte <span class="text-danger">*</span>
                            </label>
                            <div id="map" class="border rounded p-2"></div>
                            <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                            <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">
                            <div class="form-text">Veuillez confirmer l'emplacement sur la carte en cliquant sur l'emplacement exact.</div>
                            @error('latitude')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            @error('longitude')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="phone" class="form-label">
                                Téléphone <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <input type="tel" 
                                       class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" 
                                       name="phone" 
                                       value="{{ old('phone') }}" 
                                       required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="email" class="form-label">
                                Email de contact <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email', auth()->user()->email) }}" 
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="website" class="form-label">
                                Site web
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-globe"></i></span>
                                <input type="url" 
                                       class="form-control @error('website') is-invalid @enderror" 
                                       id="website" 
                                       name="website" 
                                       value="{{ old('website') }}"
                                       placeholder="https://exemple.com">
                                @error('website')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="check_in_time" class="form-label">
                                Heure d'arrivée <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-sign-in-alt"></i></span>
                                <input type="time" 
                                       class="form-control @error('check_in_time') is-invalid @enderror" 
                                       id="check_in_time" 
                                       name="check_in_time" 
                                       value="{{ old('check_in_time', '14:00') }}" 
                                       required>
                                @error('check_in_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="check_out_time" class="form-label">
                                Heure de départ <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-sign-out-alt"></i></span>
                                <input type="time" 
                                       class="form-control @error('check_out_time') is-invalid @enderror" 
                                       id="check_out_time" 
                                       name="check_out_time" 
                                       value="{{ old('check_out_time', '12:00') }}" 
                                       required>
                                @error('check_out_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                <!-- Horaires et politique d'annulation -->
                <div class="mb-5 pt-4 border-top">
                    <h5 class="card-title mb-3">
                        <i class="far fa-clock me-2 text-primary"></i>
                        Horaires et politique d'annulation
                    </h5>
                    <p class="text-muted mb-4">Définissez les horaires d'arrivée et de départ, ainsi que votre politique d'annulation.</p>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="cancellation_policy" class="form-label">
                                Politique d'annulation <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('cancellation_policy') is-invalid @enderror" 
                                    id="cancellation_policy" 
                                    name="cancellation_policy" 
                                    required>
                                <option value="flexible" {{ old('cancellation_policy') == 'flexible' ? 'selected' : '' }}>Flexible - Remboursement intégral jusqu'à 24h avant</option>
                                <option value="moderate" {{ old('cancellation_policy') == 'moderate' ? 'selected' : '' }}>Modérée - Remboursement à 50% jusqu'à 48h avant</option>
                                <option value="strict" {{ old('cancellation_policy') == 'strict' ? 'selected' : '' }}>Stricte - Aucun remboursement en cas d'annulation</option>
                            </select>
                            @error('cancellation_policy')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="min_stay" class="form-label">
                                Séjour minimum (nuits) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-moon"></i></span>
                                <input type="number" 
                                       class="form-control @error('min_stay') is-invalid @enderror" 
                                       id="min_stay" 
                                       name="min_stay" 
                                       min="1" 
                                       value="{{ old('min_stay', 1) }}" 
                                       required>
                                @error('min_stay')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Équipements et services -->
                <div class="mb-5 pt-4 border-top">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-concierge-bell me-2 text-primary"></i>
                        Équipements et services
                    </h5>
                    <p class="text-muted mb-4">Quels équipements et services proposez-vous à vos clients ?</p>
                    
                    <div class="border rounded p-3 bg-light">
                        <ul class="nav nav-tabs mb-4" id="amenityTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">
                                    <i class="fas fa-hotel me-1"></i> Général
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="services-tab" data-bs-toggle="tab" data-bs-target="#services" type="button" role="tab">
                                    <i class="fas fa-concierge-bell me-1"></i> Services
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="safety-tab" data-bs-toggle="tab" data-bs-target="#safety" type="button" role="tab">
                                    <i class="fas fa-shield-alt me-1"></i> Sécurité
                                </button>
                            </li>
                        </ul>
                        
                        <div class="row">
                            @foreach($amenities as $amenity)
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="amenity-{{ $amenity->id }}" 
                                               name="amenities[]" 
                                               value="{{ $amenity->id }}"
                                               {{ in_array($amenity->id, old('amenities', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="amenity-{{ $amenity->id }}">
                                            @if($amenity->icon)
                                                <i class="{{ $amenity->icon }} me-2 text-primary"></i>
                                            @endif
                                            {{ $amenity->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            
                            <!-- Services -->
                            <div class="tab-pane fade" id="services" role="tabpanel">
                                <div class="row">
                                    @foreach($amenities as $amenity)
                                        <div class="col-md-4 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       id="amenity-{{ $amenity->id }}" 
                                                       name="amenities[]" 
                                                       value="{{ $amenity->id }}"
                                                       {{ in_array($amenity->id, old('amenities', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="amenity-{{ $amenity->id }}">
                                                    <i class="{{ $amenity->icon }} me-2 text-primary"></i>
                                                    {{ $amenity->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            
                            <!-- Sécurité -->
                            <div class="tab-pane fade" id="safety" role="tabpanel">
                                <div class="row">
                                    @foreach($amenities as $amenity)
                                        <div class="col-md-4 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       id="amenity-{{ $amenity->id }}" 
                                                       name="amenities[]" 
                                                       value="{{ $amenity->id }}"
                                                       {{ in_array($amenity->id, old('amenities', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="amenity-{{ $amenity->id }}">
                                                    <i class="{{ $amenity->icon }} me-2 text-primary"></i>
                                                    {{ $amenity->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        
                        @error('amenities')
                            <div class="invalid-feedback d-block mt-3">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Règles de l'hôtel -->
                <div class="mb-5 pt-4 border-top">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-clipboard-list me-2 text-primary"></i>
                        Règles de l'hôtel
                    </h5>
                    <p class="text-muted mb-4">Définissez les règles que vos clients doivent respecter.</p>
                    
                    <div id="rules-container">
                        @if(old('rules'))
                            @foreach(old('rules') as $index => $rule)
                                <div class="rule-item mb-3">
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control @error('rules.' . $index) is-invalid @enderror" 
                                               name="rules[]" 
                                               value="{{ $rule }}" 
                                               placeholder="Ex: Pas de fumer dans les chambres"
                                               required>
                                        <button type="button" class="btn btn-outline-danger remove-rule">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        @error('rules.' . $index)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="rule-item mb-3">
                                <div class="input-group">
                                    <input type="text" 
                                           class="form-control" 
                                           name="rules[]" 
                                           placeholder="Ex: Pas de fumer dans les chambres"
                                           required>
                                    <button type="button" class="btn btn-outline-danger remove-rule">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <button type="button" id="add-rule" class="btn btn-outline-primary btn-sm mt-2">
                        <i class="fas fa-plus me-1"></i>
                        Ajouter une règle
                    </button>
                    
                    @error('rules')
                        <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                    @enderror
                    @error('rules.*')
                        <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- Form submission -->
                <div class="d-flex justify-content-between pt-4 border-top">
                    <a href="{{ route('hotel-manager.hotels.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>
                        Annuler
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>
                        Enregistrer l'hôtel
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
                integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
                crossorigin=""></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize Bootstrap tooltips
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Gestion de la carte
            let map, marker;
            const defaultLat = 48.8566; // Paris par défaut
            const defaultLng = 2.3522;

            // Initialisation de la carte
            map = L.map('map').setView([defaultLat, defaultLng], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Gestion du marqueur
            function updateMarker(lat, lng) {
                if (marker) {
                    map.removeLayer(marker);
                }
                marker = L.marker([lat, lng]).addTo(map);
                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lng;
            }

            // Clic sur la carte pour mettre à jour la position
            map.on('click', function(e) {
                updateMarker(e.latlng.lat, e.latlng.lng);
            });

            // Position initiale si des coordonnées existent
            const initialLat = parseFloat(document.getElementById('latitude').value) || defaultLat;
            const initialLng = parseFloat(document.getElementById('longitude').value) || defaultLng;
            updateMarker(initialLat, initialLng);
            map.setView([initialLat, initialLng], 13);

            // Gestion des images
            document.addEventListener('DOMContentLoaded', function() {
                // Éléments du DOM
                const mainPhotoInput = document.getElementById('main_photo');
                const galleryInput = document.getElementById('photos');
                const imagePreview = document.getElementById('image-preview');
                const dropZone = document.querySelector('.border-dashed');
                
                // Vérification des éléments
                if (!mainPhotoInput || !galleryInput || !imagePreview) {
                    console.error('Erreur: Éléments du formulaire introuvables');
                    return;
                }

                // Aperçu de la photo principale
                mainPhotoInput.addEventListener('change', function(e) {
                    const file = this.files[0];
                    if (!file) return;
                    
                    // Vérification du type de fichier
                    if (!file.type.startsWith('image/')) {
                        alert('Veuillez sélectionner une image valide pour la photo principale.');
                        this.value = '';
                        return;
                    }
                    
                    // Vérification de la taille (5 Mo max)
                    if (file.size > 5 * 1024 * 1024) {
                        alert('La photo principale ne doit pas dépasser 5 Mo.');
                        this.value = '';
                        return;
                    }
                    
                    // Afficher un aperçu
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const preview = document.createElement('div');
                        preview.className = 'mb-3';
                        preview.innerHTML = `
                            <div class="position-relative d-inline-block">
                                <img src="${e.target.result}" class="img-thumbnail" style="width: 200px; height: 150px; object-fit: cover;">
                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" 
                                        onclick="this.parentElement.parentElement.remove(); document.getElementById('main_photo').value = '';">
                                    ×
                                </button>
                            </div>
                        `;
                        
                        // Supprimer l'ancien aperçu s'il existe
                        const oldPreview = document.getElementById('main-photo-preview');
                        if (oldPreview) oldPreview.remove();
                        
                        preview.id = 'main-photo-preview';
                        mainPhotoInput.parentNode.insertBefore(preview, mainPhotoInput.nextSibling);
                    };
                    reader.readAsDataURL(file);
                });
                
                // Gestion de la galerie d'images
                galleryInput.addEventListener('change', function() {
                    if (!this.files || this.files.length === 0) return;
                    
                    // Parcourir chaque fichier
                    Array.from(this.files).forEach(file => {
                        // Vérification du type de fichier
                        if (!file.type.startsWith('image/')) {
                            showError(`Le fichier ${file.name} n'est pas une image valide.`);
                            return;
                        }
                        
                        // Vérification de la taille (5 Mo max)
                        if (file.size > 5 * 1024 * 1024) {
                            showError(`L'image ${file.name} dépasse la taille maximale de 5 Mo.`);
                            return;
                        }
                        
                        // Créer un aperçu
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const col = document.createElement('div');
                            col.className = 'col-6 col-md-4 col-lg-3';
                            col.innerHTML = `
                                <div class="position-relative">
                                    <img src="${e.target.result}" class="img-thumbnail w-100" style="height: 120px; object-fit: cover;">
                                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" 
                                            onclick="this.closest('.col-6').remove(); updateGalleryInput();">
                                        ×
                                    </button>
                                </div>
                            `;
                            imagePreview.appendChild(col);
                        };
                        reader.readAsDataURL(file);
                    });
                    
                    // Mettre à jour l'input de la galerie
                    updateGalleryInput();
                });
                
                // Fonction pour afficher les messages d'erreur
                function showError(message) {
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-danger alert-dismissible fade show';
                    alertDiv.role = 'alert';
                    alertDiv.innerHTML = `
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    `;
                    
                    // Insérer l'alerte avant la zone de dépôt
                    dropZone.parentNode.insertBefore(alertDiv, dropZone);
                    
                    // Supprimer l'alerte après 5 secondes
                    setTimeout(() => {
                        alertDiv.remove();
                    }, 5000);
                }
                
                // Mettre à jour l'input de la galerie avec les images restantes
                window.updateGalleryInput = function() {
                    const dataTransfer = new DataTransfer();
                    
                    // Récupérer les fichiers actuels
                    const currentFiles = Array.from(galleryInput.files);
                    
                    // Garder les fichiers qui ont encore un aperçu
                    const previews = imagePreview.querySelectorAll('img');
                    currentFiles.forEach(file => {
                        const hasPreview = Array.from(previews).some(img => 
                            img.src.startsWith('data:') && 
                            img.alt === file.name
                        );
                        
                        if (hasPreview) {
                            dataTransfer.items.add(file);
                        }
                    });
                    
                    // Mettre à jour l'input
                    galleryInput.files = dataTransfer.files;
                };
                
                // Gestion du glisser-déposer pour la galerie
                if (dropZone) {
                    // Événements de survol
                    dropZone.addEventListener('dragover', (e) => {
                        e.preventDefault();
                        dropZone.classList.add('border-primary', 'bg-light');
                    });
                    
                    dropZone.addEventListener('dragleave', () => {
                        dropZone.classList.remove('border-primary', 'bg-light');
                    });
                    
                    // Déposer les fichiers
                    dropZone.addEventListener('drop', (e) => {
                        e.preventDefault();
                        dropZone.classList.remove('border-primary', 'bg-light');
                        
                        if (e.dataTransfer.files.length) {
                            galleryInput.files = e.dataTransfer.files;
                            galleryInput.dispatchEvent(new Event('change'));
                        }
                    });
                }
            });

            function handleFiles(files) {
                console.log('Fichiers reçus:', files);
                
                // Vérifier le nombre de fichiers
                const maxFiles = 10;
                if (files.length > maxFiles) {
                    showAlert(`Vous ne pouvez télécharger que ${maxFiles} fichiers maximum.`, 'warning');
                    return;
                }
                
                // Vérifier la taille des fichiers (max 5 Mo)
                const maxSize = 5 * 1024 * 1024; // 5 Mo
                for (let i = 0; i < files.length; i++) {
                    if (files[i].size > maxSize) {
                        showAlert(`Le fichier ${files[i].name} dépasse la taille maximale de 5 Mo.`, 'danger');
                        return;
                    }
                    
                    // Vérifier le type de fichier
                    if (!files[i].type.match('image.*')) {
                        showAlert(`Le fichier ${files[i].name} n'est pas une image valide.`, 'danger');
                        return;
                    }
                }
                
                try {
                    // Créer un nouveau DataTransfer pour gérer les fichiers
                    const dataTransfer = new DataTransfer();
                    
                    // Ajouter les nouveaux fichiers
                    for (let i = 0; i < files.length; i++) {
                        dataTransfer.items.add(files[i]);
                    }
                    
                    // Mettre à jour l'input file
                    fileInput.files = dataTransfer.files;
                    
                    console.log('Fichiers après mise à jour:', fileInput.files);
                    
                    // Mettre à jour l'aperçu
                    updateImagePreview();
                } catch (error) {
                    console.error('Erreur lors de la gestion des fichiers:', error);
                    showAlert('Une erreur est survenue lors du chargement des images.', 'danger');
                }
            }

            function updateImagePreview() {
                console.log('Mise à jour de l\'aperçu des images');
                
                // Vider l'aperçu existant
                imagePreview.innerHTML = '';
                
                // Vérifier s'il y a des fichiers
                if (!fileInput.files || fileInput.files.length === 0) {
                    console.log('Aucun fichier à afficher');
                    return;
                }
                
                // Afficher les aperçus
                for (let i = 0; i < fileInput.files.length; i++) {
                    const file = fileInput.files[i];
                    
                    if (!file.type.match('image.*')) {
                        console.log('Fichier ignoré (pas une image):', file.name);
                        continue;
                    }
                    
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        const previewItem = document.createElement('div');
                        previewItem.className = 'image-preview-item';
                        previewItem.innerHTML = `
                            <img src="${e.target.result}" alt="Preview" class="img-fluid">
                            <div class="remove-image" data-index="${i}" title="Supprimer cette image">
                                <i class="fas fa-times"></i>
                            </div>
                        `;
                        imagePreview.appendChild(previewItem);
                        
                        // Gestion de la suppression d'image
                        const removeButton = previewItem.querySelector('.remove-image');
                        if (removeButton) {
                            removeButton.addEventListener('click', function(e) {
                                e.stopPropagation();
                                removeImage(parseInt(this.getAttribute('data-index')));
                            });
                        }
                    };
                    
                    reader.onerror = function(error) {
                        console.error('Erreur lors de la lecture du fichier:', file.name, error);
                    };
                    
                    reader.readAsDataURL(file);
                    console.log('Prévisualisation du fichier:', file.name);
                }
                
                // Afficher un message si aucune image valide n'a été trouvée
                if (imagePreview.children.length === 0) {
                    const noImagesMsg = document.createElement('div');
                    noImagesMsg.className = 'text-muted';
                    noImagesMsg.textContent = 'Aucune image valide sélectionnée';
                    imagePreview.appendChild(noImagesMsg);
                }
            }

            function removeImage(index) {
                try {
                    console.log('Suppression de l\'image à l\'index:', index);
                    
                    // Créer un nouveau tableau de fichiers sans l'élément à supprimer
                    const newFiles = Array.from(fileInput.files);
                    newFiles.splice(index, 1);
                    
                    // Mettre à jour l'input file
                    const newFileList = new DataTransfer();
                    newFiles.forEach(file => newFileList.items.add(file));
                    fileInput.files = newFileList.files;
                    
                    console.log('Nouvelle liste de fichiers:', fileInput.files);
                    
                    // Mettre à jour l'aperçu
                    updateImagePreview();
                } catch (error) {
                    console.error('Erreur lors de la suppression de l\'image:', error);
                    showAlert('Une erreur est survenue lors de la suppression de l\'image.', 'danger');
                }
            }

            // Gestion des règles d'hôtel
            const rulesContainer = document.getElementById('rules-container');
            const addRuleButton = document.getElementById('add-rule');
            let ruleCount = document.querySelectorAll('.rule-item').length;

            // Ajouter une règle
            addRuleButton.addEventListener('click', function() {
                const ruleItem = document.createElement('div');
                ruleItem.className = 'rule-item mb-3';
                ruleItem.innerHTML = `
                    <div class="input-group">
                        <input type="text" 
                               class="form-control" 
                               name="rules[]" 
                               placeholder="Ex: Pas de fumer dans les chambres"
                               required>
                        <button type="button" class="btn btn-outline-danger remove-rule">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
                
                // Insérer avant le bouton d'ajout
                addRuleButton.parentNode.insertBefore(ruleItem, addRuleButton);
                
                // Ajouter l'événement de suppression
                ruleItem.querySelector('.remove-rule').addEventListener('click', function() {
                    ruleItem.remove();
                });
                
                // Mettre le focus sur le nouveau champ
                ruleItem.querySelector('input').focus();
            });

            // Supprimer une règle (pour les règles existantes)
            document.addEventListener('click', function(e) {
                if (e.target.closest('.remove-rule')) {
                    e.preventDefault();
                    e.target.closest('.rule-item').remove();
                }
            });

            // Validation du formulaire
            const form = document.querySelector('form');
            
            form.addEventListener('submit', function(e) {
                let isValid = true;
                
                // Réinitialiser les états de validation
                const formControls = form.querySelectorAll('.form-control');
                formControls.forEach(control => {
                    control.classList.remove('is-invalid');
                });
                
                // Supprimer les messages d'erreur existants
                const existingAlerts = form.querySelectorAll('.alert');
                existingAlerts.forEach(alert => alert.remove());
                
                // Vérifier les champs requis
                const requiredFields = form.querySelectorAll('[required]');
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.classList.add('is-invalid');
                        
                        // Ajouter un message d'erreur si c'est un champ de règle
                        if (field.name === 'rules[]') {
                            const errorDiv = document.createElement('div');
                            errorDiv.className = 'invalid-feedback';
                            errorDiv.textContent = 'Ce champ est requis.';
                            field.parentNode.appendChild(errorDiv);
                        }
                    }
                });
                
                // Vérifier qu'au moins une photo est sélectionnée
                if (fileInput.files.length === 0) {
                    isValid = false;
                    showAlert('Veuillez sélectionner au moins une photo.', 'danger', fileInput.parentNode);
                }
                
                // Vérifier les coordonnées GPS
                if (!document.getElementById('latitude').value || !document.getElementById('longitude').value) {
                    isValid = false;
                    showAlert('Veuillez cliquer sur la carte pour définir l\'emplacement de l\'hôtel.', 'danger', document.getElementById('map').parentNode);
                }
                
                if (!isValid) {
                    e.preventDefault();
                    
                    // Défiler vers le premier champ invalide
                    const firstInvalid = form.querySelector('.is-invalid');
                    if (firstInvalid) {
                        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                    
                    // Afficher une alerte générale
                    showAlert('Veuillez corriger les erreurs dans le formulaire avant de soumettre.', 'danger', form);
                }
            });
            
            // Fonction utilitaire pour afficher des alertes
            function showAlert(message, type = 'info', container = null) {
                console.log(`[${type.toUpperCase()}] ${message}`);
                
                // Créer l'élément d'alerte
                const alertDiv = document.createElement('div');
                alertDiv.className = `alert alert-${type} alert-dismissible fade show mt-3`;
                alertDiv.role = 'alert';
                alertDiv.innerHTML = `
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                `;
                
                // Ajouter l'alerte au début du formulaire
                const firstFormGroup = form.querySelector('.mb-5');
                if (firstFormGroup) {
                    form.insertBefore(alertDiv, firstFormGroup);
                } else {
                    form.prepend(alertDiv);
                }
                
                // Fermer automatiquement l'alerte après 5 secondes
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alertDiv);
                    bsAlert.close();
                }, 5000);
            }
                                <label for="rules[${ruleCount - 1}]" class="sr-only">Règle ${ruleCount}</label>
                                <input type="text" name="rules[]" id="rules[${ruleCount - 1}]" required
                                       class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                       placeholder="Ex: Pas de fête dans les chambres">
                            </div>
                            <button type="button" class="ml-2 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 remove-rule">
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    `;
                    
                    rulesContainer.insertBefore(ruleItem, addRuleButton.parentNode);
                    
                    // Ajouter un gestionnaire pour supprimer la règle
                    const removeButton = ruleItem.querySelector('.remove-rule');
                    removeButton.addEventListener('click', function() {
                        ruleItem.remove();
                        updateRuleIndexes();
                    });
                });
                
                // Mettre à jour les index des règles
                function updateRuleIndexes() {
                    const ruleItems = document.querySelectorAll('.rule-item');
                    ruleItems.forEach((item, index) => {
                        const input = item.querySelector('input');
                        input.name = `rules[${index}]`;
                        input.id = `rules[${index}]`;
                        input.previousElementSibling.htmlFor = `rules[${index}]`;
                        input.previousElementSibling.textContent = `Règle ${index + 1}`;
                    });
                    ruleCount = ruleItems.length;
                }
                
                // Ajouter des gestionnaires pour les boutons de suppression existants
                document.querySelectorAll('.remove-rule').forEach(button => {
                    button.addEventListener('click', function() {
                        this.closest('.rule-item').remove();
                        updateRuleIndexes();
                    });
                });
                
                // Validation du formulaire
                const form = document.querySelector('form');
                form.addEventListener('submit', function(e) {
                    let isValid = true;
                    
                    // Vérifier les champs requis
                    const requiredFields = form.querySelectorAll('[required]');
                    requiredFields.forEach(field => {
                        if (!field.value.trim()) {
                            isValid = false;
                            field.classList.add('border-red-500');
                        } else {
                            field.classList.remove('border-red-500');
                        }
                    });
                    
                    // Vérifier qu'au moins une photo est sélectionnée
                    const fileInput = document.getElementById('photos');
                    if (fileInput.files.length === 0) {
                        isValid = false;
                        fileInput.classList.add('border-red-500');
                    } else {
                        fileInput.classList.remove('border-red-500');
                    }
                    
                    // Vérifier qu'au moins une règle est définie
                    const ruleInputs = document.querySelectorAll('input[name^="rules["]');
                    if (ruleInputs.length === 0) {
                        isValid = false;
                        const rulesContainer = document.getElementById('rules-container');
                        if (!rulesContainer.classList.contains('border-red-500')) {
                            rulesContainer.classList.add('border', 'border-red-500', 'p-2', 'rounded');
                        }
                    } else {
                        document.getElementById('rules-container').classList.remove('border-red-500', 'border', 'p-2', 'rounded');
                    }
                    
                    if (!isValid) {
                        e.preventDefault();
                        
                        // Faire défiler jusqu'au premier champ invalide
                        const firstInvalid = form.querySelector('.border-red-500');
                        if (firstInvalid) {
                            firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                        
                        // Afficher un message d'erreur
                        alert('Veuillez remplir tous les champs obligatoires.');
                    }
                });
                
                // Initialiser Flatpickr pour les champs de temps
                flatpickr("input[type=time]", {
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i",
                    time_24hr: true
                });
            });
        </script>
    @endpush
@endsection
