@extends('organizer.events.wizard.layout')

@section('wizard-form')
@php($d = $draft)
<div class="mb-3">
  <h5>Vérifier les informations</h5>
  <ul class="list-group">
    <li class="list-group-item"><strong>Nom:</strong> {{ $d['name'] ?? '—' }}</li>
    <li class="list-group-item"><strong>Début:</strong> {{ $d['start_date'] ?? '—' }}</li>
    <li class="list-group-item"><strong>Fin:</strong> {{ $d['end_date'] ?? '—' }}</li>
    <li class="list-group-item"><strong>Lieu:</strong> {{ $d['location'] ?? '—' }}</li>
    <li class="list-group-item"><strong>Catégorie:</strong> {{ $d['category'] ?? '—' }}</li>
    <li class="list-group-item"><strong>Prix ticket:</strong> {{ $d['ticket_price'] ?? '—' }}</li>
  </ul>
</div>
<form method="post" action="{{ route('organizer.events.wizard.submit', 5) }}" class="d-flex justify-content-between">
  @csrf
  <a href="{{ route('organizer.events.wizard.show', 4) }}" class="btn btn-outline-secondary">Retour</a>
  <button class="btn btn-success">Créer l'évènement</button>
</form>
@endsection
