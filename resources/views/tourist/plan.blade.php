@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <nav aria-label="breadcrumb" class="mb-2">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('/') }}">Accueil</a></li>
      <li class="breadcrumb-item active" aria-current="page">Planifier</li>
    </ol>
  </nav>
  <h1 class="mb-1">Planifier votre voyage</h1>
  <div class="d-flex align-items-center gap-2 mb-3">
    <p class="text-muted mb-0">Indiquez vos dates, budget et centres d’intérêt pour générer un itinéraire personnalisé.</p>
    <a href="{{ route('assistant.plan') }}" class="btn btn-sm btn-primary ms-auto"><i class="bi bi-stars me-1"></i> Ouvrir l'assistant</a>
  </div>

  @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <form method="POST" action="{{ route('tourist.plan.store') }}" class="card shadow-sm border-0" id="plan-form">
    @csrf
    <div class="card-header bg-white fw-semibold"><i class="bi bi-sliders me-1 text-primary"></i> Paramètres du voyage</div>
    <div class="card-body">
    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Date de début</label>
        <input type="date" name="start_date" value="{{ old('start_date') }}" class="form-control @error('start_date') is-invalid @enderror" required>
        @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-4">
        <label class="form-label">Date de fin</label>
        <input type="date" name="end_date" value="{{ old('end_date') }}" class="form-control @error('end_date') is-invalid @enderror" required>
        @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-4">
        <label class="form-label">Budget</label>
        <select name="budget" class="form-select @error('budget') is-invalid @enderror" required>
          <option value="">Choisir...</option>
          <option value="low" @selected(old('budget')==='low')>Bas</option>
          <option value="medium" @selected(old('budget')==='medium')>Moyen</option>
          <option value="high" @selected(old('budget')==='high')>Élevé</option>
        </select>
        @error('budget')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>

      <div class="col-12">
        <label class="form-label">Centres d’intérêt</label>
        <div class="d-flex flex-wrap gap-2">
          @php($opts = ['culture' => 'Culture traditionnelle', 'craft' => 'Artisanat', 'food' => 'Gastronomie', 'nature' => 'Nature'])
          @php($selected = (array) old('interests', []))
          @foreach($opts as $key => $label)
            <div>
              <input class="btn-check" type="checkbox" name="interests[]" value="{{ $key }}" id="int-{{ $key }}" @checked(in_array($key, $selected))>
              <label class="btn btn-sm btn-outline-primary rounded-pill" for="int-{{ $key }}">
                <i class="bi bi-bookmark-star me-1"></i>{{ $label }}
              </label>
            </div>
          @endforeach
        </div>
        @error('interests')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        <div class="form-text">Choisissez au moins un centre d’intérêt pour des recommandations plus pertinentes.</div>
      </div>

      <div class="col-12 d-flex align-items-center gap-2">
        <button class="btn btn-primary" id="plan-submit">
          <span class="spinner-border spinner-border-sm me-1 d-none" id="plan-spinner" role="status" aria-hidden="true"></span>
          <i class="bi bi-magic me-1"></i> Générer l’itinéraire
        </button>
        <a href="{{ route('assistant.plan') }}" class="btn btn-outline-secondary">Utiliser l’assistant</a>
        <span class="text-muted ms-2 small"><i class="bi bi-shield-check me-1"></i>Vos préférences restent privées.</span>
      </div>
    </div>
    </div>
  </form>
  <script>
    (function(){
      const form = document.getElementById('plan-form');
      const btn = document.getElementById('plan-submit');
      const sp = document.getElementById('plan-spinner');
      if(!form || !btn || !sp) return;
      form.addEventListener('submit', function(){
        btn.disabled = true;
        sp.classList.remove('d-none');
      });
    })();
  </script>
</div>
@endsection
