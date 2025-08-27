<div class="card border-0 shadow-sm mb-4 overflow-hidden bg-white rounded-lg {{ !$post->is_active ? 'border border-warning' : '' }}">
    <!-- En-tête de la publication -->
    <div class="p-3">
        <div class="d-flex justify-content-between align-items-start">
            <div class="d-flex align-items-center">
                <a href="#" class="text-decoration-none">
                    <img src="{{ $post->user->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($post->user->name) }}" 
                         alt="{{ $post->user->name }}" 
                         class="rounded-circle me-2" 
                         width="48" 
                         height="48"
                         onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name='+encodeURIComponent('{{ $post->user->name }}')">
                </a>
                <div>
                    <div class="d-flex align-items-center">
                        <a href="#" class="text-decoration-none text-dark fw-bold">{{ $post->user->name }}</a>
                        <span class="text-muted mx-1">•</span>
                        <span class="text-muted small">{{ $post->created_at->diffForHumans() }}</span>
                    </div>
                    <div class="text-muted small">
                        <i class="fas fa-globe-americas me-1"></i> Public
                    </div>
                </div>
            </div>
            
            <div class="d-flex align-items-center">
                @if(!$post->is_active)
                    <span class="badge bg-danger me-2" data-bs-toggle="tooltip" title="Cette publication a été désactivée par un modérateur">
                        <i class="fas fa-ban me-1"></i> Désactivée
                    </span>
                @endif
                
                @if($post->user_id === auth()->id() || auth()->user()?->hasRole('admin'))
                    <div class="dropdown">
                        <button class="btn btn-link text-muted p-0" type="button" id="postDropdown{{ $post->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-h"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="postDropdown{{ $post->id }}" style="min-width: 200px;">
                            @if($post->user_id === auth()->id())
                                <li>
                                    <a class="dropdown-item" href="{{ route('community.posts.edit', $post) }}">
                                        <i class="fas fa-edit text-primary me-2"></i>Modifier la publication
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('community.posts.destroy', $post) }}" method="POST" class="d-inline w-100">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger w-100 text-start" 
                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette publication ?')">
                                            <i class="fas fa-trash-alt me-2"></i>Supprimer
                                        </button>
                                    </form>
                                </li>
                            @endif
                            
                            @if(auth()->user()?->hasRole('admin'))
                                @if($post->is_active)
                                    <li>
                                        <form action="{{ route('admin.community.posts.deactivate', $post) }}" method="POST" class="d-inline w-100">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="dropdown-item text-warning w-100 text-start" 
                                                    onclick="return confirm('Êtes-vous sûr de vouloir désactiver cette publication ?')">
                                                <i class="fas fa-ban me-2"></i>Désactiver
                                            </button>
                                        </form>
                                    </li>
                                @else
                                    <li>
                                        <form action="{{ route('admin.community.posts.activate', $post) }}" method="POST" class="d-inline w-100">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="dropdown-item text-success w-100 text-start">
                                                <i class="fas fa-check me-2"></i>Réactiver
                                            </button>
                                        </form>
                                    </li>
                                @endif
                                <li>
                                    <form action="{{ route('admin.community.posts.force-delete', $post) }}" method="POST" class="d-inline w-100"
                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer définitivement cette publication ? Cette action est irréversible.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger w-100 text-start">
                                            <i class="fas fa-trash me-2"></i>Supprimer définitivement
                                        </button>
                                    </form>
                                </li>
                            @endif
                        </ul>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Contenu de la publication -->
        <div class="mt-3">
            <p class="mb-2" style="white-space: pre-line; font-size: 1.05rem; line-height: 1.5;">{{ $post->content }}</p>
            
            @if($post->image)
                <div class="mt-3 rounded overflow-hidden border" style="max-height: 512px;">
                    <img src="{{ Storage::url($post->image) }}" 
                         alt="Image de la publication" 
                         class="img-fluid w-100 h-100" 
                         style="object-fit: cover;"
                         onerror="this.style.display='none'"
                         data-action="zoom">
                </div>
            @endif
            
            <!-- Stats -->
            <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top text-muted">
                <div class="d-flex align-items-center">
                    <div class="reactions-count me-2" style="font-size: 0.85rem;">
                        <span class="reaction-icons" style="font-size: 1.1em;">
                            <span class="text-danger">❤️</span>
                            <span class="text-warning">👍</span>
                            <span class="text-primary">👏</span>
                        </span>
                        <span id="like-count-{{ $post->id }}" class="ms-1">{{ $post->likes_count }}</span>
                    </div>
                    <span class="mx-1">•</span>
                    <div>
                        <a href="{{ route('community.posts.show', $post) }}" class="text-muted text-decoration-none">
                            {{ $post->comments_count }} {{ Str::plural('commentaire', $post->comments_count) }}
                        </a>
                    </div>
                </div>
                <div>
                    <span class="small">
                        {{ rand(1, 20) }} partages
                    </span>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                <button class="btn btn-link text-muted text-decoration-none like-button reaction-button" 
                        data-post-id="{{ $post->id }}"
                        data-bs-toggle="tooltip" 
                        data-bs-placement="top" 
                        title="J'aime">
                    <div class="d-flex flex-column align-items-center">
                        <i class="far fa-thumbs-up {{ $post->isLikedBy(auth()->id()) ? 'fas text-primary' : 'far' }} mb-1" style="font-size: 1.2rem;"></i>
                        <span class="small">J'aime</span>
                    </div>
                </button>
                
                <a href="{{ route('community.posts.show', $post) }}" 
                   class="btn btn-link text-muted text-decoration-none"
                   data-bs-toggle="tooltip" 
                   data-bs-placement="top" 
                   title="Commenter">
                    <div class="d-flex flex-column align-items-center">
                        <i class="far fa-comment mb-1" style="font-size: 1.2rem;"></i>
                        <span class="small">Commenter</span>
                    </div>
                </a>
                
                <button class="btn btn-link text-muted text-decoration-none"
                        data-bs-toggle="tooltip" 
                        data-bs-placement="top" 
                        title="Partager">
                    <div class="d-flex flex-column align-items-center">
                        <i class="fas fa-share mb-1" style="font-size: 1.2rem;"></i>
                        <span class="small">Partager</span>
                    </div>
                </button>
                
                <button class="btn btn-link text-muted text-decoration-none"
                        data-bs-toggle="tooltip" 
                        data-bs-placement="top" 
                        title="Envoyer">
                    <div class="d-flex flex-column align-items-center">
                        <i class="fas fa-paper-plane mb-1" style="font-size: 1.2rem;"></i>
                        <span class="small">Envoyer</span>
                    </div>
                </button>
            </div>
    
    <!-- Section commentaires -->
    @if(!isset($hideComments) || !$hideComments)
        <div class="card-footer bg-light py-3 px-4">
            <form action="{{ route('community.comments.store', $post) }}" method="POST" class="mb-3">
                @csrf
                <div class="input-group">
                    <input type="text" 
                           name="content" 
                           class="form-control form-control-sm rounded-pill" 
                           placeholder="Écrire un commentaire..." 
                           required>
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill ms-2 px-3">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </form>
            
            @if($post->comments_count > 0)
                <div class="mt-2">
                    @foreach($post->comments->take(2) as $comment)
                        @include('community.partials._comment', ['comment' => $comment])
                    @endforeach
                    
                    @if($post->comments_count > 2)
                        <div class="text-center mt-2">
                            <a href="{{ route('community.posts.show', $post) }}" class="text-primary text-decoration-none small">
                                <i class="far fa-comment-dots me-1"></i> Voir les {{ $post->comments_count - 2 }} autres commentaires
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser les tooltips Bootstrap
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Gestion des réactions
    document.querySelectorAll('.reaction-button').forEach(button => {
        // Menu déroulant des réactions
        const reactionMenu = document.createElement('div');
        reactionMenu.className = 'reaction-menu p-2 bg-white rounded-pill shadow position-absolute d-none';
        reactionMenu.style.bottom = '100%';
        reactionMenu.style.zIndex = '1060';
        reactionMenu.style.transform = 'translateX(-50%)';
        reactionMenu.style.left = '50%';
        reactionMenu.style.marginBottom = '10px';
        reactionMenu.style.padding = '8px 12px';
        reactionMenu.innerHTML = `
            <div class="d-flex">
                <button type="button" class="btn btn-link p-1 reaction-option" data-reaction="like" title="J'aime">
                    <span class="reaction-emoji" style="font-size: 1.8rem;">👍</span>
                </button>
                <button type="button" class="btn btn-link p-1 reaction-option" data-reaction="love" title="J'adore">
                    <span class="reaction-emoji" style="font-size: 1.8rem;">❤️</span>
                </button>
                <button type="button" class="btn btn-link p-1 reaction-option" data-reaction="haha" title="Haha">
                    <span class="reaction-emoji" style="font-size: 1.8rem;">😄</span>
                </button>
                <button type="button" class="btn btn-link p-1 reaction-option" data-reaction="wow" title="Wow">
                    <span class="reaction-emoji" style="font-size: 1.8rem;">😲</span>
                </button>
                <button type="button" class="btn btn-link p-1 reaction-option" data-reaction="sad" title="Triste">
                    <span class="reaction-emoji" style="font-size: 1.8rem;">😢</span>
                </button>
                <button type="button" class="btn btn-link p-1 reaction-option" data-reaction="angry" title="Grrr">
                    <span class="reaction-emoji" style="font-size: 1.8rem;">😠</span>
                </button>
            </div>
        `;
        
        // Ajouter le menu des réactions au DOM
        button.parentNode.insertBefore(reactionMenu, button);
        
        // Afficher/cacher le menu des réactions
        let showTimeout, hideTimeout;
        
        button.addEventListener('mouseenter', () => {
            clearTimeout(hideTimeout);
            showTimeout = setTimeout(() => {
                reactionMenu.classList.remove('d-none');
            }, 300);
        });
        
        button.addEventListener('mouseleave', () => {
            clearTimeout(showTimeout);
            hideTimeout = setTimeout(() => {
                if (!reactionMenu.matches(':hover')) {
                    reactionMenu.classList.add('d-none');
                }
            }, 300);
        });
        
        reactionMenu.addEventListener('mouseenter', () => {
            clearTimeout(hideTimeout);
        });
        
        reactionMenu.addEventListener('mouseleave', () => {
            reactionMenu.classList.add('d-none');
        });
        
        // Gestion du clic sur une réaction
        reactionMenu.querySelectorAll('.reaction-option').forEach(option => {
            option.addEventListener('click', (e) => {
                e.preventDefault();
                const reaction = option.dataset.reaction;
                const postId = button.closest('.reaction-button').dataset.postId;
                
                // Mise à jour visuelle immédiate
                const icon = button.querySelector('i');
                const reactionEmoji = option.querySelector('.reaction-emoji').textContent;
                
                // Changer l'icône et ajouter l'effet de réaction
                button.innerHTML = `
                    <div class="d-flex flex-column align-items-center">
                        <span class="reaction-display" style="font-size: 1.2rem;">${reactionEmoji}</span>
                        <span class="small">${getReactionText(reaction)}</span>
                    </div>
                `;
                
                // Cacher le menu des réactions
                reactionMenu.classList.add('d-none');
                
                // Envoyer la réaction au serveur
                sendReaction(postId, reaction, button);
            });
        });
    });
    
    // Fonction pour envoyer la réaction au serveur
    function sendReaction(postId, reaction, button) {
        const likeCount = document.querySelector(`#like-count-${postId}`);
        const currentCount = parseInt(likeCount.textContent) || 0;
        
        // Animation de feedback visuel
        button.style.transform = 'scale(1.1)';
        setTimeout(() => {
            button.style.transform = 'scale(1)';
        }, 200);
        
        // Envoyer la requête AJAX
        fetch(`/community/posts/${postId}/react`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ reaction: reaction })
        })
        .then(response => response.json())
        .then(data => {
            // Mise à jour du compteur
            if (data.likes_count !== undefined) {
                likeCount.textContent = data.likes_count;
            }
            
            // Afficher une notification si nécessaire
            if (data.message) {
                showToast(data.message, 'success');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showToast('Une erreur est survenue. Veuillez réessayer.', 'danger');
        });
    }
    
    // Fonction utilitaire pour obtenir le texte de la réaction
    function getReactionText(reaction) {
        const reactions = {
            'like': 'J\'aime',
            'love': 'J\'adore',
            'haha': 'Haha',
            'wow': 'Wow',
            'sad': 'Triste',
            'angry': 'Grrr'
        };
        return reactions[reaction] || 'J\'aime';
    }
    
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

