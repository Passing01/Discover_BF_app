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
            .delete-image-checkbox {
                position: absolute;
                top: 0.5rem;
                right: 0.5rem;
                width: 1.25rem;
                height: 1.25rem;
                border-radius: 0.25rem;
                border: 1px solid #d1d5db;
                background-color: white;
                cursor: pointer;
            }
            .delete-image-checkbox:checked {
                background-color: #ef4444;
                border-color: #ef4444;
                background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z'/%3e%3c/svg%3e");
                background-repeat: no-repeat;
                background-position: center;
                background-size: 75% 75%;
            }
            .delete-image-label {
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                background-color: rgba(239, 68, 68, 0.8);
                color: white;
                text-align: center;
                padding: 0.25rem 0;
                font-size: 0.75rem;
                font-weight: 500;
                cursor: pointer;
            }
        </style>
    @endpush

    <x-slot name="title">
        Modifier la chambre - {{ $room->name }}
    </x-slot>

    <x-slot name="actions">
        <a href="{{ route('hotels.rooms.show', ['hotel' => $hotel, 'room' => $room]) }}" 
           class="btn btn-outline-secondary btn-sm">
            <svg class="me-2 text-secondary" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" width="20" height="20">
                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
            Retour
        </a>
    </x-slot>

    <div class="card shadow-sm mb-4">
        <form action="{{ route('hotels.rooms.update', ['hotel' => $hotel, 'room' => $room]) }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
            @csrf
            @method('PUT')
            
            <div class="card-body">
                <div class="mb-5">
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

                                <div class="col-md-4">
                                    <label for="type" class="form-label">
                                        Type de chambre <span class="text-danger">*</span>
                                    </label>
                                    <select id="type" name="type" class="form-select @error('type') is-invalid @enderror" required>
                                        <option value="">Sélectionnez un type</option>
                                        @foreach($roomTypes as $key => $label)
                                            <option value="{{ $key }}" {{ old('type', $room->type) == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label for="description" class="form-label">
                                        Description <span class="text-danger">*</span>
                                    </label>
                                    <textarea id="description" name="description" rows="3" class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $room->description) }}</textarea>
                                    <div class="form-text">Décrivez votre chambre de manière attrayante.</div>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="price_per_night" class="form-label">
                                        Prix par nuit (€) <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">€</span>
                                        <input type="number" name="price_per_night" id="price_per_night" value="{{ old('price_per_night', $room->price_per_night) }}" step="0.01" min="0" class="form-control @error('price_per_night') is-invalid @enderror" placeholder="0.00" required>
                                        @error('price_per_night')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label for="capacity" class="form-label">
                                        Capacité (personnes) <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" name="capacity" id="capacity" value="{{ old('capacity', $room->capacity) }}" min="1" max="10" class="form-control @error('capacity') is-invalid @enderror" required>
                                    @error('capacity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="quantity" class="form-label">
                                        Nombre de chambres identiques <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" name="quantity" id="quantity" value="{{ old('quantity', $room->quantity) }}" min="1" class="form-control @error('quantity') is-invalid @enderror" required>
                                    @error('quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label">
                                        Photos
                                        @if($room->photos->isEmpty())
                                            <span class="text-danger">*</span>
                                        @endif
                                    </label>

                                    @if($room->photos->isNotEmpty())
                                        <div class="d-flex flex-wrap gap-3">
                                            @foreach($room->photos as $photo)
                                                <div class="position-relative existing-image" style="width: 128px; height: 96px;">
                                                    <img src="{{ Storage::url($photo->path) }}" alt="Photo de la chambre" class="img-thumbnail w-100 h-100" style="object-fit: cover;">
                                                    <div class="form-check form-check-inline position-absolute bottom-0 start-0 m-1 bg-danger bg-opacity-75 rounded px-2 py-1">
                                                        <input class="form-check-input" type="checkbox" name="delete_photos[]" value="{{ $photo->id }}" id="delete_photo_{{ $photo->id }}">
                                                        <label class="form-check-label text-white" for="delete_photo_{{ $photo->id }}">Supprimer</label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="form-text">Cochez les images que vous souhaitez supprimer.</div>
                                    @endif

                                    <div class="border-2 border-dashed rounded p-4 text-center mt-3">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                            <div class="mb-2">
                                                <label for="photos" class="btn btn-link text-decoration-none p-0">
                                                    <span class="text-primary">Télécharger des fichiers</span>
                                                    <input id="photos" name="photos[]" type="file" multiple accept="image/*" class="d-none" {{ $room->photos->isEmpty() ? 'required' : '' }}>
                                                </label>
                                                <span class="ms-1">ou glisser-déposer</span>
                                            </div>
                                            <p class="text-muted small mb-0">PNG, JPG, JPEG jusqu'à 5 Mo. {{ $room->photos->isEmpty() ? 'Minimum 1 photo requise.' : '' }}</p>
                                        </div>
                                    </div>
                                    <div id="image-preview" class="image-preview"></div>
                                    @error('photos')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    @error('photos.*')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Caractéristiques de la chambre -->
                        <div class="pt-5 mt-5 border-top">
                            <div class="mb-3">
                                <h3 class="h5 mb-2">Caractéristiques</h3>
                                <p class="text-muted small mb-0">Détails sur la taille, les lits et les équipements de la chambre.</p>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="size" class="form-label">Superficie (m²)</label>
                                    <input type="number" name="size" id="size" value="{{ old('size', $room->size) }}" min="1" class="form-control @error('size') is-invalid @enderror">
                                    @error('size')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="bed_type" class="form-label">Type de lit</label>
                                    <select id="bed_type" name="bed_type" class="form-select @error('bed_type') is-invalid @enderror">
                                        <option value="">Sélectionnez</option>
                                        <option value="simple" {{ old('bed_type', $room->bed_type) == 'simple' ? 'selected' : '' }}>1 lit simple</option>
                                        <option value="double" {{ old('bed_type', $room->bed_type) == 'double' ? 'selected' : '' }}>1 lit double</option>
                                        <option value="twin" {{ old('bed_type', $room->bed_type) == 'twin' ? 'selected' : '' }}>2 lits simples</option>
                                        <option value="queen" {{ old('bed_type', $room->bed_type) == 'queen' ? 'selected' : '' }}>1 lit queen size</option>
                                        <option value="king" {{ old('bed_type', $room->bed_type) == 'king' ? 'selected' : '' }}>1 lit king size</option>
                                        <option value="bunk" {{ old('bed_type', $room->bed_type) == 'bunk' ? 'selected' : '' }}>Lits superposés</option>
                                        <option value="sofa_bed" {{ old('bed_type', $room->bed_type) == 'sofa_bed' ? 'selected' : '' }}>Canapé-lit</option>
                                        <option value="custom" {{ old('bed_type', $room->bed_type) == 'custom' ? 'selected' : '' }}>Personnalisé</option>
                                    </select>
                                    @error('bed_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="view" class="form-label">Vue</label>
                                    <select id="view" name="view" class="form-select @error('view') is-invalid @enderror">
                                        <option value="">Sélectionnez</option>
                                        <option value="city" {{ old('view', $room->view) == 'city' ? 'selected' : '' }}>Ville</option>
                                        <option value="garden" {{ old('view', $room->view) == 'garden' ? 'selected' : '' }}>Jardin</option>
                                        <option value="pool" {{ old('view', $room->view) == 'pool' ? 'selected' : '' }}>Piscine</option>
                                        <option value="mountain" {{ old('view', $room->view) == 'mountain' ? 'selected' : '' }}>Montagne</option>
                                        <option value="sea" {{ old('view', $room->view) == 'sea' ? 'selected' : '' }}>Mer</option>
                                        <option value="courtyard" {{ old('view', $room->view) == 'courtyard' ? 'selected' : '' }}>Cour intérieure</option>
                                        <option value="no_view" {{ old('view', $room->view) == 'no_view' ? 'selected' : '' }}>Sans vue particulière</option>
                                    </select>
                                    @error('view')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Équipements de la chambre</label>
                                    <div class="row g-2">
                                        @foreach($amenities as $amenity)
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" id="amenity-{{ $amenity->id }}" name="amenities[]" type="checkbox" value="{{ $amenity->id }}" {{ in_array($amenity->id, old('amenities', $room->amenities->pluck('id')->toArray())) ? 'checked' : '' }}>
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
                        <div class="pt-5 mt-5 border-top">
                            <div class="mb-4">
                                <h3 class="h5 mb-3">
                                    <i class="fas fa-calendar-check me-2"></i>Options de réservation
                                </h3>
                                <p class="text-muted small mb-0">
                                    Configurez les options de réservation pour cette chambre.
                                </p>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" id="is_smoking_allowed" name="is_smoking_allowed" type="checkbox" value="1" {{ old('is_smoking_allowed', $room->is_smoking_allowed) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_smoking_allowed">Fumeurs acceptés</label>
                                        <div class="form-text">Cocher si cette chambre est réservée aux fumeurs</div>
                                    </div>
                                    @error('is_smoking_allowed')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" id="is_available" name="is_available" type="checkbox" value="1" {{ old('is_available', $room->is_available) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_available">Disponible à la réservation</label>
                                        <div class="form-text">Décocher pour masquer cette chambre des résultats de recherche</div>
                                    </div>
                                    @error('is_available')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="min_stay" class="form-label">Séjour minimum (nuits)</label>
                                    <input type="number" name="min_stay" id="min_stay" value="{{ old('min_stay', $room->min_stay) }}" min="1" class="form-control @error('min_stay') is-invalid @enderror">
                                    @error('min_stay')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="max_adults" class="form-label">Nombre maximum d'adultes</label>
                                    <input type="number" name="max_adults" id="max_adults" value="{{ old('max_adults', $room->max_adults) }}" min="1" max="10" class="form-control @error('max_adults') is-invalid @enderror">
                                    @error('max_adults')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="max_children" class="form-label">Nombre maximum d'enfants</label>
                                    <input type="number" name="max_children" id="max_children" value="{{ old('max_children', $room->max_children) }}" min="0" max="10" class="form-control @error('max_children') is-invalid @enderror">
                                    @error('max_children')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="max_occupancy" class="form-label">Occupation maximale (total)</label>
                                    <input type="number" name="max_occupancy" id="max_occupancy" value="{{ old('max_occupancy', $room->max_occupancy) }}" min="1" max="20" class="form-control @error('max_occupancy') is-invalid @enderror">
                                    @error('max_occupancy')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label for="cancellation_policy" class="form-label">Politique d'annulation spécifique (optionnel)</label>
                                    <textarea id="cancellation_policy" name="cancellation_policy" rows="3" class="form-control @error('cancellation_policy') is-invalid @enderror">{{ old('cancellation_policy', $room->cancellation_policy) }}</textarea>
                                    <div class="form-text">Si vide, la politique d'annulation de l'hôtel sera utilisée.</div>
                                    @error('cancellation_policy')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Services inclus -->
                        <div class="pt-5 mt-5 border-top">
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
                                                   {{ in_array($amenity->id, old('included_amenities', [])) ? 'checked' : '' }}>
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
                    </div>
                </div>

                <div class="d-flex justify-content-between pt-4 mt-5 border-top">
                    <a href="{{ route('hotels.rooms.show', ['hotel' => $hotel, 'room' => $room]) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Annuler
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
                    for (let i = 0; i < this.files.length; i++) {
                        const file = this.files[i];
                        
                        // Vérifier le type de fichier
                        if (!file.type.startsWith('image/')) continue;
                        
                        // Créer un aperçu de l'image
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const previewItem = document.createElement('div');
                            previewItem.className = 'image-preview-item';
                            previewItem.innerHTML = `
                                <img src="${e.target.result}" alt="Aperçu">
                                <button type="button" class="remove-image" data-index="${i}">
                                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            `;
                            
                            // Ajouter un gestionnaire pour supprimer l'image
                            const removeButton = previewItem.querySelector('.remove-image');
                            removeButton.addEventListener('click', function() {
                                // Créer un nouveau DataTransfer pour mettre à jour les fichiers
                                const dataTransfer = new DataTransfer();
                                const fileInput = document.getElementById('photos');
                                
                                // Ajouter tous les fichiers sauf celui à supprimer
                                for (let j = 0; j < fileInput.files.length; j++) {
                                    if (j !== parseInt(this.dataset.index)) {
                                        dataTransfer.items.add(fileInput.files[j]);
                                    }
                                }
                                
                                // Mettre à jour l'input file
                                fileInput.files = dataTransfer.files;
                                
                                // Mettre à jour l'aperçu
                                previewItem.remove();
                            });
                            
                            imagePreview.appendChild(previewItem);
                        };
                        
                        reader.readAsDataURL(file);
                    }
                });
                
                // Gestion des cases à cocher de suppression d'images existantes
                document.querySelectorAll('.delete-image-checkbox').forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        const label = this.nextElementSibling;
                        if (this.checked) {
                            this.parentElement.classList.add('ring-2', 'ring-red-500');
                            label.classList.remove('hidden');
                        } else {
                            this.parentElement.classList.remove('ring-2', 'ring-red-500');
                            label.classList.add('hidden');
                        }
                    });
                });
                
                // Gestion du bouton "Enregistrer comme brouillon"
                const saveAsDraftBtn = document.getElementById('save-as-draft');
                if (saveAsDraftBtn) {
                    saveAsDraftBtn.addEventListener('click', function() {
                        // Ajouter un champ caché pour indiquer qu'il s'agit d'un brouillon
                        let draftInput = document.createElement('input');
                        draftInput.type = 'hidden';
                        draftInput.name = 'is_draft';
                        draftInput.value = '1';
                        this.form.appendChild(draftInput);
                        
                        // Soumettre le formulaire
                        this.form.submit();
                    });
                }
                
                // Mise à jour dynamique du nombre maximum d'occupants
                const capacityInput = document.getElementById('capacity');
                const maxAdultsInput = document.getElementById('max_adults');
                const maxChildrenInput = document.getElementById('max_children');
                const maxOccupancyInput = document.getElementById('max_occupancy');
                
                function updateMaxOccupancy() {
                    const capacity = parseInt(capacityInput.value) || 0;
                    maxAdultsInput.max = capacity;
                    maxChildrenInput.max = Math.max(0, capacity - (parseInt(maxAdultsInput.value) || 0));
                    maxOccupancyInput.value = capacity;
                }
                
                if (capacityInput && maxAdultsInput && maxChildrenInput && maxOccupancyInput) {
                    capacityInput.addEventListener('change', updateMaxOccupancy);
                    maxAdultsInput.addEventListener('change', function() {
                        const maxAdults = parseInt(this.value) || 0;
                        const capacity = parseInt(capacityInput.value) || 0;
                        maxChildrenInput.max = Math.max(0, capacity - maxAdults);
                        
                        // Ajuster la valeur des enfants si nécessaire
                        if ((parseInt(maxChildrenInput.value) || 0) > maxChildrenInput.max) {
                            maxChildrenInput.value = maxChildrenInput.max;
                        }
                    });
                    
                    // Initialiser les valeurs
                    updateMaxOccupancy();
                }
                
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
                    
                    // Vérifier qu'au moins une photo est présente (soit existante, soit nouvelle)
                    const fileInput = document.getElementById('photos');
                    const existingPhotos = document.querySelectorAll('.existing-image');
                    let hasPhotos = false;
                    
                    // Vérifier s'il y a des photos existantes non supprimées
                    existingPhotos.forEach(photo => {
                        const checkbox = photo.querySelector('.delete-image-checkbox');
                        if (!checkbox.checked) {
                            hasPhotos = true;
                        }
                    });
                    
                    // Vérifier s'il y a de nouvelles photos téléchargées
                    if (!hasPhotos && fileInput.files.length === 0) {
                        isValid = false;
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'mt-2 text-sm text-red-600';
                        errorDiv.textContent = 'Veuvez sélectionner au moins une photo ou conserver les photos existantes.';
                        
                        // Vérifier si le message d'erreur n'existe pas déjà
                        if (!fileInput.nextElementSibling || !fileInput.nextElementSibling.classList.contains('text-red-600')) {
                            fileInput.parentNode.insertBefore(errorDiv, fileInput.nextSibling);
                        }
                    } else {
                        // Supprimer le message d'erreur s'il existe
                        if (fileInput.nextElementSibling && fileInput.nextElementSibling.classList.contains('text-red-600')) {
                            fileInput.nextElementSibling.remove();
                        }
                    }
                    
                    if (!isValid) {
                        e.preventDefault();
                        
                        // Faire défiler jusqu'au premier champ invalide
                        const firstInvalid = form.querySelector('.border-red-500');
                        if (firstInvalid) {
                            firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        } else if (!hasPhotos && fileInput.files.length === 0) {
                            fileInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                        
                        // Afficher un message d'erreur
                        alert('Veuillez remplir tous les champs obligatoires.');
                    }
                });
            });
        </script>
    @endpush
@endsection
