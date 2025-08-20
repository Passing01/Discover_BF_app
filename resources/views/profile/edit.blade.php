@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <nav aria-label="breadcrumb" class="mb-2">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('tourist.dashboard') }}">Tableau de bord</a></li>
      <li class="breadcrumb-item active" aria-current="page">Profil</li>
    </ol>
  </nav>

  <div class="d-flex align-items-center justify-content-between mb-2">
    <h1 class="h4 mb-0">Mon profil</h1>
    @if (session('status') === 'profile-updated')
      <span class="badge text-bg-success">Modifications enregistrées</span>
    @endif
  </div>

  <div class="row g-3">
    <div class="col-lg-7">
      <div class="panel-cream rounded-20 p-3 mb-3">
        <div class="fw-semibold mb-2">Informations personnelles</div>
        @include('profile.partials.update-profile-information-form')
      </div>

      <div class="panel-cream rounded-20 p-3 mb-3">
        <div class="fw-semibold mb-2">Sécurité • Mot de passe</div>
        @include('profile.partials.update-password-form')
      </div>

      <div class="panel-cream rounded-20 p-3">
        <div class="fw-semibold mb-2 text-danger">Suppression du compte</div>
        @include('profile.partials.delete-user-form')
      </div>
    </div>

    <div class="col-lg-5">
      <div class="panel-cream rounded-20 p-3 position-sticky" style="top: 12px;">
        <div class="d-flex align-items-center gap-3 mb-3">
          @php $avatar = auth()->user()->profile_picture ? asset('storage/'.auth()->user()->profile_picture) : null; @endphp
          <img id="profile-avatar-display" src="{{ $avatar }}" alt="Avatar" class="rounded-circle object-fit-cover" style="width:64px;height:64px; {{ $avatar ? '' : 'display:none;' }}">
          <div id="profile-initial-fallback" class="rounded-circle bg-light border" style="width:64px;height:64px; display:flex; align-items:center; justify-content:center; font-weight:600; {{ $avatar ? 'display:none;' : '' }}">
            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
          </div>
          <div>
            <div class="fw-semibold">{{ auth()->user()->name }}</div>
            <div class="small text-muted">{{ auth()->user()->email }}</div>
          </div>
        </div>
        <div class="small text-muted mb-2">Conseils</div>
        <ul class="small ps-3 mb-3">
          <li>Utilisez une adresse e‑mail accessible pour les confirmations.</li>
          <li>Un mot de passe fort améliore la sécurité de votre compte.</li>
          <li>Mettez à jour vos informations avant toute réservation.</li>
        </ul>
        <div class="d-grid gap-2">
          <a href="{{ route('tourist.bookings.index') }}" class="btn btn-light border">Mes réservations</a>
          <a href="{{ route('tourist.itinerary') }}" class="btn btn-light border">Mon itinéraire</a>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
  (function(){
    const input = document.querySelector('input[name="avatar"]');
    if(!input) return;
    const preview = document.getElementById('profile-avatar-preview');
    const display = document.getElementById('profile-avatar-display');
    const fallback = document.getElementById('profile-initial-fallback');
    input.addEventListener('change', function(){
      const file = this.files && this.files[0];
      if(!file) return;
      const reader = new FileReader();
      reader.onload = function(e){
        if(preview){ preview.src = e.target.result; preview.classList.remove('d-none'); }
        if(display){ display.src = e.target.result; display.style.display = 'block'; }
        if(fallback){ fallback.style.display = 'none'; }
      };
      reader.readAsDataURL(file);
    });
  })();
  </script>
@endpush
@endsection
