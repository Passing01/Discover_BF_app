@extends('organizer.events.wizard.layout')

@section('wizard-form')
@php($selected = $draft['ticket_template_id'] ?? null)
<div class="mb-3">
  <p>Choisissez une affiche pour votre évènement. Vous pouvez rechercher des modèles et en sélectionner un. Une fois choisi, il apparaîtra dans l'aperçu à droite.</p>
</div>
<div class="mb-3">
  <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#templatesModal">Rechercher une affiche</button>
  @if($selected)
    <span class="badge text-bg-success ms-2">Affiche sélectionnée</span>
  @endif
</div>
<form method="post" action="{{ route('organizer.events.wizard.submit', 4) }}" class="row g-3">
  @csrf
  <div class="col-12">
    <label class="form-label">Identifiant du modèle choisi</label>
    <input type="text" readonly class="form-control @error('ticket_template_id') is-invalid @enderror" name="ticket_template_id" value="{{ old('ticket_template_id', $selected) }}" placeholder="Sélectionnez via le modal">
    @error('ticket_template_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-12 d-flex justify-content-between">
    <a href="{{ route('organizer.events.wizard.show', 3) }}" class="btn btn-outline-secondary">Retour</a>
    <button class="btn btn-primary">Suivant</button>
  </div>
</form>
@include('organizer.events.wizard.partials.templates-modal')
@endsection
