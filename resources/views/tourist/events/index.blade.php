@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <nav aria-label="breadcrumb" class="mb-2">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('/') }}">Accueil</a></li>
      <li class="breadcrumb-item active" aria-current="page">Agenda culturel</li>
    </ol>
  </nav>
  <div class="d-flex align-items-end flex-wrap gap-2 mb-3">
    <div>
      <h1 class="mb-0">Agenda culturel</h1>
      <div class="text-muted">Festivals, cérémonies, concerts et plus.</div>
    </div>
    <div class="ms-auto d-flex align-items-center gap-2">
      <span class="small text-muted">{{ $events->total() ?? $events->count() }} résultat(s)</span>
      <a class="btn btn-cream btn-sm" href="{{ route('events.index') }}"><i class="bi bi-arrow-clockwise me-1"></i>Actualiser</a>
    </div>
  </div>

  <div class="row g-3">
    @forelse($events as $event)
      <div class="col-md-4">
        <div class="panel-cream rounded-20 h-100 overflow-hidden d-flex flex-column">
          @if(!empty($event->image_path))
            <img src="{{ asset('storage/'.$event->image_path) }}" class="w-100" style="height:160px; object-fit:cover;" alt="{{ $event->name }}">
          @endif
          <div class="p-3 d-flex flex-column h-100">
            <div class="fw-semibold mb-1">{{ $event->name }}</div>
            <div class="text-muted small mb-1"><i class="bi bi-geo-alt me-1"></i>{{ $event->location }}</div>
            <div class="text-muted small mb-2"><i class="bi bi-calendar3 me-1"></i>{{ $event->start_date }} → {{ $event->end_date }}</div>
            <p class="mb-2">{{ \Illuminate\Support\Str::limit($event->description, 120) }}</p>
            <div class="mt-auto d-flex gap-2">
              <a class="btn btn-outline-primary btn-sm" href="{{ route('events.show', $event) }}">Voir</a>
              <a class="btn btn-primary btn-sm" href="{{ route('bookings.create', $event) }}">Réserver</a>
            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12"><div class="alert alert-info">Aucun évènement.</div></div>
    @endforelse
  </div>

  <div class="d-flex justify-content-center mt-3">{!! $events->onEachSide(1)->links() !!}</div>
</div>
@endsection
