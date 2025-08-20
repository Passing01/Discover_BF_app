@extends('layouts.admin')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <h4 class="section-title"><i class="bi bi-bell"></i> Notifications</h4>
  </div>
  @if(session('status'))
    <div class="alert alert-success d-flex align-items-center gap-2"><i class="bi bi-check-circle"></i> <span>{{ session('status') }}</span></div>
  @endif

  <div class="bg-secondary rounded p-4">
    <div class="d-flex align-items-center justify-content-between mb-2">
      <h6 class="card-title"><i class="bi bi-send"></i> Envoyer une notification à un rôle</h6>
    </div>
    <form method="POST" action="{{ route('admin.notifications.send') }}" class="row g-3">
      @csrf
      <div class="col-md-3">
        <label class="form-label">Rôle</label>
        <select name="role" class="form-select" required>
          @foreach(['tourist','guide','event_organizer','driver','hotel_manager','admin'] as $r)
            <option value="{{ $r }}">{{ $r }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Type</label>
        <input type="text" name="type" class="form-control" placeholder="broadcast, promo, info">
      </div>
      <div class="col-md-6">
        <label class="form-label">Titre</label>
        <input type="text" name="title" class="form-control" required>
      </div>
      <div class="col-12">
        <label class="form-label">Message</label>
        <textarea name="message" class="form-control" rows="5" placeholder="Tapez votre message..." required></textarea>
      </div>
      <div class="col-12 d-flex justify-content-end">
        <button class="btn btn-primary"><i class="bi bi-cursor-fill"></i> Envoyer</button>
      </div>
    </form>
  </div>
@endsection
