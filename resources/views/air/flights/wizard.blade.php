@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-2">
    <h1 class="h4 mb-0">Book Flight</h1>
    <a href="{{ route('tourist.dashboard') }}" class="btn btn-outline-secondary btn-sm">Back</a>
  </div>

  <div class="row g-3">
    <aside class="col-lg-3">
      <div class="panel-cream rounded-20 p-3 mb-3">
        <div class="fw-semibold mb-2">Trip details</div>
        <form method="get" class="vstack gap-2">
          <div>
            <label class="form-label small">From (IATA/City)</label>
            <input class="form-control" name="origin_iata" value="{{ request('origin_iata') }}" placeholder="ex: CDG">
          </div>
          <div>
            <label class="form-label small">To</label>
            <input class="form-control" name="destination_iata" value="{{ request('destination_iata','OUA') }}" placeholder="OUA">
          </div>
          <div class="row g-2">
            <div class="col-6">
              <label class="form-label small">Departure</label>
              <input type="date" class="form-control" name="out_date" value="{{ request('out_date') }}">
            </div>
            <div class="col-6">
              <label class="form-label small">Return</label>
              <input type="date" class="form-control" name="ret_date" value="{{ request('ret_date') }}">
            </div>
          </div>
          <button class="btn btn-cream w-100">Refresh</button>
        </form>
      </div>
      <div class="small text-muted">Prices in XOF • Data refreshed on {{ now()->format('M j, H:i') }}</div>
    </aside>

    <main class="col-lg-9">
      @if(session('status'))
        <div class="alert alert-success py-2">{{ session('status') }}</div>
      @endif
      <div class="panel-cream rounded-20 p-3 mb-3">
        <div class="fw-semibold mb-2">Outbound options @if(request('out_date')) • {{ request('out_date') }} @endif</div>
        <div class="vstack gap-2">
          @forelse($outbound as $f)
            <form method="post" action="{{ route('air.flights.select') }}" class="border rounded-20 p-3 d-flex justify-content-between align-items-center">
              @csrf
              <input type="hidden" name="leg" value="outbound">
              <input type="hidden" name="flight_id" value="{{ $f->id }}">
              <div>
                <div class="fw-semibold">{{ $f->airline ?? 'Air' }} • {{ \Illuminate\Support\Carbon::parse($f->departure_time)->format('H:i') }} → {{ \Illuminate\Support\Carbon::parse($f->arrival_time)->format('H:i') }}</div>
                <div class="small text-muted">{{ $f->origin->city }} → {{ $f->destination->city }}</div>
              </div>
              <div class="text-end">
                <div class="fw-semibold">{{ number_format($f->base_price, 0, ',', ' ') }} XOF</div>
                <button class="btn btn-orange btn-sm mt-1">Select</button>
              </div>
            </form>
          @empty
            <div class="text-muted">No outbound flights found.</div>
          @endforelse
        </div>
      </div>

      <div class="panel-cream rounded-20 p-3 mb-3">
        <div class="fw-semibold mb-2">Return options @if(request('ret_date')) • {{ request('ret_date') }} @endif</div>
        <div class="vstack gap-2">
          @forelse($return as $f)
            <form method="post" action="{{ route('air.flights.select') }}" class="border rounded-20 p-3 d-flex justify-content-between align-items-center">
              @csrf
              <input type="hidden" name="leg" value="return">
              <input type="hidden" name="flight_id" value="{{ $f->id }}">
              <div>
                <div class="fw-semibold">{{ $f->airline ?? 'Air' }} • {{ \Illuminate\Support\Carbon::parse($f->departure_time)->format('H:i') }} → {{ \Illuminate\Support\Carbon::parse($f->arrival_time)->format('H:i') }}</div>
                <div class="small text-muted">{{ $f->origin->city }} → {{ $f->destination->city }}</div>
              </div>
              <div class="text-end">
                <div class="fw-semibold">{{ number_format($f->base_price, 0, ',', ' ') }} XOF</div>
                <button class="btn btn-orange btn-sm mt-1">Select</button>
              </div>
            </form>
          @empty
            <div class="text-muted">No return flights found.</div>
          @endforelse
        </div>
      </div>

      <div class="panel-cream rounded-20 p-3">
        <div class="fw-semibold mb-2">Your selection</div>
        <div class="vstack gap-1 small">
          <div>Outbound: <span class="text-muted">{{ $selected['outbound']['summary']['route'] ?? '—' }}</span> @isset($selected['outbound']) • {{ number_format($selected['outbound']['summary']['price'], 0, ',', ' ') }} XOF @endisset</div>
          <div>Return: <span class="text-muted">{{ $selected['return']['summary']['route'] ?? '—' }}</span> @isset($selected['return']) • {{ number_format($selected['return']['summary']['price'], 0, ',', ' ') }} XOF @endisset</div>
        </div>
        <div class="d-flex justify-content-end mt-2">
          <a class="btn btn-orange" href="{{ route('air.flights.details') }}">Continue to details</a>
        </div>
      </div>
    </main>
  </div>
</div>
@endsection
