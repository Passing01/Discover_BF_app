@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <h1 class="mb-3 text-orange">Ajouter une chambre — {{ $hotel->name }}</h1>

  <form method="POST" action="{{ route('agency.rooms.store', $hotel) }}" enctype="multipart/form-data" class="panel-cream rounded-20 p-3 p-md-4">
    @csrf
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Nom</label>
        <input type="text" name="name" class="form-control" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Type</label>
        <input type="text" name="type" class="form-control" placeholder="Standard, Suite…" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Prix / nuit (CFA)</label>
        <input type="number" step="0.01" name="price_per_night" class="form-control" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Capacité</label>
        <input type="number" name="capacity" class="form-control" min="1">
      </div>
      <div class="col-12">
        <label class="form-label">Description</label>
        <textarea name="description" rows="4" class="form-control"></textarea>
      </div>
      <div class="col-12">
        <label class="form-label">Photo (optionnel)</label>
        <input type="file" name="photo" accept="image/*" class="form-control">
      </div>
      <div class="col-12">
        <label class="form-label">Galerie (plusieurs, optionnel)</label>
        <input type="file" name="gallery[]" accept="image/*" class="form-control" multiple>
      </div>
      <div class="col-12">
        <button class="btn btn-orange">Ajouter</button>
        <a href="{{ route('agency.hotels.index') }}" class="btn btn-cream">Annuler</a>
      </div>
    </div>
  </form>
</div>
@endsection
