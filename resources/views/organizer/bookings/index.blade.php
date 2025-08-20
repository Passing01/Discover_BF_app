@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <div class="d-flex align-items-center mb-3">
    <h1 class="h4 mb-0 text-orange">Ventes (billets)</h1>
    <div class="ms-auto d-flex gap-2">
      <a href="{{ route('organizer.events.index') }}" class="btn btn-cream"><i class="bi bi-collection me-1"></i> Mes évènements</a>
    </div>
  </div>

  @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <div class="row g-3">
    <div class="col-12">
      <div class="panel-cream rounded-20">
        <div class="p-3 p-md-4">
          <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <div>
              <span class="text-muted">Total encaissé</span>
              <div class="fs-5 fw-semibold">{{ number_format($totalAmount ?? 0, 0, ',', ' ') }} XOF</div>
            </div>
            <div class="d-flex align-items-center gap-2 ms-auto">
              <div class="small text-muted">{{ $bookings->total() }} réservation(s)</div>
              <a href="{{ route('organizer.sales.export', request()->query()) }}" class="btn btn-cream btn-sm"><i class="bi bi-download me-1"></i> Export CSV</a>
            </div>
          </div>

          <form method="get" action="{{ route('organizer.sales.index') }}" class="mb-3">
            <div class="row g-2 align-items-end">
              <div class="col-12 col-md-3">
                <label class="form-label small text-muted mb-1">Statut</label>
                <select name="status" class="form-select form-select-sm">
                  <option value="">Tous</option>
                  @php($currentStatus = request('status'))
                  @foreach(['paid' => 'Payé','pending' => 'En attente','cancelled' => 'Annulé','refunded' => 'Remboursé'] as $val => $label)
                    <option value="{{ $val }}" @selected($currentStatus === $val)>{{ $label }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-12 col-md-4">
                <label class="form-label small text-muted mb-1">Évènement</label>
                <select name="event_id" class="form-select form-select-sm">
                  <option value="">Tous</option>
                  @php($currentEvent = (string) request('event_id'))
                  @foreach(($events ?? []) as $ev)
                    <option value="{{ $ev->id }}" @selected($currentEvent === (string) $ev->id)>{{ $ev->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-6 col-md-2">
                <label class="form-label small text-muted mb-1">Du</label>
                <input type="date" name="from" value="{{ request('from') }}" class="form-control form-control-sm">
              </div>
              <div class="col-6 col-md-2">
                <label class="form-label small text-muted mb-1">Au</label>
                <input type="date" name="to" value="{{ request('to') }}" class="form-control form-control-sm">
              </div>
              <div class="col-12 col-md-1 d-flex gap-2">
                <button type="submit" class="btn btn-orange btn-sm w-100"><i class="bi bi-funnel me-1"></i> Filtrer</button>
              </div>
            </div>
          </form>

          @if($bookings->count())
            <div class="table-responsive">
              <table class="table align-middle">
                <thead>
                  <tr>
                    <th>Réf.</th>
                    <th>Évènement</th>
                    <th>Acheteur</th>
                    <th class="text-end">Montant</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                @foreach($bookings as $booking)
                  <tr>
                    <td class="text-muted small">{{ Str::limit($booking->id, 8, '') }}</td>
                    <td>
                      <div class="fw-semibold">{{ $booking->event?->name }}</div>
                      <div class="small text-muted">{{ $booking->event?->location }}</div>
                    </td>
                    <td>
                      <div class="fw-semibold">{{ $booking->buyer_name ?? $booking->user?->name }}</div>
                      <div class="small text-muted">{{ $booking->buyer_email ?? $booking->user?->email }}</div>
                    </td>
                    <td class="text-end">{{ number_format($booking->total_amount ?? 0, 0, ',', ' ') }} XOF</td>
                    <td>
                      @php($status = $booking->status ?? 'paid')
                      <span class="badge @if($status==='paid') bg-success @elseif($status==='pending') bg-warning text-dark @else bg-secondary @endif">{{ ucfirst($status) }}</span>
                    </td>
                    <td>{{ $booking->created_at?->format('d/m/Y H:i') }}</td>
                    <td class="text-end">
                      <a href="{{ route('organizer.sales.show', $booking) }}" class="btn btn-sm btn-orange"><i class="bi bi-receipt me-1"></i> Détails</a>
                    </td>
                  </tr>
                @endforeach
                </tbody>
              </table>
            </div>
            <div class="mt-3">{{ $bookings->links() }}</div>
          @else
            <div class="alert alert-info rounded-20 mb-0">Aucune vente pour le moment.</div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
