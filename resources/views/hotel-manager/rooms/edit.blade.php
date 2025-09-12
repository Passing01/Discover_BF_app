@extends('layouts.hotel-manager')

@section('content')
    @push('styles')
        <style>
            .image-preview {
                display: flex;
                flex-wrap: wrap;
                gap: 1rem;
                margin-top: 1rem;
            }
            .image-preview-item {
                position: relative;
                width: 150px;
                height: 100px;
                border-radius: 0.5rem;
                overflow: hidden;
                border: 1px solid #e5e7eb;
            }
            .image-preview-item img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
            .remove-image {
                position: absolute;
                top: 0.25rem;
                right: 0.25rem;
                background-color: rgba(239, 68, 68, 0.8);
                color: white;
                border-radius: 50%;
                width: 1.5rem;
                height: 1.5rem;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
            }
            .amenity-item {
                display: flex;
                align-items: center;
                margin-bottom: 0.5rem;
            }
            .amenity-icon {
                margin-right: 0.5rem;
                color: #4f46e5;
            }
            .existing-image {
                position: relative;
                transition: all 0.2s;
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
        <a href="{{ route('hotel-manager.rooms.show', ['hotel' => $hotel, 'room' => $room]) }}" 
           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
            Retour
        </a>
    </x-slot>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <form action="{{ route('hotel-manager.rooms.update', ['hotel' => $hotel, 'room' => $room]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="px-4 py-5 sm:p-6">
                <div class="space-y-8 divide-y divide-gray-200">
                    <!-- Informations de base -->
                    <div class="space-y-8 divide-y divide-gray-200">
                        <div>
                            <div>
                                <h3 class="text-lg leading-6 font-medium text-gray-900">
                                    Informations de base
                                </h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    Les informations essentielles pour votre chambre.
                                </p>
                            </div>

                            <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                <div class="sm:col-span-4">
                                    <label for="name" class="block text-sm font-medium text-gray-700">
                                        Nom de la chambre <span class="text-red-500">*</span>
                                    </label>
                                    <div class="mt-1">
                                        <input type="text" name="name" id="name" value="{{ old('name', $room->name) }}" required
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                        @error('name')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="room_type_id" class="block text-sm font-medium text-gray-700">
                                        Type de chambre <span class="text-red-500">*</span>
                                    </label>
                                    <div class="mt-1">
                                        <select id="room_type_id" name="room_type_id" required
                                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                            <option value="">Sélectionnez un type</option>
                                            @foreach($roomTypes as $type)
                                                <option value="{{ $type->id }}" {{ old('room_type_id', $room->room_type_id) == $type->id ? 'selected' : '' }}>
                                                    {{ $type->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('room_type_id')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="sm:col-span-6">
                                    <label for="description" class="block text-sm font-medium text-gray-700">
                                        Description <span class="text-red-500">*</span>
                                    </label>
                                    <div class="mt-1">
                                        <textarea id="description" name="description" rows="3" required
                                                  class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-300 rounded-md">{{ old('description', $room->description) }}</textarea>
                                        <p class="mt-2 text-sm text-gray-500">Décrivez votre chambre de manière attrayante.</p>
                                        @error('description')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="price_per_night" class="block text-sm font-medium text-gray-700">
                                        Prix par nuit (€) <span class="text-red-500">*</span>
                                    </label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">€</span>
                                        </div>
                                        <input type="number" name="price_per_night" id="price_per_night" 
                                               value="{{ old('price_per_night', $room->price_per_night) }}" step="0.01" min="0" required
                                               class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md"
                                               placeholder="0.00">
                                        @error('price_per_night')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="capacity" class="block text-sm font-medium text-gray-700">
                                        Capacité (personnes) <span class="text-red-500">*</span>
                                    </label>
                                    <div class="mt-1">
                                        <input type="number" name="capacity" id="capacity" value="{{ old('capacity', $room->capacity) }}" min="1" max="10" required
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                        @error('capacity')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="quantity" class="block text-sm font-medium text-gray-700">
                                        Nombre de chambres identiques <span class="text-red-500">*</span>
                                    </label>
                                    <div class="mt-1">
                                        <input type="number" name="quantity" id="quantity" value="{{ old('quantity', $room->quantity) }}" min="1" required
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                        @error('quantity')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="sm:col-span-6">
                                    <label class="block text-sm font-medium text-gray-700">
                                        Photos
                                        @if($room->photos->isEmpty())
                                            <span class="text-red-500">*</span>
                                        @endif
                                    </label>
                                    
                                    <!-- Aperçu des images existantes avec option de suppression -->
                                    @if($room->photos->isNotEmpty())
                                        <div class="mt-2 flex flex-wrap gap-4">
                                            @foreach($room->photos as $photo)
                                                <div class="existing-image group relative w-32 h-24 rounded-md overflow-hidden">
                                                    <img src="{{ Storage::url($photo->path) }}" alt="Photo de la chambre" class="w-full h-full object-cover">
                                                    <input type="checkbox" 
                                                           name="delete_photos[]" 
                                                           value="{{ $photo->id }}" 
                                                           id="delete_photo_{{ $photo->id }}"
                                                           class="delete-image-checkbox">
                                                    <label for="delete_photo_{{ $photo->id }}" class="delete-image-label hidden group-hover:block">
                                                        Supprimer
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                        <p class="mt-1 text-sm text-gray-500">Cochez les images que vous souhaitez supprimer.</p>
                                    @endif
                                    
                                    <!-- Téléchargement de nouvelles images -->
                                    <div class="mt-4 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                        <div class="space-y-1 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <div class="flex text-sm text-gray-600">
                                                <label for="photos" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                    <span>Télécharger des fichiers</span>
                                                    <input id="photos" name="photos[]" type="file" multiple accept="image/*" class="sr-only" {{ $room->photos->isEmpty() ? 'required' : '' }}>
                                                </label>
                                                <p class="pl-1">ou glisser-déposer</p>
                                            </div>
                                            <p class="text-xs text-gray-500">
                                                PNG, JPG, JPEG jusqu'à 5 Mo. {{ $room->photos->isEmpty() ? 'Minimum 1 photo requise.' : '' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div id="image-preview" class="image-preview">
                                        <!-- Les aperçus des nouvelles images seront ajoutés ici -->
                                    </div>
                                    @error('photos')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    @error('photos.*')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Caractéristiques de la chambre -->
                        <div class="pt-8">
                            <div>
                                <h3 class="text-lg leading-6 font-medium text-gray-900">
                                    Caractéristiques
                                </h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    Détails sur la taille, les lits et les équipements de la chambre.
                                </p>
                            </div>

                            <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                <div class="sm:col-span-2">
                                    <label for="size" class="block text-sm font-medium text-gray-700">
                                        Superficie (m²)
                                    </label>
                                    <div class="mt-1">
                                        <input type="number" name="size" id="size" value="{{ old('size', $room->size) }}" min="1"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                        @error('size')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="bed_type" class="block text-sm font-medium text-gray-700">
                                        Type de lit
                                    </label>
                                    <div class="mt-1">
                                        <select id="bed_type" name="bed_type"
                                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
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
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="view" class="block text-sm font-medium text-gray-700">
                                        Vue
                                    </label>
                                    <div class="mt-1">
                                        <select id="view" name="view"
                                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
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
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="sm:col-span-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Équipements de la chambre
                                    </label>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2">
                                        @foreach($amenities as $amenity)
                                            <div class="relative flex items-start">
                                                <div class="flex items-center h-5">
                                                    <input id="amenity-{{ $amenity->id }}" name="amenities[]" type="checkbox" value="{{ $amenity->id }}"
                                                           {{ in_array($amenity->id, old('amenities', $room->amenities->pluck('id')->toArray())) ? 'checked' : '' }}
                                                           class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                                </div>
                                                <div class="ml-3 text-sm">
                                                    <label for="amenity-{{ $amenity->id }}" class="font-medium text-gray-700">{{ $amenity->name }}</label>
                                                    @if($amenity->description)
                                                        <p class="text-gray-500 text-xs">{{ $amenity->description }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('amenities')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Options de réservation -->
                        <div class="pt-8">
                            <div>
                                <h3 class="text-lg leading-6 font-medium text-gray-900">
                                    Options de réservation
                                </h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    Configurez les options de réservation pour cette chambre.
                                </p>
                            </div>

                            <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                <div class="sm:col-span-3">
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="is_smoking_allowed" name="is_smoking_allowed" type="checkbox" value="1"
                                                   {{ old('is_smoking_allowed', $room->is_smoking_allowed) ? 'checked' : '' }}
                                                   class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="is_smoking_allowed" class="font-medium text-gray-700">Fumeurs acceptés</label>
                                            <p class="text-gray-500">Cocher si cette chambre est réservée aux fumeurs</p>
                                        </div>
                                    </div>
                                    @error('is_smoking_allowed')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="sm:col-span-3">
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="is_available" name="is_available" type="checkbox" value="1"
                                                   {{ old('is_available', $room->is_available) ? 'checked' : '' }}
                                                   class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="is_available" class="font-medium text-gray-700">Disponible à la réservation</label>
                                            <p class="text-gray-500">Décocher pour masquer cette chambre des résultats de recherche</p>
                                        </div>
                                    </div>
                                    @error('is_available')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="sm:col-span-3">
                                    <label for="min_stay" class="block text-sm font-medium text-gray-700">
                                        Séjour minimum (nuits)
                                    </label>
                                    <div class="mt-1">
                                        <input type="number" name="min_stay" id="min_stay" value="{{ old('min_stay', $room->min_stay) }}" min="1"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                        @error('min_stay')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="sm:col-span-3">
                                    <label for="max_adults" class="block text-sm font-medium text-gray-700">
                                        Nombre maximum d'adultes
                                    </label>
                                    <div class="mt-1">
                                        <input type="number" name="max_adults" id="max_adults" value="{{ old('max_adults', $room->max_adults) }}" min="1" max="10"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                        @error('max_adults')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="sm:col-span-3">
                                    <label for="max_children" class="block text-sm font-medium text-gray-700">
                                        Nombre maximum d'enfants
                                    </label>
                                    <div class="mt-1">
                                        <input type="number" name="max_children" id="max_children" value="{{ old('max_children', $room->max_children) }}" min="0" max="10"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                        @error('max_children')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="sm:col-span-3">
                                    <label for="max_occupancy" class="block text-sm font-medium text-gray-700">
                                        Occupation maximale (total)
                                    </label>
                                    <div class="mt-1">
                                        <input type="number" name="max_occupancy" id="max_occupancy" value="{{ old('max_occupancy', $room->max_occupancy) }}" min="1" max="20"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                        @error('max_occupancy')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="sm:col-span-6">
                                    <label for="cancellation_policy" class="block text-sm font-medium text-gray-700">
                                        Politique d'annulation spécifique (optionnel)
                                    </label>
                                    <div class="mt-1">
                                        <textarea id="cancellation_policy" name="cancellation_policy" rows="3"
                                                  class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-300 rounded-md">{{ old('cancellation_policy', $room->cancellation_policy) }}</textarea>
                                        <p class="mt-2 text-sm text-gray-500">Si vide, la politique d'annulation de l'hôtel sera utilisée.</p>
                                        @error('cancellation_policy')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Services inclus -->
                        <div class="pt-8">
                            <div>
                                <h3 class="text-lg leading-6 font-medium text-gray-900">
                                    Services inclus
                                </h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    Cochez les services inclus dans le prix de la chambre.
                                </p>
                            </div>

                            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                                @foreach($services as $service)
                                    <div class="relative flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="service-{{ $service->id }}" name="included_services[]" type="checkbox" value="{{ $service->id }}"
                                                   {{ in_array($service->id, old('included_services', $room->includedServices->pluck('id')->toArray())) ? 'checked' : '' }}
                                                   class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="service-{{ $service->id }}" class="font-medium text-gray-700">{{ $service->name }}</label>
                                            @if($service->description)
                                                <p class="text-gray-500 text-xs">{{ $service->description }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('included_services')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="pt-5">
                    <div class="flex justify-between">
                        <a href="{{ route('hotel-manager.rooms.show', ['hotel' => $hotel, 'room' => $room]) }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Annuler
                        </a>
                        <div>
                            <button type="button" id="save-as-draft" class="mr-3 bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Enregistrer comme brouillon
                            </button>
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Mettre à jour la chambre
                            </button>
                        </div>
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
