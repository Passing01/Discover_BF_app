@extends('organizer.events.wizard.layout')

@section('wizard-form')
<form method="post" action="{{ route('organizer.events.wizard.submit', 3) }}" class="row g-3">
  @csrf
  <div class="col-12">
    <p class="text-muted">Les informations de l'organisateur proviennent de votre profil. Vous pourrez mettre à jour votre logo dans <a href="{{ route('organizer.profile.logo.edit') }}">Profil organisateur</a>.</p>
  </div>
  <div class="col-12 d-flex justify-content-between">
    <a href="{{ route('organizer.events.wizard.show', 2) }}" class="btn btn-cream">Retour</a>
    <button class="btn btn-orange">Suivant</button>
  </div>
</form>
@endsection
