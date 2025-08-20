@extends('layouts.tourist')

@section('content')
@php($sel = $selected ?? [])
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-2">
    <h1 class="h4 mb-0">Traveler details</h1>
    <a href="{{ route('air.flights.wizard') }}" class="btn btn-outline-secondary btn-sm">Back</a>
  </div>

  <div class="row g-3">
    <div class="col-lg-7">
      <div class="panel-cream rounded-20 p-3 mb-3">
        <div class="fw-semibold mb-2">Selected flights</div>
        <div class="vstack gap-2">
          <div class="border rounded-20 p-3 d-flex justify-content-between align-items-center">
            <div>
              <div class="fw-semibold">Outbound</div>
              <div class="small text-muted">{{ $sel['outbound']['summary']['route'] ?? '—' }}</div>
            </div>
            <div class="fw-semibold">@isset($sel['outbound']) {{ number_format($sel['outbound']['summary']['price'], 0, ',', ' ') }} XOF @endisset</div>
          </div>
          <div class="border rounded-20 p-3 d-flex justify-content-between align-items-center">
            <div>
              <div class="fw-semibold">Return</div>
              <div class="small text-muted">{{ $sel['return']['summary']['route'] ?? '—' }}</div>
            </div>
            <div class="fw-semibold">@isset($sel['return']) {{ number_format($sel['return']['summary']['price'], 0, ',', ' ') }} XOF @endisset</div>
          </div>
        </div>
      </div>

      <div class="panel-cream rounded-20 p-3">
        <div class="fw-semibold mb-2">Passengers</div>
        @php($flightId = $sel['outbound']['id'] ?? null)
        @if($flightId)
        <form method="post" action="{{ route('air.flights.book.store', $flightId) }}" class="vstack gap-2">
          @csrf
          <div class="row g-2">
            <div class="col-6">
              <label class="form-label small">Adults</label>
              <input type="number" class="form-control" name="adult_count" min="1" value="2">
            </div>
            <div class="col-6">
              <label class="form-label small">Children</label>
              <input type="number" class="form-control" name="child_count" min="0" value="0">
            </div>
            <div class="col-6">
              <label class="form-label small">Infants</label>
              <input type="number" class="form-control" name="infant_count" min="0" value="0">
            </div>
            <div class="col-6">
              <label class="form-label small">Baggage</label>
              <input type="number" class="form-control" name="baggage_count" min="0" value="1">
            </div>
            <div class="col-12">
              <label class="form-label small">Class</label>
              <select class="form-select" name="class">
                <option value="economy">Economy</option>
                <option value="business">Business</option>
                <option value="first">First</option>
              </select>
            </div>
            <div class="col-6">
              <label class="form-label small">Contact name</label>
              <input class="form-control" name="contact_name" required>
            </div>
            <div class="col-6">
              <label class="form-label small">Contact email</label>
              <input type="email" class="form-control" name="contact_email" required>
            </div>
            <div class="col-12">
              <label class="form-label small">Contact phone</label>
              <input class="form-control" name="contact_phone">
            </div>
          </div>
          <div class="d-flex justify-content-end mt-2">
            <button class="btn btn-orange">Continue to payment</button>
          </div>
        </form>
        @else
          <p class="text-muted mb-0">Please select at least an outbound flight.</p>
        @endif
      </div>
    </div>

    <div class="col-lg-5">
      <div class="panel-cream rounded-20 p-3">
        <div class="fw-semibold mb-2">Fare rules</div>
        <p class="small text-muted mb-0">Sample text. Refundability and changes depend on airline and class. Taxes are estimated.</p>
      </div>
    </div>
  </div>
</div>
@endsection
