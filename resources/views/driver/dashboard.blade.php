@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <div class="row g-4">
    <div class="col-lg-8">
      <h2 class="mb-3">Mon taxi</h2>
      <div class="card mb-4">
        <div class="card-body">
          @if($taxi)
            <div class="row g-2">
              <div class="col-md-4"><strong>Immatriculation:</strong> {{ $taxi->license_plate }}</div>
              <div class="col-md-4"><strong>Modèle:</strong> {{ $taxi->model }}</div>
              <div class="col-md-4"><strong>Couleur:</strong> {{ $taxi->color }}</div>
            </div>
            <div class="row g-2 mt-2">
              <div class="col-md-4"><strong>Disponible:</strong> {!! $taxi->available ? '<span class="badge bg-success">Oui</span>' : '<span class="badge bg-secondary">Non</span>' !!}</div>
              <div class="col-md-4"><strong>Prix/km:</strong> {{ $taxi->price_per_km }} FCFA</div>
            </div>
          @else
            <span class="text-muted">Aucun taxi enregistré.</span>
          @endif
        </div>
      </div>

      <h3 class="mb-3">Mes courses</h3>
      <div class="card">
        <div class="table-responsive">
          <table class="table align-middle mb-0">
            <thead>
              <tr><th>Date</th><th>Départ</th><th>Arrivée</th><th>Prix</th></tr>
            </thead>
            <tbody>
              @forelse($rides as $ride)
                <tr>
                  <td class="text-muted">{{ $ride->created_at }}</td>
                  <td>{{ $ride->pickup ?? '—' }}</td>
                  <td>{{ $ride->dropoff ?? '—' }}</td>
                  <td class="text-muted">{{ $ride->price ?? '—' }}</td>
                </tr>
              @empty
                <tr><td colspan="4" class="text-muted">Aucune course</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="p-3">{{ $rides->links() }}</div>
      </div>

      <h3 class="mt-4 mb-3">Annonces pour vous</h3>
      @php($ads = $adsFeed)
      @include('partials.ads')
    </div>

    <div class="col-lg-4">
      @include('components.notifications-widget')

      <x-ad-banner placement="dashboard_sidebar" />
      <h5 class="mb-3">Promotions</h5>
      @php($ads = $adsSidebar)
      @include('partials.ads')
    </div>
  </div>
</div>
@endsection
