@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <h1 class="mb-3 text-orange">Gestion de la réservation</h1>

  @if(session('status'))
    <div class="alert alert-success rounded-20">{{ session('status') }}</div>
  @endif

  <div class="panel-cream rounded-20 p-3 p-md-4 mb-3">
      <h5 class="mb-3">Détails</h5>
      <div class="row g-3">
        <div class="col-md-4">
          <div><strong>Référence</strong></div>
          <div>{{ $booking->reference ?? substr($booking->id,0,8) }}</div>
        </div>
        <div class="col-md-4">
          <div><strong>Statut</strong></div>
          <div><span class="badge badge-soft text-capitalize">{{ $booking->status }}</span></div>
        </div>
        <div class="col-md-4">
          <div><strong>Montant</strong></div>
          <div>{{ number_format($booking->total_price, 0, ',', ' ') }} CFA</div>
        </div>
        <div class="col-md-4">
          <div><strong>Client</strong></div>
          <div>{{ $booking->user?->name }} ({{ $booking->user?->email }})</div>
        </div>
        <div class="col-md-4">
          <div><strong>Hôtel</strong></div>
          <div>{{ $booking->room?->hotel?->name }}</div>
        </div>
        <div class="col-md-4">
          <div><strong>Chambre</strong></div>
          <div>{{ $booking->room?->name }}</div>
        </div>
        <div class="col-md-4">
          <div><strong>Dates</strong></div>
          <div>{{ $booking->start_date }} → {{ $booking->end_date }}</div>
        </div>
      </div>
  </div>

  <div class="panel-cream rounded-20 p-3 p-md-4">
      <h5 class="mb-3">Actions</h5>
      <div class="d-flex gap-2 flex-wrap">
        <form method="POST" action="{{ route('agency.reservations.status', $booking) }}" onsubmit="return confirm('Confirmer cette réservation ?');">
          @csrf
          <input type="hidden" name="action" value="confirm">
          <button class="btn btn-orange" @disabled(!in_array($booking->status, ['pending']))>Confirmer</button>
        </form>
        <form method="POST" action="{{ route('agency.reservations.status', $booking) }}" onsubmit="return confirm('Annuler cette réservation ?');">
          @csrf
          <input type="hidden" name="action" value="cancel">
          <button class="btn btn-cream" @disabled(!in_array($booking->status, ['pending','confirmed']))>Annuler</button>
        </form>
        <form method="POST" action="{{ route('agency.reservations.status', $booking) }}" onsubmit="return confirm('Marquer comme check-in ?');">
          @csrf
          <input type="hidden" name="action" value="checkin">
          <button class="btn btn-orange" @disabled(!in_array($booking->status, ['confirmed']))>Check‑in</button>
        </form>
        <form method="POST" action="{{ route('agency.reservations.status', $booking) }}" onsubmit="return confirm('Marquer comme check-out ?');">
          @csrf
          <input type="hidden" name="action" value="checkout">
          <button class="btn btn-secondary" @disabled(!in_array($booking->status, ['checked_in']))>Check‑out</button>
        </form>
      </div>
      <div class="mt-3">
        <a href="{{ route('agency.reservations.index') }}" class="btn btn-outline-secondary">Retour à la liste</a>
      </div>
    </div>
  </div>
</div>
@endsection
