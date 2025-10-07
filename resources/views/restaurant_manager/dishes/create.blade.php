@extends('layouts.restau')

@section('title', 'Ajouter un plat - ' . $restaurant->name)

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-md-5 col-8 align-self-center">
            <h3 class="text-themecolor">Ajouter un plat</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('restaurant-manager.dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item"><a href="{{ route('restaurant-manager.restaurants.index') }}">Mes restaurants</a></li>
                <li class="breadcrumb-item"><a href="{{ route('restaurant-manager.restaurants.dishes.index', $restaurant) }}">Plats - {{ $restaurant->name }}</a></li>
                <li class="breadcrumb-item active">Ajouter un plat</li>
            </ol>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('restaurant-manager.restaurants.dishes.store', $restaurant) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="form-group">
                            <label for="name">Nom du plat *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Description *</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="price">Prix (€) *</label>
                                    <input type="number" step="0.01" min="0" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price') }}" required>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="category">Catégorie *</label>
                                    <input type="text" class="form-control @error('category') is-invalid @enderror" id="category" name="category" value="{{ old('category') }}" required>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="is_available" name="is_available" value="1" {{ old('is_available', true) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_available">Disponible à la commande</label>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="image">Image du plat</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                                <label class="custom-file-label" for="image">Choisir une image</label>
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">Formats acceptés: JPG, PNG, JPEG. Taille max: 2MB</small>
                            <div class="mt-2" id="imagePreview"></div>
                        </div>
                        
                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Enregistrer
                            </button>
                            <a href="{{ route('restaurant-manager.restaurants.dishes.index', $restaurant) }}" class="btn btn-secondary">
                                <i class="fa fa-arrow-left"></i> Retour
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
    document.getElementById('image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('imagePreview');
                preview.innerHTML = `
                    <div class="mt-2">
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
    
    // Initialiser les tooltips
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@endpush
@endsection
