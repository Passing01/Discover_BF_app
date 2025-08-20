@extends('layouts.admin')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <h4 class="section-title"><i class="bi bi-people"></i> Gestion des utilisateurs</h4>
  </div>

  @if(session('status'))
    <div class="alert alert-success d-flex align-items-center gap-2"><i class="bi bi-check-circle"></i> <span>{{ session('status') }}</span></div>
  @endif

  <div class="bg-secondary rounded p-4 mb-3">
    <form method="GET" action="{{ route('admin.users') }}" class="row g-2 align-items-end">
      <div class="col-md-4">
        <label class="form-label">Recherche</label>
        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Nom, email...">
      </div>
      <div class="col-md-3">
        <label class="form-label">Rôle</label>
        <select name="role" class="form-select">
          <option value="">Tous les rôles</option>
          @foreach(['tourist','guide','event_organizer','driver','hotel_manager','admin'] as $r)
            <option value="{{ $r }}" @selected(request('role')===$r)>{{ $r }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Statut</label>
        <select name="status" class="form-select">
          <option value="">Tous</option>
          <option value="active" @selected(request('status')==='active')>Actif</option>
          <option value="inactive" @selected(request('status')==='inactive')>Inactif</option>
        </select>
      </div>
      <div class="col-md-2 d-flex gap-2">
        <button class="btn btn-primary w-100"><i class="bi bi-funnel"></i> Filtrer</button>
        <a href="{{ route('admin.users') }}" class="btn btn-outline-light" title="Réinitialiser"><i class="bi bi-arrow-counterclockwise"></i></a>
      </div>
    </form>
  </div>

  <div class="bg-secondary rounded p-4">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th><i class="bi bi-person-badge"></i> Nom</th>
            <th><i class="bi bi-envelope"></i> Email</th>
            <th><i class="bi bi-person-gear"></i> Rôle</th>
            <th><i class="bi bi-activity"></i> Statut</th>
            <th><i class="bi bi-flag"></i> Onboarding</th>
            <th class="text-end"><i class="bi bi-gear"></i> Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($users as $u)
            <tr>
              <td>{{ $u->first_name }} {{ $u->last_name }}</td>
              <td class="text-secondary">{{ $u->email }}</td>
              <td>
                <form method="POST" action="{{ route('admin.users.role', $u->id) }}" class="d-flex align-items-center gap-2">
                  @csrf
                  <select name="role" class="form-select form-select-sm" style="width:auto">
                    @foreach(['tourist','guide','event_organizer','driver','hotel_manager','admin'] as $r)
                      <option value="{{ $r }}" @selected($u->role === $r)>{{ $r }}</option>
                    @endforeach
                  </select>
                  <button class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Mettre à jour le rôle" aria-label="Mettre à jour le rôle"><i class="bi bi-arrow-repeat"></i></button>
                </form>
              </td>
              <td>
                @if($u->is_active)
                  <span class="badge bg-success">Actif</span>
                @else
                  <span class="badge bg-secondary">Inactif</span>
                @endif
              </td>
              <td>
                @if($u->role_onboarded_at)
                  <span class="badge bg-success">Terminé</span>
                @else
                  <span class="badge bg-secondary">À faire</span>
                @endif
              </td>
              <td class="text-end">
                <form method="POST" action="{{ route('admin.users.reset_onboarding', $u->id) }}" class="d-inline" title="Réinitialiser l'onboarding" data-bs-toggle="tooltip">
                  @csrf
                  <button class="btn btn-sm btn-outline-light" aria-label="Réinitialiser l'onboarding"><i class="bi bi-arrow-counterclockwise"></i></button>
                </form>
                @if($u->is_active)
                  <form method="POST" action="{{ route('admin.users.deactivate', $u->id) }}" class="d-inline ms-1" title="Désactiver le compte" data-bs-toggle="tooltip">
                    @csrf
                    <button class="btn btn-sm btn-outline-light" aria-label="Désactiver le compte"><i class="bi bi-pause-circle"></i></button>
                  </form>
                @else
                  <form method="POST" action="{{ route('admin.users.activate', $u->id) }}" class="d-inline ms-1" title="Activer le compte" data-bs-toggle="tooltip">
                    @csrf
                    <button class="btn btn-sm btn-outline-light" aria-label="Activer le compte"><i class="bi bi-play-circle"></i></button>
                  </form>
                @endif

                <a href="{{ route('admin.users.edit', $u->id) }}" class="btn btn-sm btn-outline-light ms-1" data-bs-toggle="tooltip" title="Modifier l'utilisateur" aria-label="Modifier l'utilisateur"><i class="bi bi-pencil"></i></a>

                <form method="POST" action="{{ route('admin.users.destroy', $u->id) }}" class="d-inline ms-1" onsubmit="return confirm('Supprimer cet utilisateur ? Cette action est irréversible.');" data-bs-toggle="tooltip" title="Supprimer l'utilisateur">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger" aria-label="Supprimer l'utilisateur"><i class="bi bi-trash"></i></button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-secondary">Aucun utilisateur</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="mt-2">{{ $users->links() }}</div>
  </div>
@endsection
@push('scripts')
<script>
  (function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    tooltipTriggerList.forEach(function (tooltipTriggerEl) {
      new bootstrap.Tooltip(tooltipTriggerEl)
    })
  })();
</script>
@endpush
