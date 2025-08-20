@extends('organizer.events.wizard.layout')

@section('wizard-form')
<form method="post" action="{{ route('organizer.events.wizard.submit', 1) }}" class="row g-3">
  @csrf
  <div class="col-12">
    <label class="form-label">Nom de l'évènement</label>
    <input type="text" name="name" value="{{ old('name', $draft['name'] ?? '') }}" class="form-control @error('name') is-invalid @enderror" required>
    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-6">
    <label class="form-label">Date/heure de début</label>
    <input type="datetime-local" name="start_date" value="{{ old('start_date', $draft['start_date'] ?? '') }}" class="form-control @error('start_date') is-invalid @enderror" required>
    @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-6">
    <label class="form-label">Date/heure de fin</label>
    <input type="datetime-local" name="end_date" value="{{ old('end_date', $draft['end_date'] ?? '') }}" class="form-control @error('end_date') is-invalid @enderror">
    @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-12">
    <label class="form-label">Emplacement</label>
    <input type="text" name="location" value="{{ old('location', $draft['location'] ?? '') }}" class="form-control @error('location') is-invalid @enderror" placeholder="En ligne ou adresse" required>
    @error('location')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-12 d-flex justify-content-between">
    <a href="{{ route('organizer.events.index') }}" class="btn btn-outline-secondary">Annuler</a>
    <button class="btn btn-primary">Suivant</button>
  </div>
</form>
@endsection