<style>
/* Styles pour le menu des réactions */
.reaction-menu {
    transition: all 0.2s ease;
    opacity: 0;
    transform: translate(-50%, 10px) scale(0.9);
    pointer-events: none;
    box-shadow: 0 2px 15px rgba(0, 0, 0, 0.2);
}

.reaction-menu.show {
    opacity: 1;
    transform: translate(-50%, 0) scale(1);
    pointer-events: auto;
}

.reaction-option {
    transition: all 0.2s ease;
    transform: scale(1);
    display: inline-block;
    margin: 0 2px;
}

.reaction-option:hover {
    transform: scale(1.3) translateY(-5px);
    z-index: 10;
}

.reaction-emoji {
    transition: all 0.2s ease;
    display: inline-block;
}

.reaction-option:hover .reaction-emoji {
    transform: scale(1.2);
}

/* Animation de réaction */
@keyframes pop {
    0% { transform: scale(1); }
    50% { transform: scale(1.3); }
    100% { transform: scale(1); }
}

.reaction-display {
    animation: pop 0.3s ease;
    display: inline-block;
}

/* Style pour les boutons d'action */
.btn-link {
    position: relative;
    color: #666 !important;
    transition: all 0.2s ease;
    border-radius: 4px;
    padding: 8px 12px;
}

.btn-link:hover {
    background-color: rgba(0, 0, 0, 0.05);
    color: #0a66c2 !important;
    text-decoration: none;
}

/* Style pour le compteur de réactions */
.reactions-count {
    cursor: pointer;
    padding: 2px 8px;
    border-radius: 10px;
    background-color: #f0f2f5;
    transition: background-color 0.2s ease;
}

.reactions-count:hover {
    background-color: #e4e6e9;
}

.reaction-icons {
    display: inline-flex;
    align-items: center;
}

.reaction-icons > span {
    margin-right: -6px;
    transition: all 0.2s ease;
}

.reaction-icons > span:hover {
    transform: scale(1.2);
    z-index: 1;
}
</style>
@endpush
