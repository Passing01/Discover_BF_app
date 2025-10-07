@extends('layouts.hotel-manager')

@php
    $isEdit = isset($client) && $client->exists;
    $route = $isEdit 
        ? route('hotels.clients.update', [$hotel->id, $client->id])
        : route('hotels.clients.store', $hotel->id);
    $method = $isEdit ? 'PUT' : 'POST';
    $title = $isEdit ? 'Modifier le client' : 'Nouveau client';
    $buttonText = $isEdit ? 'Mettre à jour' : 'Créer le client';

    // Valeurs par défaut pour éviter les erreurs
    $client = $client ?? new \App\Models\User();
    $profile = $client->profile ?? new \App\Models\UserProfile();
@endphp

@section('title', $title)

@section('content')

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $title }}</h1>
        <a href="{{ route('hotels.clients.index', $hotel->id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ $route }}" method="POST" id="clientForm">
                @csrf
                @method($method)

                <div class="row">
                    <div class="col-md-6">
                        <h5 class="mb-3">Informations personnelles</h5>
                        
                        <div class="form-group">
                            <label for="first_name">Prénom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                   id="first_name" name="first_name" 
                                   value="{{ old('first_name', $client->first_name) }}" required>
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="last_name">Nom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                   id="last_name" name="last_name" 
                                   value="{{ old('last_name', $client->last_name) }}" required>
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" 
                                   value="{{ old('email', $client->email) }}" {{ $isEdit ? 'readonly' : 'required' }}>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="phone">Téléphone</label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" 
                                   value="{{ old('phone', $client->phone) }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="date_of_birth">Date de naissance</label>
                            <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" 
                                   id="date_of_birth" name="date_of_birth" 
                                   value="{{ old('date_of_birth', $profile->date_of_birth ? $profile->date_of_birth->format('Y-m-d') : '') }}">
                            @error('date_of_birth')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="gender">Genre</label>
                            <select class="form-control @error('gender') is-invalid @enderror" id="gender" name="gender">
                                <option value="">Sélectionner...</option>
                                <option value="male" {{ old('gender', $profile->gender) == 'male' ? 'selected' : '' }}>Homme</option>
                                <option value="female" {{ old('gender', $profile->gender) == 'female' ? 'selected' : '' }}>Femme</option>
                                <option value="other" {{ old('gender', $profile->gender) == 'other' ? 'selected' : '' }}>Autre</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h5 class="mb-3">Adresse</h5>
                        
                        <div class="form-group">
                            <label for="address">Adresse</label>
                            <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                   id="address" name="address" 
                                   value="{{ old('address', $profile->address) }}">
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="postal_code">Code postal</label>
                                    <input type="text" class="form-control @error('postal_code') is-invalid @enderror" 
                                           id="postal_code" name="postal_code" 
                                           value="{{ old('postal_code', $profile->postal_code) }}">
                                    @error('postal_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="city">Ville</label>
                                    <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                           id="city" name="city" 
                                           value="{{ old('city', $profile->city) }}">
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="country">Pays</label>
                            <input type="text" class="form-control @error('country') is-invalid @enderror" 
                                   id="country" name="country" 
                                   value="{{ old('country', $profile->country) }}">
                            @error('country')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" 
                                      rows="4">{{ old('notes', $profile->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12 text-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> {{ $buttonText }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialisation des sélecteurs
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%'
        });

        // Validation du formulaire
        $('#clientForm').validate({
            rules: {
                first_name: 'required',
                last_name: 'required',
                email: {
                    required: true,
                    email: true
                },
                phone: {
                    minlength: 8,
                    maxlength: 20
                },
                postal_code: {
                    minlength: 4,
                    maxlength: 10
                }
            },
            messages: {
                first_name: 'Veuillez saisir le prénom',
                last_name: 'Veuillez saisir le nom',
                email: {
                    required: 'Veuillez saisir une adresse email',
                    email: 'Veuillez saisir une adresse email valide'
                },
                phone: {
                    minlength: 'Le numéro de téléphone doit contenir au moins 8 caractères',
                    maxlength: 'Le numéro de téléphone ne doit pas dépasser 20 caractères'
                }
            },
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            }
        });
    });
</script>
@endpush
