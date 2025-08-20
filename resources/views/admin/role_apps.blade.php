@extends('layouts.admin')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <h4 class="section-title"><i class="bi bi-person-check"></i> Demandes de rôles</h4>
  </div>

  @if(session('status'))
    <div class="alert alert-success d-flex align-items-center gap-2"><i class="bi bi-check-circle"></i> <span>{{ session('status') }}</span></div>
  @endif

  <div class="bg-secondary rounded p-4">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>#</th>
            <th><i class="bi bi-person"></i> Utilisateur</th>
            <th><i class="bi bi-award"></i> Rôle demandé</th>
            <th><i class="bi bi-card-text"></i> Note</th>
            <th><i class="bi bi-activity"></i> Statut</th>
            <th class="text-end"><i class="bi bi-gear"></i> Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($apps as $app)
            <tr>
              <td>{{ $app->id }}</td>
              <td>
                {{ $app->user->first_name ?? '' }} {{ $app->user->last_name ?? '' }}
                <div class="small text-secondary">{{ $app->user->email }}</div>
              </td>
              <td><span class="badge bg-secondary">{{ $app->requested_role }}</span></td>
              <td class="text-secondary">{{ $app->data['note'] ?? '' }}</td>
              <td>
                @if($app->status === 'pending')
                  <span class="badge bg-secondary">En attente</span>
                @elseif($app->status === 'approved')
                  <span class="badge bg-success">Approuvée</span>
                @else
                  <span class="badge bg-danger">Rejetée</span>
                @endif
              </td>
              <td class="text-end">
                @if($app->status === 'pending')
                  <form method="POST" action="{{ route('admin.role_apps.approve', $app->id) }}" class="d-inline">
                    @csrf
                    <button class="btn btn-sm btn-outline-light"><i class="bi bi-check2-circle"></i> Approuver</button>
                  </form>
                  <form method="POST" action="{{ route('admin.role_apps.reject', $app->id) }}" class="d-inline ms-1">
                    @csrf
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-x-circle"></i> Rejeter</button>
                  </form>
                @else
                  <span class="text-secondary">—</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-secondary">Aucune demande</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="mt-2">{{ $apps->links() }}</div>
  </div>
@endsection
