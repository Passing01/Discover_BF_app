@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <nav aria-label="breadcrumb" class="mb-2">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('/') }}">Accueil</a></li>
      <li class="breadcrumb-item"><a href="{{ route('food.restaurants.index') }}">Restaurants</a></li>
      <li class="breadcrumb-item"><a href="{{ route('food.restaurants.show', $reservation->restaurant) }}">{{ $reservation->restaurant->name }}</a></li>
      <li class="breadcrumb-item active" aria-current="page">Réservation</li>
    </ol>
  </nav>

  <div class="row g-3">
    <div class="col-lg-8">
      <div class="panel-cream rounded-20 p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h1 class="h4 mb-0">Votre réservation</h1>
          <span class="badge bg-primary text-uppercase">{{ $reservation->status }}</span>
        </div>
        <ul class="list-unstyled mb-0">
          <li><strong>Restaurant:</strong> {{ $reservation->restaurant->name }}</li>
          <li><strong>Date & heure:</strong> {{ $reservation->reservation_at->format('d/m/Y H:i') }}</li>
          <li><strong>Nombre de personnes:</strong> {{ $reservation->party_size }}</li>
          @if($reservation->special_requests)
            <li><strong>Demandes spéciales:</strong> {{ $reservation->special_requests }}</li>
          @endif
        </ul>
        @if(!empty($orderedItems))
          <hr>
          <h6 class="mb-2">Pré-commande</h6>
          <div class="table-responsive">
            <table class="table align-middle">
              <thead>
                <tr>
                  <th>Plat</th>
                  <th class="text-center" style="width:100px;">Qté</th>
                  <th class="text-end" style="width:140px;">Prix</th>
                  <th class="text-end" style="width:140px;">Total</th>
                </tr>
              </thead>
              <tbody>
                @php $sum = 0; @endphp
                @foreach($orderedItems as $it)
                  @php $line = $it['qty'] * (float)$it['price']; $sum += $line; @endphp
                  <tr>
                    <td>
                      <div class="d-flex align-items-center gap-2">
                        @if(!empty($it['image']))
                          <img src="{{ \Illuminate\Support\Str::startsWith($it['image'], ['http://','https://','/']) ? $it['image'] : asset('storage/'.$it['image']) }}" style="width:44px;height:44px;object-fit:cover;border-radius:6px;">
                        @endif
                        <span>{{ $it['name'] }}</span>
                      </div>
                    </td>
                    <td class="text-center">{{ $it['qty'] }}</td>
                    <td class="text-end">{{ number_format($it['price'], 0, ',', ' ') }} CFA</td>
                    <td class="text-end">{{ number_format($line, 0, ',', ' ') }} CFA</td>
                  </tr>
                @endforeach
              </tbody>
              <tfoot>
                <tr>
                  <th colspan="3" class="text-end">Total pré-commande</th>
                  <th class="text-end">{{ number_format($sum, 0, ',', ' ') }} CFA</th>
                </tr>
              </tfoot>
            </table>
          </div>
        @endif
        <div class="mt-3">
          <a href="{{ route('food.restaurants.show', $reservation->restaurant) }}" class="btn btn-outline-secondary">Retour au restaurant</a>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <x-ad-banner placement="restaurant_reservation_sidebar" />
    </div>
  </div>
</div>
@endsection
