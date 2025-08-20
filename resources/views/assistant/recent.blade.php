@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <div class="panel-cream rounded-20 p-3 shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <div class="fw-bold">Plans récents</div>
      <a href="{{ route('assistant.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Retour</a>
    </div>
    <div class="panel-inner rounded-20 p-3 bg-white">
      @if($plan)
        <div class="mb-2"><span class="badge text-bg-light">Dernier</span> Séjour du {{ $plan['start'] ?? '?' }} au {{ $plan['end'] ?? '?' }}</div>
        <pre class="small bg-light p-2 rounded" style="max-height:300px;overflow:auto">{{ json_encode($plan['input'] ?? [], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
      @else
        <p class="text-muted mb-0">Aucun plan récent.</p>
      @endif
      @if(!empty($custom))
        <hr>
        <div class="fw-semibold mb-2">Éléments ajoutés</div>
        <ul class="list-group list-group-flush">
          @foreach($custom as $c)
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <div>{{ $c['label'] }} <span class="small text-muted">({{ $c['type'] }})</span></div>
              <span class="small text-muted">{{ $c['added_at'] }}</span>
            </li>
          @endforeach
        </ul>
      @endif
    </div>
  </div>
</div>
@endsection
