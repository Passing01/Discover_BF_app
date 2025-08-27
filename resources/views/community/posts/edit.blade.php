@extends('layouts.tourist')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- En-tête de la page -->
            <div class="d-flex justify-content-between align-items-center mb-5">
                <div>
                    <a href="{{ route('community.posts.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
                        <i class="fas fa-arrow-left me-1"></i> Retour aux publications
                    </a>
                    <h1 class="h3 mb-0 text-primary">
                        <i class="fas fa-edit me-2"></i>Modifier la publication
                    </h1>
                </div>
            </div>

            <!-- Carte du formulaire -->
            <div class="card shadow-sm border-0 overflow-hidden">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 text-primary">
                        <i class="fas fa-pen me-2"></i>Modifier votre publication
                    </h5>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('community.posts.update', $post) }}" method="POST" enctype="multipart/form-data" id="editPostForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- Affichage des erreurs de validation -->
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        <!-- Champ de contenu -->
                        <div class="mb-4">
                            <label for="content" class="form-label fw-semibold">Contenu de la publication</label>
                            <div class="position-relative">
                                <textarea class="form-control @error('content') is-invalid @enderror" 
                                          id="content" 
                                          name="content" 
                                          rows="5" 
                                          placeholder="Partagez vos pensées, questions ou expériences..."
                                          required
                                          oninput="autoResize(this)">{{ old('content', $post->content) }}</textarea>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="addEmojiBtn">
                                        <i class="far fa-smile"></i>
                                    </button>
                                    <small class="text-muted">
                                        <span id="charCount">{{ strlen(old('content', $post->content)) }}</span>/2000 caractères
                                    </small>
                                </div>
                                @error('content')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Zone de téléchargement d'image -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold d-block">Image (optionnel)</label>
                            
                            <!-- Aperçu de l'image actuelle -->
                            @if($post->image)
                                <div class="mb-3">
                                    <p class="text-muted mb-2">Image actuelle :</p>
                                    <div class="position-relative d-inline-block">
                                        <img src="{{ Storage::url($post->image) }}" 
                                             alt="Image actuelle" 
                                             class="img-fluid rounded shadow-sm" 
                                             style="max-height: 200px;">
                                    </div>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" id="remove_image" name="remove_image">
                                        <label class="form-check-label text-danger" for="remove_image">
                                            <i class="fas fa-trash-alt me-1"></i> Supprimer l'image actuelle
                                        </label>
                                    </div>
                                </div>
                                <p class="text-muted mb-2">Ou remplacez par une nouvelle image :</p>
                            @endif
                            
                            <!-- Zone de glisser-déposer -->
                            <div id="imageUploadArea" class="border rounded-3 p-4 text-center mb-3" 
                                 style="border-style: dashed !important; cursor: pointer;">
                                <input type="file" 
                                       class="d-none" 
                                       id="image" 
                                       name="image"
                                       accept="image/*">
                                
                                <div id="uploadPlaceholder">
                                    <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                    <h5>Glissez-déposez une image ici</h5>
                                    <p class="text-muted mb-0">ou cliquez pour sélectionner un fichier</p>
                                    <small class="text-muted">Taille maximale : 5MB • Formats : JPG, PNG, GIF</small>
                                </div>
                                
                                <!-- Aperçu de la nouvelle image -->
                                <div id="imagePreview" class="d-none">
                                    <img id="previewImage" src="#" alt="Aperçu de l'image" class="img-fluid rounded shadow-sm mb-2">
                                    <button type="button" id="removeImageBtn" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash-alt me-1"></i> Supprimer l'image
                                    </button>
                                </div>
                            </div>
                            
                            @error('image')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        
                        <!-- Boutons d'action -->
                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <a href="{{ route('community.posts.show', $post) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i> Annuler
                            </a>
                            <div>
                                <a href="{{ route('community.posts.show', $post) }}" class="btn btn-outline-primary me-2">
                                    <i class="fas fa-eye me-1"></i> Voir la publication
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Enregistrer les modifications
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    #imageUploadArea {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    #imageUploadArea:hover {
        border-color: #adb5bd !important;
        background-color: #f8f9fa;
    }
    #imageUploadArea.dragover {
        border-color: #0d6efd !important;
        background-color: rgba(13, 110, 253, 0.05);
    }
    #previewImage {
        max-width: 100%;
        max-height: 400px;
        object-fit: contain;
    }
    .emoji-picker {
        position: absolute;
        z-index: 1000;
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        display: none;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Éléments du DOM
        const imageUploadArea = document.getElementById('imageUploadArea');
        const imageInput = document.getElementById('image');
        const previewImage = document.getElementById('previewImage');
        const imagePreview = document.getElementById('imagePreview');
        const uploadPlaceholder = document.getElementById('uploadPlaceholder');
        const removeImageBtn = document.getElementById('removeImageBtn');
        const contentTextarea = document.getElementById('content');
        const charCount = document.getElementById('charCount');
        const addEmojiBtn = document.getElementById('addEmojiBtn');
        const removeImageCheckbox = document.getElementById('remove_image');
        const maxLength = 2000;

        // Mettre à jour le compteur de caractères
        function updateCharCount() {
            const currentLength = contentTextarea.value.length;
            charCount.textContent = currentLength;
            
            if (currentLength > maxLength) {
                charCount.classList.add('text-danger');
                contentTextarea.classList.add('is-invalid');
            } else {
                charCount.classList.remove('text-danger');
                contentTextarea.classList.remove('is-invalid');
            }
        }

        // Redimensionner automatiquement la zone de texte
        function autoResize(textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = (textarea.scrollHeight) + 'px';
            updateCharCount();
        }

        // Gestion du glisser-déposer
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            imageUploadArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            imageUploadArea.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            imageUploadArea.addEventListener(eventName, unhighlight, false);
        });

        function highlight() {
            imageUploadArea.classList.add('dragover');
        }

        function unhighlight() {
            imageUploadArea.classList.remove('dragover');
        }

        // Gestion du dépôt de fichier
        imageUploadArea.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            handleFiles(files);
        }

        // Gestion de la sélection de fichier
        imageUploadArea.addEventListener('click', () => {
            imageInput.click();
        });

        imageInput.addEventListener('change', function() {
            handleFiles(this.files);
        });

        // Afficher l'aperçu de l'image
        function handleFiles(files) {
            if (files.length > 0) {
                const file = files[0];
                
                // Vérifier le type de fichier
                if (!file.type.match('image.*')) {
                    showAlert('Veuillez sélectionner un fichier image valide.', 'danger');
                    return;
                }
                
                // Vérifier la taille du fichier (5MB max)
                if (file.size > 5 * 1024 * 1024) {
                    showAlert('La taille de l\'image ne doit pas dépasser 5MB.', 'danger');
                    return;
                }
                
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewImage.style.display = 'block';
                    imagePreview.classList.remove('d-none');
                    uploadPlaceholder.style.display = 'none';
                    removeImageBtn.style.display = 'block';
                    
                    // Décocher la case de suppression de l'image actuelle si une nouvelle image est sélectionnée
                    if (removeImageCheckbox) {
                        removeImageCheckbox.checked = false;
                    }
                }
                
                reader.readAsDataURL(file);
            }
        }

        // Supprimer l'image
        removeImageBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            previewImage.src = '#';
            previewImage.style.display = 'none';
            imagePreview.classList.add('d-none');
            uploadPlaceholder.style.display = 'block';
            removeImageBtn.style.display = 'none';
            imageInput.value = '';
            
            // Cocher la case de suppression de l'image actuelle si on supprime la nouvelle image
            if (removeImageCheckbox) {
                removeImageCheckbox.checked = true;
            }
        });

        // Gestion des émoticônes
        addEmojiBtn.addEventListener('click', function(e) {
            e.preventDefault();
            // Implémentation basique du sélecteur d'émojis
            const emoji = prompt('Entrez un émoji:');
            if (emoji) {
                const start = contentTextarea.selectionStart;
                const end = contentTextarea.selectionEnd;
                const text = contentTextarea.value;
                const before = text.substring(0, start);
                const after = text.substring(end, text.length);
                contentTextarea.value = before + emoji + after;
                contentTextarea.focus();
                contentTextarea.selectionStart = start + emoji.length;
                contentTextarea.selectionEnd = start + emoji.length;
                autoResize(contentTextarea);
            }
        });

        // Initialisation
        updateCharCount();
        
        // Fonction utilitaire pour afficher des alertes
        function showAlert(message, type = 'info') {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show mt-3`;
            alertDiv.role = 'alert';
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            document.querySelector('form').prepend(alertDiv);
            
            // Supprimer l'alerte après 5 secondes
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }
        
        // Empêcher la soumission du formulaire si le nombre de caractères dépasse la limite
        document.getElementById('editPostForm').addEventListener('submit', function(e) {
            if (contentTextarea.value.length > maxLength) {
                e.preventDefault();
                showAlert('Le contenu ne peut pas dépasser 2000 caractères.', 'danger');
                contentTextarea.focus();
            }
        });
        
        // Gestion de la suppression de l'image actuelle
        const removeImageCheckbox = document.getElementById('remove_image');
        if (removeImageCheckbox) {
            const currentImagePreview = document.querySelector('.current-image-preview');
            
            removeImageCheckbox.addEventListener('change', function() {
                if (currentImagePreview) {
                    if (this.checked) {
                        currentImagePreview.classList.add('opacity-25');
                        // Si une nouvelle image est sélectionnée, décocher la case de suppression
                        if (previewImage.style.display === 'block') {
                            this.checked = false;
                            currentImagePreview.classList.remove('opacity-25');
                        }
                    } else {
                        currentImagePreview.classList.remove('opacity-25');
                    }
                }
            });
        }
    });
</script>
@endpush
@endsection
