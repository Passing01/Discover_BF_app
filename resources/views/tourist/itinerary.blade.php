@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <nav aria-label="breadcrumb" class="mb-2">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('/') }}">Accueil</a></li>
      <li class="breadcrumb-item"><a href="{{ route('assistant.index') }}">Planifier</a></li>
      <li class="breadcrumb-item active" aria-current="page">Mon itinéraire</li>
    </ol>
  </nav>
  <h1 class="mb-1">Votre itinéraire</h1>
  <div class="d-flex align-items-center gap-2 mb-3">
    <p class="text-muted mb-0">Voici votre parcours personnalisé. Ajustez vos préférences à tout moment.</p>
    <a href="{{ route('assistant.index') }}" class="btn btn-sm btn-primary ms-auto"><i class="bi bi-stars me-1"></i> Ouvrir l'assistant</a>
  </div>

  @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  @if(empty($itinerary))
    <div class="card shadow-sm border-0 bg-light rounded-3">
      <div class="card-body d-flex align-items-center">
        <i class="bi bi-map text-primary fs-2 me-3"></i>
        <div>
          <div class="fw-semibold mb-1">Aucun itinéraire disponible</div>
          <div class="text-muted">Commencez par préciser vos dates, budget et centres d’intérêt.</div>
          <a href="{{ route('assistant.index') }}" class="btn btn-primary btn-sm mt-3"><i class="bi bi-magic me-1"></i> Planifier maintenant</a>
        </div>
      </div>
    </div>
  @else
    <div class="mb-3 d-flex flex-wrap align-items-center gap-2">
      <span class="badge rounded-pill text-bg-light border"><i class="bi bi-calendar2-week me-1"></i>{{ $itinerary['start_date'] }} → {{ $itinerary['end_date'] }}</span>
      <span class="badge rounded-pill text-bg-primary"><i class="bi bi-cash-coin me-1"></i>{{ strtoupper($itinerary['budget']) }}</span>
      @foreach(($itinerary['interests'] ?? []) as $interest)
        <span class="badge rounded-pill text-bg-secondary"><i class="bi bi-bookmark-heart me-1"></i>{{ $interest }}</span>
      @endforeach
      <div class="ms-auto d-flex gap-2">
        <a href="{{ route('assistant.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-sliders me-1"></i> Ajuster</a>
        <button type="button" class="btn btn-outline-primary btn-sm" onclick="window.print()"><i class="bi bi-printer me-1"></i> Imprimer</button>
      </div>
    </div>

    <div class="row g-3">
      @foreach($itinerary['days'] as $day)
        <div class="col-md-6">
          <div class="card h-100 shadow-sm border-0 rounded-3">
            <div class="card-header bg-white fw-semibold"><i class="bi bi-geo-alt me-1 text-primary"></i>Jour {{ $day['day'] }} — {{ $day['city'] }}</div>
            <div class="card-body">
              <p class="card-text mb-0">{{ $day['activity'] }}</p>
            </div>
            <div class="card-footer bg-white border-0 pt-0">
              <a href="{{ route('assistant.index') }}" class="btn btn-light btn-sm border"><i class="bi bi-magic me-1"></i> Affiner avec l'assistant</a>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @endif

  <hr class="my-4">
  <h3 class="mb-2">Suggestions</h3>
  <div class="row g-3">
    <div class="col-md-4">
      <div class="card h-100 shadow-sm border-0 rounded-3">
        <div class="card-header bg-white fw-semibold"><i class="bi bi-calendar-event me-1 text-primary"></i> Événements</div>
        <ul class="list-group list-group-flush">
          @forelse($suggestedEvents as $e)
            <li class="list-group-item">{{ $e->name ?? 'Événement' }} @if(!empty($e->date))<span class="text-muted">— {{ $e->date }}</span>@endif</li>
          @empty
            <li class="list-group-item text-muted">Aucun événement</li>
          @endforelse
        </ul>
        <div class="card-footer bg-white border-0">
          <a href="{{ route('events.index') }}" class="btn btn-light btn-sm border"><i class="bi bi-search me-1"></i> Explorer les évènements</a>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card h-100 shadow-sm border-0 rounded-3">
        <div class="card-header bg-white fw-semibold"><i class="bi bi-building me-1 text-primary"></i> Hôtels</div>
        <ul class="list-group list-group-flush">
          @forelse($suggestedHotels as $h)
            <li class="list-group-item">{{ $h->name ?? 'Hôtel' }}</li>
          @empty
            <li class="list-group-item text-muted">Aucun hôtel</li>
          @endforelse
        </ul>
        <div class="card-footer bg-white border-0">
          <a href="{{ route('tourist.hotels.index') }}" class="btn btn-light btn-sm border"><i class="bi bi-search me-1"></i> Explorer les séjours</a>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card h-100 shadow-sm border-0 rounded-3">
        <div class="card-header bg-white fw-semibold"><i class="bi bi-people me-1 text-primary"></i> Guides</div>
        <ul class="list-group list-group-flush">
          @forelse($guides as $g)
            <li class="list-group-item">{{ $g->name ?? 'Guide' }}</li>
          @empty
            <li class="list-group-item text-muted">Aucun guide</li>
          @endforelse
        </ul>
        <div class="card-footer bg-white border-0">
          <a href="{{ route('tourist.community') }}" class="btn btn-light btn-sm border"><i class="bi bi-people me-1"></i> Contacter la communauté</a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
