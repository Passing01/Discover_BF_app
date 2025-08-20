@extends('layouts.site')

@section('content')
<div class="container py-4">
  <h1 class="mb-3">Mes réservations</h1>

  @if($bookings->count())
    <div class="list-group">
      @foreach($bookings as $bk)
        <a href="{{ route('bookings.show', $bk) }}" class="list-group-item list-group-item-action">
          <div class="d-flex w-100 justify-content-between">
            <h5 class="mb-1">{{ $bk->event->name ?? 'Évènement' }}</h5>
            <small class="text-muted">Total: {{ number_format($bk->total_amount, 0, ',', ' ') }} CFA</small>
          </div>
          <p class="mb-1 text-muted">{{ $bk->event->location ?? '' }}</p>
          <small class="text-muted">{{ $bk->tickets->count() }} ticket(s)</small>
        </a>
      @endforeach
    </div>
    <div class="mt-3">{{ $bookings->links() }}</div>
  @else
    <div class="alert alert-secondary">Aucune réservation pour le moment.</div>
  @endif
</div>
@endsection
