@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-2">
    <div>
      <div class="text-muted small">Bookings</div>
      <h1 class="h4 mb-0">Your reservations</h1>
    </div>
    <div class="d-flex gap-2">
      <a class="btn btn-cream" href="{{ route('air.bookings.index') }}">Refresh</a>
      <a class="btn btn-orange" href="{{ route('air.bookings.export') }}">Export all</a>
    </div>
  </div>

  <div class="row g-3">
    <aside class="col-lg-3">
      <div class="panel-cream rounded-20 p-3 mb-3">
        <div class="fw-semibold mb-2">Manage</div>
        <ul class="list-unstyled vstack gap-1 small mb-0">
          <li><a class="text-decoration-none" href="{{ route('air.bookings.index') }}">All bookings</a></li>
          <li class="text-muted">Flights</li>
          <li class="text-muted">Stays</li>
          <li class="text-muted">Transport</li>
          <li class="text-muted">Activities</li>
        </ul>
      </div>
      <div class="panel-cream rounded-20 p-3">
        <div class="fw-semibold mb-2">Actions</div>
        <div class="vstack gap-2">
          <a class="btn btn-light">Link confirmation</a>
          <a class="btn btn-light">Add booking</a>
          <a class="btn btn-light" href="{{ route('air.bookings.export') }}">Export</a>
        </div>
      </div>
    </aside>

    <main class="col-lg-9">
      <div class="panel-cream rounded-20 p-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <div class="fw-semibold">Your reservations</div>
          <div class="btn-group btn-group-sm" role="group">
            <a class="btn btn-outline-secondary active">All</a>
            <a class="btn btn-outline-secondary">Upcoming</a>
            <a class="btn btn-outline-secondary">Past</a>
          </div>
        </div>

        <div class="vstack gap-2">
          @forelse($bookings as $b)
            <div class="border rounded-20 p-3 d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 bg-light" style="width:48px;height:48px;display:flex;align-items:center;justify-content:center">✈️</div>
                <div>
                  <div class="fw-semibold">Flight • {{ $b->flight->origin->city }} → {{ $b->flight->destination->city }}</div>
                  <div class="small text-muted">{{ \Illuminate\Support\Carbon::parse($b->flight->departure_time)->format('M j, H:i') }} → {{ \Illuminate\Support\Carbon::parse($b->flight->arrival_time)->format('M j, H:i') }}</div>
                </div>
              </div>
              <div class="text-end">
                <div class="badge text-bg-light text-uppercase">{{ $b->status }}</div>
                <div class="fw-semibold">{{ number_format($b->total_price, 0, ',', ' ') }} XOF</div>
                <div class="d-flex gap-1 justify-content-end mt-1">
                  <a class="btn btn-cream btn-sm" href="{{ route('air.bookings.show', $b) }}">View</a>
                  <form action="{{ route('air.bookings.destroy', $b) }}" method="post" onsubmit="return confirm('Supprimer cette réservation ?');">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-outline-danger btn-sm">Delete</button>
                  </form>
                </div>
              </div>
            </div>
          @empty
            <div class="text-muted">No bookings yet.</div>
          @endforelse
        </div>

        <div class="mt-3">{{ $bookings->links() }}</div>
      </div>
    </main>
  </div>
</div>
@endsection
