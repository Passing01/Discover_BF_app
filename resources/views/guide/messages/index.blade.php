@extends('layouts.tourist')
@php use Illuminate\Support\Str; @endphp

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="mb-0">Mes messages</h1>
    <a href="{{ route('guide.dashboard') }}" class="btn btn-outline-secondary">Retour au tableau de bord</a>
  </div>

  @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <form method="GET" class="row g-2 align-items-end mb-3">
    <div class="col-auto">
      <label class="form-label small mb-1">Statut</label>
      <select name="status" class="form-select">
        <option value="">Tous</option>
        <option value="new" @selected(request('status')==='new')>Nouveau</option>
        <option value="contacted" @selected(request('status')==='contacted')>Contacté</option>
        <option value="closed" @selected(request('status')==='closed')>Fermé</option>
      </select>
    </div>
    <div class="col-auto">
      <button class="btn btn-primary">Filtrer</button>
    </div>
  </form>

  <div class="card">
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead>
          <tr>
            <th>Reçu</th>
            <th>Expéditeur</th>
            <th>Contact</th>
            <th>Message</th>
            <th>Statut</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($contacts as $c)
            <tr>
              <td class="text-nowrap">{{ $c->created_at?->format('d/m/Y H:i') }}</td>
              <td>{{ $c->name }}</td>
              <td>
                <div><a href="mailto:{{ $c->email }}">{{ $c->email }}</a></div>
                @if($c->phone)
                  <div><a href="tel:{{ $c->phone }}">{{ $c->phone }}</a></div>
                @endif
              </td>
              <td>{{ Str::limit($c->message, 120) }}</td>
              <td>
                @if($c->status==='new')
                  <span class="badge bg-warning text-dark">Nouveau</span>
                @elseif($c->status==='contacted')
                  <span class="badge bg-info text-dark">Contacté</span>
                @else
                  <span class="badge bg-secondary">Fermé</span>
                @endif
              </td>
              <td class="text-end">
                @if($c->status==='new')
                  <form method="POST" action="{{ route('guide.messages.read', $c) }}" class="d-inline">
                    @csrf
                    <button class="btn btn-sm btn-outline-primary">Marquer comme lu</button>
                  </form>
                @endif
                <a class="btn btn-sm btn-outline-secondary" href="mailto:{{ $c->email }}?subject=Re:%20Votre%20demande">Répondre</a>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-muted py-4">Aucun message</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-footer">
      {{ $contacts->links() }}
    </div>
  </div>
</div>
@endsection
