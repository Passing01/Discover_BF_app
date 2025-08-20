@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <h1 class="mb-3 text-orange">Réservations des hôtels</h1>

  @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <form method="GET" class="panel-cream rounded-20 p-3 p-md-4 mb-3">
    <div class="row g-2 align-items-end">
      <div class="col-md-3">
        <label class="form-label">Statut</label>
        <select name="status" class="form-select">
          <option value="">Tous statuts</option>
          @foreach(['pending','confirmed','checked_in','checked_out','cancelled'] as $st)
            <option value="{{ $st }}" @selected(request('status')===$st)>{{ $st }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Du</label>
        <input type="date" name="from" class="form-control" value="{{ request('from') }}">
      </div>
      <div class="col-md-3">
        <label class="form-label">Au</label>
        <input type="date" name="to" class="form-control" value="{{ request('to') }}">
      </div>
      <div class="col-md-3 d-grid">
        <button class="btn btn-orange">Filtrer</button>
      </div>
    </div>
  </form>

  @if($bookings->count() === 0)
    <div class="alert alert-info">Aucune réservation trouvée.</div>
  @else
    <div class="panel-cream rounded-20 p-0">
      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead>
            <tr>
              <th>Réf.</th>
              <th>Hôtel</th>
              <th>Chambre</th>
              <th>Client</th>
              <th>Dates</th>
              <th>Montant</th>
              <th>Statut</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach($bookings as $b)
              <tr>
                <td>{{ $b->reference ?? substr($b->id,0,8) }}</td>
                <td>{{ $b->room?->hotel?->name }}</td>
                <td>{{ $b->room?->name }}</td>
                <td>{{ $b->user?->name }}</td>
                <td>{{ $b->start_date }} → {{ $b->end_date }}</td>
                <td>{{ number_format($b->total_price, 0, ',', ' ') }} CFA</td>
                <td><span class="badge badge-soft text-capitalize">{{ $b->status }}</span></td>
                <td><a href="{{ route('agency.reservations.show', $b) }}" class="btn btn-sm btn-cream">Gérer</a></td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    <div class="mt-3">{{ $bookings->links() }}</div>
  @endif
</div>
@endsection
