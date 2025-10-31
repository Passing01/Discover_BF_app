@php
  $events = $events ?? \App\Models\Event::latest()->take(3)->get();
@endphp
@extends('layouts.tourist')
@section('content')
<div class="container py-5">
  <div class="dashboard-card">
    <div class="card-header">
      <i class="bi bi-star me-2"></i>Recommandations
    </div>
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <div class="dash-section-title">Recommandations</div>
        <div class="btn-group btn-group-sm">
          <button class="btn btn-light active">Activities</button>
          <button class="btn btn-light">Food</button>
          <button class="btn btn-light">Culture</button>
        </div>
      </div>
      <div class="vstack gap-2">
        @foreach($events->take(3) as $r)
          <a href="{{ route('events.show', $r) }}" class="text-decoration-none">
            <div class="border rounded p-2">
              <div class="fw-semibold">{{ $r->title }}</div>
              <div class="small text-muted">{{ $r->city ?? '—' }} · {{ $r->starts_at }}</div>
            </div>
          </a>
        @endforeach
      </div>
    </div>
  </div>
</div>
@endsection
