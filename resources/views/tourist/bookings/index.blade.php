@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <nav aria-label="breadcrumb" class="mb-2">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('tourist.dashboard') }}">Tableau de bord</a></li>
      <li class="breadcrumb-item active" aria-current="page">Mes réservations</li>
    </ol>
  </nav>

  @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  {{-- Restaurants (Réservations de table) --}}
  @if(($restaurantReservations->count() ?? 0) > 0)
  <div class="panel-cream rounded-20 mb-4">
    <div class="p-3 d-flex justify-content-between align-items-center">
      <div>
        <h2 class="h5 mb-0">Restaurants — Réservations de table</h2>
        <div class="text-muted small">{{ $restaurantReservations->count() }} réservation(s)</div>
      </div>
      <a class="btn btn-sm btn-cream" href="{{ route('food.restaurants.index') }}"><i class="bi bi-egg-fried me-1"></i> Explorer les restaurants</a>
    </div>
    <div class="p-0">
      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Restaurant</th>
              <th>Date/heure</th>
              <th>Personnes</th>
              <th>Statut</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
          @foreach($restaurantReservations as $rr)
            <tr>
              <td>{{ $rr->restaurant?->name }}</td>
              <td>{{ optional($rr->reservation_at)->format('d/m/Y H:i') }}</td>
              <td>{{ $rr->party_size }}</td>
              <td><span class="badge text-bg-light border text-uppercase">{{ $rr->status }}</span></td>
              <td class="text-end">
                <a class="btn btn-sm btn-outline-primary" href="{{ route('food.restaurants.reservations.show', $rr) }}">Détails</a>
              </td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  @endif

  @php
    $totalCount = (
      $hotelBookings->count() +
      $eventBookings->count() +
      $busBookings->count() +
      $tourBookings->count() +
      $rides->count() +
      ($restaurantReservations->count() ?? 0) +
      ($dishOrders->count() ?? 0)
    );
  @endphp

  @if($totalCount === 0)
    <div class="panel-cream rounded-20 p-4 text-center text-muted">
      Aucune réservation pour le moment.
      <div class="mt-2">
        <a class="btn btn-sm btn-cream me-2" href="{{ route('tourist.events.index') }}"><i class="bi bi-ticket-perforated me-1"></i> Voir les évènements</a>
        <a class="btn btn-sm btn-cream me-2" href="{{ route('tourist.hotels.index') }}"><i class="bi bi-building me-1"></i> Trouver un hôtel</a>
        <a class="btn btn-sm btn-cream me-2" href="{{ route('transport.bus.index') }}"><i class="bi bi-bus-front me-1"></i> Réserver un bus</a>
        <a class="btn btn-sm btn-cream" href="{{ route('transport.taxi.index') }}"><i class="bi bi-taxi-front me-1"></i> Commander un taxi</a>
      </div>
    </div>
  @endif

  {{-- Hotels --}}
  @if($hotelBookings->count() > 0)
  <div class="panel-cream rounded-20 mb-4">
    <div class="p-3 d-flex justify-content-between align-items-center">
      <div>
        <h2 class="h5 mb-0">Hôtels</h2>
        <div class="text-muted small">{{ $hotelBookings->count() }} réservation(s)</div>
      </div>
      <a class="btn btn-sm btn-cream" href="{{ route('tourist.hotels.index') }}"><i class="bi bi-building me-1"></i> Réserver un séjour</a>
    </div>
    <div class="p-0">
      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Réf.</th>
              <th>Hôtel</th>
              <th>Chambre</th>
              <th>Dates</th>
              <th>Montant</th>
              <th>Statut</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
          @foreach($hotelBookings as $b)
            <tr>
              <td class="small text-muted">{{ $b->reference ?? substr($b->id,0,8) }}</td>
              <td>{{ $b->room?->hotel?->name }}</td>
              <td>{{ $b->room?->name }}</td>
              <td>{{ $b->start_date }} → {{ $b->end_date }}</td>
              <td class="fw-semibold">{{ number_format($b->total_price, 0, ',', ' ') }} CFA</td>
              <td><span class="badge text-bg-light border text-uppercase">{{ $b->status }}</span></td>
              <td class="text-end">
                <a class="btn btn-sm btn-outline-primary" href="{{ route('tourist.bookings.show', $b) }}">Détails</a>
              </td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  @endif

  {{-- Events --}}
  @if($eventBookings->count() > 0)
  <div class="panel-cream rounded-20 mb-4">
    <div class="p-3 d-flex justify-content-between align-items-center">
      <div>
        <h2 class="h5 mb-0">Évènements</h2>
        <div class="text-muted small">{{ $eventBookings->count() }} réservation(s)</div>
      </div>
      <a class="btn btn-sm btn-cream" href="{{ route('tourist.events.index') }}"><i class="bi bi-ticket-perforated me-1"></i> Voir les évènements</a>
    </div>
    <div class="p-0">
      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Évènement</th>
              <th>Lieu</th>
              <th>Dates</th>
              <th>Montant</th>
              <th>Statut</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
          @foreach($eventBookings as $eb)
            <tr>
              <td>{{ $eb->event?->name }}</td>
              <td>{{ $eb->event?->location }}</td>
              <td>{{ optional($eb->event?->start_date)->format('d/m/Y') }} → {{ optional($eb->event?->end_date)->format('d/m/Y') }}</td>
              <td class="fw-semibold">{{ number_format($eb->total_amount, 0, ',', ' ') }} CFA</td>
              <td><span class="badge text-bg-light border text-uppercase">{{ $eb->status }}</span></td>
              <td class="text-end">
                <a class="btn btn-sm btn-outline-primary" href="{{ route('bookings.show', $eb) }}">Détails</a>
              </td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  @endif

  {{-- Bus --}}
  @if($busBookings->count() > 0)
  <div class="panel-cream rounded-20 mb-4">
    <div class="p-3 d-flex justify-content-between align-items-center">
      <div>
        <h2 class="h5 mb-0">Bus</h2>
        <div class="text-muted small">{{ $busBookings->count() }} réservation(s)</div>
      </div>
      <a class="btn btn-sm btn-cream" href="{{ route('transport.bus.index') }}"><i class="bi bi-bus-front me-1"></i> Voir les trajets</a>
    </div>
    <div class="p-0">
      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Trajet</th>
              <th>Départ</th>
              <th>Places</th>
              <th>Montant</th>
              <th>Statut</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
          @foreach($busBookings as $bb)
            <tr>
              <td>{{ $bb->trip?->origin }} → {{ $bb->trip?->destination }}</td>
              <td>{{ optional($bb->trip?->departure_time)->format('d/m/Y H:i') }}</td>
              <td>{{ $bb->seats }}</td>
              <td class="fw-semibold">{{ number_format($bb->total_price, 0, ',', ' ') }} CFA</td>
              <td><span class="badge text-bg-light border text-uppercase">{{ $bb->status }}</span></td>
              <td class="text-end">
                <a class="btn btn-sm btn-outline-primary" href="{{ route('transport.bus.booking.show', $bb) }}">Détails</a>
              </td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  @endif

  {{-- Tours / Sites touristiques --}}
  @if($tourBookings->count() > 0)
  <div class="panel-cream rounded-20 mb-4">
    <div class="p-3 d-flex justify-content-between align-items-center">
      <div>
        <h2 class="h5 mb-0">Sites touristiques</h2>
        <div class="text-muted small">{{ $tourBookings->count() }} réservation(s)</div>
      </div>
    </div>
    <div class="p-0">
      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Visite</th>
              <th>Date</th>
              <th>Montant</th>
              <th>Statut</th>
            </tr>
          </thead>
          <tbody>
          @foreach($tourBookings as $tb)
            <tr>
              <td>{{ $tb->tour?->name }}</td>
              <td>{{ optional($tb->booking_date)->format('d/m/Y') }}</td>
              <td class="fw-semibold">{{ number_format($tb->total_price, 0, ',', ' ') }} CFA</td>
              <td><span class="badge text-bg-light border text-uppercase">{{ $tb->status }}</span></td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  @endif

  {{-- Taxi --}}
  @if($rides->count() > 0)
  <div class="panel-cream rounded-20 mb-4">
    <div class="p-3 d-flex justify-content-between align-items-center">
      <div>
        <h2 class="h5 mb-0">Taxis</h2>
        <div class="text-muted small">{{ $rides->count() }} course(s)</div>
      </div>
      <a class="btn btn-sm btn-cream" href="{{ route('transport.taxi.index') }}"><i class="bi bi-taxi-front me-1"></i> Commander un taxi</a>
    </div>
    <div class="p-0">
      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Trajet</th>
              <th>Date</th>
              <th>Taxi</th>
              <th>Montant</th>
              <th>Statut</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
          @foreach($rides as $ride)
            <tr>
              <td>{{ $ride->pickup_location }} → {{ $ride->dropoff_location }}</td>
              <td>{{ optional($ride->ride_date)->format('d/m/Y H:i') }}</td>
              <td>{{ $ride->taxi?->model }} ({{ $ride->taxi?->license_plate }})</td>
              <td class="fw-semibold">{{ number_format($ride->price, 0, ',', ' ') }} CFA</td>
              <td><span class="badge text-bg-light border text-uppercase">{{ $ride->status }}</span></td>
              <td class="text-end">
                <a class="btn btn-sm btn-outline-primary" href="{{ route('transport.taxi.ride.show', $ride) }}">Détails</a>
              </td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  @endif

  {{-- Commandes de plats (Delivery) --}}
  @if(($dishOrders->count() ?? 0) > 0)
  <div class="panel-cream rounded-20 mb-4">
    <div class="p-3 d-flex justify-content-between align-items-center">
      <div>
        <h2 class="h5 mb-0">Commandes de plats</h2>
        <div class="text-muted small">{{ $dishOrders->count() }} commande(s)</div>
      </div>
      <a class="btn btn-sm btn-cream" href="{{ route('food.restaurants.index') }}"><i class="bi bi-egg-fried me-1"></i> Commander un plat</a>
    </div>
    <div class="p-0">
      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Plat</th>
              <th>Restaurant</th>
              <th>Quantité</th>
              <th>Montant</th>
              <th>Statut</th>
              <th>Date</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
          @foreach($dishOrders as $do)
            <tr>
              <td>{{ $do->dish?->name }}</td>
              <td>{{ $do->restaurant?->name }}</td>
              <td>{{ $do->quantity }}</td>
              <td class="fw-semibold">{{ number_format($do->total_price, 0, ',', ' ') }} CFA</td>
              <td><span class="badge text-bg-light border text-uppercase">{{ $do->status }}</span></td>
              <td>{{ optional($do->created_at)->format('d/m/Y H:i') }}</td>
              <td class="text-end">
                <a class="btn btn-sm btn-outline-primary" href="{{ route('food.dishes.orders.show', $do) }}">Détails</a>
              </td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  @endif

</div>
@endsection
