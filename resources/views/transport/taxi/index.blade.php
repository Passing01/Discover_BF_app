@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <nav aria-label="breadcrumb" class="mb-2">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('tourist.dashboard') }}">Tableau de bord</a></li>
      <li class="breadcrumb-item active" aria-current="page">Taxis disponibles</li>
    </ol>
  </nav>

  <div class="d-flex align-items-center justify-content-between mb-2">
    <h1 class="h4 mb-0">Taxis disponibles</h1>
    <div class="text-muted small">Choisissez un taxi pour commander une course</div>
  </div>

  <div class="row g-3">
    @forelse($taxis as $taxi)
      <div class="col-md-4">
        <div class="panel-cream rounded-20 h-100 p-3 d-flex flex-column">
          <div class="flex-grow-1">
            <div class="fw-semibold mb-1">{{ $taxi->model }} • {{ $taxi->color }}</div>
            <div class="small text-muted mb-1">Immat: {{ $taxi->license_plate }}</div>
            <div class="small"><span class="text-muted">Tarif/km:</span> {{ number_format($taxi->price_per_km, 0, ',', ' ') }} FCFA</div>
          </div>
          <div class="pt-2">
            <a class="btn btn-orange w-100" href="{{ route('transport.taxi.ride.create', $taxi) }}">Commander une course</a>
          </div>
        </div>
      </div>
    @empty
      <p class="text-muted">Aucun taxi disponible pour le moment.</p>
    @endforelse
  </div>
  <div class="mt-3">{{ $taxis->links() }}</div>
  </div>
@endsection
