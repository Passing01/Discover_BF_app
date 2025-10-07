@extends('layouts.site-manager')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Ajouter un site touristique</h1>
        <p class="mb-0">Remplissez le formulaire pour ajouter un nouveau site touristique</p>
    </div>
    <a href="{{ route('site-manager.sites.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Retour à la liste
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('site-manager.sites.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nom du site <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category" class="form-label">Catégorie <span class="text-danger">*</span></label>
                                <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                                    <option value="" disabled {{ old('category') ? '' : 'selected' }}>Sélectionnez une catégorie</option>
                                    <option value="historique" {{ old('category') == 'historique' ? 'selected' : '' }}>Site historique</option>
                                    <option value="naturel" {{ old('category') == 'naturel' ? 'selected' : '' }}>Site naturel</option>
                                    <option value="culturel" {{ old('category') == 'culturel' ? 'selected' : '' }}>Site culturel</option>
                                    <option value="religieux" {{ old('category') == 'religieux' ? 'selected' : '' }}>Site religieux</option>
                                    <option value="archéologique" {{ old('category') == 'archéologique' ? 'selected' : '' }}>Site archéologique</option>
                                    <option value="artisanal" {{ old('category') == 'artisanal' ? 'selected' : '' }}>Site artisanal</option>
                                </select>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="city" class="form-label">Ville <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" name="city" value="{{ old('city') }}" required>
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" required>{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Coordonnées</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="address" class="form-label">Adresse <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address') }}" required>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Téléphone <span class="text-danger">*</span></label>
                                        <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" required>
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="website" class="form-label">Site web</label>
                                        <input type="url" class="form-control @error('website') is-invalid @enderror" id="website" name="website" value="{{ old('website') }}" placeholder="https://exemple.com">
                                        @error('website')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Prix et disponibilité</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="price_min" class="form-label">Prix minimum (FCFA) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="number" class="form-control @error('price_min') is-invalid @enderror" id="price_min" name="price_min" value="{{ old('price_min', 0) }}" min="0" step="0.01" required>
                                            <span class="input-group-text">FCFA</span>
                                            @error('price_min')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="price_max" class="form-label">Prix maximum (FCFA) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="number" class="form-control @error('price_max') is-invalid @enderror" id="price_max" name="price_max" value="{{ old('price_max', 0) }}" min="0" step="0.01" required>
                                            <span class="input-group-text">FCFA</span>
                                            @error('price_max')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Jours d'ouverture <span class="text-danger">*</span></label>
                                <div class="row g-2">
                                    @php
                                        $days = [
                                            'monday' => 'Lundi',
                                            'tuesday' => 'Mardi',
                                            'wednesday' => 'Mercredi',
                                            'thursday' => 'Jeudi',
                                            'friday' => 'Vendredi',
                                            'saturday' => 'Samedi',
                                            'sunday' => 'Dimanche'
                                        ];
                                        $openingDays = old('opening_days', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday']);
                                    @endphp
                                    @foreach($days as $key => $day)
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="day_{{ $key }}" name="opening_days[]" value="{{ $key }}" {{ in_array($key, $openingDays) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="day_{{ $key }}">{{ $day }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('opening_days')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="opening_time" class="form-label">Heure d'ouverture <span class="text-danger">*</span></label>
                                        <input type="time" class="form-control @error('opening_time') is-invalid @enderror" id="opening_time" name="opening_time" value="{{ old('opening_time', '09:00') }}" required>
                                        @error('opening_time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="closing_time" class="form-label">Heure de fermeture <span class="text-danger">*</span></label>
                                        <input type="time" class="form-control @error('closing_time') is-invalid @enderror" id="closing_time" name="closing_time" value="{{ old('closing_time', '18:00') }}" required>
                                        @error('closing_time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Localisation</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="latitude" class="form-label">Latitude <span class="text-danger">*</span></label>
                                        <input type="number" step="any" class="form-control @error('latitude') is-invalid @enderror" id="latitude" name="latitude" value="{{ old('latitude') }}" required>
                                        @error('latitude')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="longitude" class="form-label">Longitude <span class="text-danger">*</span></label>
                                        <input type="number" step="any" class="form-control @error('longitude') is-invalid @enderror" id="longitude" name="longitude" value="{{ old('longitude') }}" required>
                                        @error('longitude')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-1"></i> Vous pouvez utiliser <a href="https://www.google.com/maps" target="_blank">Google Maps</a> pour obtenir les coordonnées de votre site. Faites un clic droit sur l'emplacement et sélectionnez "Plus d'infos sur cet endroit" pour voir les coordonnées.
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Image principale</h6>
                        </div>
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <img id="imagePreview" src="{{ asset('assets/img/no-image.jpg') }}" alt="Aperçu de l'image" class="img-fluid rounded" style="max-height: 200px;">
                            </div>
                            <div class="mb-3">
                                <input type="file" class="form-control @error('photo') is-invalid @enderror" id="photo" name="photo" accept="image/*" onchange="previewImage(this)">
                                @error('photo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Format recommandé : 1200x800px, max 2MB</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Galerie d'images</h6>
                        </div>
                        <div class="card-body">
                            <div id="galleryPreview" class="mb-3">
                                <div class="text-center text-muted py-4">
                                    <i class="bi bi-images" style="font-size: 2rem;"></i>
                                    <p class="mb-0">Aucune image sélectionnée</p>
                                </div>
                            </div>
                            <div class="mb-3">
                                <input type="file" class="form-control" id="gallery" name="gallery[]" multiple accept="image/*" onchange="previewGallery(this)">
                                <div class="form-text">Sélectionnez jusqu'à 10 images, max 2MB chacune</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Statut</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Activer ce site</label>
                            </div>
                            <p class="small text-muted mb-0">Si désactivé, le site ne sera pas visible par les visiteurs.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('site-manager.sites.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg me-1"></i> Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> Enregistrer le site
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Aperçu de l'image principale
    function previewImage(input) {
        const preview = document.getElementById('imagePreview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    
    // Aperçu de la galerie
    function previewGallery(input) {
        const preview = document.getElementById('galleryPreview');
        preview.innerHTML = '';
        
        if (input.files.length > 0) {
            const row = document.createElement('div');
            row.className = 'row g-2';
            
            for (let i = 0; i < Math.min(input.files.length, 10); i++) {
                const reader = new FileReader();
                const col = document.createElement('div');
                col.className = 'col-6 col-md-4';
                
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'img-fluid rounded';
                    img.style.height = '80px';
                    img.style.objectFit = 'cover';
                    img.style.width = '100%';
                    col.appendChild(img);
                }
                
                reader.readAsDataURL(input.files[i]);
                row.appendChild(col);
            }
            
            preview.appendChild(row);
            
            if (input.files.length > 10) {
                const alert = document.createElement('div');
                alert.className = 'alert alert-warning mt-2 mb-0';
                alert.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i> Seules les  premières images seront téléchargées.';
                preview.appendChild(alert);
            }
        } else {
            preview.innerHTML = `
                <div class="text-center text-muted py-4">
                    <i class="bi bi-images" style="font-size: 2rem;"></i>
                    <p class="mb-0">Aucune image sélectionnée</p>
                </div>
            `;
        }
    }
    
    // Initialisation des tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush
