@extends('layouts.hotel')

@section('content')
    <div class="container py-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h2 class="h4 mb-0"><i class="fas fa-plus-circle me-2"></i>Ajouter une nouvelle chambre</h2>
            </div>
            
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Erreurs dans le formulaire :</h5>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('hotels.rooms.store', $hotel) }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                    @csrf

                    <!-- Informations de base -->
                    <div class="mb-5">
                        <h4 class="mb-4 pb-2 border-bottom d-flex align-items-center">
                            <i class="fas fa-info-circle me-2 text-primary"></i>Informations de base
                        </h4>
                        
                        <div class="row g-4">
                            <div class="col-md-8">
                                <label for="name" class="form-label fw-bold">
                                    Nom de la chambre <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-door-open"></i></span>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="{{ old('name') }}" required>
                                    <div class="invalid-feedback">
                                        Veuillez entrer un nom pour la chambre.
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label for="room_type_id" class="form-label fw-bold">
                                    Type de chambre <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                    <select class="form-select" id="room_type_id" name="room_type_id" required>
                                        <option value="">Sélectionnez un type</option>
                                        @foreach($roomTypes as $roomType)
                                            <option value="{{ $roomType->id }}" {{ old('room_type_id') == $roomType->id ? 'selected' : '' }}>
                                                {{ $roomType->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">
                                        Veuillez sélectionner un type de chambre.
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label fw-bold">Description</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-align-left"></i></span>
                                    <textarea class="form-control" id="description" name="description" 
                                              rows="3" placeholder="Décrivez la chambre...">{{ old('description') }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label for="room_number" class="form-label fw-bold">
                                    Numéro de chambre <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                    <input type="text" class="form-control" id="room_number" name="room_number" 
                                           value="{{ old('room_number') }}" required>
                                    <div class="invalid-feedback">Veuillez saisir un numéro de chambre.</div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label for="floor" class="form-label fw-bold">
                                    Étage <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-building"></i></span>
                                    <input type="number" class="form-control" id="floor" name="floor" 
                                           value="{{ old('floor') }}" min="-10" max="200" step="1" required>
                                    <div class="invalid-feedback">Veuillez saisir un étage valide.</div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label for="price_per_night" class="form-label fw-bold">
                                    Prix par nuit (€) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-euro-sign"></i></span>
                                    <input type="number" class="form-control" id="price_per_night" 
                                           name="price_per_night" value="{{ old('price_per_night') }}" 
                                           min="0" step="0.01" required>
                                    <div class="invalid-feedback">
                                        Veuillez entrer un prix valide.
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label for="capacity" class="form-label fw-bold">
                                    Capacité (personnes) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-users"></i></span>
                                    <input type="number" class="form-control" id="capacity" 
                                           name="capacity" value="{{ old('capacity', 2) }}" 
                                           min="1" max="10" required>
                                    <div class="invalid-feedback">
                                        La capacité doit être entre 1 et 10 personnes.
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label for="quantity" class="form-label fw-bold">
                                    Nombre de chambres identiques <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-copy"></i></span>
                                    <input type="number" class="form-control" id="quantity" 
                                           name="quantity" value="{{ old('quantity', 1) }}" 
                                           min="1" required>
                                    <div class="invalid-feedback">
                                        Veuillez entrer un nombre valide.
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">Photos de la chambre <span class="text-danger">*</span></label>
                                <div class="border rounded p-4 bg-light">
                                    <div class="mb-3">
                                        <input type="file" class="form-control" id="photos" name="photos[]" multiple required
                                               accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">
                                        <div class="form-text mt-2">
                                            <i class="fas fa-info-circle me-1"></i>Formats acceptés : JPG, PNG, GIF, WebP (max 5 Mo par fichier)
                                        </div>
                                    </div>
                                    @error('photos')
                                        <div class="alert alert-danger mt-2">
                                            <i class="fas fa-exclamation-triangle me-2"></i>{{ $message }}
                                        </div>
                                    @enderror
                                    @error('photos.*')
                                        <div class="alert alert-danger mt-2">
                                            <i class="fas fa-exclamation-triangle me-2"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Caractéristiques de la chambre -->
                    <div class="mb-5">
                        <h4 class="mb-4 pb-2 border-bottom d-flex align-items-center">
                            <i class="fas fa-list-ul me-2 text-primary"></i>Caractéristiques
                        </h4>
                        
                        <div class="row g-4">
                            <div class="col-12">
                                <h5 class="mb-3 fw-bold">
                                    <i class="fas fa-tv me-2 text-muted"></i>Équipements de la chambre
                                </h5>
                                <div class="row g-3">
                                    @foreach($amenities as $amenity)
                                        <div class="col-md-4">
                                            <div class="card h-100 border-0 shadow-sm">
                                                <div class="card-body">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" 
                                                               id="amenity-{{ $amenity->id }}" 
                                                               name="amenities[]" 
                                                               value="{{ $amenity->id }}"
                                                               {{ in_array($amenity->id, old('amenities', [])) ? 'checked' : '' }}>
                                                        <label class="form-check-label fw-bold" for="amenity-{{ $amenity->id }}">
                                                            {{ $amenity->name }}
                                                        </label>
                                                        @if($amenity->description)
                                                            <div class="text-muted small mt-1">
                                                                <i class="fas fa-info-circle me-1"></i>{{ $amenity->description }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="mb-3 fw-bold">
                                            <i class="fas fa-bed me-2 text-muted"></i>Configuration du lit
                                        </h5>
                                        <div class="mb-3">
                                            <label for="bed_type" class="form-label fw-bold">Type de lit <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-bed"></i></span>
                                                <select class="form-select" id="bed_type" name="bed_type" required>
                                                    <option value="">Sélectionnez un type de lit</option>
                                                    <option value="single" {{ old('bed_type') == 'single' ? 'selected' : '' }}>1 lit simple</option>
                                                    <option value="double" {{ old('bed_type', 'double') == 'double' ? 'selected' : '' }}>1 lit double</option>
                                                    <option value="twin" {{ old('bed_type') == 'twin' ? 'selected' : '' }}>2 lits simples</option>
                                                    <option value="queen" {{ old('bed_type') == 'queen' ? 'selected' : '' }}>1 lit queen size</option>
                                                    <option value="king" {{ old('bed_type') == 'king' ? 'selected' : '' }}>1 lit king size</option>
                                                    <option value="multiple" {{ old('bed_type') == 'multiple' ? 'selected' : '' }}>Plusieurs lits</option>
                                                </select>
                                            </div>
                                            <div class="invalid-feedback">Veuillez sélectionner un type de lit.</div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="bed_count" class="form-label fw-bold">Nombre de lits <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-bed"></i></span>
                                                <input type="number" class="form-control" id="bed_count" name="bed_count" value="{{ old('bed_count', 1) }}" min="1" max="10" step="1" required>
                                            </div>
                                            <div class="invalid-feedback">Veuillez saisir un nombre de lits valide.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="mb-3 fw-bold">
                                            <i class="fas fa-mountain me-2 text-muted"></i>Vue depuis la chambre
                                        </h5>
                                        <div class="mb-3">
                                            <label for="view" class="form-label fw-bold">Vue</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-binoculars"></i></span>
                                                <select class="form-select" id="view" name="view">
                                                    <option value="">Sélectionnez une vue</option>
                                                    <option value="city" {{ old('view') == 'city' ? 'selected' : '' }}>Ville</option>
                                                    <option value="garden" {{ old('view') == 'garden' ? 'selected' : '' }}>Jardin</option>
                                                    <option value="pool" {{ old('view') == 'pool' ? 'selected' : '' }}>Piscine</option>
                                                    <option value="mountain" {{ old('view') == 'mountain' ? 'selected' : '' }}>Montagne</option>
                                                    <option value="sea" {{ old('view') == 'sea' ? 'selected' : '' }}>Mer</option>
                                                    <option value="courtyard" {{ old('view') == 'courtyard' ? 'selected' : '' }}>Cour intérieure</option>
                                                    <option value="no_view" {{ old('view') == 'no_view' ? 'selected' : '' }}>Sans vue particulière</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="mb-3 fw-bold">
                                            <i class="fas fa-vector-square me-2 text-muted"></i>Superficie
                                        </h5>
                                        <div class="mb-3">
                                            <label for="size" class="form-label fw-bold">Superficie (m²)</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-ruler-combined"></i></span>
                                                <input type="number" class="form-control" id="size" 
                                                       name="size" value="{{ old('size') }}" 
                                                       min="1" step="1" placeholder="Ex: 25">
                                                <span class="input-group-text">m²</span>
                                            </div>
                                            <div class="form-text">Laissez vide si non spécifié</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Options de réservation -->
                    <div class="mb-4">
                        <h4 class="mb-4 pb-2 border-bottom d-flex align-items-center">
                            <i class="fas fa-calendar-check me-2 text-primary"></i>Options de réservation
                        </h4>
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="mb-4 fw-bold">
                                            <i class="fas fa-toggle-on me-2 text-muted"></i>Options de la chambre
                                        </h5>
                                        
                                        <div class="form-check form-switch mb-4 p-3 bg-light rounded">
                                            <input class="form-check-input" type="checkbox" 
                                                   id="is_smoking_allowed" name="is_smoking_allowed" 
                                                   value="1" {{ old('is_smoking_allowed') ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="is_smoking_allowed">
                                                <i class="fas fa-smoking me-1"></i>Fumeur autorisé
                                            </label>
                                            <div class="form-text ms-4 mt-1">
                                                Cochez si la chambre est destinée aux fumeurs.
                                            </div>
                                        </div>

                                        <div class="form-check form-switch p-3 bg-light rounded">
                                            <input class="form-check-input" type="checkbox" 
                                                   id="is_available" name="is_available" 
                                                   value="1" {{ old('is_available', true) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="is_available">
                                                <i class="fas fa-eye me-1"></i>Disponible à la réservation
                                            </label>
                                            <div class="form-text ms-4 mt-1">
                                                Décochez pour masquer cette chambre des résultats de recherche.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="mb-4 fw-bold">
                                            <i class="fas fa-users me-2 text-muted"></i>Occupation
                                        </h5>
                                        
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="min_stay" class="form-label fw-bold">
                                                    <i class="fas fa-moon me-1"></i>Séjour minimum (nuits)
                                                </label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-calendar-day"></i></span>
                                                    <input type="number" class="form-control" id="min_stay" 
                                                           name="min_stay" value="{{ old('min_stay', 1) }}" 
                                                           min="1">
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="max_adults" class="form-label fw-bold">
                                                    <i class="fas fa-user-friends me-1"></i>Max adultes
                                                </label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                    <input type="number" class="form-control" id="max_adults" 
                                                           name="max_adults" value="{{ old('max_adults', 2) }}" 
                                                           min="1" max="10">
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="max_children" class="form-label fw-bold">
                                                    <i class="fas fa-child me-1"></i>Max enfants
                                                </label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-baby"></i></span>
                                                    <input type="number" class="form-control" id="max_children" 
                                                           name="max_children" value="{{ old('max_children', 2) }}" 
                                                           min="0" max="10">
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="max_occupancy" class="form-label fw-bold">
                                                    <i class="fas fa-users me-1"></i>Occupation max
                                                </label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-user-friends"></i></span>
                                                    <input type="number" class="form-control" id="max_occupancy" 
                                                           name="max_occupancy" value="{{ old('max_occupancy', 2) }}" 
                                                           min="1" max="20">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="mb-3 fw-bold">
                                            <i class="fas fa-ban me-2 text-muted"></i>Politique d'annulation
                                        </h5>
                                        <div class="form-text mb-2">
                                            <i class="fas fa-info-circle me-1"></i>Si vide, la politique d'annulation de l'hôtel sera utilisée.
                                        </div>
                                        <textarea class="form-control" id="cancellation_policy" 
                                                  name="cancellation_policy" rows="3"
                                                  placeholder="Décrivez les conditions d'annulation spécifiques à cette chambre...">{{ old('cancellation_policy') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between pt-4 mt-4 border-top">
                        <a href="{{ route('hotels.rooms.index', $hotel) }}" 
                           class="btn btn-lg btn-outline-secondary px-4">
                            <i class="fas fa-arrow-left me-2"></i>Annuler
                        </a>
                        <div>
                            <button type="reset" class="btn btn-lg btn-outline-secondary me-3">
                                <i class="fas fa-undo me-2"></i>Réinitialiser
                            </button>
                            <button type="submit" class="btn btn-lg btn-primary px-4">
                                <i class="fas fa-save me-2"></i>Enregistrer la chambre
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <!-- Script de validation du formulaire Bootstrap -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            'use strict';
            
            // Appliquer la validation à tous les formulaires avec la classe 'needs-validation'
            const forms = document.querySelectorAll('.needs-validation');
            
            // Empêcher la soumission des formulaires non valides
            Array.from(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    
                    form.classList.add('was-validated');
                }, false);
            });
        });
    </script>
    @endpush
@endsection
