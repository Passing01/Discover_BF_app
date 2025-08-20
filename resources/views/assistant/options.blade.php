@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <div class="panel-cream rounded-20 p-3 shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <div class="fw-bold">Options avancées</div>
      <a href="{{ route('assistant.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Retour</a>
    </div>
    <div class="panel-inner rounded-20 p-3 bg-white">
      <form method="get" action="{{ route('assistant.index') }}" class="vstack gap-3">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Nombre de voyageurs</label>
            <input type="number" class="form-control" min="1" value="2">
          </div>
          <div class="col-md-6">
            <label class="form-label">Thème</label>
            <input type="text" class="form-control" placeholder="Nature + Ville">
          </div>
        </div>
        <div class="d-flex justify-content-end">
          <button class="btn btn-orange">Appliquer</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
