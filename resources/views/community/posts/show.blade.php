@extends('layouts.tourist')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- En-tête de la page -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <a href="{{ route('community.posts.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
                        <i class="fas fa-arrow-left me-1"></i> Retour aux publications
                    </a>
                    <h1 class="h3 font-weight-bold text-primary mb-0">Publication</h1>
                </div>
            </div>

            <!-- Message de succès -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Publication -->
            <div class="mb-4">
                @include('community.partials._post', ['post' => $post, 'hideComments' => true])
            </div>
            
            <!-- Section commentaires -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 d-flex align-items-center">
                        <i class="far fa-comments text-primary me-2"></i>
                        <span>{{ $post->comments_count }} {{ Str::plural('commentaire', $post->comments_count) }}</span>
                    </h5>
                </div>
                
                <div class="card-body p-0">
                    <!-- Liste des commentaires -->
                    @if($post->comments->count() > 0)
                        <div class="p-4">
                            @foreach($post->comments as $comment)
                                @include('community.partials._comment', ['comment' => $comment])
                                @if(!$loop->last)
                                    <hr class="my-3">
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="far fa-comment-dots fa-3x text-muted opacity-25"></i>
                            </div>
                            <h5 class="text-muted mb-2">Aucun commentaire pour le moment</h5>
                            <p class="text-muted mb-0">Soyez le premier à partager votre avis !</p>
                        </div>
                    @endif
                    
                    <!-- Formulaire d'ajout de commentaire -->
                    <div class="p-4 border-top">
                        <form action="{{ route('community.comments.store', $post) }}" method="POST" class="mb-0">
                            @csrf
                            <div class="input-group">
                                <input type="text" 
                                       name="content" 
                                       class="form-control form-control-lg rounded-pill @error('content') is-invalid @enderror" 
                                       placeholder="Écrire un commentaire..." 
                                       required>
                                <button type="submit" class="btn btn-primary rounded-pill ms-2 px-4">
                                    <i class="fas fa-paper-plane me-1"></i> Envoyer
                                </button>
                            </div>
                            @error('content')
                                <div class="invalid-feedback d-block mt-2">
                                    <i class="fas fa-exclamation-circle me-1"></i> {{ $message }}
                                </div>
                            @enderror
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Gestion des likes en AJAX
    document.addEventListener('DOMContentLoaded', function() {
        const likeButton = document.querySelector('.like-button');
        
        if (likeButton) {
            likeButton.addEventListener('click', function(e) {
                e.preventDefault();
                const postId = this.dataset.postId;
                const likeCount = document.querySelector(`#like-count-${postId}`);
                const likeIcon = this.querySelector('i');
                
                fetch(`/community/posts/${postId}/like`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    likeCount.textContent = data.likes_count;
                    if (data.liked) {
                        likeIcon.classList.remove('far');
                        likeIcon.classList.add('fas', 'text-danger');
                    } else {
                        likeIcon.classList.remove('fas', 'text-danger');
                        likeIcon.classList.add('far');
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        }
    });
</script>
@endpush
@endsection
