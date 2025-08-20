@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h3 class="mb-0"><i class="bi bi-bell me-1"></i> Mes notifications</h3>
    <a href="{{ url()->previous() }}" class="btn btn-light btn-sm"><i class="bi bi-arrow-left"></i> Retour</a>
  </div>

  @if(session('status'))
    <div class="alert alert-success d-flex align-items-center gap-2"><i class="bi bi-check-circle"></i><span>{{ session('status') }}</span></div>
  @endif

  <div class="card">
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead>
          <tr>
            <th>Type</th>
            <th>Titre</th>
            <th>Message</th>
            <th>Envoyé</th>
            <th>Statut</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @forelse($notifications as $n)
            <tr>
              <td class="text-muted small">{{ $n->type ?? 'info' }}</td>
              <td class="fw-semibold">{{ $n->title }}</td>
              <td>{{ $n->message }}</td>
              <td class="text-muted small">{{ optional($n->sent_at)->format('d/m/Y H:i') ?? $n->created_at->format('d/m/Y H:i') }}</td>
              <td>
                @if($n->read)
                  <span class="badge text-bg-success">Lu</span>
                @else
                  <span class="badge text-bg-warning">Non lu</span>
                @endif
              </td>
              <td class="text-end">
                @if(!$n->read)
                <form method="POST" action="{{ route('user.notifications.read', $n) }}">
                  @csrf
                  <button class="btn btn-sm btn-outline-primary"><i class="bi bi-check2"></i> Marquer comme lu</button>
                </form>
                @endif
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-muted">Aucune notification.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="p-3">{!! $notifications->onEachSide(1)->links() !!}</div>
  </div>
</div>
@endsection
