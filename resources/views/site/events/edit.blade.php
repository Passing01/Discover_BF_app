@extends('layouts.tourist')

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('site.events.index') }}">Événements</a></li>
            <li class="breadcrumb-item"><a href="{{ route('site.events.show', $event) }}">{{ $event->title }}</a></li>
            <li class="breadcrumb-item active">Modifier</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Modifier l'événement</h1>
        <div>
            <a href="{{ route('site.events.show', $event) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Annuler
            </a>
        </div>
    </div>

    <form action="{{ route('site.events.update', $event) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="card mb-4">
            <div class="card-header bg-white">
                <ul class="nav nav-tabs card-header-tabs" id="eventTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="details-tab" data-bs-toggle="tab" 
                                data-bs-target="#details" type="button">
                            <i class="fas fa-info-circle me-1"></i> Détails
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="media-tab" data-bs-toggle="tab" 
                                data-bs-target="#media" type="button">
                            <i class="fas fa-images me-1"></i> Médias
                        </button>
                    </li>
                </ul>
            </div>
            
            <div class="card-body">
                <div class="tab-content">
                    <!-- Détails de l'événement -->
                    <div class="tab-pane fade show active" id="details">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Titre <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                           id="title" name="title" value="{{ old('title', $event->title) }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="5" required>{{ old('description', $event->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="event_category_id" class="form-label">Catégorie <span class="text-danger">*</span></label>
                                            <select class="form-select @error('event_category_id') is-invalid @enderror" 
                                                    id="event_category_id" name="event_category_id" required>
                                                <option value="">Sélectionner une catégorie</option>
                                                @foreach($categories as $category)
                                                    @if($category->parent_id === null)
                                                        <optgroup label="{{ $category->name }}">
                                                            @foreach($categories as $subcategory)
                                                                @if($subcategory->parent_id === $category->id)
                                                                    <option value="{{ $subcategory->id }}" 
                                                                            {{ old('event_category_id', $event->event_category_id) == $subcategory->id ? 'selected' : '' }}>
                                                                        {{ $subcategory->name }}
                                                                    </option>
                                                                @endif
                                                            @endforeach
                                                        </optgroup>
                                                    @endif
                                                @endforeach
                                            </select>
                                            @error('event_category_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Statut <span class="text-danger">*</span></label>
                                            <select class="form-select @error('status') is-invalid @enderror" 
                                                    id="status" name="status" required>
                                                <option value="draft" {{ old('status', $event->status) === 'draft' ? 'selected' : '' }}>Brouillon</option>
                                                <option value="published" {{ old('status', $event->status) === 'published' ? 'selected' : '' }}>Publié</option>
                                                <option value="cancelled" {{ old('status', $event->status) === 'cancelled' ? 'selected' : '' }}>Annulé</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" 
                                           value="1" {{ old('is_featured', $event->is_featured) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_featured">Mettre en avant</label>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h5 class="card-title">Dates et heures</h5>
                                        
                                        <div class="mb-3">
                                            <label for="start_date" class="form-label">Date de début <span class="text-danger">*</span></label>
                                            <input type="datetime-local" class="form-control @error('start_date') is-invalid @enderror" 
                                                   id="start_date" name="start_date" 
                                                   value="{{ old('start_date', $event->start_date ? $event->start_date->format('Y-m-d\TH:i') : '') }}" required>
                                            @error('start_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="has_end_date" 
                                                   name="has_end_date" value="1" {{ old('has_end_date', $event->end_date) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="has_end_date">Ajouter une date de fin</label>
                                        </div>
                                        
                                        <div id="endDateContainer" style="display: {{ old('has_end_date', $event->end_date) ? 'block' : 'none' }};">
                                            <div class="mb-3">
                                                <label for="end_date" class="form-label">Date de fin <span class="text-danger">*</span></label>
                                                <input type="datetime-local" class="form-control @error('end_date') is-invalid @enderror" 
                                                       id="end_date" name="end_date" 
                                                       value="{{ old('end_date', $event->end_date ? $event->end_date->format('Y-m-d\TH:i') : '') }}" 
                                                       {{ old('has_end_date', $event->end_date) ? '' : 'disabled' }}>
                                                @error('end_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" id="is_all_day" name="is_all_day" 
                                                       value="1" {{ old('is_all_day', $event->is_all_day) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_all_day">Toute la journée</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Médias -->
                    <div class="tab-pane fade" id="media">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label">Image à la une <span class="text-danger">*</span></label>
                                    
                                    @if($event->hasMedia('featured'))
                                        <div class="mb-3">
                                            <img src="{{ $event->getFirstMediaUrl('featured', 'large') }}" 
                                                 alt="Image à la une" 
                                                 class="img-fluid rounded mb-2" 
                                                 style="max-height: 200px;">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       id="remove_featured_image" name="remove_featured_image" value="1">
                                                <label class="form-check-label text-danger" for="remove_featured_image">
                                                    Supprimer l'image actuelle
                                                </label>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <input type="file" class="form-control @error('featured_image') is-invalid @enderror" 
                                           id="featured_image" name="featured_image" accept="image/*">
                                    @error('featured_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Format recommandé : 1200x630px (16:9). Taille max : 5MB</div>
                                </div>
                                
                                <div>
                                    <label class="form-label">Galerie d'images</label>
                                    
                                    @if($event->hasMedia('gallery'))
                                        <div class="row g-2 mb-3">
                                            @foreach($event->getMedia('gallery') as $media)
                                                <div class="col-4">
                                                    <div class="position-relative">
                                                        <img src="{{ $media->getUrl('thumb') }}" 
                                                             alt="" 
                                                             class="img-fluid rounded">
                                                        <button type="button" 
                                                                class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1"
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#deleteImageModal"
                                                                data-image-id="{{ $media->id }}">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                    
                                    <input type="file" class="form-control @error('gallery_images.*') is-invalid @enderror" 
                                           id="gallery_images" name="gallery_images[]" multiple accept="image/*">
                                    @error('gallery_images.*')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Taille max : 5MB par image</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card-footer bg-white">
                <div class="d-flex justify-content-between">
                    <div>
                        @if($event->status === 'draft')
                            <button type="submit" name="save_action" value="save_draft" class="btn btn-outline-secondary">
                                <i class="far fa-save me-1"></i> Enregistrer le brouillon
                            </button>
                        @endif
                    </div>
                    <div>
                        <button type="submit" name="save_action" value="save_and_close" class="btn btn-outline-primary me-2">
                            <i class="fas fa-save me-1"></i> Enregistrer et fermer
                        </button>
                        <button type="submit" name="save_action" value="save_and_continue" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Enregistrer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Modal de confirmation de suppression d'image -->
<div class="modal fade" id="deleteImageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer cette image ?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteImageForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Gestion de l'affichage/masquage de la date de fin
document.getElementById('has_end_date').addEventListener('change', function() {
    const endDateContainer = document.getElementById('endDateContainer');
    const endDateInput = document.getElementById('end_date');
    
    if (this.checked) {
        endDateContainer.style.display = 'block';
        endDateInput.disabled = false;
    } else {
        endDateContainer.style.display = 'none';
        endDateInput.disabled = true;
    }
});

// Gestion de la suppression d'image de la galerie
const deleteImageModal = document.getElementById('deleteImageModal');
if (deleteImageModal) {
    deleteImageModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const imageId = button.getAttribute('data-image-id');
        const form = document.getElementById('deleteImageForm');
        form.action = `/media/${imageId}`;
    });
}

// Validation du formulaire
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    
    form.addEventListener('submit', function(event) {
        const title = document.getElementById('title').value.trim();
        const description = document.getElementById('description').value.trim();
        const startDate = document.getElementById('start_date').value;
        
        if (!title || !description || !startDate) {
            event.preventDefault();
            alert('Veuillez remplir tous les champs obligatoires.');
        }
    });
});
</script>
@endpush

@endsection
