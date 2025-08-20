@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-lg-9 col-xl-8">
      <div class="panel-cream rounded-20 p-3 p-md-4">
        <div class="d-flex justify-content-between align-items-start mb-3">
          <div>
            <div class="text-muted small">Réservation</div>
            <h1 class="h4 mb-0">Votre vol</h1>
          </div>
          <span class="badge badge-soft">Statut: {{ strtoupper($booking->status) }}</span>
        </div>

        <div class="mb-3">
          <div class="fw-semibold mb-1">Itinéraire</div>
          <div class="small text-muted">Trajet</div>
          <div class="mb-1">{{ $booking->flight->origin->city }} → {{ $booking->flight->destination->city }}</div>
          <div class="small text-muted">Horaires</div>
          <div>
            {{ \Illuminate\Support\Carbon::parse($booking->flight->departure_time)->format('d M Y, H:i') }} →
            {{ \Illuminate\Support\Carbon::parse($booking->flight->arrival_time)->format('d M Y, H:i') }}
          </div>
        </div>

        <div class="mb-3">
          <div class="fw-semibold mb-1">Détails</div>
          <div>Passagers: {{ $booking->passengers_count }}</div>
          <div>Classe: {{ ucfirst($booking->class) }}</div>
          <div class="fw-semibold mt-1">Total: {{ number_format($booking->total_price, 0, ',', ' ') }} XOF</div>
        </div>

        @php($summary = data_get($booking->passengers, 'summary'))
        @if($summary)
          <div class="rounded-20 p-3 mb-3" style="background:#fff; border:1px dashed #e9ecef;">
            <div class="fw-semibold mb-2">Détail tarifaire</div>
            <div class="row row-cols-2 row-cols-md-3 g-2 small">
              <div>Adultes: {{ $summary['adults'] ?? 0 }}</div>
              <div>Enfants: {{ $summary['children'] ?? 0 }}</div>
              <div>Bébés: {{ $summary['infants'] ?? 0 }}</div>
              <div>Bagages: {{ number_format($summary['baggage'] ?? 0) }}</div>
              <div>Sous-total: {{ number_format($summary['fare_subtotal'] ?? 0, 0, ',', ' ') }} XOF</div>
              <div>Taxes: {{ number_format($summary['taxes'] ?? 0, 0, ',', ' ') }} XOF</div>
              <div>Frais bagages: {{ number_format($summary['baggage_fee'] ?? 0, 0, ',', ' ') }} XOF</div>
            </div>
          </div>
        @endif

        <div class="mb-3">
          <div class="fw-semibold mb-1">Contact</div>
          <div>Nom: {{ $booking->contact_name }}</div>
          <div>Email: {{ $booking->contact_email }}</div>
          @if($booking->contact_phone)
            <div>Téléphone: {{ $booking->contact_phone }}</div>
          @endif
        </div>

        <div class="mt-3 d-flex gap-2">
          <a class="btn btn-cream" href="{{ route('air.bookings.index') }}">Mes réservations</a>
          <a class="btn btn-orange" href="{{ route('air.flights.index') }}">Voir d'autres vols</a>
          <form action="{{ route('air.bookings.destroy', $booking) }}" method="post" onsubmit="return confirm('Supprimer cette réservation ? Cette action est définitive.');">
            @csrf
            @method('DELETE')
            <button class="btn btn-outline-danger">Supprimer</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
