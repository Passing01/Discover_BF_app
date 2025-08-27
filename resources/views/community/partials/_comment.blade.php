<div class="d-flex mb-3 position-relative">
    <!-- Photo de profil de l'utilisateur -->
    <a href="#" class="text-decoration-none">
        <img src="{{ $comment->user->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($comment->user->name) }}" 
             alt="{{ $comment->user->name }}" 
             class="rounded-circle me-3 shadow-sm" 
             width="40" 
             height="40"
             onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name='+encodeURIComponent('{{ $comment->user->name }}')">
    </a>
    
    <!-- Contenu du commentaire -->
    <div class="flex-grow-1">
        <div class="position-relative">
            <div class="bg-white rounded-3 p-3 shadow-sm">
                <!-- En-tête du commentaire -->
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0 fw-bold text-primary">{{ $comment->user->name }}</h6>
                    <small class="text-muted">
                        <i class="far fa-clock me-1"></i> {{ $comment->created_at->diffForHumans() }}
                    </small>
                </div>
                
                <!-- Contenu du commentaire -->
                <p class="mb-0 text-dark" style="white-space: pre-line;">{{ $comment->content }}</p>
                
                <!-- Actions du commentaire -->
                @if($comment->user_id === auth()->id())
                    <div class="position-absolute" style="top: 0.5rem; right: 0.5rem;">
                        <div class="dropdown">
                            <button class="btn btn-link text-muted p-0" type="button" id="commentDropdown{{ $comment->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-h"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="commentDropdown{{ $comment->id }}" style="min-width: 10rem;">
                                <li>
                                    <form action="{{ route('community.comments.destroy', $comment) }}" method="POST" class="d-inline w-100">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="dropdown-item text-danger" 
                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?')">
                                            <i class="fas fa-trash-alt me-2"></i>Supprimer
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
            
            <!-- Pointe de la bulle de commentaire -->
            <div class="position-absolute" style="top: 15px; left: -8px; width: 0; height: 0; border-top: 8px solid transparent; border-right: 10px solid #fff; border-bottom: 8px solid transparent;"></div>
        </div>
    </div>
</div>
