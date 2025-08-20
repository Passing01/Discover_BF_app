@extends('layouts.admin')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
   <h4 class="section-title"><i class="bi bi-pencil-square"></i> Modifier l'utilisateur</h4>
  </div>

  @if ($errors->any())
    <div class="alert alert-danger d-flex align-items-start gap-2">
      <i class="bi bi-exclamation-triangle-fill"></i>
      <div>
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    </div>
  @endif

  <form method="POST" action="{{ route('admin.users.update', $user->id) }}" class="bg-secondary rounded p-4">
    @csrf
    @method('PATCH')

    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Prénom</label>
        <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $user->first_name) }}" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Nom</label>
        <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $user->last_name) }}" required>
      </div>

      <div class="col-md-6">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Téléphone</label>
        <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
      </div>

      <div class="col-md-6">
        <label class="form-label">Rôle</label>
        <select name="role" class="form-select" required>
          @foreach(['tourist','guide','event_organizer','driver','hotel_manager','admin'] as $r)
            <option value="{{ $r }}" @selected(old('role', $user->role) === $r)>{{ $r }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-md-6">
        <label class="form-label">Statut du compte</label>
        <select name="is_active" class="form-select" required>
          <option value="1" @selected(old('is_active', $user->is_active) == true)>Actif</option>
          <option value="0" @selected(old('is_active', $user->is_active) == false)>Inactif</option>
        </select>
      </div>
    </div>

    <div class="mt-3 d-flex gap-2">
      <button class="btn btn-primary" type="submit"><i class="bi bi-save"></i> Enregistrer</button>
      <a href="{{ route('admin.users') }}" class="btn btn-outline-light"><i class="bi bi-x-circle"></i> Annuler</a>
    </div>
  </form>
@endsection
