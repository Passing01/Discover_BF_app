@extends('layouts.tourist')
@section('content')
<div class="container py-5">
  <div class="dashboard-card">
    <div class="card-header">
      <i class="bi bi-lightbulb me-2"></i>Conseils locaux
    </div>
    <div class="card-body">
      <div class="alert alert-info d-flex align-items-start">
        <i class="bi bi-info-circle-fill me-2 mt-1"></i>
        <div>
          <strong>Bonne adresse</strong>
          <p class="mb-0">Essayez le restaurant "La Paisible" pour une délicieuse cuisine locale.</p>
        </div>
      </div>
      <div class="alert alert-warning d-flex align-items-start">
        <i class="bi bi-exclamation-triangle-fill me-2 mt-1"></i>
        <div>
          <strong>À éviter</strong>
          <p class="mb-0">Évitez la circulation aux heures de pointe (7h-9h et 17h-19h).</p>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
