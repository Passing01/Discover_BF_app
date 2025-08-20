@extends('organizer.events.wizard.layout')

@section('wizard-form')
<form method="post" action="{{ route('organizer.events.wizard.submit', 2) }}" class="row g-3">
  @csrf
  <div class="col-12">
    <label class="form-label">Description</label>
    <textarea name="description" rows="5" class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $draft['description'] ?? '') }}</textarea>
    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-6">
    <label class="form-label">Catégorie</label>
    <input type="text" name="category" value="{{ old('category', $draft['category'] ?? '') }}" class="form-control @error('category') is-invalid @enderror" required>
    @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-6">
    <label class="form-label">Prix du ticket (optionnel)</label>
    <input type="number" name="ticket_price" value="{{ old('ticket_price', $draft['ticket_price'] ?? '') }}" class="form-control @error('ticket_price') is-invalid @enderror" min="0" step="0.01">
    @error('ticket_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-12 d-flex justify-content-between">
    <a href="{{ route('organizer.events.wizard.show', 1) }}" class="btn btn-outline-secondary">Retour</a>
    <button class="btn btn-primary">Suivant</button>
  </div>
</form>
@endsection
