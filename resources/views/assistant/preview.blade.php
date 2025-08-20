@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <div class="panel-cream rounded-20 p-3 shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <div class="fw-bold">Prévisualisation — {{ $date }}</div>
      <a href="{{ route('assistant.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Retour</a>
    </div>
    <div class="panel-inner rounded-20 p-3 bg-white">
      @if(!empty($items))
        <ul class="list-group list-group-flush">
          @foreach($items as $it)
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <div>
                <div class="fw-semibold">{{ $it['title'] ?? $it['label'] ?? 'Élément' }}</div>
                <div class="small text-muted">{{ ucfirst($it['type'] ?? 'item') }} @if(!empty($it['city'])) · {{ $it['city'] }} @endif @if(!empty($it['time'])) · {{ \Illuminate\Support\Carbon::parse($it['time'])->format('H:i') }} @endif</div>
              </div>
              <span class="badge text-bg-light">{{ $it['type'] ?? 'item' }}</span>
            </li>
          @endforeach
        </ul>
      @else
        <p class="text-muted mb-0">Aucun élément pour aujourd'hui.</p>
      @endif
    </div>
  </div>
</div>
@endsection
