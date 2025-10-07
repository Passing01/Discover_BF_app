@extends('layouts.restau')

@section('title', 'Ajouter un restaurant')

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-md-5 col-8 align-self-center">
            <h3 class="text-themecolor">Ajouter un restaurant</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('restaurant-manager.dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item"><a href="{{ route('restaurant-manager.restaurants.index') }}">Mes restaurants</a></li>
                <li class="breadcrumb-item active">Ajouter un restaurant</li>
            </ol>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('restaurant-manager.restaurants.store') }}" method="POST" enctype="multipart/form-data" id="restaurantForm">
                        @csrf
                            <div class="col-md-8">
                                <div class="mb-4">
                                    <h5 class="mb-3">Informations de base</h5>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="name" class="form-label">Nom du restaurant <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" 
                                                   name="name" value="{{ old('name') }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" 
                                                   name="email" value="{{ old('email') }}" required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="phone" class="form-label">Téléphone <span class="text-danger">*</span></label>
                                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" 
                                                   name="phone" value="{{ old('phone') }}" required>
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="avg_price" class="form-label">Prix moyen (FCFA) <span class="text-danger">*</span></label>
                                            <input type="number" min="0" step="100" class="form-control @error('avg_price') is-invalid @enderror" 
                                                   id="avg_price" name="avg_price" value="{{ old('avg_price', '0') }}" required>
                                            @error('avg_price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" 
                                                  name="description" rows="4" required>{{ old('description') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="form-check form-switch mb-4">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                               value="1" {{ old('is_active') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">Restaurant actif</label>
                                    </div>
                                    
                                    <h5 class="mb-3">Localisation</h5>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="address" class="form-label">Adresse <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" 
                                                   name="address" value="{{ old('address') }}" required>
                                            @error('address')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="city" class="form-label">Ville <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" 
                                                   name="city" value="{{ old('city') }}" required>
                                            @error('city')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="latitude" class="form-label">Latitude</label>
                                            <input type="text" class="form-control @error('latitude') is-invalid @enderror" 
                                                   id="latitude" name="latitude" value="{{ old('latitude') }}">
                                            @error('latitude')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="longitude" class="form-label">Longitude</label>
                                            <input type="text" class="form-control @error('longitude') is-invalid @enderror" 
                                                   id="longitude" name="longitude" value="{{ old('longitude') }}">
                                            @error('longitude')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label for="map_url" class="form-label">Lien Google Maps</label>
                                        <input type="url" class="form-control @error('map_url') is-invalid @enderror" id="map_url" 
                                               name="map_url" value="{{ old('map_url') }}">
                                        @error('map_url')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <h5 class="mb-3">Médias</h5>
                                    
                                    <div class="mb-4">
                                        <label class="form-label">Galerie de photos</label>
                                        <div class="gallery-preview mb-3">
                                            <div class="text-center py-4 border rounded bg-light">
                                                <i class="fas fa-images fa-2x text-muted mb-2"></i>
                                                <p class="mb-0 text-muted">Aucune image sélectionnée</p>
                                            </div>
                                        </div>
                                        <input type="file" class="form-control @error('gallery.*') is-invalid @enderror" 
                                               id="gallery" name="gallery[]" multiple accept="image/*" onchange="previewGallery(this)">
                                        @error('gallery.*')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted d-block mt-1">Sélectionnez une ou plusieurs images. Taille maximale : 2MB par image. Formats : JPG, PNG, JPEG</small>
                                        <input type="hidden" name="removed_gallery_images" id="removed_gallery_images" value="">
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label class="form-label">Vidéos (URL YouTube ou Vimeo)</label>
                                        <div id="video-container" class="mb-3">
                                            <div class="input-group mb-2 video-url-group" data-index="0">
                                                <input type="url" class="form-control" name="video_urls[]" 
                                                       placeholder="https://www.youtube.com/watch?v=...">
                                                <button type="button" class="btn btn-outline-danger" onclick="removeVideoField(this)">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addVideoField()">
                                            <i class="fas fa-plus me-1"></i> Ajouter une vidéo
                                        </button>
                                        <small class="text-muted d-block mt-1">Collez les liens complets des vidéos (YouTube ou Vimeo)</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Image de couverture</h6>
                                    </div>
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <div class="bg-light p-5 rounded d-flex align-items-center justify-content-center" 
                                                 style="height: 200px;">
                                                <div class="text-center">
                                                    <i class="fas fa-image fa-3x text-muted mb-2"></i>
                                                    <p class="mb-0 text-muted">Aperçu de l'image</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-grid">
                                            <input type="file" class="form-control @error('cover_image') is-invalid @enderror" 
                                                   id="cover_image" name="cover_image" accept="image/*" onchange="previewImage(this)">
                                            @error('cover_image')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted d-block mt-1">Taille maximale : 2MB. Formats : JPG, PNG, JPEG</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Options</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-1"></i> Créer le restaurant
                                            </button>
                                            <a href="{{ route('restaurant-manager.restaurants.index') }}" class="btn btn-outline-secondary">
                                                <i class="fas fa-arrow-left me-1"></i> Annuler
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                                
                                <div class="form-group">
                                    <label for="address">Adresse *</label>
                                    <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address') }}" required>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="city">Ville *</label>
                                            <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" name="city" value="{{ old('city') }}" required>
                                            @error('city')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="postal_code">Code postal *</label>
                                            <input type="text" class="form-control @error('postal_code') is-invalid @enderror" id="postal_code" name="postal_code" value="{{ old('postal_code') }}" required>
                                            @error('postal_code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="phone">Téléphone *</label>
                                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" required>
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">Email *</label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="description">Description *</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" required>{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="cuisine_type">Type de cuisine *</label>
                                    <input type="text" class="form-control @error('cuisine_type') is-invalid @enderror" id="cuisine_type" name="cuisine_type" value="{{ old('cuisine_type') }}" required>
                                    @error('cuisine_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="cover_image">Image de couverture</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input @error('cover_image') is-invalid @enderror" id="cover_image" name="cover_image" accept="image/*">
                                        <label class="custom-file-label" for="cover_image">Choisir une image</label>
                                        @error('cover_image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="form-text text-muted">Format: JPG, PNG, JPEG. Taille max: 2MB</small>
                                    <div class="mt-2" id="imagePreview"></div>
                                </div>
                                
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_active">Activer le restaurant</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Enregistrer
                            </button>
                            <a href="{{ route('restaurant-manager.restaurants.index') }}" class="btn btn-secondary">
                                <i class="fa fa-arrow-left"></i> Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Afficher un aperçu de l'image sélectionnée
    document.getElementById('cover_image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('imagePreview');
                preview.innerHTML = `
                    <div class="mt-2">
                        <p class="mb-1">Aperçu:</p>
                        <img src="${e.target.result}" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                    </div>
                `;
            };
            reader.readAsDataURL(file);
            
            // Mettre à jour le label du fichier
            const label = document.querySelector('.custom-file-label');
            label.textContent = file.name;
        }
    });
</script>
@endpush
@endsection
