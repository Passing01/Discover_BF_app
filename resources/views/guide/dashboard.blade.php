@extends('layouts.tourist')
@php use Illuminate\Support\Str; @endphp

@section('content')
<div class="container py-4">
  <h1 class="mb-3">Tableau de bord Guide</h1>

  @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <!-- Notifications + Ads -->
  <div class="row g-3 mb-3">
    <div class="col-md-8">
      @include('components.notifications-widget')
    </div>
    <div class="col-md-4">
      <x-ad-banner placement="dashboard_sidebar" />
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header">Disponibilité</div>
    <div class="card-body">
      <form method="POST" action="{{ route('guide.availability.update') }}" class="row g-3">
        @csrf
        <div class="col-md-4">
          <label class="form-label">Disponible du</label>
          <input type="date" name="available_from" class="form-control @error('available_from') is-invalid @enderror" required>
          @error('available_from')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
          <label class="form-label">au</label>
          <input type="date" name="available_to" class="form-control @error('available_to') is-invalid @enderror" required>
          @error('available_to')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
          <label class="form-label">Note</label>
          <input type="text" name="note" class="form-control" placeholder="Ex: spécialité danse traditionnelle">
        </div>
        <div class="col-12">
          <button class="btn btn-primary">Enregistrer</button>
        </div>
      </form>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-md-6">
      <div class="card h-100">
        <div class="card-header">Dernières réservations (Tours)</div>
        <ul class="list-group list-group-flush">
          @forelse($tourBookings as $b)
            <li class="list-group-item">#{{ $b->id }} — {{ $b->date ?? '' }} — {{ $b->status ?? '' }}</li>
          @empty
            <li class="list-group-item text-muted">Aucune réservation</li>
          @endforelse
        </ul>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card h-100">
        <div class="card-header">Dernières réservations (Événements)</div>
        <ul class="list-group list-group-flush">
          @forelse($eventBookings as $b)
            <li class="list-group-item">#{{ $b->id }} — {{ $b->date ?? '' }} — {{ $b->status ?? '' }}</li>
          @empty
            <li class="list-group-item text-muted">Aucune réservation</li>
          @endforelse
        </ul>
      </div>
    </div>
    <div class="col-12">
      <div class="card h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
          <span>Messages récents des touristes</span>
          <a href="{{ route('sites.index') }}" class="btn btn-sm btn-outline-primary">Voir les sites</a>
        </div>
        <ul class="list-group list-group-flush">
          @forelse($contacts as $c)
            <li class="list-group-item">
              <div class="d-flex justify-content-between">
                <div>
                  <div class="fw-semibold">{{ $c->name }} <span class="text-muted">&lt;{{ $c->email }}&gt;</span></div>
                  <div class="small text-muted">{{ $c->created_at?->format('d/m/Y H:i') }}</div>
                  <div class="mt-1">{{ Str::limit($c->message, 160) }}</div>
                </div>
                @if($c->phone)
                  <div class="text-end">
                    <a class="btn btn-sm btn-outline-secondary" href="tel:{{ $c->phone }}">Appeler</a>
                  </div>
                @endif
              </div>
            </li>
          @empty
            <li class="list-group-item text-muted">Aucun message pour le moment.</li>
          @endforelse
        </ul>
      </div>
    </div>
  </div>
</div>
@endsection
