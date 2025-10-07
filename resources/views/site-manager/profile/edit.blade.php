@extends('layouts.site-manager')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Mon profil</h1>
        <p class="text-muted mb-0">Gérez vos informations personnelles et vos paramètres de compte</p>
    </div>
    <div>
        <a href="{{ route('site-manager.dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Retour
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">Informations personnelles</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('site-manager.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row mb-3">
                        <div class="col-md-4 text-center">
                            <div class="position-relative d-inline-block mb-3">
                                @if(auth()->user()->profile_photo_path)
                                    <img src="{{ Storage::url(auth()->user()->profile_photo_path) }}" 
                                         alt="Photo de profil" 
                                         class="rounded-circle" 
                                         style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #fff; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);">
                                @else
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 120px; height: 120px; border: 3px solid #fff; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);">
                                        <i class="bi bi-person fs-1 text-muted"></i>
                                    </div>
                                @endif
                                <label for="photo" class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-2" style="cursor: pointer;">
                                    <i class="bi bi-camera"></i>
                                    <input type="file" id="photo" name="photo" class="d-none" onchange="previewImage(this)">
                                </label>
                            </div>
                            <div class="small text-muted">
                                <p class="mb-0">Format: JPG, PNG, GIF</p>
                                <p class="mb-0">Taille max: 2MB</p>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="first_name" class="form-label">Prénom <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                           id="first_name" name="first_name" 
                                           value="{{ old('first_name', auth()->user()->first_name) }}" required>
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="last_name" class="form-label">Nom <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                           id="last_name" name="last_name" 
                                           value="{{ old('last_name', auth()->user()->last_name) }}" required>
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Adresse email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" 
                                           value="{{ old('email', auth()->user()->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Téléphone</label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" 
                                           value="{{ old('phone', auth()->user()->phone) }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="address" class="form-label">Adresse</label>
                            <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                   id="address" name="address" 
                                   value="{{ old('address', auth()->user()->address) }}">
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="city" class="form-label">Ville</label>
                            <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                   id="city" name="city" 
                                   value="{{ old('city', auth()->user()->city) }}">
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="country" class="form-label">Pays</label>
                            <select class="form-select @error('country') is-invalid @enderror" id="country" name="country">
                                <option value="">Sélectionner un pays</option>
                                @foreach($countries as $code => $name)
                                    <option value="{{ $code }}" {{ old('country', auth()->user()->country) == $code ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('country')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="postal_code" class="form-label">Code postal</label>
                            <input type="text" class="form-control @error('postal_code') is-invalid @enderror" 
                                   id="postal_code" name="postal_code" 
                                   value="{{ old('postal_code', auth()->user()->postal_code) }}">
                            @error('postal_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="language" class="form-label">Langue</label>
                            <select class="form-select @error('language') is-invalid @enderror" id="language" name="language">
                                <option value="fr" {{ old('language', auth()->user()->language ?? 'fr') == 'fr' ? 'selected' : '' }}>Français</option>
                                <option value="en" {{ old('language', auth()->user()->language) == 'en' ? 'selected' : '' }}>English</option>
                            </select>
                            @error('language')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label for="bio" class="form-label">À propos de moi</label>
                            <textarea class="form-control @error('bio') is-invalid @enderror" 
                                      id="bio" name="bio" 
                                      rows="3">{{ old('bio', auth()->user()->bio) }}</textarea>
                            @error('bio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="mb-0">Changer de mot de passe</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('site-manager.profile.password') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="current_password" class="form-label">Mot de passe actuel <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                       id="current_password" name="current_password" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="current_password">
                                    <i class="bi bi-eye"></i>
                                </button>
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="new_password" class="form-label">Nouveau mot de passe <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('new_password') is-invalid @enderror" 
                                       id="new_password" name="new_password" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="new_password">
                                    <i class="bi bi-eye"></i>
                                </button>
                                @error('new_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-text">Minimum 8 caractères</div>
                        </div>
                        <div class="col-md-6">
                            <label for="new_password_confirmation" class="form-label">Confirmer le mot de passe <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" 
                                       id="new_password_confirmation" name="new_password_confirmation" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="new_password_confirmation">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="progress mt-2" style="height: 5px;">
                                <div id="password-strength-bar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div id="password-strength-text" class="small mt-1"></div>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="logout_other_devices" name="logout_other_devices">
                                <label class="form-check-label" for="logout_other_devices">
                                    Me déconnecter de tous les autres appareils
                                </label>
                            </div>
                        </div>
                        <div class="col-12 mt-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-key me-1"></i> Mettre à jour le mot de passe
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">Sécurité du compte</h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-3">
                        <i class="bi bi-shield-lock text-primary"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">Authentification à deux facteurs</h6>
                        <small class="text-muted">Ajoutez une couche de sécurité supplémentaire à votre compte</small>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="badge bg-{{ auth()->user()->two_factor_secret ? 'success' : 'secondary' }}">
                        {{ auth()->user()->two_factor_secret ? 'Activé' : 'Désactivé' }}
                    </span>
                    <a href="#" class="btn btn-sm btn-outline-primary">
                        {{ auth()->user()->two_factor_secret ? 'Gérer' : 'Activer' }}
                    </a>
                </div>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">Sessions actives</h6>
            </div>
            <div class="card-body">
                <p class="small text-muted">Voici les appareils sur lesquels vous êtes actuellement connecté.</p>
                
                @foreach($sessions as $session)
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-light p-2 rounded-circle me-3">
                            <i class="bi {{ $session->agent->isMobile() ? 'bi-phone' : ($session->agent->isTablet() ? 'bi-tablet' : 'bi-laptop') }} text-muted"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <h6 class="mb-0">
                                    {{ $session->agent->browser() }} sur {{ $session->agent->platform() }}
                                    @if($session->id === $currentSessionId)
                                        <span class="badge bg-primary">Cet appareil</span>
                                    @endif
                                </h6>
                                @if($session->id !== $currentSessionId)
                                    <form action="{{ route('site-manager.profile.logout-other-sessions') }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="session_id" value="{{ $session->id }}">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-box-arrow-right"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                            <div class="small text-muted">
                                {{ $session->ip_address }}
                                <span class="mx-1">•</span>
                                {{ $session->last_activity->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                @endforeach
                
                @if(count($sessions) > 1)
                    <form action="{{ route('site-manager.profile.logout-other-sessions') }}" method="POST" class="mt-3">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bi bi-box-arrow-right me-1"></i> Se déconnecter de tous les autres appareils
                        </button>
                    </form>
                @endif
            </div>
        </div>
        
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="mb-0">Supprimer le compte</h6>
            </div>
            <div class="card-body">
                <p class="small text-muted">
                    La suppression de votre compte est irréversible. Toutes vos données seront définitivement supprimées.
                </p>
                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                    <i class="bi bi-trash me-1"></i> Supprimer mon compte
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Account Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteAccountModalLabel">Supprimer votre compte</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('site-manager.profile.destroy') }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir supprimer définitivement votre compte ? Cette action est irréversible.</p>
                    <div class="mb-3">
                        <label for="password" class="form-label">Pour confirmer, veuillez entrer votre mot de passe :</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="confirm_delete" name="confirm_delete" required>
                        <label class="form-check-label" for="confirm_delete">
                            Je comprends que cette action est irréversible et que toutes mes données seront définitivement supprimées.
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">Supprimer définitivement</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Style pour l'aperçu de l'image */
    .profile-photo-preview {
        width: 100%;
        max-width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 50%;
        border: 3px solid #fff;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    
    /* Style pour les champs de mot de passe */
    .input-group-text.toggle-password {
        cursor: pointer;
    }
    
    /* Style pour la barre de force du mot de passe */
    #password-strength-bar {
        transition: width 0.3s ease-in-out;
    }
    
    /* Style pour les indicateurs de force du mot de passe */
    .password-weak {
        background-color: #dc3545;
    }
    
    .password-medium {
        background-color: #fd7e14;
    }
    
    .password-strong {
        background-color: #20c997;
    }
    
    .password-very-strong {
        background-color: #198754;
    }
    
    /* Style pour les sessions actives */
    .session-device-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background-color: #f8f9fa;
    }
    
    /* Style pour les cartes */
    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        margin-bottom: 1.5rem;
    }
    
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        font-weight: 600;
    }
    
    /* Style pour les formulaires */
    .form-label {
        font-weight: 500;
        margin-bottom: 0.25rem;
    }
    
    /* Style pour les boutons de bascule du mot de passe */
    .toggle-password {
        cursor: pointer;
    }
    
    /* Style pour les messages d'erreur */
    .invalid-feedback {
        font-size: 0.8rem;
    }
    
    /* Style pour les onglets */
    .nav-tabs .nav-link {
        color: #6c757d;
        font-weight: 500;
        border: none;
        padding: 0.75rem 1.25rem;
    }
    
    .nav-tabs .nav-link.active {
        color: #0d6efd;
        background-color: transparent;
        border-bottom: 2px solid #0d6efd;
    }
    
    /* Style pour les sélecteurs de pays */
    .select2-container--default .select2-selection--single {
        height: calc(1.5em + 0.75rem + 2px);
        padding: 0.375rem 0.75rem;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: calc(1.5em + 0.75rem);
    }
</style>
@endpush

@push('scripts')
<script>
    // Aperçu de l'image de profil
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function(e) {
                // Créer un élément d'aperçu s'il n'existe pas
                var preview = document.getElementById('profile-photo-preview');
                if (!preview) {
                    preview = document.createElement('img');
                    preview.id = 'profile-photo-preview';
                    preview.className = 'profile-photo-preview mt-2';
                    input.parentNode.appendChild(preview);
                }
                preview.src = e.target.result;
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
    
    // Basculer la visibilité du mot de passe
    document.addEventListener('DOMContentLoaded', function() {
        // Désactiver le bouton de soumission si les champs de mot de passe sont vides
        const passwordForm = document.querySelector('form[action$="password"]');
        if (passwordForm) {
            const currentPassword = document.getElementById('current_password');
            const newPassword = document.getElementById('new_password');
            const confirmPassword = document.getElementById('new_password_confirmation');
            const submitButton = passwordForm.querySelector('button[type="submit"]');
            
            function checkPasswords() {
                if (currentPassword.value && newPassword.value && confirmPassword.value) {
                    submitButton.disabled = false;
                } else {
                    submitButton.disabled = true;
                }
            }
            
            currentPassword.addEventListener('input', checkPasswords);
            newPassword.addEventListener('input', checkPasswords);
            confirmPassword.addEventListener('input', checkPasswords);
            
            // Désactiver le bouton au chargement si les champs sont vides
            checkPasswords();
            
            // Vérifier la force du mot de passe
            newPassword.addEventListener('input', function() {
                const strengthBar = document.getElementById('password-strength-bar');
                const strengthText = document.getElementById('password-strength-text');
                
                if (!strengthBar || !strengthText) return;
                
                const password = this.value;
                let strength = 0;
                let message = '';
                
                // Vérifier la longueur
                if (password.length >= 8) strength += 20;
                // Vérifier les minuscules
                if (password.match(/[a-z]+/)) strength += 20;
                // Vérifier les majuscules
                if (password.match(/[A-Z]+/)) strength += 20;
                // Vérifier les chiffres
                if (password.match(/[0-9]+/)) strength += 20;
                // Vérifier les caractères spéciaux
                if (password.match(/[!@#$%^&*(),.?":{}|<>]+/)) strength += 20;
                
                // Mettre à jour la barre de progression
                strengthBar.style.width = strength + '%';
                
                // Définir la couleur et le message en fonction de la force
                if (strength < 40) {
                    strengthBar.className = 'progress-bar bg-danger';
                    message = 'Faible';
                } else if (strength < 70) {
                    strengthBar.className = 'progress-bar bg-warning';
                    message = 'Moyen';
                } else if (strength < 90) {
                    strengthBar.className = 'progress-bar bg-info';
                    message = 'Fort';
                } else {
                    strengthBar.className = 'progress-bar bg-success';
                    message = 'Très fort';
                }
                
                strengthText.textContent = message;
                strengthText.className = 'small mt-1 text-' + 
                    (strength < 40 ? 'danger' : 
                     strength < 70 ? 'warning' : 
                     strength < 90 ? 'info' : 'success');
            });
        }
        
        // Activer les tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Gestion de l'affichage/masquage des mots de passe
        document.querySelectorAll('.toggle-password').forEach(function(button) {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const targetInput = document.getElementById(targetId);
                const icon = this.querySelector('i');
                
                if (targetInput.type === 'password') {
                    targetInput.type = 'text';
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                } else {
                    targetInput.type = 'password';
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                }
            });
        });
        
        // Initialisation de Select2 pour les sélecteurs de pays
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $('#country').select2({
                placeholder: 'Sélectionner un pays',
                allowClear: true,
                width: '100%',
                theme: 'bootstrap-5',
                language: 'fr'
            });
        }
    });
    
    // Confirmation avant suppression de compte
    document.addEventListener('DOMContentLoaded', function() {
        const deleteForm = document.querySelector('form[action$="profile"]');
        if (deleteForm) {
            deleteForm.addEventListener('submit', function(e) {
                if (!confirm('Êtes-vous sûr de vouloir supprimer définitivement votre compte ? Cette action est irréversible.')) {
                    e.preventDefault();
                }
            });
        }
    });
</script>
@endpush
