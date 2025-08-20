@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('/') }}">Accueil</a></li>
      <li class="breadcrumb-item"><a href="{{ route('food.restaurants.index') }}">Restaurants</a></li>
      <li class="breadcrumb-item active" aria-current="page">Mes réservations</li>
    </ol>
  </nav>

  <div class="d-flex align-items-center mb-3">
    <h1 class="h4 mb-0">Mes réservations</h1>
    <a href="{{ route('food.restaurants.index') }}" class="btn btn-sm btn-outline-secondary ms-auto">Explorer les restaurants</a>
  </div>

  @if($reservations->count() === 0)
    <div class="alert alert-info">Vous n'avez pas encore de réservation.</div>
  @else
    <div class="list-group shadow-sm">
      @foreach($reservations as $reservation)
        <a href="{{ route('food.restaurants.reservations.show', $reservation) }}" class="list-group-item list-group-item-action">
          <div class="d-flex w-100 justify-content-between">
            <h5 class="mb-1">{{ $reservation->restaurant->name }}</h5>
            <small class="text-muted">{{ $reservation->reservation_at->format('d/m/Y H:i') }}</small>
          </div>
          <p class="mb-1 small text-muted">
            {{ $reservation->restaurant->city ?? $reservation->restaurant->address }}
          </p>
          <div class="d-flex small gap-3">
            <span><strong>Personnes:</strong> {{ $reservation->party_size }}</span>
            <span><strong>Statut:</strong> <span class="badge bg-secondary text-uppercase">{{ $reservation->status }}</span></span>
            @if($reservation->order_items)
              <span><strong>Pré-commandes:</strong> {{ collect($reservation->order_items)->sum('qty') }}</span>
            @endif
          </div>
        </a>
      @endforeach
    </div>

    <div class="mt-3">
      {{ $reservations->links() }}
    </div>
  @endif
</div>
@endsection
