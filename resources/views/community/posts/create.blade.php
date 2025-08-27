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
                    <h1 class="h3 font-weight-bold text-primary mb-0">Créer une publication</h1>
                </div>
            </div>
            
            <!-- Carte du formulaire -->
            <div class="card shadow-sm border-0 overflow-hidden">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 text-primary">
                        <i class="fas fa-edit me-2"></i>Nouvelle publication
                    </h5>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('community.posts.store') }}" method="POST" enctype="multipart/form-data" id="createPostForm">
                        @csrf
                        
                        <!-- Champ de contenu -->
                        <div class="mb-4">
                            <label for="content" class="form-label fw-medium text-muted mb-2">Partagez vos pensées...</label>
                            <textarea class="form-control form-control-lg @error('content') is-invalid @enderror" 
                                      id="content" 
                                      name="content" 
                                      rows="6" 
                                      placeholder="Qu'avez-vous en tête ?"
                                      style="resize: none; border: 2px solid #e9ecef; border-radius: 12px;"
                                      oninput="autoResize(this)">{{ old('content') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback d-flex align-items-center">
                                    <i class="fas fa-exclamation-circle me-2"></i> {{ $message }}
                                </div>
                            @enderror
                            <div class="form-text text-end text-muted">
                                <span id="charCount">0</span>/2000 caractères
                            </div>
                        </div>
                        
                        <!-- Champ d'image avec aperçu -->
                        <div class="mb-4">
                            <div class="border-2 border-dashed rounded-3 p-4 text-center position-relative" 
                                 style="border-color: #e9ecef;"
                                 id="imageUploadArea">
                                <div id="imagePreview" class="d-none">
                                    <img id="previewImage" src="#" alt="Aperçu de l'image" class="img-fluid rounded mb-3" style="max-height: 300px; display: none;">
                                    <button type="button" class="btn btn-sm btn-outline-danger position-absolute top-0 end-0 m-2" id="removeImageBtn" style="display: none;">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                
                                <div id="uploadPlaceholder">
                                    <i class="fas fa-image fa-3x text-muted mb-3"></i>
                                    <h5 class="h6 text-muted mb-2">Ajouter une photo</h5>
                                    <p class="small text-muted mb-2">Glissez-déposez une image ou cliquez pour sélectionner</p>
                                    <span class="badge bg-light text-muted">JPG, PNG, GIF (max. 5MB)</span>
                                </div>
                                
                                <input type="file" 
                                       class="d-none" 
                                       id="image" 
                                       name="image"
                                       accept="image/*">
                            </div>
                            @error('image')
                                <div class="invalid-feedback d-flex align-items-center mt-2">
                                    <i class="fas fa-exclamation-circle me-2"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>
                        
                        <!-- Boutons d'action -->
                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <button type="button" class="btn btn-link text-muted" id="addEmojiBtn">
                                <i class="far fa-smile fa-lg"></i>
                            </button>
                            
                            <div>
                                <button type="reset" class="btn btn-outline-secondary me-2">
                                    <i class="fas fa-undo me-1"></i> Effacer
                                </button>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-paper-plane me-1"></i> Publier
                                </button>
                            </div>
                            </button>
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
        });

        // Gestion des émoticônes
        addEmojiBtn.addEventListener('click', function(e) {
            e.preventDefault();
            // Implémentation basique du sélecteur d'émojis
            // Pour une solution complète, envisagez d'utiliser une bibliothèque comme emoji-picker-element
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
        document.getElementById('createPostForm').addEventListener('submit', function(e) {
            if (contentTextarea.value.length > maxLength) {
                e.preventDefault();
                showAlert('Le contenu ne peut pas dépasser 2000 caractères.', 'danger');
                contentTextarea.focus();
            }
        });
    });
</script>
@endpush
@endsection
