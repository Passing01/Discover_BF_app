@extends('layouts.admin')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <h4 class="section-title"><i class="bi bi-calendar-event"></i> Gestion des événements</h4>
  </div>

  @if(session('status'))
    <div class="alert alert-success d-flex align-items-center gap-2"><i class="bi bi-check-circle"></i> <span>{{ session('status') }}</span></div>
  @endif

  <div class="bg-secondary rounded p-4 mb-3">
    <form method="GET" action="{{ route('admin.events') }}" class="row g-2 align-items-end">
      <div class="col-md-4">
        <label class="form-label text-light">Recherche</label>
        <input type="text" name="q" value="{{ request('q') }}" class="form-control bg-dark text-light border-0" placeholder="Nom, lieu...">
      </div>
      <div class="col-md-3">
        <label class="form-label text-light">Date</label>
        <input type="date" name="date" value="{{ request('date') }}" class="form-control bg-dark text-light border-0">
      </div>
      <div class="col-md-3">
        <label class="form-label text-light">Lieu</label>
        <input type="text" name="location" value="{{ request('location') }}" class="form-control bg-dark text-light border-0" placeholder="Ville, zone...">
      </div>
      <div class="col-md-2 d-flex gap-2">
        <button class="btn btn-primary w-100"><i class="bi bi-funnel"></i> Filtrer</button>
        <a href="{{ route('admin.events') }}" class="btn btn-outline-light" title="Réinitialiser"><i class="bi bi-arrow-counterclockwise"></i></a>
      </div>
    </form>
  </div>

  <div class="bg-secondary rounded p-4 mb-3">
    <div class="d-flex align-items-center justify-content-between mb-2">
      <h6 class="card-title text-light mb-0"><i class="bi bi-broadcast-pin"></i> Envoyer une alerte festival</h6>
    </div>
    <form method="POST" action="{{ route('admin.events.alerts') }}" class="row g-3">
      @csrf
      <div class="col-md-6">
        <label class="form-label text-light">Événement</label>
        <select name="event_id" class="form-select bg-dark text-white border-0" required>
          @foreach($events as $ev)
            <option value="{{ $ev->id }}">{{ $ev->name ?? 'Événement' }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label text-light">Message</label>
        <input type="text" name="message" class="form-control bg-dark text-light border-0" placeholder="Ex: Défilé masqué à 17h" required>
      </div>
      <div class="col-12 d-flex justify-content-end">
        <button class="btn btn-primary"><i class="bi bi-send"></i> Envoyer</button>
      </div>
    </form>
  </div>

  <div class="bg-secondary rounded p-4">
    <div class="d-flex align-items-center justify-content-between mb-2">
      <h6 class="card-title text-light mb-0"><i class="bi bi-collection"></i> Événements récents</h6>
    </div>
    <div class="table-responsive">
      <table class="table table-hover mb-0 align-middle text-light">
        <thead>
          <tr>
            <th><i class="bi bi-card-text"></i> Nom</th>
            <th><i class="bi bi-clock-history"></i> Période</th>
            <th><i class="bi bi-geo"></i> Lieu</th>
          </tr>
        </thead>
        <tbody>
          @forelse($events as $ev)
            <tr>
              <td>{{ $ev->name ?? '—' }}</td>
              <td class="text-light">{{ $ev->start_date ?? '—' }} → {{ $ev->end_date ?? '—' }}</td>
              <td class="text-light">{{ $ev->location ?? '—' }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="3" class="text-secondary">
                <div class="py-3 d-flex flex-column align-items-center">
                  <i class="bi bi-calendar-x fs-3 mb-2 text-secondary"></i>
                  <div>Aucun événement</div>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
      <div class="mt-2">{{ $events->links() }}</div>
    </div>
  </div>
@endsection
