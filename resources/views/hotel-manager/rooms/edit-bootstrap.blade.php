@extends('layouts.hotel-manager')

@section('content')
    @push('styles')
        <style>
            .image-preview {
                display: flex;
                flex-wrap: wrap;
                gap: 15px;
                margin-top: 15px;
            }
            .image-preview-item {
                position: relative;
                width: 150px;
                height: 100px;
                border-radius: 0.375rem;
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
                top: 5px;
                right: 5px;
                background-color: rgba(220, 53, 69, 0.8);
                color: white;
                border-radius: 50%;
                width: 24px;
                height: 24px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                border: none;
                padding: 0;
            }
            .amenity-item {
                display: flex;
                align-items: center;
                margin-bottom: 8px;
            }
            .amenity-icon {
                margin-right: 8px;
                color: #0d6efd;
            }
            .existing-image {
                position: relative;
                transition: all 0.2s;
                margin-bottom: 15px;
            }
            .existing-image:hover {
                transform: scale(1.02);
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            }
        </style>
    @endpush

    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="h3 mb-0">Modifier la chambre</h1>
                    <a href="{{ route('hotels.rooms.show', ['hotel' => $hotel, 'room' => $room]) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Retour
                    </a>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <form action="{{ route('hotels.rooms.update', ['hotel' => $hotel, 'room' => $room]) }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf
                @method('PUT')
                
                <div class="card-body">
                    <!-- Informations de base -->
                    <div class="mb-5">
                        <div class="mb-4">
                            <h3 class="h5 mb-3">
                                <i class="fas fa-info-circle me-2"></i>Informations de base
                            </h3>
                            <p class="text-muted small mb-0">
                                Les informations essentielles pour votre chambre.
                            </p>
                        </div>

                        <div class="row g-3">
                            <!-- Nom de la chambre -->
                            <div class="col-md-8">
                                <label for="name" class="form-label">
                                    Nom de la chambre <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $room->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Type de chambre -->
                            <div class="col-md-4">
                                <label for="type" class="form-label">
                                    Type de chambre <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="" disabled {{ old('type', $room->type) ? '' : 'selected' }}>Sélectionnez un type</option>
                                    @foreach($roomTypes as $value => $label)
                                        <option value="{{ $value }}" {{ old('type', $room->type) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <label for="description" class="form-label">
                                    Description <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="4" required>{{ old('description', $room->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Prix par nuit -->
                            <div class="col-md-4">
                                <label for="price_per_night" class="form-label">
                                    Prix par nuit (FCFA) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">FCFA</span>
                                    <input type="number" class="form-control @error('price_per_night') is-invalid @enderror" 
                                           id="price_per_night" name="price_per_night" 
                                           value="{{ old('price_per_night', $room->price_per_night) }}" required>
                                    @error('price_per_night')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Capacité -->
                            <div class="col-md-4">
                                <label for="capacity" class="form-label">
                                    Capacité (personnes) <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control @error('capacity') is-invalid @enderror" 
                                       id="capacity" name="capacity" min="1"
                                       value="{{ old('capacity', $room->capacity) }}" required>
                                @error('capacity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Statut -->
                            <div class="col-md-4">
                                <label class="form-label d-block">
                                    Statut
                                </label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" 
                                           id="is_available" name="is_available" value="1"
                                           {{ old('is_available', $room->is_available) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_available">Disponible</label>
                                </div>
                            </div>

                            <!-- Téléchargement des photos -->
                            <div class="col-12">
                                <label class="form-label">
                                    Photos
                                    @if($room->photos->isEmpty())
                                        <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <div class="border-2 border-dashed rounded p-5 text-center">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                        <div class="mb-2">
                                            <label for="photos" class="btn btn-link text-decoration-none p-0">
                                                <span class="text-primary">Téléverser des fichiers</span>
                                                <input id="photos" name="photos[]" type="file" class="d-none" multiple>
                                            </label>
                                            <span class="ms-2">ou glisser-déposer</span>
                                        </div>
                                        <p class="text-muted small mb-0">
                                            PNG, JPG, GIF jusqu'à 10MB
                                        </p>
                                    </div>
                                </div>
                                @error('photos')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                @error('photos.*')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror

                                <!-- Aperçu des nouvelles images -->
                                <div id="image-preview" class="image-preview">
                                    <!-- Les aperçus des nouvelles images seront ajoutés ici -->
                                </div>

                                <!-- Photos existantes -->
                                @if($room->photos->isNotEmpty())
                                    <div class="mt-4">
                                        <label class="form-label">
                                            Photos existantes
                                        </label>
                                        <div class="d-flex flex-wrap gap-3" id="existing-photos">
                                            @foreach($room->photos as $photo)
                                                <div class="position-relative existing-image" data-photo-id="{{ $photo->id }}">
                                                    <img src="{{ Storage::url($photo->path) }}" alt="Photo de la chambre" class="img-thumbnail" style="height: 150px; width: auto;">
                                                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 rounded-circle p-1" data-photo-id="{{ $photo->id }}">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                    <input type="hidden" name="existing_photos[]" value="{{ $photo->id }}">
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Caractéristiques de la chambre -->
                    <div class="mb-5 pt-5 border-top">
                        <div class="mb-4">
                            <h3 class="h5 mb-3">
                                <i class="fas fa-list-ul me-2"></i>Caractéristiques
                            </h3>
                            <p class="text-muted small mb-0">
                                Détails sur les caractéristiques de la chambre.
                            </p>
                        </div>

                        <div class="row g-3">
                            <!-- Superficie -->
                            <div class="col-md-4">
                                <label for="size" class="form-label">
                                    Superficie (m²)
                                </label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('size') is-invalid @enderror" 
                                           id="size" name="size" min="0" step="0.1"
                                           value="{{ old('size', $room->size) }}">
                                    <span class="input-group-text">m²</span>
                                    @error('size')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Type de lit -->
                            <div class="col-md-4">
                                <label for="bed_type" class="form-label">
                                    Type de lit
                                </label>
                                <select class="form-select @error('bed_type') is-invalid @enderror" id="bed_type" name="bed_type">
                                    <option value="" {{ old('bed_type', $room->bed_type) ? '' : 'selected' }}>Sélectionnez un type de lit</option>
                                    <option value="simple" {{ old('bed_type', $room->bed_type) == 'simple' ? 'selected' : '' }}>Simple</option>
                                    <option value="double" {{ old('bed_type', $room->bed_type) == 'double' ? 'selected' : '' }}>Double</option>
                                    <option value="queen" {{ old('bed_type', $room->bed_type) == 'queen' ? 'selected' : '' }}>Queen</option>
                                    <option value="king" {{ old('bed_type', $room->bed_type) == 'king' ? 'selected' : '' }}>King</option>
                                    <option value="twin" {{ old('bed_type', $room->bed_type) == 'twin' ? 'selected' : '' }}>Lits jumeaux</option>
                                    <option value="bunk" {{ old('bed_type', $room->bed_type) == 'bunk' ? 'selected' : '' }}>Lits superposés</option>
                                </select>
                                @error('bed_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Vue -->
                            <div class="col-md-4">
                                <label for="view" class="form-label">
                                    Vue
                                </label>
                                <select class="form-select @error('view') is-invalid @enderror" id="view" name="view">
                                    <option value="" {{ old('view', $room->view) ? '' : 'selected' }}>Sélectionnez une vue</option>
                                    <option value="garden" {{ old('view', $room->view) == 'garden' ? 'selected' : '' }}>Jardin</option>
                                    <option value="pool" {{ old('view', $room->view) == 'pool' ? 'selected' : '' }}>Piscine</option>
                                    <option value="sea" {{ old('view', $room->view) == 'sea' ? 'selected' : '' }}>Mer</option>
                                    <option value="mountain" {{ old('view', $room->view) == 'mountain' ? 'selected' : '' }}>Montagne</option>
                                    <option value="city" {{ old('view', $room->view) == 'city' ? 'selected' : '' }}>Ville</option>
                                </select>
                                @error('view')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Équipements -->
                            <div class="col-12">
                                <label class="form-label">
                                    Équipements de la chambre
                                </label>
                                <div class="row g-3">
                                    @foreach($amenities as $amenity)
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       id="amenity-{{ $amenity->id }}" 
                                                       name="amenities[]" 
                                                       value="{{ $amenity->id }}"
                                                       {{ in_array($amenity->id, old('amenities', $room->amenities->pluck('id')->toArray())) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="amenity-{{ $amenity->id }}">
                                                    {{ $amenity->name }}
                                                    @if($amenity->description)
                                                        <small class="d-block text-muted">{{ $amenity->description }}</small>
                                                    @endif
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('amenities')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Options de réservation -->
                    <div class="mb-5 pt-5 border-top">
                        <div class="mb-4">
                            <h3 class="h5 mb-3">
                                <i class="fas fa-calendar-check me-2"></i>Options de réservation
                            </h3>
                            <p class="text-muted small mb-0">
                                Configurez les options de réservation pour cette chambre.
                            </p>
                        </div>

                        <div class="row g-3">
                            <!-- Fumeur autorisé -->
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" role="switch" 
                                           id="is_smoking_allowed" name="is_smoking_allowed" value="1"
                                           {{ old('is_smoking_allowed', $room->is_smoking_allowed) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_smoking_allowed">
                                        Fumeur autorisé
                                    </label>
                                    <div class="form-text">Cochez cette case si la chambre est destinée aux fumeurs.</div>
                                </div>
                                @error('is_smoking_allowed')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Petit-déjeuner inclus -->
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" role="switch" 
                                           id="has_breakfast" name="has_breakfast" value="1"
                                           {{ old('has_breakfast', $room->has_breakfast) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="has_breakfast">
                                        Petit-déjeuner inclus
                                    </label>
                                    <div class="form-text">Cochez cette case si le petit-déjeuner est inclus dans le prix.</div>
                                </div>
                                @error('has_breakfast')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Heure d'arrivée -->
                            <div class="col-md-6">
                                <label for="check_in_time" class="form-label">
                                    Heure d'arrivée
                                </label>
                                <input type="time" class="form-control @error('check_in_time') is-invalid @enderror" 
                                       id="check_in_time" name="check_in_time"
                                       value="{{ old('check_in_time', $room->check_in_time ?? '14:00') }}">
                                @error('check_in_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Heure de départ -->
                            <div class="col-md-6">
                                <label for="check_out_time" class="form-label">
                                    Heure de départ
                                </label>
                                <input type="time" class="form-control @error('check_out_time') is-invalid @enderror" 
                                       id="check_out_time" name="check_out_time"
                                       value="{{ old('check_out_time', $room->check_out_time ?? '12:00') }}">
                                @error('check_out_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Nombre maximum d'adultes -->
                            <div class="col-md-6">
                                <label for="max_adults" class="form-label">
                                    Nombre maximum d'adultes
                                </label>
                                <input type="number" class="form-control @error('max_adults') is-invalid @enderror" 
                                       id="max_adults" name="max_adults" min="1"
                                       value="{{ old('max_adults', $room->max_adults ?? 2) }}">
                                @error('max_adults')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Nombre maximum d'enfants -->
                            <div class="col-md-6">
                                <label for="max_children" class="form-label">
                                    Nombre maximum d'enfants
                                </label>
                                <input type="number" class="form-control @error('max_children') is-invalid @enderror" 
                                       id="max_children" name="max_children" min="0"
                                       value="{{ old('max_children', $room->max_children ?? 2) }}">
                                @error('max_children')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Services inclus -->
                    <div class="mb-4 pt-5 border-top">
                        <div class="mb-4">
                            <h3 class="h5 mb-3">
                                <i class="fas fa-concierge-bell me-2"></i>Services inclus
                            </h3>
                            <p class="text-muted small mb-0">
                                Cochez les services inclus dans le prix de la chambre.
                            </p>
                        </div>

                        <div class="row g-3">
                            @foreach($amenities as $amenity)
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               id="included_amenity_{{ $amenity->id }}" 
                                               name="included_amenities[]" 
                                               value="{{ $amenity->id }}"
                                               {{ in_array($amenity->id, old('included_amenities', $room->includedAmenities->pluck('id')->toArray())) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="included_amenity_{{ $amenity->id }}">
                                            {{ $amenity->name }}
                                            @if($amenity->description)
                                                <small class="d-block text-muted">{{ $amenity->description }}</small>
                                            @endif
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('included_amenities')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Boutons d'action -->
                    <div class="d-flex justify-content-between pt-4 mt-5 border-top">
                        <a href="{{ route('hotels.rooms.show', ['hotel' => $hotel, 'room' => $room]) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Annuler
                        </a>
                        <div>
                            <button type="button" id="save-as-draft" class="btn btn-outline-secondary me-3">
                                <i class="far fa-save me-2"></i>Enregistrer comme brouillon
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Mettre à jour la chambre
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Gestion des images
                const fileInput = document.getElementById('photos');
                const imagePreview = document.getElementById('image-preview');
                
                fileInput.addEventListener('change', function(e) {
                    // Vider l'aperçu existant
                    imagePreview.innerHTML = '';
                    
                    // Parcourir les fichiers sélectionnés
                    Array.from(this.files).forEach(file => {
                        // Vérifier le type de fichier
                        if (!file.type.match('image.*')) {
                            return;
                        }
                        
                        // Créer un aperçu de l'image
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const previewItem = document.createElement('div');
                            previewItem.className = 'image-preview-item';
                            previewItem.innerHTML = `
                                <img src="${e.target.result}" alt="Aperçu de l'image" class="img-fluid">
                                <button type="button" class="remove-image btn btn-danger btn-sm position-absolute top-0 end-0 m-1 rounded-circle p-1" data-file-name="${file.name}">
                                    <i class="fas fa-times"></i>
                                </button>
                            `;
                            imagePreview.appendChild(previewItem);
                            
                            // Ajouter un écouteur d'événement pour le bouton de suppression
                            previewItem.querySelector('.remove-image').addEventListener('click', function() {
                                // Supprimer l'aperçu
                                previewItem.remove();
                                
                                // Créer un nouveau FileList sans le fichier supprimé
                                const dataTransfer = new DataTransfer();
                                const input = document.getElementById('photos');
                                
                                for (let i = 0; i < input.files.length; i++) {
                                    if (input.files[i].name !== file.name) {
                                        dataTransfer.items.add(input.files[i]);
                                    }
                                }
                                
                                // Mettre à jour l'input file
                                input.files = dataTransfer.files;
                            });
                        };
                        
                        // Lire le fichier
                        reader.readAsDataURL(file);
                    });
                });
                
                // Gestion de la suppression des images existantes
                const removeButtons = document.querySelectorAll('.remove-image[data-photo-id]');
                removeButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const photoId = this.getAttribute('data-photo-id');
                        const photoElement = this.closest('.existing-image');
                        
                        // Ajouter un champ caché pour indiquer que la photo doit être supprimée
                        const deleteInput = document.createElement('input');
                        deleteInput.type = 'hidden';
                        deleteInput.name = 'deleted_photos[]';
                        deleteInput.value = photoId;
                        
                        // Ajouter le champ caché au formulaire
                        document.querySelector('form').appendChild(deleteInput);
                        
                        // Cacher l'élément photo
                        photoElement.style.opacity = '0.5';
                        photoElement.style.pointerEvents = 'none';
                        this.disabled = true;
                    });
                });
                
                // Gestion du bouton "Enregistrer comme brouillon"
                const saveAsDraftBtn = document.getElementById('save-as-draft');
                const form = document.querySelector('form');
                
                if (saveAsDraftBtn) {
                    saveAsDraftBtn.addEventListener('click', function() {
                        // Ajouter un champ caché pour indiquer qu'il s'agit d'un brouillon
                        let draftInput = document.createElement('input');
                        draftInput.type = 'hidden';
                        draftInput.name = 'is_draft';
                        draftInput.value = '1';
                        form.appendChild(draftInput);
                        
                        // Soumettre le formulaire
                        form.submit();
                    });
                }
                
                // Validation du formulaire
                form.addEventListener('submit', function(e) {
                    let isValid = true;
                    
                    // Vérifier les champs requis
                    const requiredFields = form.querySelectorAll('[required]');
                    requiredFields.forEach(field => {
                        if (!field.value.trim()) {
                            isValid = false;
                            field.classList.add('is-invalid');
                            
                            // Ajouter un message d'erreur s'il n'y en a pas déjà un
                            if (!field.nextElementSibling || !field.nextElementSibling.classList.contains('invalid-feedback')) {
                                const errorDiv = document.createElement('div');
                                errorDiv.className = 'invalid-feedback';
                                errorDiv.textContent = 'Ce champ est obligatoire.';
                                field.parentNode.insertBefore(errorDiv, field.nextSibling);
                            }
                        } else {
                            field.classList.remove('is-invalid');
                            
                            // Supprimer le message d'erreur s'il existe
                            if (field.nextElementSibling && field.nextElementSibling.classList.contains('invalid-feedback')) {
                                field.nextElementSibling.remove();
                            }
                        }
                    });
                    
                    // Vérifier qu'au moins une photo est sélectionnée ou qu'il y a des photos existantes
                    const fileInput = document.getElementById('photos');
                    const existingPhotos = document.querySelectorAll('.existing-image:not([style*="opacity: 0.5"])');
                    
                    if (fileInput.files.length === 0 && existingPhotos.length === 0) {
                        isValid = false;
                        
                        // Afficher un message d'erreur
                        let errorDiv = document.getElementById('photos-error');
                        if (!errorDiv) {
                            errorDiv = document.createElement('div');
                            errorDiv.id = 'photos-error';
                            errorDiv.className = 'invalid-feedback d-block';
                            fileInput.parentNode.insertBefore(errorDiv, fileInput.nextSibling);
                        }
                        errorDiv.textContent = 'Veuillez sélectionner au moins une photo ou conserver les photos existantes.';
                    } else {
                        // Supprimer le message d'erreur s'il existe
                        const errorDiv = document.getElementById('photos-error');
                        if (errorDiv) {
                            errorDiv.remove();
                        }
                    }
                    
                    if (!isValid) {
                        e.preventDefault();
                        
                        // Faire défiler jusqu'au premier champ invalide
                        const firstInvalid = form.querySelector('.is-invalid');
                        if (firstInvalid) {
                            firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                    }
                });
                
                // Gestion du glisser-déposer des fichiers
                const dropZone = fileInput.closest('div');
                
                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                    dropZone.addEventListener(eventName, preventDefaults, false);
                });
                
                function preventDefaults(e) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                
                ['dragenter', 'dragover'].forEach(eventName => {
                    dropZone.addEventListener(eventName, highlight, false);
                });
                
                ['dragleave', 'drop'].forEach(eventName => {
                    dropZone.addEventListener(eventName, unhighlight, false);
                });
                
                function highlight() {
                    dropZone.classList.add('bg-light');
                }
                
                function unhighlight() {
                    dropZone.classList.remove('bg-light');
                }
                
                dropZone.addEventListener('drop', handleDrop, false);
                
                function handleDrop(e) {
                    const dt = e.dataTransfer;
                    const files = dt.files;
                    
                    if (files.length) {
                        fileInput.files = files;
                        // Déclencher l'événement change manuellement
                        const event = new Event('change');
                        fileInput.dispatchEvent(event);
                    }
                }
                
                // Activer les tooltips Bootstrap
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            });
        </script>
    @endpush
@endsection
