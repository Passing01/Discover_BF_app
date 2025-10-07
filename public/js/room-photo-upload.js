document.addEventListener('DOMContentLoaded', function() {
    'use strict';

    // Éléments du DOM
    const fileInput = document.getElementById('photos');
    const imagePreview = document.getElementById('image-preview');
    const fileSelectedCount = document.getElementById('file-selected-count');
    const imageErrors = document.getElementById('image-errors');
    const form = document.querySelector('form');
    const submitButton = form ? form.querySelector('button[type="submit"]') : null;
    
    // Vérification des éléments du DOM
    if (!fileInput || !imagePreview || !fileSelectedCount || !imageErrors || !submitButton) {
        console.error('Un ou plusieurs éléments du DOM sont manquants');
        return;
    }

    // Configuration
    const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB
    const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
    
    // Fonction pour afficher les erreurs
    function showError(message) {
        imageErrors.innerHTML = `<div class="alert alert-danger py-2"><i class="fas fa-exclamation-triangle me-2"></i>${message}</div>`;
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-ban me-2"></i>Corrigez les erreurs';
    }
    
    // Fonction pour effacer les erreurs
    function clearErrors() {
        imageErrors.innerHTML = '';
    }
    
    // Fonction pour mettre à jour le compteur de fichiers
    function updateFileCount(files) {
        const count = files.length;
        if (count === 0) {
            fileSelectedCount.textContent = 'Aucun fichier sélectionné';
            fileSelectedCount.className = 'small text-muted mt-1';
        } else if (count === 1) {
            fileSelectedCount.textContent = '1 fichier sélectionné';
            fileSelectedCount.className = 'small text-success fw-bold mt-1';
        } else {
            fileSelectedCount.textContent = `${count} fichiers sélectionnés`;
            fileSelectedCount.className = 'small text-success fw-bold mt-1';
        }
    }
    
    // Fonction pour valider un fichier
    function validateFile(file) {
        // Vérifier le type de fichier
        if (!ALLOWED_TYPES.includes(file.type)) {
            return {
                valid: false,
                message: `Le fichier "${file.name}" n'est pas un type d'image valide.`
            };
        }
        
        // Vérifier la taille du fichier
        if (file.size > MAX_FILE_SIZE) {
            return {
                valid: false,
                message: `Le fichier "${file.name}" dépasse la taille maximale de 5 Mo.`
            };
        }
        
        return { valid: true };
    }
    
    // Fonction pour supprimer un fichier
    function removeFile(index) {
        const files = Array.from(fileInput.files);
        files.splice(index, 1);
        
        // Mettre à jour l'input
        const dataTransfer = new DataTransfer();
        files.forEach(file => dataTransfer.items.add(file));
        fileInput.files = dataTransfer.files;
        
        // Mettre à jour l'interface
        const event = new Event('change');
        fileInput.dispatchEvent(event);
    }
    
    // Fonction pour mettre à jour l'aperçu des images
    function updateImagePreview(files) {
        imagePreview.innerHTML = '';
        
        files.forEach((file, index) => {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const col = document.createElement('div');
                col.className = 'col-6 col-md-4 col-lg-3 mb-3';
                
                const card = document.createElement('div');
                card.className = 'card h-100 border-0 shadow-sm position-relative';
                
                // Image
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'card-img-top';
                img.style.height = '150px';
                img.style.objectFit = 'cover';
                
                // Corps de la carte
                const cardBody = document.createElement('div');
                cardBody.className = 'card-body p-2';
                
                // Nom du fichier
                const fileName = document.createElement('div');
                fileName.className = 'small text-truncate';
                fileName.title = file.name;
                fileName.textContent = file.name;
                
                // Taille du fichier
                const fileSize = document.createElement('div');
                fileSize.className = 'small text-muted';
                fileSize.textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                
                // Bouton de suppression
                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'btn btn-danger btn-sm position-absolute top-0 end-0 m-1 rounded-circle p-1';
                removeBtn.style.width = '28px';
                removeBtn.style.height = '28px';
                removeBtn.innerHTML = '<i class="fas fa-times"></i>';
                removeBtn.setAttribute('aria-label', 'Supprimer cette image');
                removeBtn.addEventListener('click', () => removeFile(index));
                
                // Construction du DOM
                cardBody.appendChild(fileName);
                cardBody.appendChild(fileSize);
                
                card.appendChild(removeBtn);
                card.appendChild(img);
                card.appendChild(cardBody);
                col.appendChild(card);
                imagePreview.appendChild(col);
            };
            
            reader.readAsDataURL(file);
        });
    }
    
    // Gestion du changement de fichiers
    fileInput.addEventListener('change', function(e) {
        clearErrors();
        const files = Array.from(e.target.files);
        
        // Vérifier si des fichiers ont été sélectionnés
        if (files.length === 0) {
            updateFileCount([]);
            imagePreview.innerHTML = '';
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-ban me-2"></i>Sélectionnez au moins une photo';
            return;
        }
        
        // Valider chaque fichier
        const validationResults = files.map(file => ({
            file,
            validation: validateFile(file)
        }));
        
        // Filtrer les fichiers valides
        const validFiles = validationResults
            .filter(({ validation }) => validation.valid)
            .map(({ file }) => file);
        
        // Afficher les erreurs
        const invalidFiles = validationResults.filter(({ validation }) => !validation.valid);
        if (invalidFiles.length > 0) {
            const errorMessages = invalidFiles.map(
                ({ file, validation }) => validation.message
            );
            showError(errorMessages.join('<br>'));
        } else {
            clearErrors();
        }
        
        // Mettre à jour l'input avec uniquement les fichiers valides
        const dataTransfer = new DataTransfer();
        validFiles.forEach(file => dataTransfer.items.add(file));
        fileInput.files = dataTransfer.files;
        
        // Mettre à jour le compteur
        updateFileCount(validFiles);
        
        // Mettre à jour l'aperçu des images
        updateImagePreview(validFiles);
        
        // Activer/désactiver le bouton de soumission
        if (validFiles.length > 0) {
            submitButton.disabled = false;
            submitButton.innerHTML = '<i class="fas fa-save me-2"></i>Enregistrer la chambre';
        } else {
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-ban me-2"></i>Sélectionnez au moins une photo valide';
        }
    });

    // Gestion de la suppression d'image
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-danger') && e.target.closest('.btn-danger') !== fileInput) {
            e.preventDefault();
            const button = e.target.closest('.btn-danger');
            const card = button.closest('.col-6');
            const index = Array.from(card.parentNode.children).indexOf(card);
            
            // Supprimer le fichier
            removeFile(index);
        }
    });

    // Initialisation
    updateFileCount([]);
});
