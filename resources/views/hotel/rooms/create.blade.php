@extends('layouts.tourist')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Ajouter une nouvelle chambre</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('hotel.rooms.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="room_number" class="form-label">Numéro de chambre *</label>
                                    <input type="text" class="form-control @error('room_number') is-invalid @enderror" 
                                           id="room_number" name="room_number" 
                                           value="{{ old('room_number') }}" required>
                                    @error('room_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="room_type_id" class="form-label">Type de chambre *</label>
                                    <select class="form-select @error('room_type_id') is-invalid @enderror" 
                                            id="room_type_id" name="room_type_id" required>
                                        <option value="">Sélectionner un type</option>
                                        @foreach($roomTypes as $type)
                                            <option value="{{ $type->id }}" {{ old('room_type_id') == $type->id ? 'selected' : '' }}>
                                                {{ $type->name }} ({{ number_format($type->base_price, 0, ',', ' ') }} FCFA)
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('room_type_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="floor" class="form-label">Étage</label>
                                    <input type="number" class="form-control @error('floor') is-invalid @enderror" 
                                           id="floor" name="floor" value="{{ old('floor', 0) }}" min="0">
                                    @error('floor')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="capacity" class="form-label">Capacité (personnes) *</label>
                                    <input type="number" class="form-control @error('capacity') is-invalid @enderror" 
                                           id="capacity" name="capacity" value="{{ old('capacity', 1) }}" min="1" required>
                                    @error('capacity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="price_per_night" class="form-label">Prix/nuit (FCFA) *</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control @error('price_per_night') is-invalid @enderror" 
                                               id="price_per_night" name="price_per_night" 
                                               value="{{ old('price_per_night') }}" min="0" step="100" required>
                                        <span class="input-group-text">FCFA</span>
                                    </div>
                                    @error('price_per_night')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="3">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">Équipements</label>
                                    <div class="row g-2">
                                        @foreach($amenities as $amenity)
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" 
                                                           id="amenity_{{ $amenity->id }}" 
                                                           name="amenities[]" 
                                                           value="{{ $amenity->id }}"
                                                           {{ in_array($amenity->id, old('amenities', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="amenity_{{ $amenity->id }}">
                                                        <i class="fas {{ $amenity->icon }} me-1"></i>
                                                        {{ $amenity->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="photos" class="form-label">Photos de la chambre</label>
                                    <input class="form-control @error('photos.*') is-invalid @enderror" 
                                           type="file" id="photos" name="photos[]" multiple 
                                           accept="image/jpeg,image/png,image/webp">
                                    <div class="form-text">Sélectionnez jusqu'à 5 photos (max 2MB chacune)</div>
                                    @error('photos.*')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" 
                                           id="is_available" name="is_available" value="1" 
                                           {{ old('is_available', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_available">
                                        Disponible à la réservation
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('hotel.rooms.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Enregistrer la chambre
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Mise à jour dynamique du prix selon le type de chambre sélectionné
    document.getElementById('room_type_id').addEventListener('change', function() {
        const typeId = this.value;
        if (!typeId) return;
        
        // Ici, vous pouvez ajouter une requête AJAX pour récupérer le prix de base
        // du type de chambre sélectionné et mettre à jour le champ price_per_night
        // Exemple avec des données en dur :
        const typePrices = {
            @foreach($roomTypes as $type)
                '{{ $type->id }}': {{ $type->base_price }},
            @endforeach
        };
        
        if (typePrices[typeId]) {
            document.getElementById('price_per_night').value = typePrices[typeId];
        }
    });
</script>
@endpush
@endsection
