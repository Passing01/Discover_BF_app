@extends('layouts.admin')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <h4 class="section-title"><i class="bi bi-megaphone"></i> Gestion des publicités</h4>
  </div>
  @if(session('status'))
    <div class="alert alert-success d-flex align-items-center gap-2"><i class="bi bi-check-circle"></i> <span>{{ session('status') }}</span></div>
  @endif

  <div class="row g-3">
    <div class="col-lg-5">
      <div class="bg-secondary rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <h6 class="card-title"><i class="bi bi-plus-circle"></i> Créer une publicité</h6>
        </div>
        <form method="POST" action="{{ route('admin.ads.store') }}" class="row g-3">
          @csrf
          <div class="col-md-6">
            <label class="form-label">Emplacement</label>
            <input type="text" name="placement" class="form-control" placeholder="ex: role_sidebar" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Poids (ordre)</label>
            <input type="number" name="weight" class="form-control" min="0" max="1000" value="0">
          </div>
          <div class="col-12">
            <label class="form-label">Titre</label>
            <input type="text" name="title" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Image (path)</label>
            <input type="text" name="image_path" class="form-control" placeholder="/storage/ads/banner.jpg">
          </div>
          <div class="col-md-6">
            <label class="form-label">URL cible</label>
            <input type="text" name="target_url" class="form-control" placeholder="https://...">
          </div>
          <div class="col-md-6">
            <label class="form-label">CTA</label>
            <input type="text" name="cta_text" class="form-control" placeholder="Découvrir">
          </div>
          <div class="col-md-3">
            <label class="form-label">Début</label>
            <input type="datetime-local" name="starts_at" class="form-control">
          </div>
          <div class="col-md-3">
            <label class="form-label">Fin</label>
            <input type="datetime-local" name="ends_at" class="form-control">
          </div>
          <div class="col-12">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="enabled" id="ad-enabled" checked>
              <label class="form-check-label" for="ad-enabled">Activer</label>
            </div>
          </div>
          <div class="col-12">
            <button class="btn btn-primary"><i class="bi bi-save"></i> Enregistrer</button>
          </div>
        </form>
      </div>
    </div>

    <div class="col-lg-7">
      <div class="bg-secondary rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <h6 class="card-title"><i class="bi bi-collection"></i> Publicités</h6>
        </div>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead>
              <tr>
                <th><i class="bi bi-pin-map"></i> Emplacement</th>
                <th><i class="bi bi-card-text"></i> Titre</th>
                <th><i class="bi bi-power"></i> Actif</th>
                <th><i class="bi bi-clock-history"></i> Période</th>
                <th class="text-end"><i class="bi bi-gear"></i> Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($ads as $ad)
                <tr>
                  <td><span class="badge bg-secondary">{{ $ad->placement }}</span></td>
                  <td>{{ $ad->title }}</td>
                  <td>
                    @if($ad->enabled)
                      <span class="badge bg-success">Oui</span>
                    @else
                      <span class="badge bg-secondary">Non</span>
                    @endif
                  </td>
                  <td class="text-secondary">{{ $ad->starts_at?->format('d/m/Y H:i') ?? '—' }} → {{ $ad->ends_at?->format('d/m/Y H:i') ?? '—' }}</td>
                  <td class="text-end">
                    <form method="POST" action="{{ route('admin.ads.toggle', $ad) }}" class="d-inline">@csrf<button class="btn btn-sm btn-outline-light"><i class="bi bi-toggle2-on"></i> Basculer</button></form>
                  </td>
                </tr>
              @empty
                <tr><td colspan="5" class="text-secondary">Aucune publicité</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="mt-2">{{ $ads->links() }}</div>
      </div>
    </div>
  </div>
@endsection
