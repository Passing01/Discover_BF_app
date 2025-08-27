@extends('layouts.admin')

@section('title', 'Publications désactivées')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3">Publications désactivées</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Tableau de bord</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.community.posts.index') }}">Publications</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Désactivées</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.community.posts.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-1"></i> Retour aux publications
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if($posts->isEmpty())
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-trash-alt fa-4x text-muted"></i>
                    </div>
                    <h5 class="text-muted">Aucune publication désactivée</h5>
                    <p class="text-muted">Les publications désactivées apparaîtront ici.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Contenu</th>
                                <th>Auteur</th>
                                <th>Désactivé le</th>
                                <th>Par</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($posts as $post)
                                <tr class="table-warning">
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
                                    <td>{{ $post->updated_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if($post->deletedBy)
                                            {{ $post->deletedBy->name }}
                                        @else
                                            <span class="text-muted">Système</span>
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
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $posts->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
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
