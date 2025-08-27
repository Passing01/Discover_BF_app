@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <div class="panel-cream rounded-20 p-3 shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <div class="fw-bold">Communauté</div>
      <a href="{{ route('community.posts.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg"></i> Nouvelle publication
      </a>
    </div>
    <div class="panel-inner rounded-20 p-3 bg-white">
      @if($posts->count() > 0)
        @foreach($posts as $post)
          @include('community.partials._post', ['post' => $post])
        @endforeach
        
        <div class="d-flex justify-content-center mt-4">
          {{ $posts->links() }}
        </div>
      @else
        <div class="text-center py-5">
          <i class="bi bi-people fs-1 text-muted mb-3"></i>
          <h5>Aucune publication pour le moment</h5>
          <p class="text-muted mb-4">Soyez le premier à partager quelque chose avec la communauté !</p>
          <a href="{{ route('community.posts.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Créer une publication
          </a>
        </div>
      @endif
    </div>
  </div>
</div>

@push('scripts')
<script>
  // Gestion des likes en AJAX
  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.like-button').forEach(button => {
      button.addEventListener('click', function(e) {
        e.preventDefault();
        const postId = this.dataset.postId;
        const likeCount = document.querySelector(`#like-count-${postId}`);
        
        fetch(`/community/posts/${postId}/like`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          }
        })
        .then(response => response.json())
        .then(data => {
          likeCount.textContent = data.likesCount;
          const icon = button.querySelector('i');
          if (data.liked) {
            icon.classList.remove('bi-heart');
            icon.classList.add('bi-heart-fill', 'text-danger');
          } else {
            icon.classList.remove('bi-heart-fill', 'text-danger');
            icon.classList.add('bi-heart');
          }
        });
      });
    });
  });
</script>
@endpush
@endsection
