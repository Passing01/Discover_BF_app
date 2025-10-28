@php
    $isEdit = isset($restaurant);
    $title = $isEdit ? 'Modifier le restaurant' : 'Ajouter un restaurant';
    $route = $isEdit ? route('restaurant-manager.restaurants.update', $restaurant) : route('restaurant-manager.restaurants.store');
    $method = $isEdit ? 'PUT' : 'POST';
    $buttonText = $isEdit ? 'Mettre à jour' : 'Créer le restaurant';
    $coverImage = $isEdit && $restaurant->cover_image ? ($restaurant->cover_image_url ?? Storage::url($restaurant->cover_image)) : null;
@endphp

@extends('layouts.restau')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <h1>{{ $title }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('restaurant-manager.dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item"><a href="{{ route('restaurant-manager.restaurants.index') }}">Mes Restaurants</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $isEdit ? 'Modifier' : 'Ajouter' }}</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ $route }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method($method)
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-4">
                            <h5 class="mb-3">Informations de base</h5>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Nom du restaurant <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" 
                                           value="{{ old('name', $restaurant->name ?? '') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" 
                                           value="{{ old('email', $restaurant->email ?? '') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Téléphone <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" 
                                           value="{{ old('phone', $restaurant->phone ?? '') }}" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="avg_price" class="form-label">Prix moyen (FCFA) <span class="text-danger">*</span></label>
                                    <input type="number" min="0" step="100" class="form-control @error('avg_price') is-invalid @enderror" 
                                           id="avg_price" name="avg_price" value="{{ old('avg_price', $restaurant->avg_price ?? '0') }}" required>
                                    @error('avg_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" 
                                          name="description" rows="4" required>{{ old('description', $restaurant->description ?? '') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-check form-switch mb-4">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       value="1" {{ (old('is_active', $restaurant->is_active ?? false) ? 'checked' : '') }}>
                                <label class="form-check-label" for="is_active">Restaurant actif</label>
                            </div>
                            
                            <h5 class="mb-3">Localisation</h5>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="address" class="form-label">Adresse <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" 
                                           name="address" value="{{ old('address', $restaurant->address ?? '') }}" required>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="city" class="form-label">Ville <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" 
                                           name="city" value="{{ old('city', $restaurant->city ?? '') }}" required>
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="latitude" class="form-label">Latitude</label>
                                    <input type="text" class="form-control @error('latitude') is-invalid @enderror" 
                                           id="latitude" name="latitude" value="{{ old('latitude', $restaurant->latitude ?? '') }}">
                                    @error('latitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="longitude" class="form-label">Longitude</label>
                                    <input type="text" class="form-control @error('longitude') is-invalid @enderror" 
                                           id="longitude" name="longitude" value="{{ old('longitude', $restaurant->longitude ?? '') }}">
                                    @error('longitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="map_url" class="form-label">Lien Google Maps</label>
                                <input type="url" class="form-control @error('map_url') is-invalid @enderror" id="map_url" 
                                       name="map_url" value="{{ old('map_url', $restaurant->map_url ?? '') }}">
                                @error('map_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <h5 class="mb-3">Médias</h5>
                            
                            <div class="mb-4">
                                <label class="form-label">Galerie de photos</label>
                                <div class="gallery-preview mb-3">
                                    @if($isEdit && !empty($restaurant->gallery_urls))
                                        <div class="row g-2">
                                            @foreach($restaurant->gallery_urls as $index => $imageUrl)
                                                <div class="col-4 col-md-3">
                                                    <div class="position-relative">
                                                        <img src="{{ $imageUrl }}" class="img-fluid rounded border" alt="Galerie {{ $index + 1 }}">
                                                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1" 
                                                                onclick="removeGalleryImage(this, '{{ is_array($restaurant->gallery[$index] ?? null) ? ($restaurant->gallery[$index]['path'] ?? ($restaurant->gallery[$index][0] ?? '')) : ($restaurant->gallery[$index] ?? '') }}')">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-4 border rounded bg-light">
                                            <i class="fas fa-images fa-2x text-muted mb-2"></i>
                                            <p class="mb-0 text-muted">Aucune image dans la galerie</p>
                                        </div>
                                    @endif
                                </div>
                                <input type="file" class="form-control @error('gallery.*') is-invalid @enderror" 
                                       id="gallery" name="gallery[]" multiple accept="image/*" onchange="previewGallery(this)">
                                @error('gallery.*')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted d-block mt-1">Sélectionnez une ou plusieurs images. Taille maximale : 2MB par image. Formats : JPG, PNG, JPEG</small>
                                <div id="removed_gallery_images_container"></div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">Vidéos (URL YouTube ou Vimeo)</label>
                                <div id="video-container" class="mb-3">
                                    @if($isEdit && !empty($restaurant->video_urls))
                                        @foreach($restaurant->video_urls as $index => $url)
                                            <div class="input-group mb-2 video-url-group" data-index="{{ $index }}">
                                                <input type="url" class="form-control" name="video_urls[]" 
                                                       value="{{ $url }}" placeholder="https://www.youtube.com/watch?v=...">
                                                <button type="button" class="btn btn-outline-danger" onclick="removeVideoField(this)">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="input-group mb-2 video-url-group" data-index="0">
                                            <input type="url" class="form-control" name="video_urls[]" 
                                                   placeholder="https://www.youtube.com/watch?v=...">
                                            <button type="button" class="btn btn-outline-danger" onclick="removeVideoField(this)">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    @endif
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
                                    @if($coverImage)
                                        <img src="{{ $coverImage }}" id="coverPreview" class="img-fluid rounded" alt="Aperçu de l'image">
                                    @else
                                        <div class="bg-light p-5 rounded d-flex align-items-center justify-content-center" 
                                             style="height: 200px;">
                                            <div class="text-center">
                                                <i class="fas fa-image fa-3x text-muted mb-2"></i>
                                                <p class="mb-0 text-muted">Aucune image</p>
                                            </div>
                                        </div>
                                    @endif
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
                                        <i class="fas fa-save me-1"></i> {{ $buttonText }}
                                    </button>
                                    <a href="{{ route('restaurant-manager.restaurants.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left me-1"></i> Annuler
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .gallery-preview img {
        max-height: 100px;
        object-fit: cover;
    }
    .video-url-group {
        transition: all 0.3s ease;
    }
    .video-url-group.removing {
        opacity: 0.5;
        transform: translateX(-20px);
    }
</style>
@endpush

@push('scripts')
<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('coverPreview');
                if (preview) {
                    preview.src = e.target.result;
                } else {
                    // Créer l'élément image s'il n'existe pas
                    const img = document.createElement('img');
                    img.id = 'coverPreview';
                    img.src = e.target.result;
                    img.className = 'img-fluid rounded';
                    input.closest('.card-body').querySelector('.mb-3').innerHTML = '';
                    input.closest('.card-body').querySelector('.mb-3').appendChild(img);
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function addVideoField() {
        const container = document.getElementById('video-container');
        const group = document.createElement('div');
        group.className = 'input-group mb-2 video-url-group';
        group.innerHTML = `
            <input type="url" class="form-control" name="video_urls[]" placeholder="https://www.youtube.com/watch?v=...">
            <button type="button" class="btn btn-outline-danger" onclick="removeVideoField(this)">
                <i class="fas fa-times"></i>
            </button>
        `;
        container.appendChild(group);
    }

    function removeVideoField(button) {
        const group = button.closest('.video-url-group');
        if (group) {
            group.classList.add('removing');
            setTimeout(() => group.remove(), 200);
        }
    }

    function previewGallery(input) {
        const previewContainer = input.closest('.mb-4').querySelector('.gallery-preview');
        if (!previewContainer) { return; }
        previewContainer.innerHTML = '';
        const files = Array.from(input.files || []);
        if (files.length === 0) {
            previewContainer.innerHTML = `
                <div class="text-center py-4 border rounded bg-light">
                    <i class="fas fa-images fa-2x text-muted mb-2"></i>
                    <p class="mb-0 text-muted">Aucune image sélectionnée</p>
                </div>`;
            return;
        }
        const row = document.createElement('div');
        row.className = 'row g-2';
        files.forEach((file) => {
            const col = document.createElement('div');
            col.className = 'col-4 col-md-3';
            const wrap = document.createElement('div');
            wrap.className = 'position-relative';
            const img = document.createElement('img');
            img.className = 'img-fluid rounded border';
            img.alt = 'Preview';
            const reader = new FileReader();
            reader.onload = (e) => { img.src = e.target.result; };
            reader.readAsDataURL(file);
            wrap.appendChild(img);
            col.appendChild(wrap);
            row.appendChild(col);
        });
        previewContainer.appendChild(row);
    }

    function removeGalleryImage(button, imagePath) {
        // Visually remove the image card
        const col = button.closest('.col-4, .col-md-3');
        if (col) { col.remove(); }
        // Append a hidden input for removal
        const container = document.getElementById('removed_gallery_images_container');
        if (container) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'removed_gallery_images[]';
            input.value = imagePath;
            container.appendChild(input);
        }
    }
</script>
@endpush
