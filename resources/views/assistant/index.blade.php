@extends('layouts.tourist')

@push('styles')
<style>
  .panel-cream{background:#f9f1e7;border:1px solid #f0e3d7;}
  .panel-cream .panel-inner{background:#fff7ef;border:1px solid #ead6c5;}
  .soft-card{border:1px solid #efdfd2;border-radius:16px;}
  .chip{display:inline-flex;align-items:center;gap:.35rem;padding:.35rem .6rem;border-radius:999px;border:1px solid #e5d3c2;background:#fff; font-size:.85rem;}
  .btn-orange{background:#e85b3a;border-color:#e85b3a;color:#fff;}
  .btn-orange:hover{background:#d04f32;border-color:#d04f32;color:#fff;}
  .btn-cream{background:#f3e5d8;border-color:#e7d3c1;color:#5c4536;}
  .btn-cream:hover{background:#ecd8c7;border-color:#e0c8b3;color:#4a392e;}
  .rounded-20{border-radius:20px;}
  .header-actions .btn{border-radius:16px;}
</style>
@endpush

@section('content')
<div class="container py-4">
  <div class="panel-cream rounded-20 p-3 shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <div class="fw-bold text-muted">Assistant de voyage</div>
      <div class="header-actions d-flex gap-2">
        <a href="{{ route('assistant.recent') }}" class="btn btn-cream btn-sm"><i class="bi bi-clock-history"></i> Récent</a>
        <a href="{{ route('tourist.dashboard') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-x-lg"></i> Fermer</a>
      </div>
    </div>
    <div class="panel-inner rounded-20 p-3">
      <div class="d-flex flex-wrap gap-2 mb-3">
        <span class="chip"><i class="bi bi-plane me-1"></i> Vols</span>
        <span class="chip"><i class="bi bi-bus-front me-1"></i> Bus</span>
        <span class="chip"><i class="bi bi-taxi-front me-1"></i> Taxi</span>
        <span class="chip"><i class="bi bi-geo-alt me-1"></i> Destinations</span>
      </div>

      <form method="post" action="{{ route('assistant.plan') }}" class="soft-card p-3 bg-white">
        @csrf
        @if ($errors->any())
          <div class="alert alert-danger">
            <strong>Veuillez corriger les erreurs suivantes :</strong>
            <ul class="mb-0">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Origine (IATA/Ville)</label>
            <input name="origin_iata" class="form-control @error('origin_iata') is-invalid @enderror" placeholder="ex: ABJ ou Abidjan" list="assistant-airports" value="{{ old('origin_iata') }}">
            <datalist id="assistant-airports">
              @foreach($airports as $a)
                <option value="{{ $a->iata_code }}">{{ $a->city }} — {{ $a->name }}</option>
                <option value="{{ $a->city }}">{{ $a->iata_code }} — {{ $a->name }}</option>
              @endforeach
            </datalist>
          </div>
          <div class="col-md-4">
            <label class="form-label">Date d'arrivée souhaitée</label>
            <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" required min="{{ now()->toDateString() }}" value="{{ old('start_date') }}">
          </div>
          <div class="col-md-4">
            <label class="form-label">Date de départ</label>
            <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" required min="{{ old('start_date', now()->toDateString()) }}" value="{{ old('end_date') }}">
          </div>
          <div class="col-md-4">
            <label class="form-label">Budget total (FCFA)</label>
            <input type="number" name="budget" min="0" class="form-control @error('budget') is-invalid @enderror" placeholder="ex: 750000" required value="{{ old('budget') }}">
          </div>
          <div class="col-md-8">
            <label class="form-label">Centres d'intérêt (optionnel)</label>
            <div class="d-flex flex-wrap gap-3">
              @php($opts = ['Culture','Patrimoine','Musique','Nature','Gastronomie','Artisanat'])
              @foreach($opts as $opt)
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="interests[]" value="{{ $opt }}" id="interest-{{ $loop->index }}" {{ in_array($opt, old('interests', [])) ? 'checked' : '' }}>
                  <label class="form-check-label" for="interest-{{ $loop->index }}">{{ $opt }}</label>
                </div>
              @endforeach
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-3">
          <a href="{{ route('assistant.options') }}" class="btn btn-cream"><i class="bi bi-gear"></i> Options avancées</a>
          <button class="btn btn-orange"><i class="bi bi-magic"></i> Générer mon plan</button>
        </div>
      </form>
    </div>
  </div>

</div>
@endsection
