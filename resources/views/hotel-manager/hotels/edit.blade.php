@extends('layouts.hotel-manager')

@section('title', 'Modifier l\'hôtel : ' . $hotel->name)

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
        .amenity-item input[type="checkbox"] {
            margin-right: 0.5rem;
        }
        .rule-item {
            display: flex;
            align-items: center;
            margin-bottom: 0.75rem;
        }
        .rule-item .form-control {
            flex-grow: 1;
            margin-right: 0.5rem;
        }
        .image-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 1rem;
        }
        .image-preview-item {
            position: relative;
            width: 120px;
            height: 120px;
            border-radius: 0.5rem;
            overflow: hidden;
            border: 1px solid #dee2e6;
        }
        .image-preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .image-preview-item .remove-image {
            position: absolute;
            top: 0.25rem;
            right: 0.25rem;
            background: rgba(0, 0, 0, 0.6);
            color: white;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
    </style>
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0">Modifier l'hôtel : {{ $hotel->name }}</h2>
        <a href="{{ route('hotel-manager.hotels.show', $hotel) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>
            Retour
        </a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('hotel-manager.hotels.update', $hotel) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <!-- Informations de base -->
                <div class="mb-5">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-info-circle me-2 text-primary"></i>
                        Informations de base
                    </h5>
                    
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label for="name" class="form-label">
                                Nom de l'hôtel <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $hotel->name) }}" 
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
                                <option value="" disabled {{ old('stars', $hotel->stars) === null ? 'selected' : '' }}>Sélectionnez...</option>
                                @for($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}" {{ old('stars', $hotel->stars) == $i ? 'selected' : '' }}>
                                        {{ $i }} {{ $i > 1 ? 'étoiles' : 'étoile' }}
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
                                      required>{{ old('description', $hotel->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Adresse et contact -->
                <div class="mb-5 pt-4 border-top">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                        Adresse et contact
                    </h5>
                    
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label for="address" class="form-label">
                                Adresse <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('address') is-invalid @enderror" 
                                   id="address" 
                                   name="address" 
                                   value="{{ old('address', $hotel->address) }}" 
                                   required>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4">
                            <label for="city" class="form-label">
                                Ville <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('city') is-invalid @enderror" 
                                   id="city" 
                                   name="city" 
                                   value="{{ old('city', $hotel->city) }}" 
                                   required>
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4">
                            <label for="country" class="form-label">
                                Pays <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('country') is-invalid @enderror" 
                                   id="country" 
                                   name="country" 
                                   value="{{ old('country', $hotel->country) }}" 
                                   required>
                            @error('country')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4">
                            <label for="postal_code" class="form-label">
                                Code postal
                            </label>
                            <input type="text" 
                                   class="form-control @error('postal_code') is-invalid @enderror" 
                                   id="postal_code" 
                                   name="postal_code" 
                                   value="{{ old('postal_code', $hotel->postal_code) }}">
                            @error('postal_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4">
                            <label for="email" class="form-label">
                                Email <span class="text-danger">*</span>
                            </label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $hotel->email) }}" 
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="phone" class="form-label">
                                Téléphone <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">+</span>
                                <input type="tel" 
                                       class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" 
                                       name="phone" 
                                       value="{{ old('phone', $hotel->phone) }}" 
                                       required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="website" class="form-label">
                                Site web
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">https://</span>
                                <input type="text" 
                                       class="form-control @error('website') is-invalid @enderror" 
                                       id="website" 
                                       name="website" 
                                       value="{{ old('website', str_replace(['https://', 'http://'], '', $hotel->website)) }}">
                                @error('website')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <label for="map" class="form-label">
                                Localisation sur la carte <span class="text-danger">*</span>
                            </label>
                            <div id="map"></div>
                            <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude', $hotel->latitude) }}">
                            <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude', $hotel->longitude) }}">
                            @error('latitude')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            @error('longitude')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Horaires -->
                <div class="mb-5 pt-4 border-top">
                    <h5 class="card-title mb-3">
                        <i class="far fa-clock me-2 text-primary"></i>
                        Horaires
                    </h5>
                    
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="check_in_time" class="form-label">
                                Heure d'arrivée <span class="text-danger">*</span>
                            </label>
                            <input type="time" 
                                   class="form-control @error('check_in_time') is-invalid @enderror" 
                                   id="check_in_time" 
                                   name="check_in_time" 
                                   value="{{ old('check_in_time', $hotel->check_in_time) }}" 
                                   required>
                            @error('check_in_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-3">
                            <label for="check_out_time" class="form-label">
                                Heure de départ <span class="text-danger">*</span>
                            </label>
                            <input type="time" 
                                   class="form-control @error('check_out_time') is-invalid @enderror" 
                                   id="check_out_time" 
                                   name="check_out_time" 
                                   value="{{ old('check_out_time', $hotel->check_out_time) }}" 
                                   required>
                            @error('check_out_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12">
                            <label for="cancellation_policy" class="form-label">
                                Politique d'annulation
                            </label>
                            <textarea class="form-control @error('cancellation_policy') is-invalid @enderror" 
                                      id="cancellation_policy" 
                                      name="cancellation_policy" 
                                      rows="2">{{ old('cancellation_policy', $hotel->cancellation_policy) }}</textarea>
                            @error('cancellation_policy')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Photos -->
                <div class="mb-5 pt-4 border-top">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-images me-2 text-primary"></i>
                        Photos de l'hôtel
                    </h5>
                    
                    <div class="mb-3">
                        <label for="photos" class="form-label">
                            Ajouter des photos
                        </label>
                        <input type="file" 
                               class="form-control @error('photos') is-invalid @enderror" 
                               id="photos" 
                               name="photos[]" 
                               multiple 
                               accept="image/*">
                        <div class="form-text">
                            Vous pouvez sélectionner plusieurs images. Formats acceptés : JPG, PNG, WEBP. Taille maximale : 5 Mo par image.
                        </div>
                        @error('photos')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        @error('photos.*')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div id="image-preview" class="image-preview">
                        @if($hotel->photos->isNotEmpty())
                            @foreach($hotel->photos as $photo)
                                <div class="image-preview-item">
                                    <img src="{{ $photo->url }}" alt="Photo de l'hôtel">
                                    <button type="button" class="remove-image" data-photo-id="{{ $photo->id }}">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <!-- Équipements -->
                <div class="mb-5 pt-4 border-top">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-concierge-bell me-2 text-primary"></i>
                        Équipements et services
                    </h5>
                    
                    <div class="border rounded p-3 bg-light">
                        <div class="row">
                            @foreach($amenities as $amenity)
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="amenity-{{ $amenity->id }}" 
                                               name="amenities[]" 
                                               value="{{ $amenity->id }}"
                                               {{ in_array($amenity->id, old('amenities', $hotel->amenities->pluck('id')->toArray())) ? 'checked' : '' }}>
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
                        
                        @error('amenities')
                            <div class="invalid-feedback d-block mt-3">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Règles de l'hôtel -->
                <div class="mb-5 pt-4 border-top">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-list-check me-2 text-primary"></i>
                        Règles de l'hôtel
                    </h5>
                    
                    <div id="rules-container">
                        @if(old('rules') && count(old('rules')) > 0)
                            @foreach(old('rules') as $index => $rule)
                                <div class="rule-item">
                                    <input type="text" 
                                           name="rules[]" 
                                           class="form-control @if($errors->has('rules.' . $index)) is-invalid @endif" 
                                           value="{{ $rule }}" 
                                           placeholder="Ex: Pas de fête dans les chambres"
                                           required>
                                    <button type="button" class="btn btn-outline-danger remove-rule">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    @if($errors->has('rules.' . $index))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('rules.' . $index) }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @elseif($hotel->rules->isNotEmpty())
                            @foreach($hotel->rules as $rule)
                                <div class="rule-item">
                                    <input type="text" 
                                           name="rules[]" 
                                           class="form-control" 
                                           value="{{ $rule->description }}" 
                                           placeholder="Ex: Pas de fête dans les chambres"
                                           required>
                                    <button type="button" class="btn btn-outline-danger remove-rule">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            @endforeach
                        @else
                            <div class="rule-item">
                                <input type="text" 
                                       name="rules[]" 
                                       class="form-control" 
                                       placeholder="Ex: Pas de fête dans les chambres"
                                       required>
                                <button type="button" class="btn btn-outline-danger remove-rule">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        @endif
                    </div>
                    
                    <button type="button" id="add-rule" class="btn btn-outline-primary mt-3">
                        <i class="fas fa-plus me-1"></i> Ajouter une règle
                    </button>
                    
                    @error('rules')
                        <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between pt-4 border-top">
                    <a href="{{ route('hotel-manager.hotels.show', $hotel) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i> Annuler
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
            crossorigin=""></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize map
            const map = L.map('map').setView([{{ $hotel->latitude ?? '12.3714' }}, {{ $hotel->longitude ?? '-1.5197' }}], 13);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
            
            let marker;
            
            // If we have coordinates, add a marker
            @if($hotel->latitude && $hotel->longitude)
                marker = L.marker([{{ $hotel->latitude }}, {{ $hotel->longitude }}]).addTo(map);
            @endif
            
            // Update marker on map click
            map.on('click', function(e) {
                if (marker) {
                    map.removeLayer(marker);
                }
                
                marker = L.marker(e.latlng).addTo(map);
                document.getElementById('latitude').value = e.latlng.lat;
                document.getElementById('longitude').value = e.latlng.lng;
                
                // Update address using reverse geocoding
                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${e.latlng.lat}&lon=${e.latlng.lng}&accept-language=fr`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.display_name) {
                            document.getElementById('address').value = data.display_name.split(',')[0];
                            
                            if (data.address) {
                                const addr = data.address;
                                if (addr.city) document.getElementById('city').value = addr.city;
                                else if (addr.town) document.getElementById('city').value = addr.town;
                                else if (addr.village) document.getElementById('city').value = addr.village;
                                
                                if (addr.country) document.getElementById('country').value = addr.country;
                                if (addr.postcode) document.getElementById('postal_code').value = addr.postcode;
                            }
                        }
                    })
                    .catch(error => console.error('Error:', error));
            });
            
            // Handle image preview
            const imagePreview = document.getElementById('image-preview');
            const fileInput = document.getElementById('photos');
            
            fileInput.addEventListener('change', function(e) {
                updateImagePreview();
            });
            
            // Handle removal of existing images
            document.addEventListener('click', function(e) {
                if (e.target.closest('.remove-image')) {
                    const button = e.target.closest('.remove-image');
                    const photoId = button.dataset.photoId;
                    
                    if (confirm('Voulez-vous vraiment supprimer cette photo ?')) {
                        // Add a hidden input to mark the photo for deletion
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'deleted_photos[]';
                        input.value = photoId;
                        document.querySelector('form').appendChild(input);
                        
                        // Remove the image preview
                        button.closest('.image-preview-item').remove();
                    }
                }
            });
            
            function updateImagePreview() {
                // Clear existing preview
                const existingPreviews = imagePreview.querySelectorAll('.new-image-preview');
                existingPreviews.forEach(preview => preview.remove());
                
                // Add new previews
                if (fileInput.files) {
                    Array.from(fileInput.files).forEach(file => {
                        const reader = new FileReader();
                        
                        reader.onload = function(e) {
                            const previewItem = document.createElement('div');
                            previewItem.className = 'image-preview-item new-image-preview';
                            previewItem.innerHTML = `
                                <img src="${e.target.result}" alt="Aperçu de l'image">
                                <button type="button" class="remove-image">
                                    <i class="fas fa-times"></i>
                                </button>
                            `;
                            imagePreview.appendChild(previewItem);
                        };
                        
                        reader.readAsDataURL(file);
                    });
                }
            }
            
            // Handle rules management
            const rulesContainer = document.getElementById('rules-container');
            const addRuleButton = document.getElementById('add-rule');
            
            addRuleButton.addEventListener('click', function() {
                const ruleItem = document.createElement('div');
                ruleItem.className = 'rule-item mt-2';
                ruleItem.innerHTML = `
                    <input type="text" 
                           name="rules[]" 
                           class="form-control" 
                           placeholder="Ex: Pas de fête dans les chambres"
                           required>
                    <button type="button" class="btn btn-outline-danger remove-rule">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                rulesContainer.appendChild(ruleItem);
            });
            
            rulesContainer.addEventListener('click', function(e) {
                if (e.target.closest('.remove-rule')) {
                    const ruleItem = e.target.closest('.rule-item');
                    if (rulesContainer.querySelectorAll('.rule-item').length > 1) {
                        ruleItem.remove();
                    } else {
                        // If it's the last rule, just clear the input
                        ruleItem.querySelector('input').value = '';
                    }
                }
            });
            
            // Form validation
            const form = document.querySelector('form');
            
            form.addEventListener('submit', function(e) {
                let isValid = true;
                
                // Reset validation states
                const formControls = form.querySelectorAll('.form-control');
                formControls.forEach(control => {
                    control.classList.remove('is-invalid');
                });
                
                // Validate required fields
                const requiredFields = form.querySelectorAll('[required]');
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        field.classList.add('is-invalid');
                        isValid = false;
                    }
                });
                
                // Validate email format
                const emailField = form.querySelector('input[type="email"]');
                if (emailField && emailField.value) {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(emailField.value)) {
                        emailField.classList.add('is-invalid');
                        emailField.nextElementSibling.textContent = 'Veuillez entrer une adresse email valide.';
                        isValid = false;
                    }
                }
                
                // Validate at least one amenity is selected
                const amenities = form.querySelectorAll('input[name="amenities[]"]:checked');
                if (amenities.length === 0) {
                    const amenityError = document.createElement('div');
                    amenityError.className = 'invalid-feedback d-block mt-3';
                    amenityError.textContent = 'Veuillez sélectionner au moins un équipement.';
                    
                    const amenityContainer = document.querySelector('#amenityTabs');
                    if (amenityContainer && !document.querySelector('#amenity-error')) {
                        amenityError.id = 'amenity-error';
                        amenityContainer.parentNode.insertBefore(amenityError, amenityContainer.nextSibling);
                    }
                    
                    isValid = false;
                } else {
                    const errorElement = document.querySelector('#amenity-error');
                    if (errorElement) {
                        errorElement.remove();
                    }
                }
                
                if (!isValid) {
                    e.preventDefault();
                    
                    // Scroll to first error
                    const firstError = form.querySelector('.is-invalid');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            });
            
            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endpush
