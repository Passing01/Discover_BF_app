@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <div class="panel-cream rounded-20 p-3 shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <div class="fw-bold">Conditions d'utilisation</div>
      <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Retour</a>
    </div>
    <div class="panel-inner rounded-20 p-3 bg-white">
      <p class="text-muted">Texte légal à compléter...</p>
    </div>
  </div>
</div>
@endsection
