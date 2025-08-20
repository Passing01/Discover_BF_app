@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <div class="panel-cream rounded-20 p-3 shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <div class="fw-bold">Communauté</div>
      <a href="{{ route('tourist.dashboard') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Retour</a>
    </div>
    <div class="panel-inner rounded-20 p-3 bg-white">
      <p class="text-muted mb-0">Espace communauté à venir (discussions, conseils, partages d'itinéraires).</p>
    </div>
  </div>
</div>
@endsection
