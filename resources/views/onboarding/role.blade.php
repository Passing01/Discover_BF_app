@extends('layouts.site')

@section('content')
<div class="container py-4">
  <h1 class="mb-3">Compléter votre profil ({{ strtoupper($role) }})</h1>

  @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <div class="alert alert-info">Votre compte a été activé en tant que <strong>{{ $role }}</strong>. Merci de renseigner les informations nécessaires.</div>

  <form method="POST" action="{{ route('onboarding.store') }}" enctype="multipart/form-data" class="card card-body">
    @csrf

    @if($role === 'guide')
      <div class="mb-3">
        <label class="form-label">Bio</label>
        <textarea name="bio" rows="3" class="form-control @error('bio') is-invalid @enderror" required>{{ old('bio') }}</textarea>
        @error('bio')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Langues parlées</label>
          <input type="text" name="languages" value="{{ old('languages') }}" class="form-control">
        </div>
        <div class="col-md-6">
          <label class="form-label">Spécialité</label>
          <input type="text" name="specialty" value="{{ old('specialty') }}" class="form-control">
        </div>
      </div>
    @elseif($role === 'event_organizer')
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Organisation</label>
          <input type="text" name="organization" value="{{ old('organization') }}" class="form-control @error('organization') is-invalid @enderror" required>
          @error('organization')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label">Site web (optionnel)</label>
          <input type="url" name="website" value="{{ old('website') }}" class="form-control">
        </div>
        <div class="col-md-4">
          <label class="form-label">Couleur de marque (hex)</label>
          <input type="text" name="brand_color" value="{{ old('brand_color') }}" class="form-control" placeholder="#0d6efd">
        </div>
        <div class="col-md-8">
          <label class="form-label">Logo (optionnel)</label>
          <input type="file" name="logo" class="form-control" accept="image/*">
          <div class="form-text">PNG/JPG, 4 Mo max.</div>
        </div>
      </div>
    @elseif($role === 'driver')
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Numéro de permis</label>
          <input type="text" name="license_number" value="{{ old('license_number') }}" class="form-control @error('license_number') is-invalid @enderror" required>
          @error('license_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label">Véhicule</label>
          <input type="text" name="vehicle" value="{{ old('vehicle') }}" class="form-control @error('vehicle') is-invalid @enderror" required>
          @error('vehicle')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
      </div>
    @elseif($role === 'hotel_manager')
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Nom de l'hôtel</label>
          <input type="text" name="hotel_name" value="{{ old('hotel_name') }}" class="form-control @error('hotel_name') is-invalid @enderror" required>
          @error('hotel_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label">Adresse</label>
          <input type="text" name="address" value="{{ old('address') }}" class="form-control @error('address') is-invalid @enderror" required>
          @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
      </div>
    @else
      <p>Rôle non reconnu.</p>
    @endif

    <div class="mt-3">
      <button class="btn btn-primary">Enregistrer</button>
      <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Plus tard</a>
    </div>
  </form>
</div>
@endsection
