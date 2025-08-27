@extends('layouts.tourist')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- En-tête de la communauté -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5">
                <div class="mb-3 mb-md-0">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-1">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">Accueil</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Communauté</li>
                        </ol>
                    </nav>
                    <h1 class="h3 mb-1 text-primary">
                        <i class="fas fa-users me-2"></i>Communauté des voyageurs
                    </h1>
                    <p class="text-muted mb-0">Partagez vos expériences, posez des questions et échangez avec d'autres voyageurs</p>
                </div>
                <a href="{{ route('community.posts.create') }}" class="btn btn-primary px-4">
                    <i class="fas fa-plus-circle me-2"></i> Nouvelle publication
                </a>
            </div>

            <!-- Messages de succès -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle me-2"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Filtres et tri -->
            <div class="card shadow-sm mb-4">
                <div class="card-body p-3">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                        <div class="mb-2 mb-md-0">
                            <span class="me-2 text-muted">Trier par :</span>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'latest']) }}" 
                                   class="btn {{ request('sort', 'latest') === 'latest' ? 'btn-primary' : 'btn-outline-secondary' }}">
                                    <i class="fas fa-clock me-1"></i> Récents
                                </a>
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'popular']) }}" 
                                   class="btn {{ request('sort') === 'popular' ? 'btn-primary' : 'btn-outline-secondary' }}">
                                    <i class="fas fa-fire me-1"></i> Populaires
                                </a>
                            </div>
                        </div>
                        <div>
                            <span class="text-muted me-2">{{ $posts->total() }} publication(s)</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Liste des publications -->
            @if($posts->count() > 0)
                <div class="mb-4">
                    @foreach($posts as $post)
                        <div class="mb-4">
                            @include('community.partials._post', ['post' => $post])
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-5">
                    <nav aria-label="Pagination" class="shadow-sm">
                        {{ $posts->onEachSide(1)->links('pagination::bootstrap-5') }}
                    </nav>
                </div>
            @else
                <!-- Aucune publication -->
                <div class="text-center py-5 my-5">
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 120px; height: 120px;">
                        <i class="fas fa-comments fa-3x text-primary opacity-25"></i>
                    </div>
                    <h3 class="h4 text-muted mb-3">Aucune publication pour le moment</h3>
                    <p class="text-muted mb-4">Soyez le premier à partager votre expérience avec la communauté !</p>
                    <a href="{{ route('community.posts.create') }}" class="btn btn-primary px-4">
                        <i class="fas fa-plus-circle me-2"></i> Créer une publication
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gestion des likes en AJAX avec feedback visuel
        document.querySelectorAll('.like-button').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Désactiver temporairement le bouton pour éviter les clics multiples
                this.disabled = true;
                const originalHtml = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>';
                
                const postId = this.dataset.postId;
                const likeCount = document.querySelector(`#like-count-${postId}`);
                const likeIcon = this.querySelector('i');
                const isLiked = likeIcon.classList.contains('text-danger');
                
                // Animation de feedback visuel
                if (isLiked) {
                    likeIcon.classList.remove('text-danger', 'fas');
                    likeIcon.classList.add('far');
                    likeCount.textContent = parseInt(likeCount.textContent) - 1;
                } else {
                    likeIcon.classList.remove('far');
                    likeIcon.classList.add('fas', 'text-danger');
                    likeCount.textContent = parseInt(likeCount.textContent) + 1;
                }
                
                // Envoi de la requête AJAX
                fetch(`/community/posts/${postId}/like`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur réseau');
                    }
                    return response.json();
                })
                .then(data => {
                    // Mise à jour du compteur avec la valeur du serveur
                    likeCount.textContent = data.likes_count;
                    
                    // Mise à jour de l'icône en fonction de la réponse
                    if (data.liked) {
                        likeIcon.classList.remove('far');
                        likeIcon.classList.add('fas', 'text-danger');
                    } else {
                        likeIcon.classList.remove('fas', 'text-danger');
                        likeIcon.classList.add('far');
                    }
                    
                    // Afficher une notification si nécessaire
                    if (data.message) {
                        showToast(data.message, 'success');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    // Annuler les changements visuels en cas d'erreur
                    if (isLiked) {
                        likeIcon.classList.add('text-danger', 'fas');
                        likeIcon.classList.remove('far');
                        likeCount.textContent = parseInt(likeCount.textContent) + 1;
                    } else {
                        likeIcon.classList.remove('text-danger', 'fas');
                        likeIcon.classList.add('far');
                        likeCount.textContent = Math.max(0, parseInt(likeCount.textContent) - 1);
                    }
                    showToast('Une erreur est survenue. Veuillez réessayer.', 'danger');
                })
                .finally(() => {
                    // Réactiver le bouton
                    this.disabled = false;
                    this.innerHTML = originalHtml;
                });
            });
        });
        
        // Fonction utilitaire pour afficher des toasts
        function showToast(message, type = 'info') {
            const toastContainer = document.getElementById('toast-container');
            if (!toastContainer) return;
            
            const toastId = 'toast-' + Date.now();
            const toast = document.createElement('div');
            toast.id = toastId;
            toast.className = `toast align-items-center text-white bg-${type} border-0 show`;
            toast.role = 'alert';
            toast.setAttribute('aria-live', 'assertive');
            toast.setAttribute('aria-atomic', 'true');
            
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="${type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle'} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            `;
            
            toastContainer.appendChild(toast);
            
            // Supprimer le toast après 5 secondes
            setTimeout(() => {
                const bsToast = new bootstrap.Toast(toast);
                toast.addEventListener('hidden.bs.toast', () => {
                    toast.remove();
                });
                bsToast.hide();
            }, 5000);
        }
    });
</script>
@endpush

<!-- Conteneur pour les toasts -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div id="toast-container" class="toast-container">
        <!-- Les toasts seront ajoutés ici dynamiquement -->
    </div>
</div>
@endsection
