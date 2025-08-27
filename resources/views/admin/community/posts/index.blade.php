@extends('layouts.admin')

@section('title', 'Gestion des publications')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Gestion des publications</h1>
        <div>
            <a href="{{ route('admin.community.posts.trashed') }}" class="btn btn-outline-danger">
                <i class="fas fa-trash-alt me-1"></i> Publications désactivées
                @if($trashedCount = \App\Models\CommunityPost::where('is_active', false)->count())
                    <span class="badge bg-danger ms-1">{{ $trashedCount }}</span>
                @endif
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Contenu</th>
                            <th>Auteur</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($posts as $post)
                            <tr class="{{ !$post->is_active ? 'table-warning' : '' }}">
                                <td>{{ $post->id }}</td>
                                <td>
                                    <div class="text-truncate" style="max-width: 300px;" title="{{ $post->content }}">
                                        {{ Str::limit($post->content, 100) }}
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $post->user->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($post->user->name) }}" 
                                             class="rounded-circle me-2" 
                                             width="30" 
                                             height="30" 
                                             alt="{{ $post->user->name }}">
                                        {{ $post->user->name }}
                                    </div>
                                </td>
                                <td>{{ $post->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($post->is_active)
                                        <span class="badge bg-success">Actif</span>
                                    @else
                                        <span class="badge bg-danger">Désactivé</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('community.posts.show', $post) }}" 
                                           class="btn btn-info" 
                                           title="Voir"
                                           target="_blank">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if($post->is_active)
                                            <button type="button" 
                                                    class="btn btn-warning" 
                                                    title="Désactiver"
                                                    onclick="confirmDeactivate('{{ $post->id }}')">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                            <form id="deactivate-form-{{ $post->id }}" 
                                                  action="{{ route('admin.community.posts.deactivate', $post) }}" 
                                                  method="POST" 
                                                  style="display: none;">
                                                @csrf
                                                @method('PATCH')
                                            </form>
                                        @else
                                            <button type="button" 
                                                    class="btn btn-success" 
                                                    title="Réactiver"
                                                    onclick="confirmActivate('{{ $post->id }}')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <form id="activate-form-{{ $post->id }}" 
                                                  action="{{ route('admin.community.posts.activate', $post) }}" 
                                                  method="POST" 
                                                  style="display: none;">
                                                @csrf
                                                @method('PATCH')
                                            </form>
                                        @endif
                                        
                                        <button type="button" 
                                                class="btn btn-danger" 
                                                title="Supprimer définitivement"
                                                onclick="confirmDelete('{{ $post->id }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <form id="delete-form-{{ $post->id }}" 
                                              action="{{ route('admin.community.posts.force-delete', $post) }}" 
                                              method="POST" 
                                              style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="text-muted">Aucune publication trouvée.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $posts->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmDeactivate(postId) {
    if (confirm('Êtes-vous sûr de vouloir désactiver cette publication ?')) {
        document.getElementById('deactivate-form-' + postId).submit();
    }
}

function confirmActivate(postId) {
    if (confirm('Êtes-vous sûr de vouloir réactiver cette publication ?')) {
        document.getElementById('activate-form-' + postId).submit();
    }
}

function confirmDelete(postId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer définitivement cette publication ? Cette action est irréversible.')) {
        document.getElementById('delete-form-' + postId).submit();
    }
}
</script>
@endpush
@endsection
