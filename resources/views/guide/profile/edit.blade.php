@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="mb-0">Profil du guide</h1>
    <a href="{{ route('guide.dashboard') }}" class="btn btn-outline-secondary">Retour</a>
  </div>

  @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('guide.profile.update') }}" class="row g-3">
        @csrf
        <div class="col-12">
          <label class="form-label">Bio / Description</label>
          <textarea name="description" rows="5" class="form-control @error('description') is-invalid @enderror" placeholder="Présentez-vous, vos expériences, vos spécialités...">{{ old('description', $guide->description) }}</textarea>
          @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label">Langues parlées</label>
          <input type="text" name="spoken_languages" value="{{ old('spoken_languages', implode(', ', $guide->spoken_languages ?? [])) }}" class="form-control @error('spoken_languages') is-invalid @enderror" placeholder="ex: Français, Anglais, Mooré">
          @error('spoken_languages')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-3">
          <label class="form-label">Tarif horaire (FCFA)</label>
          <input type="number" step="1" min="0" name="hourly_rate" value="{{ old('hourly_rate', $guide->hourly_rate) }}" class="form-control @error('hourly_rate') is-invalid @enderror">
          @error('hourly_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-3 d-flex align-items-end">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="certified" value="1" id="certified" @checked(old('certified', $guide->certified))>
            <label class="form-check-label" for="certified">
              Guide certifié
            </label>
          </div>
        </div>
        <div class="col-12 d-flex gap-2">
          <button class="btn btn-primary">Enregistrer</button>
          <a href="{{ route('guide.dashboard') }}" class="btn btn-light">Annuler</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
